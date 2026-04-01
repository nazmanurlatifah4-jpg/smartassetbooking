<?php

namespace App\Http\Controllers\Peminjam;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Aset;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Notifikasi;
use App\Models\Denda;
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
                            ->where('status', 'Disetujui')
                            ->where('tanggal_kembali', '<', now()->toDateString())
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
        $request->validate([
            'tanggal_pengajuan' => ['required', 'date'],
            'tanggal_kembali'   => ['required', 'date', 'after_or_equal:tanggal_pengajuan'],
            'keperluan'         => ['nullable', 'string', 'max:500'],
            'cart'              => ['required', 'string'], // JSON dari localStorage
        ]);

        $cart = json_decode($request->cart, true);

        if (empty($cart)) {
            return redirect()->route('peminjam.peminjaman')
                ->with('error', 'Keranjang kosong!');
        }

        // Validasi durasi maksimal 7 hari
        $diff = \Carbon\Carbon::parse($request->tanggal_pengajuan)
                    ->diffInDays(\Carbon\Carbon::parse($request->tanggal_kembali));
        if ($diff > 7) {
            return redirect()->route('peminjam.peminjaman')
                ->with('error', 'Maksimal durasi peminjaman adalah 7 hari!');
        }

        DB::transaction(function () use ($request, $cart) {
            foreach ($cart as $item) {
                $aset = Aset::find($item['id']);
                if (!$aset || $aset->stokTersedia() < 1) continue;

                // Buat record peminjaman — kolom sesuai migration
                $peminjaman = Peminjaman::create([
                    'user_id'           => auth()->id(),
                    'aset_id'           => $item['id'],
                    'tanggal_pengajuan' => $request->tanggal_pengajuan,
                    'tanggal_kembali'   => $request->tanggal_kembali,
                    'keperluan'         => $request->keperluan,
                    'status'            => 'Menunggu', // enum: Menunggu
                ]);

                // Kirim notifikasi ke peminjam
                Notifikasi::kirim(
                    auth()->id(),
                    'Pengajuan Dikirim',
                    'Pengajuan peminjaman ' . $aset->nama_aset . ' sedang diproses admin.'
                );
            }
        });

        return redirect()->route('peminjam.riwayat')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim! Tunggu persetujuan admin.');
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
        $request->validate([
            'peminjaman_id'  => ['required', 'exists:peminjamans,id'],
            'jumlah'         => ['required', 'integer', 'min:1'],
            'kondisi_barang' => ['required', Rule::in(['Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'])],
            'foto'           => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'catatan_admin'  => ['nullable', 'string', 'max:500'],
        ]);

        // Pastikan peminjaman milik user ini
        $peminjaman = Peminjaman::where('id', $request->peminjaman_id)
            ->where('user_id', auth()->id())
            ->where('status', 'Disetujui')
            ->firstOrFail();

        DB::transaction(function () use ($request, $peminjaman) {
            // Simpan foto
            $fotoPath = $request->file('foto')->store('pengembalian', 'public');

            // Buat record pengembalian
            $pengembalian = Pengembalian::create([
                'peminjaman_id'       => $peminjaman->id,
                'tanggal_pengembalian'=> now()->toDateString(),
                'kondisi_barang'      => $request->kondisi_barang,
                'foto'                => $fotoPath,
                'status_verifikasi'   => 'Menunggu', // enum: Menunggu, Diterima, Ditolak
                'catatan_admin'       => $request->catatan_admin,
            ]);

            // Update status peminjaman
            $peminjaman->update(['status' => 'Selesai']);

            // Cek apakah terlambat → buat denda otomatis
            if ($peminjaman->isTerlambat()) {
                $hariTerlambat = $peminjaman->hari_terlambat;
                $tarifPerHari  = 5000;
                Denda::create([
                    'peminjaman_id'   => $peminjaman->id,
                    'pengembalian_id' => $pengembalian->id,
                    'jenis_denda'     => 'Telat',
                    'jumlah_hari'     => $hariTerlambat,
                    'tarif_per_hari'  => $tarifPerHari,
                    'total_denda'     => $hariTerlambat * $tarifPerHari,
                    'status_bayar'    => 'Belum Lunas',
                ]);
                Notifikasi::kirim(
                    auth()->id(),
                    'Denda Keterlambatan',
                    'Kamu terlambat ' . $hariTerlambat . ' hari mengembalikan ' . $peminjaman->aset->nama_aset . '. Denda akan diinformasikan admin.'
                );
            }

            // Cek kondisi rusak/hilang → buat denda
            if (in_array($request->kondisi_barang, ['Rusak Berat', 'Hilang'])) {
                Denda::create([
                    'peminjaman_id'   => $peminjaman->id,
                    'pengembalian_id' => $pengembalian->id,
                    'jenis_denda'     => $request->kondisi_barang === 'Hilang' ? 'Hilang' : 'Rusak Berat',
                    'jumlah_hari'     => 0,
                    'tarif_per_hari'  => 0,
                    'total_denda'     => 0, // nominal ditentukan admin
                    'status_bayar'    => 'Belum Lunas',
                    'catatan_admin'   => 'Menunggu penilaian admin — kondisi: ' . $request->kondisi_barang,
                ]);
                Notifikasi::kirim(
                    auth()->id(),
                    'Laporan Kondisi Aset',
                    'Kondisi ' . $peminjaman->aset->nama_aset . ' dilaporkan ' . $request->kondisi_barang . '. Admin akan menindaklanjuti.'
                );
            } else {
                Notifikasi::kirim(
                    auth()->id(),
                    'Pengembalian Terkirim',
                    'Pengembalian ' . $peminjaman->aset->nama_aset . ' berhasil dikirim. Menunggu verifikasi admin.'
                );
            }
        });

        return redirect()->route('peminjam.riwayat')
            ->with('success', 'Pengembalian berhasil dikirim! Admin akan memverifikasi.');
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
