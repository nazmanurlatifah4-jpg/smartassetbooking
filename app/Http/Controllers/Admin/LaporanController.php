<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Peminjaman;
use App\Models\Denda;
use App\Models\Aset;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LaporanController extends Controller
{
    // ── INDEX ─────────────────────────────────────────────────────
    public function index(Request $request)
    {
        // Default: bulan ini
        $dari   = $request->dari   ?? now()->startOfMonth()->toDateString();
        $sampai = $request->sampai ?? now()->toDateString();
        $filter = $request->filter ?? 'semua';

        // Data transaksi untuk rekap
        $query = Peminjaman::with(['user', 'aset', 'denda', 'pengembalian'])
            ->whereBetween('tanggal_pengajuan', [$dari, $sampai]);

        if ($filter === 'aktif') {
            $query->where('status', 'Disetujui');
        } elseif ($filter === 'selesai') {
            $query->where('status', 'Selesai');
        } elseif ($filter === 'denda') {
            $query->whereHas('denda');
        }

        $peminjaman = $query->latest()->get();

        // Summary
        $summary = [
            'total'         => $peminjaman->count(),
            'selesai'       => $peminjaman->where('status', 'Selesai')->count(),
            'terlambat'     => $peminjaman->filter(fn($p) => $p->isTerlambat())->count(),
            'total_denda'   => $peminjaman->sum(fn($p) => $p->denda?->total_denda ?? 0),
        ];

        // Top 5 aset paling sering dipinjam
        $topAset = Aset::withCount(['peminjamans as jumlah_pinjam' => fn($q) =>
                $q->whereBetween('tanggal_pengajuan', [$dari, $sampai])
            ])
            ->orderByDesc('jumlah_pinjam')
            ->limit(5)
            ->get();

        // Daftar laporan tersimpan
        $laporanList = Laporan::with('admin')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.laporan', compact(
            'peminjaman', 'summary', 'topAset',
            'laporanList', 'dari', 'sampai', 'filter'
        ));
    }

    // ── STORE (simpan laporan sebagai Draft atau Final) ────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'      => ['required', 'string', 'max:200'],
            'periode'    => ['required', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string'],
            'status'     => ['required', Rule::in(['Draft', 'Final'])],
        ]);

        $data['admin_id'] = auth()->id();

        Laporan::create($data);

        return redirect()->route('admin.laporan')
            ->with('success', 'Laporan berhasil disimpan!');
    }

    // ── UPDATE ────────────────────────────────────────────────────
    public function update(Request $request, Laporan $laporan)
    {
        $data = $request->validate([
            'judul'      => ['required', 'string', 'max:200'],
            'periode'    => ['required', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string'],
            'status'     => ['required', Rule::in(['Draft', 'Final'])],
        ]);

        $laporan->update($data);

        return redirect()->route('admin.laporan')
            ->with('success', 'Laporan berhasil diperbarui!');
    }

    // ── DESTROY ───────────────────────────────────────────────────
    public function destroy(Laporan $laporan)
    {
        $laporan->delete(); // SoftDelete

        return redirect()->route('admin.laporan')
            ->with('success', 'Laporan berhasil dihapus!');
    }
}