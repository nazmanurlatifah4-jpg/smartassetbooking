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

        // Mengambil kategori dari tabel referensi 'kategoris' yang dikelola di KategoriController
        $kategoris = Kategori::orderBy('nama')->get();

        return view('admin.masterdata', compact('users', 'asets', 'kategoris'));
    }
}