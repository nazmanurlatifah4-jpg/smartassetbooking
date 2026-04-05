<?php

namespace App\Http\Controllers\Peminjam;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Aset;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Notifikasi;
use App\Models\Denda;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PeminjamController extends Controller
{
    // ── DASHBOARD ────────────────────────────────────────────────
    // ── DASHBOARD ────────────────────────────────────────────────
    public function dashboard()
    {
        $user = auth()->user();

        // 1. Hitung angka untuk Card
        $stats = [
            'aktif'     => Peminjaman::where('user_id', $user->id)->where('status', 'Disetujui')->count(),
            'diproses'  => Peminjaman::where('user_id', $user->id)->where('status', 'Menunggu')->count(),
            'terlambat' => Peminjaman::where('user_id', $user->id)
                            ->whereIn('status', ['Disetujui', 'Dipinjam']) // Status aktif
                            ->where('tanggal_kembali', '<', Carbon::now()->toDateString())
                            ->count(),
            'selesai'   => Peminjaman::where('user_id', $user->id)->where('status', 'Selesai')->count(),
            'total'     => Peminjaman::where('user_id', $user->id)->count(),
        ];

        // 2. Ambil data untuk Tabel & Sidebar (Gunakan nama yang konsisten)
        $riwayatTerbaru = Peminjaman::with('aset')->where('user_id', $user->id)->latest()->limit(5)->get();
        $riwayatSidebar = Peminjaman::with('aset')->where('user_id', $user->id)->where('status', 'Selesai')->latest()->limit(3)->get();
        $notifSidebar   = Notifikasi::where('user_id', $user->id)->latest()->limit(3)->get();

        return view('peminjam.dashboard', compact('stats', 'riwayatSidebar', 'notifSidebar', 'riwayatTerbaru'));
    }
    // ── DATA ASET ────────────────────────────────────────────────
    public function aset(Request $request)
    {
        // Tampilkan semua aset (stok tersedia maupun habis)
        $asets = Aset::latest()->get();

        // Kategori unik untuk filter button
        $kategoris = Aset::select('kategori')->distinct()->orderBy('kategori')->pluck('kategori');

        return view('peminjam.dataaset', compact('asets', 'kategoris'));
    }

    // ── TRANSAKSI PEMINJAMAN - Form ───────────────────────────────
    public function peminjaman()
    {
        return view('peminjam.transaksi-peminjaman');
    }

    // ── TRANSAKSI PEMINJAMAN - Store ──────────────────────────────
    public function peminjamanStore(Request $request)
{   
    $pinjamanAktif = Peminjaman::where('user_id', auth()->id())
        ->whereIn('status', ['Menunggu', 'Disetujui', 'Terlambat']) // Status yang dianggap belum selesai
        ->exists();

    if ($pinjamanAktif) {
        return redirect()->back()->with('error', 'Gagal! Kamu masih memiliki pinjaman yang belum dikembalikan. Selesaikan dulu pinjaman sebelumnya ya!');
    }

    $request->validate([
        'tanggal_pengajuan' => ['required', 'date'],
        'tanggal_kembali'   => ['required', 'date', 'after_or_equal:tanggal_pengajuan'],
        'keperluan'         => ['nullable', 'string', 'max:500'],
        'cart'              => ['required', 'string'], // Temanmu pakai nama 'cart'
    ]);

    // AMBIL DATA DARI INPUT 'cart'
    $cart = json_decode($request->cart, true);

    if (empty($cart)) {
        return redirect()->back()->with('error', 'Keranjang kosong!');
    }

    // Validasi durasi maksimal 7 hari
    $diff = Carbon::parse($request->tanggal_pengajuan)
                ->diffInDays(Carbon::parse($request->tanggal_kembali));
    if ($diff > 7) {
        return redirect()->back()->with('error', 'Maksimal durasi peminjaman adalah 7 hari!');
    }

    try {
        DB::transaction(function () use ($request, $cart) {
            foreach ($cart as $item) {
                $aset = Aset::find($item['id']);
                
                if (!$aset || $aset->stok < $item['jumlah']) {
                    throw new \Exception("Stok aset '{$item['name']}' tidak mencukupi!");
                }

                // 1. STOK DIBIARKAN UTUH KARENA SUDAH DIKALKULASI OTOMATIS OLEH FUNGSI stokTersedia()
                // $aset->decrement('stok', $item['jumlah']);

                // 2. BUAT RECORD (Biar muncul di riwayat & admin)
                Peminjaman::create([
                    'user_id'           => auth()->id(),
                    'aset_id'           => $item['id'],
                    'jumlah'            => $item['jumlah'],
                    'tanggal_pengajuan' => $request->tanggal_pengajuan,
                    'tanggal_kembali'   => $request->tanggal_kembali,
                    'keperluan'         => $request->keperluan,
                    'status'            => 'Menunggu',
                ]);

                // 3. NOTIFIKASI
                Notifikasi::create([
                    'user_id' => auth()->id(),
                    'judul'   => 'Pengajuan Dikirim',
                    'pesan'   => 'Pengajuan peminjaman ' . $aset->nama_aset . ' sedang diproses.',
                    'is_read' => false
                ]);
            }
        });

        return redirect()->route('peminjam.riwayat')
            ->with('success', 'Berhasil! Data sudah masuk ke riwayat dan stok berkurang.');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
    }
}

    // ── TRANSAKSI PENGEMBALIAN - Form ─────────────────────────────
    public function pengembalian()
    {
        // Hanya tampilkan peminjaman milik user ini yang berstatus Disetujui
        $peminjamansAktif = Peminjaman::with('aset')
            ->where('user_id', auth()->id())
            ->where('status', 'Disetujui')
            ->get();

        return view('peminjam.transaksi-pengembalian', compact('peminjamansAktif'));
    }

    // ── TRANSAKSI PENGEMBALIAN - Store ────────────────────────────
    public function pengembalianStore(Request $request)
{
    // 1. Ambil data peminjaman dulu untuk validasi 'max' jumlah
    $peminjaman = Peminjaman::where('id', $request->peminjaman_id)
    ->where('id', $request->peminjaman_id)
    ->where('user_id', auth()->id())
    ->where('status', 'Disetujui')
    ->firstOrFail();

    if (!$peminjaman) {
        return redirect()->back()->with('error', 'Data peminjaman tidak ditemukan atau sudah diproses.');
    }

    $maxJumlah = $peminjaman->jumlah ?? 0;

    // 2. Validasi dengan tambahan limitasi 'max'
    $request->validate([
    'peminjaman_id'  => ['required', 'exists:peminjamans,id'],
    
    // Gunakan ?? 0 atau ?? 1 agar tidak NULL jika data di DB kosong
    'jumlah'         => ['required', 'integer', 'min:1', 'max:' . ($peminjaman->jumlah ?? 0)], 
    
    'kondisi_barang' => ['required', Rule::in(['Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'])],
    'foto'           => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
], [
    'jumlah.max' => 'Jumlah yang dikembalikan tidak boleh melebihi jumlah pinjam (' . ($peminjaman->jumlah ?? 0) . ' unit).',
]);

    DB::transaction(function () use ($request, $peminjaman) {
        // Simpan foto
        $fotoPath = $request->file('foto')->store('pengembalian', 'public');

        // 3. Buat record pengembalian (Status langsung 'Diterima')
        Pengembalian::create([
            'peminjaman_id'       => $peminjaman->id,
            'tanggal_pengembalian'=> now()->toDateString(),
            'kondisi_barang'      => $request->kondisi_barang,
            'foto'                => $fotoPath,
            'status_verifikasi'   => 'Diterima', // Langsung diterima tanpa nunggu admin
            'catatan_admin'       => $request->catatan_admin,
        ]);

        // 4. Update status peminjaman JADI 'Selesai'
        $peminjaman->update([
            'status'     => 'Selesai', 
        ]);

        // 5. STOK TIDAK PERLU DIUBAH SECARA MANUAL, FUNGSI stokTersedia() AKAN OTOMATIS MENYESUAIKAN
        // $peminjaman->aset->increment('stok', (int)$request->jumlah);
        // 6. Notifikasi untuk Admin (Opsional, buat info saja)
        Notifikasi::create([
            'user_id' => 1, // ID Admin (biasanya 1)
            'judul'   => 'Aset Dikembalikan',
            'pesan'   => auth()->user()->name . ' telah mengembalikan ' . $peminjaman->aset->nama_aset,
            'is_read' => false
        ]);
    });

    return redirect()->route('peminjam.riwayat')
        ->with('success', 'Barang berhasil dikembalikan. Stok aset telah diperbarui!');
}

    // ── RIWAYAT ──────────────────────────────────────────────────
    public function riwayat(Request $request)
    {
        $query = Peminjaman::with(['aset', 'pengembalian'])
            ->where('user_id', auth()->id());

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search nama aset
        if ($request->filled('search')) {
            $q = $request->search;
            $query->whereHas('aset', fn($a) => $a->where('nama_aset', 'like', "%$q%"));
        }

        $riwayat = $query->latest()->paginate(15);

        return view('peminjam.riwayat', compact('riwayat'));
    }

    // ── TENTANG ──────────────────────────────────────────────────
    public function tentang()
    {
        return view('peminjam.tentang');
    }

    // ── DENDA ────────────────────────────────────────────────────
    public function denda(Request $request)
    {
        $query = Denda::with(['peminjaman.aset'])
            ->whereHas('peminjaman', fn($q) => $q->where('user_id', auth()->id()));

        // Filter status_bayar: Belum Lunas | Lunas
        if ($request->filled('status')) {
            $query->where('status_bayar', $request->status);
        }

        $dendas          = $query->latest()->paginate(15);
        $dendaBelumLunas = Denda::whereHas('peminjaman', fn($q) => $q->where('user_id', auth()->id()))
                            ->where('status_bayar', 'Belum Lunas')
                            ->get();

        // Stats
        $stats = [
            'belum_lunas'  => Denda::whereHas('peminjaman', fn($q) => $q->where('user_id', auth()->id()))
                                ->where('status_bayar', 'Belum Lunas')->count(),
            'total_nominal'=> Denda::whereHas('peminjaman', fn($q) => $q->where('user_id', auth()->id()))
                                ->where('status_bayar', 'Belum Lunas')->sum('total_denda'),
            'sudah_lunas'  => Denda::whereHas('peminjaman', fn($q) => $q->where('user_id', auth()->id()))
                                ->where('status_bayar', 'Lunas')->count(),
            'total_lunas'  => Denda::whereHas('peminjaman', fn($q) => $q->where('user_id', auth()->id()))
                                ->where('status_bayar', 'Lunas')->sum('total_denda'),
        ];

        // JSON untuk modal detail di JS
        $dendaJson = Denda::with(['peminjaman.aset'])
            ->whereHas('peminjaman', fn($q) => $q->where('user_id', auth()->id()))
            ->get()
            ->mapWithKeys(fn($d) => [
                $d->id => [
                    'aset'          => $d->peminjaman->aset->nama_aset,
                    'jenis_denda'   => $d->jenis_denda,
                    'jumlah_hari'   => $d->jumlah_hari,
                    'tarif_per_hari'=> $d->tarif_per_hari,
                    'total_format'  => $d->total_format,
                    'status_bayar'  => $d->status_bayar,
                    'tgl_pengajuan' => $d->peminjaman->tanggal_pengajuan->format('d M Y'),
                    'catatan_admin' => $d->catatan_admin,
                ]
            ]);

        return view('peminjam.denda', compact(
            'dendas', 'dendaBelumLunas', 'stats', 'dendaJson'
        ));
    }

    }
