<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AsetController extends Controller
{

    // public function masterData()
    // {
    // // Ambil data asli dari database lewat Model
    // $users = \App\Models\User::all();
    // $asets = \App\Models\Aset::all();p

    // // Kirim ke view
    // return view('admin.masterdata', compact('users', 'asets'));
    // }

    // ── STORE ─────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_aset' => ['required', 'string', 'max:50', 'unique:asets,kode_aset'],
            'nama_aset' => ['required', 'string', 'max:150'],
            'kategori'  => ['required', 'string', 'max:100'],
            'kondisi'   => ['required', Rule::in(['Baik', 'Rusak Ringan', 'Rusak Berat'])],
            'stok'      => ['required', 'integer', 'min:1'],
            'lokasi'    => ['nullable', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'foto'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('asets', 'public');
        }

        Aset::create($data);

        return redirect()->route('admin.masterdata')
            ->with('success', 'Aset ' . $data['nama_aset'] . ' berhasil ditambahkan!')
            ->with('active_tab', 'aset');
    }

    // ── UPDATE ────────────────────────────────────────────────────
    public function update(Request $request, Aset $aset)
    {
        $data = $request->validate([
            'kode_aset' => ['required', 'string', 'max:50', Rule::unique('asets', 'kode_aset')->ignore($aset->id)],
            'nama_aset' => ['required', 'string', 'max:150'],
            'kategori'  => ['required', 'string', 'max:100'],
            'kondisi'   => ['required', Rule::in(['Baik', 'Rusak Ringan', 'Rusak Berat'])],
            'stok'      => ['required', 'integer', 'min:0'],
            'lokasi'    => ['nullable', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'foto'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($aset->foto) Storage::disk('public')->delete($aset->foto);
            $data['foto'] = $request->file('foto')->store('asets', 'public');
        }

        $aset->update($data);

        return redirect()->route('admin.masterdata')
            ->with('success', 'Aset ' . $aset->nama_aset . ' berhasil diperbarui!')
            ->with('active_tab', 'aset');
    }

    // ── DESTROY ───────────────────────────────────────────────────
    public function destroy(Aset $aset)
    {
        $masihDipinjam = $aset->peminjaman()
            ->whereIn('status', ['Menunggu', 'Disetujui'])
            ->exists();

        if ($masihDipinjam) {
            return redirect()->route('admin.masterdata')
                ->with('error', 'Aset sedang dipinjam, tidak bisa dihapus!');
        }

        if ($aset->foto) {
            Storage::disk('public')->delete($aset->foto);
        }

        $aset->delete(); // SoftDelete

        return redirect()->route('admin.masterdata')
            ->with('success', 'Aset ' . $aset->nama_aset . ' berhasil dihapus!')
            ->with('active_tab', 'aset');
    }
}