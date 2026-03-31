<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Aset;

class MasterdataController extends Controller
{
    public function index()
    {
    // Ambil SEMUA user tanpa filter role agar muncul semua di tabel
    $users = User::latest()->get();

    // Ambil semua aset
    $asets = Aset::latest()->get();


        // Daftar kategori unik dari tabel asets (bukan tabel sendiri)
        $kategoris = Aset::select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        return view('admin.masterdata', compact('users', 'asets', 'kategoris'));
    }
}