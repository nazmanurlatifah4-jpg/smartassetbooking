<?php

use Illuminate\Support\Facades\Route;

// ── Controllers ───────────────────────────────────────────────────
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MasterdataController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AsetController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\DendaController;
use App\Http\Controllers\Admin\LaporanController;

// ─────────────────────────────────────────────────────────────────
// PUBLIC
// ─────────────────────────────────────────────────────────────────

Route::get('/', fn() => view('landing'))->name('home');

// Auth — hanya bisa diakses kalau belum login
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

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Master Data ──────────────────────────────────────────────
    Route::get('/masterdata', [MasterdataController::class, 'index'])->name('masterdata');

    // User CRUD — kolom: nama, email, role (admin|peminjam), kelas, jurusan
    Route::post  ('/users',        [UserController::class, 'store']  )->name('users.store');
    Route::put   ('/users/{user}', [UserController::class, 'update'] )->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Aset CRUD — kolom: kode_aset, nama_aset, kategori, kondisi, stok, lokasi
    Route::post  ('/aset',        [AsetController::class, 'store']  )->name('aset.store');
    Route::put   ('/aset/{aset}', [AsetController::class, 'update'] )->name('aset.update');
    Route::delete('/aset/{aset}', [AsetController::class, 'destroy'])->name('aset.destroy');

    // ── Transaksi ────────────────────────────────────────────────
    Route::get   ('/transaksi',                          [TransaksiController::class, 'index']           )->name('transaksi');
    Route::post  ('/transaksi',                          [TransaksiController::class, 'store']           )->name('transaksi.store');
    Route::get   ('/transaksi/{peminjaman}',             [TransaksiController::class, 'show']            )->name('transaksi.show');
    Route::post  ('/transaksi/{peminjaman}/approve',     [TransaksiController::class, 'approve']         )->name('transaksi.approve');
    Route::post  ('/transaksi/{peminjaman}/reject',      [TransaksiController::class, 'reject']          )->name('transaksi.reject');
    Route::post  ('/transaksi/{peminjaman}/kembali',     [TransaksiController::class, 'konfirmasiKembali'])->name('transaksi.kembali');
    Route::delete('/transaksi/{peminjaman}',             [TransaksiController::class, 'destroy']         )->name('transaksi.destroy');

    // ── Denda ─────────────────────────────────────────────────────
    // Kolom: jenis_denda, jumlah_hari, tarif_per_hari, total_denda, status_bayar, tanggal_lunas
    Route::get   ('/denda',              [DendaController::class, 'index']         )->name('denda');
    Route::post  ('/denda/{denda}/lunas',[DendaController::class, 'konfirmasiLunas'])->name('denda.lunas');
    Route::put   ('/denda/{denda}',      [DendaController::class, 'update']        )->name('denda.update');
    Route::delete('/denda/{denda}',      [DendaController::class, 'destroy']       )->name('denda.destroy');

    // ── Laporan ───────────────────────────────────────────────────
    Route::get   ('/laporan',           [LaporanController::class, 'index']  )->name('laporan');
    Route::post  ('/laporan',           [LaporanController::class, 'store']  )->name('laporan.store');
    Route::put   ('/laporan/{laporan}', [LaporanController::class, 'update'] )->name('laporan.update');
    Route::delete('/laporan/{laporan}', [LaporanController::class, 'destroy'])->name('laporan.destroy');
});

// ─────────────────────────────────────────────────────────────────
// PEMINJAM ROUTES — role:peminjam
// (views akan dikembangkan di sesi berikutnya)
// ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
});