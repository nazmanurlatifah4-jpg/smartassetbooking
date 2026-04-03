<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Verifikasi;
use App\Models\Pengembalian;
use App\Models\Aset;
use App\Models\User;
use App\Models\Denda;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransaksiController extends Controller
{
    // ── INDEX ─────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'aset']);

        // Filter status — enum sesuai migration: Menunggu, Disetujui, Ditolak, Selesai
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search nama peminjam atau nama aset
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('user', fn($u) => $u->where('nama', 'like', "%$q%"))
                    ->orWhereHas('aset', fn($a) => $a->where('nama_aset', 'like', "%$q%"));
            });
        }

        $peminjaman = $query->latest()->paginate(15);

        // Stats
        $stats = [
        'total'     => Peminjaman::count(),
        'menunggu'  => Peminjaman::where('status', 'Menunggu')->count(),
        'aktif'     => Peminjaman::where('status', 'Disetujui')->count(),
        'terlambat' => Peminjaman::where('status', 'Disetujui')
                        ->where('tanggal_kembali', '<', now()->toDateString())
                        ->count(),
    ];

        $users = User::where('role', 'peminjam')->get();
        $asets = Aset::all();

        return view('admin.transaksi', compact('peminjaman', 'stats', 'users', 'asets'));
    }

    // ── SHOW ──────────────────────────────────────────────────────
    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['user', 'aset', 'verifikasi.admin', 'pengembalian', 'denda']);
        return view('admin.transaksi-detail', compact('peminjaman'));
    }

    // ── STORE (admin buat peminjaman manual) ──────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'         => ['required', 'exists:users,id'],
            'aset_id'         => ['required', 'exists:asets,id'],
            'tanggal_pinjam'  => ['required', 'date'],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
            'keperluan'       => ['nullable', 'string', 'max:500'],
        ]);

        $aset = Aset::findOrFail($data['aset_id']);

        if ($aset->stok < 1) { 
        return redirect()->back()->with('error', 'Stok aset tidak tersedia!');
    }

        DB::transaction(function () use ($data, $aset) {

            $peminjaman = Peminjaman::create([
                'user_id'         => $data['user_id'],
                'aset_id'         => $data['aset_id'],
                'tanggal_pinjam'  => $data['tanggal_pinjam'],
                'tanggal_kembali' => $data['tanggal_kembali'], // Sesuai DB
                'keperluan'       => $data['keperluan'],
                'status'          => 'Disetujui', 
                'tanggal_disetujui' => now()->toDateString(),
            ]);

            // Buat record verifikasi
            Verifikasi::create([
                'peminjaman_id' => $peminjaman->id,
                'admin_id'      => auth()->id(),
                'status'        => 'Disetujui',
                'tanggal'       => now()->toDateString(),
                'catatan'       => 'Dibuat langsung oleh admin.',
            ]);

            // Kurangi stok aset
            $aset->decrement('stok');

            // Buat record verifikasi
            Verifikasi::create([
            'peminjaman_id' => $peminjaman->id,
            'admin_id'      => auth()->id(),
            'status'        => 'Disetujui',
            'tanggal'       => now()->toDateString(),
            'catatan'       => 'Dibuat langsung oleh admin.',
        ]);

            // Kirim notifikasi ke peminjam
                Notifikasi::kirim(
                $peminjaman->user_id,
                'Peminjaman Disetujui',
                'Peminjaman ' . $aset->nama_aset . ' telah disetujui. Silakan ambil aset.'
            );
        });

        return redirect()->route('admin.transaksi')->with('success', 'Peminjaman berhasil dibuat!');
}

public function approve(Request $request, Peminjaman $peminjaman)
{
    // 1. Cek stok di tabel asets (panggil kolom 'stok')
    if ($peminjaman->aset->stok < 1) {
        return redirect()->back()->with('error', 'Maaf, stok aset sudah habis!');
    }

    DB::transaction(function () use ($peminjaman, $request) {
        // 2. Update status peminjaman
        $peminjaman->update([
            'status' => 'Disetujui',
            'tanggal_disetujui' => now(),
        ]);

        // 3. KURANGI STOK ASET (PENTING!)
        $peminjaman->aset->decrement('stok', 1);

        // 4. Catat Verifikasi
        Verifikasi::create([
            'peminjaman_id' => $peminjaman->id,
            'admin_id'      => auth()->id(),
            'status'        => 'Disetujui',
            'tanggal'       => now(),
        ]);
    });

    return redirect()->back()->with('success', 'Peminjaman disetujui & stok otomatis berkurang!');
}

