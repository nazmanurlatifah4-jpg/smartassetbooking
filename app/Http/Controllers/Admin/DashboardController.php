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
        // 1. Ambil data statistik (Sesuaikan nama variabel dengan yang dipanggil di Blade)
        $totalUser = User::where('role', 'peminjam')->count();
        $totalAset = Aset::count();
        
        // Di Blade kamu panggil $peminjaman untuk "Peminjaman Aktif"
        $peminjaman = Peminjaman::where('status', 'Disetujui')->count(); 
        
        $terlambat = Peminjaman::where('status', 'Disetujui')
                        ->where('tanggal_kembali', '<', now()->toDateString())
                        ->count();

        $denda = Denda::where('status_bayar', 'Belum Lunas')->count();

        // Di Blade kamu panggil $selesai untuk "Selesai Hari Ini"
        $selesai = Peminjaman::where('status', 'Selesai')
                        ->whereDate('updated_at', today())
                        ->count();

        // 2. Ambil data peminjaman terbaru untuk tabel
        $peminjamanTerbaru = Peminjaman::with(['user', 'aset'])
                                ->latest()
                                ->limit(10)
                                ->get();

        // 3. Kirim ke view dengan nama variabel yang pas
        return view('admin.dashboard', compact(
            'totalUser', 
            'totalAset', 
            'peminjaman', // Pastikan namanya 'peminjaman' bukan 'aktif'
            'terlambat', 
            'denda', 
            'selesai',    // Pastikan namanya 'selesai' bukan 'selesaiHariIni'
            'peminjamanTerbaru'
        ));
    }
}