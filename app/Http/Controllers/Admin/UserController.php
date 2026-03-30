<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // ── STORE ─────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role'     => ['required', Rule::in(['admin', 'peminjam'])], // hanya 2 role
            'kelas'    => ['nullable', 'string', 'max:50'],
            'jurusan'  => ['nullable', 'string', 'max:100'],
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.masterdata')
            ->with('success', 'User ' . $data['nama'] . ' berhasil ditambahkan!');
    }

    // ── UPDATE ────────────────────────────────────────────────────
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nama'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role'    => ['required', Rule::in(['admin', 'peminjam'])],
            'kelas'   => ['nullable', 'string', 'max:50'],
            'jurusan' => ['nullable', 'string', 'max:100'],
        ]);

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.masterdata')
            ->with('success', 'Data user ' . $user->nama . ' berhasil diperbarui!');
    }

    // ── DESTROY ───────────────────────────────────────────────────
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.masterdata')
                ->with('error', 'Tidak bisa menghapus akun sendiri!');
        }

        // Cek peminjaman aktif (Menunggu atau Disetujui)
        $masihAktif = $user->peminjamans()
            ->whereIn('status', ['Menunggu', 'Disetujui'])
            ->exists();

        if ($masihAktif) {
            return redirect()->route('admin.masterdata')
                ->with('error', 'User masih memiliki peminjaman aktif, tidak bisa dihapus!');
        }

        $user->delete(); // SoftDelete

        return redirect()->route('admin.masterdata')
            ->with('success', 'User ' . $user->nama . ' berhasil dihapus!');
    }
}