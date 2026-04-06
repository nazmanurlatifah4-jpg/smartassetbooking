<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Aset;
use App\Models\Kategori;

class MasterdataController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        $asets = Aset::latest()->get();

        // Daftar kategori unik dari tabel asets (bukan tabel sendiri)
        $kategoris = Aset::select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        return view('admin.masterdata', compact('users', 'asets', 'kategoris'));
    }
}