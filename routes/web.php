<?php

use Illuminate\Support\Facades\Route;

// ── Controllers (Admin) ──────────────────────────────────────────
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MasterdataController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AsetController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\DendaController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Peminjam\PeminjamController;

// ─────────────────────────────────────────────────────────────────
// PUBLIC & AUTH
// ─────────────────────────────────────────────────────────────────

Route::get('/', fn() => view('landing'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ─────────────────────────────────────────────────────────────────
// ADMIN ROUTES — role:admin
// ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data
    Route::get('/masterdata', [MasterdataController::class, 'index'])->name('masterdata');

    // User CRUD
    Route::post  ('/users',         [UserController::class, 'store']  )->name('users.store');
    Route::put   ('/users/{user}', [UserController::class, 'update'] )->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Aset CRUD
    Route::post  ('/aset',         [AsetController::class, 'store']  )->name('aset.store');
    Route::put   ('/aset/{aset}', [AsetController::class, 'update'] )->name('aset.update');
    Route::delete('/aset/{aset}', [AsetController::class, 'destroy'])->name('aset.destroy');

    // Kategori CRUD
    Route::post  ('/kategori',             [KategoriController::class, 'store']   )->name('kategori.store');
    Route::put   ('/kategori/{kategori}',   [KategoriController::class, 'update']  )->name('kategori.update');
    Route::delete('/kategori/{kategori}',   [KategoriController::class, 'destroy'] )->name('kategori.destroy');

    // Transaksi
    Route::get   ('/transaksi',                          [TransaksiController::class, 'index']           )->name('transaksi');
    Route::post  ('/transaksi',                          [TransaksiController::class, 'store']           )->name('transaksi.store');
    Route::get   ('/transaksi/{peminjaman}',             [TransaksiController::class, 'show']            )->name('transaksi.show');
    Route::post  ('/transaksi/{peminjaman}/approve',     [TransaksiController::class, 'approve']         )->name('transaksi.approve');
    Route::post  ('/transaksi/{peminjaman}/reject',      [TransaksiController::class, 'reject']          )->name('transaksi.reject');
    Route::post  ('/transaksi/{peminjaman}/kembali',     [TransaksiController::class, 'konfirmasiKembali'])->name('transaksi.kembali');
    Route::delete('/transaksi/{peminjaman}',             [TransaksiController::class, 'destroy']         )->name('transaksi.destroy');

    // Denda 
    Route::get   ('/denda',               [DendaController::class, 'index']         )->name('denda');
    Route::post  ('/denda/{denda}/lunas',[DendaController::class, 'konfirmasiLunas'])->name('denda.lunas');
    Route::put   ('/denda/settings',     [DendaController::class, 'updateSettings'])->name('denda.settings');
    Route::put   ('/denda/{denda}',      [DendaController::class, 'update']         )->name('denda.update');
    Route::delete('/denda/{denda}',      [DendaController::class, 'destroy']        )->name('denda.destroy');
    
    

   // Pastikan ini berada di dalam group yang memiliki prefix 'admin'
Route::get   ('/laporan',             [LaporanController::class, 'index']     )->name('laporan');
Route::post  ('/laporan',             [LaporanController::class, 'store']     )->name('laporan.store');
Route::put   ('/laporan/{laporan}',   [LaporanController::class, 'update']    )->name('laporan.update');
Route::delete('/laporan/{laporan}',   [LaporanController::class, 'destroy']   )->name('laporan.destroy');

// Export PDF dari laporan tersimpan
// Quick export tanpa simpan
Route::post  ('/laporan/quick-export',      [LaporanController::class, 'quickExport'])->name('laporan.quick-export');
Route::get   ('/laporan/{laporan}/export',  [LaporanController::class, 'exportPdf'] )->name('laporan.export');
Route::get   ('/laporan/{laporan}/preview', [LaporanController::class, 'previewPdf'])->name('laporan.preview');

// Notifikasi
    Route::patch('/notif/read',       [PeminjamController::class, 'notifRead']   )->name('notif.read');
    Route::patch('/notif/read-all',   [PeminjamController::class, 'notifReadAll'])->name('notif.readAll');

});


// ─────────────────────────────────────────────────────────────────
// PEMINJAM ROUTES — role:peminjam
// ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:peminjam'])
    ->prefix('peminjam')
    ->name('peminjam.')
    ->group(function () {

    Route::get('/dashboard', [PeminjamController::class, 'index'])->name('peminjam.dashboard');

    // Dashboard
    Route::get('/dashboard', [PeminjamController::class, 'dashboard'])->name('dashboard');

    // Data Aset
    Route::get('/aset', [PeminjamController::class, 'aset'])->name('aset');

    // Transaksi Peminjaman
    Route::get('/peminjaman',         [PeminjamController::class, 'peminjaman']     )->name('peminjaman');
    Route::post('/peminjaman',        [PeminjamController::class, 'peminjamanStore'])->name('peminjaman.store');

    // Transaksi Pengembalian
    Route::get('/pengembalian',       [PeminjamController::class, 'pengembalian']      )->name('pengembalian');
    Route::post('/pengembalian',      [PeminjamController::class, 'pengembalianStore'] )->name('pengembalian.store');

    // Riwayat
    Route::get('/riwayat',            [PeminjamController::class, 'riwayat'])->name('riwayat');

    // Tentang
    Route::get('/tentang',            [PeminjamController::class, 'tentang'])->name('tentang');

     // Denda
    Route::get('/denda', [PeminjamController::class, 'denda'])->name('denda');

    // Notifikasi
    Route::patch('/notif/read',       [PeminjamController::class, 'notifRead']   )->name('notif.read');
    Route::patch('/notif/read-all',   [PeminjamController::class, 'notifReadAll'])->name('notif.readAll');
});
