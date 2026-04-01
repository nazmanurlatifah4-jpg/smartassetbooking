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
        // 1. Ambil data statistik
        $totalUser      = User::where('role', 'peminjam')->count();
        $totalAset      = Aset::count();
        $menunggu       = Peminjaman::where('status', 'Menunggu')->count();
        $aktif          = Peminjaman::where('status', 'Disetujui')->count();
        
        $terlambat      = Peminjaman::where('status', 'Disetujui')
                            ->where('tanggal_kembali', '<', now()->toDateString())
                            ->count();

        $denda          = Denda::where('status_bayar', 'Belum Lunas')->count();

        $selesaiHariIni = Peminjaman::where('status', 'Selesai')
                            ->whereDate('updated_at', today())
                            ->count();

        // 2. Ambil data peminjaman untuk tabel (PAKAI SATU NAMA SAJA)
        // Kita pakai nama 'peminjamanTerbaru' supaya cocok dengan @foreach di Blade kamu
        $peminjamanTerbaru = Peminjaman::with(['user', 'aset'])
                            ->latest()
                            ->limit(10)
                            ->get();

        // 3. Masukkan ke array statCards untuk kotak-kotak di dashboard
        $statCards = [
            'total'     => $totalAset,
            'menunggu'  => $menunggu,
            'dipinjam'  => $aktif,
            'terlambat' => $terlambat,
        ];

return view('admin.dashboard', compact(
    'totalUser', 'totalAset', 'menunggu',
    'aktif', 'terlambat', 'denda',
    'selesaiHariIni', 'pengajuanTerbaru', 
    '$statCards' 
));
    }
}