// ── REJECT ────────────────────────────────────────────────────
public function reject(Request $request, Peminjaman $peminjaman)
{
    // Cek status biar gak double proses
    if ($peminjaman->status !== 'Menunggu') {
        return redirect()->back()->with('error', 'Transaksi ini sudah diproses sebelumnya.');
    }

    DB::transaction(function () use ($peminjaman, $request) {
        // 1. Update status jadi Ditolak
        $peminjaman->update([
            'status' => 'Ditolak',
            'catatan' => $request->catatan ?? 'Ditolak oleh admin.'
        ]);

        // 2. Buat Verifikasi (agar muncul di history)
        Verifikasi::create([
            'peminjaman_id' => $peminjaman->id,
            'admin_id'      => auth()->id(),
            'status'        => 'Ditolak',
            'tanggal'       => now()->toDateString(),
            'catatan'       => $request->catatan,
        ]);
    });

    return redirect()->back()->with('success', 'Peminjaman berhasil ditolak.');
}

// ── KONFIRMASI KEMBALI ────────────────────────────────────────
    public function konfirmasiKembali(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'kondisi_barang' => ['required', Rule::in(['Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'])],
            'catatan_admin'  => ['nullable', 'string', 'max:500'],
            'foto'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if (!in_array($peminjaman->status, ['Disetujui'])) {
            return redirect()->route('admin.transaksi')
                ->with('error', 'Status peminjaman tidak valid untuk dikembalikan!');
        }

        DB::transaction(function () use ($peminjaman, $request) {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('pengembalian', 'public');
            }

            // Buat record pengembalian
            $pengembalian = Pengembalian::create([
                'peminjaman_id'       => $peminjaman->id,
                'tanggal_pengembalian'=> now()->toDateString(),
                'kondisi_barang'      => $request->kondisi_barang,
                'foto'                => $fotoPath,
                'status_verifikasi'   => 'Diterima',
                'catatan_admin'       => $request->catatan_admin,
            ]);

            // Update status peminjaman
            $peminjaman->update(['status' => 'Selesai']);

            $peminjaman->aset->increment('stok', 1);

            // Cek apakah perlu denda
            $terlambat     = $peminjaman->isTerlambat();
            $hariTerlambat = $peminjaman->hari_terlambat;
            $butuhDenda    = $terlambat || in_array($request->kondisi_barang, ['Rusak Berat', 'Hilang']);

            if ($butuhDenda) {
                $tarifPerHari = 5000; // default, bisa diambil dari config/db
                $jenisDenda   = 'Telat';

                if ($request->kondisi_barang === 'Hilang') {
                    $jenisDenda = 'Hilang';
                    $hariTerlambat = 0;
                } elseif ($request->kondisi_barang === 'Rusak Berat') {
                    $jenisDenda = 'Rusak Berat';
                    $hariTerlambat = 0;
                }

                $totalDenda = match($jenisDenda) {
                    'Telat'       => $hariTerlambat * $tarifPerHari,
                    'Rusak Berat' => 100000, // nominal tetap, sesuaikan kebutuhan
                    'Hilang'      => 200000, // nominal tetap, sesuaikan kebutuhan
                    default       => 0,
                };

                Denda::create([
                    'peminjaman_id'  => $peminjaman->id,
                    'pengembalian_id'=> $pengembalian->id,
                    'jenis_denda'    => $jenisDenda,
                    'jumlah_hari'    => $hariTerlambat,
                    'tarif_per_hari' => $tarifPerHari,
                    'total_denda'    => $totalDenda,
                    'status_bayar'   => 'Belum Lunas',
                ]);

                Notifikasi::kirim(
                    $peminjaman->user_id,
                    'Denda Peminjaman',
                    'Kamu memiliki denda Rp ' . number_format($totalDenda, 0, ',', '.') . ' untuk peminjaman ' . $peminjaman->aset->nama_aset . '. Segera lunasi.'
                );
            } else {
                Notifikasi::kirim(
                    $peminjaman->user_id,
                    'Pengembalian Diterima',
                    'Pengembalian ' . $peminjaman->aset->nama_aset . ' telah diterima. Terima kasih!'
                );
            }
        });

        return redirect()->route('admin.transaksi')
            ->with('success', 'Pengembalian berhasil dikonfirmasi!');
    }

    // ── DESTROY ───────────────────────────────────────────────────
    public function destroy(Peminjaman $peminjaman)
    {
        if (in_array($peminjaman->status, ['Disetujui', 'Menunggu'])) {
            return redirect()->route('admin.transaksi')
                ->with('error', 'Tidak bisa menghapus peminjaman yang masih aktif!');
        }

        $peminjaman->delete(); // SoftDelete

        return redirect()->route('admin.transaksi')
            ->with('success', 'Data peminjaman berhasil dihapus!');
    }
}