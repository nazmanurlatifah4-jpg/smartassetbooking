<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama'      => ['required', 'string', 'max:100', 'unique:kategoris,nama'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Kategori::create($request->only('nama', 'deskripsi'));

        return redirect()->route('admin.masterdata')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama'      => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $kategori->update($request->only('nama', 'deskripsi'));

        return redirect()->route('admin.masterdata')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->asets()->exists()) {
            return redirect()->route('admin.masterdata')
                ->with('error', 'Kategori ini masih digunakan oleh aset!');
        }

        $kategori->delete();

        return redirect()->route('admin.masterdata')
            ->with('success', 'Kategori berhasil dihapus!');
    }
}
