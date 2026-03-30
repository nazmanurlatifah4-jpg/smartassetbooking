<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Aset;
use App\Models\Peminjaman;
use App\Models\Denda;

class DashboardController extends Controller
{
    public function index()
    {
        // Stats — pakai nama kolom & enum sesuai migration
        $totalUser  = User::where('role', 'peminjam')->count();
        $totalAset  = Aset::count();

        $menunggu   = Peminjaman::where('status', 'Menunggu')->count();

        $aktif      = Peminjaman::where('status', 'Disetujui')->count();

        // Terlambat = status Disetujui tapi tanggal_kembali sudah lewat
        $terlambat  = Peminjaman::where('status', 'Disetujui')
                        ->where('tanggal_kembali', '<', now()->toDateString())
                        ->count();

        $denda      = Denda::where('status_bayar', 'Belum Lunas')->count();

        $selesaiHariIni = Peminjaman::where('status', 'Selesai')
                            ->whereDate('updated_at', today())
                            ->count();

        // Pengajuan terbaru (10 terakhir) — eager load user & aset
        $pengajuanTerbaru = Peminjaman::with(['user', 'aset'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalUser', 'totalAset', 'menunggu',
            'aktif', 'terlambat', 'denda',
            'selesaiHariIni', 'pengajuanTerbaru'
        ));
    }
}