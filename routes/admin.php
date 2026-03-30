<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data
    Route::get('/master-data', function () {
        return view('admin.master-data.index');
    })->name('master-data');

    // Transaksi
    Route::get('/transaksi', function () {
        return view('admin.transaksi.index');
    })->name('transaksi.index');

    // Laporan
    Route::get('/laporan', function () {
        return view('admin.laporan.index');
    })->name('laporan.index');

});
