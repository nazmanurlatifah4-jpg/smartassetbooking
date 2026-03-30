<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Denda;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DendaController extends Controller
{
    // ── INDEX ─────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Denda::with(['peminjaman.user', 'peminjaman.aset', 'pengembalian']);

        // Filter status_bayar — enum sesuai migration: Belum Lunas, Lunas
        if ($request->filled('status')) {
            $query->where('status_bayar', $request->status);
        }

        // Filter jenis_denda — enum: Telat, Rusak Berat, Hilang
        if ($request->filled('jenis')) {
            $query->where('jenis_denda', $request->jenis);
        }

        // Search nama peminjam
        if ($request->filled('search')) {
            $q = $request->search;
            $query->whereHas('peminjaman.user', fn($u) => $u->where('nama', 'like', "%$q%"));
        }

        $dendas = $query->latest()->paginate(15);

        // Stats — pakai kolom migration: status_bayar, total_denda
        $stats = [
            'aktif'           => Denda::where('status_bayar', 'Belum Lunas')->count(),
            'total_nominal'   => Denda::where('status_bayar', 'Belum Lunas')->sum('total_denda'),
            'sudah_lunas'     => Denda::where('status_bayar', 'Lunas')->count(),
            'total_terkumpul' => Denda::where('status_bayar', 'Lunas')->sum('total_denda'),
        ];

        return view('admin.denda', compact('dendas', 'stats'));
    }

    // ── KONFIRMASI LUNAS ──────────────────────────────────────────
    public function konfirmasiLunas(Request $request, Denda $denda)
    {
        if ($denda->sudahLunas()) {
            return redirect()->route('admin.denda')
                ->with('error', 'Denda ini sudah dikonfirmasi lunas sebelumnya!');
        }

        $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $denda->update([
            'status_bayar'  => 'Lunas',        // kolom migration: status_bayar
            'tanggal_lunas' => now()->toDateString(), // kolom migration: tanggal_lunas
            'catatan_admin' => $request->catatan_admin,
        ]);

        // Kirim notifikasi ke peminjam
        Notifikasi::kirim(
            $denda->peminjaman->user_id,
            'Denda Lunas',
            'Pembayaran denda untuk peminjaman ' . $denda->peminjaman->aset->nama_aset . ' telah dikonfirmasi lunas. Terima kasih!'
        );

        return redirect()->route('admin.denda')
            ->with('success', 'Denda berhasil dikonfirmasi lunas!');
    }

    // ── UPDATE (edit nominal denda jika perlu koreksi) ────────────
    public function update(Request $request, Denda $denda)
    {
        if ($denda->sudahLunas()) {
            return redirect()->route('admin.denda')
                ->with('error', 'Denda yang sudah lunas tidak bisa diedit!');
        }

        $data = $request->validate([
            'jenis_denda'   => ['required', Rule::in(['Telat', 'Rusak Berat', 'Hilang'])],
            'jumlah_hari'   => ['required', 'integer', 'min:0'],
            'tarif_per_hari'=> ['required', 'integer', 'min:0'],
            'total_denda'   => ['required', 'integer', 'min:0'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $denda->update($data);

        return redirect()->route('admin.denda')
            ->with('success', 'Data denda berhasil diperbarui!');
    }

    // ── DESTROY ───────────────────────────────────────────────────
    public function destroy(Denda $denda)
    {
        if ($denda->sudahLunas()) {
            return redirect()->route('admin.denda')
                ->with('error', 'Denda yang sudah lunas tidak bisa dihapus!');
        }

        $denda->delete();

        return redirect()->route('admin.denda')
            ->with('success', 'Data denda berhasil dihapus!');
    }
}