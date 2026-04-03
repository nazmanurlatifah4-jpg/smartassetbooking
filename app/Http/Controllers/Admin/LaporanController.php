<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Denda;
use App\Models\Aset;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    // ── INDEX — halaman utama laporan ─────────────────────────────
    public function index(Request $request)
    {
        // Default: bulan ini
        $bulan  = $request->bulan  ?? now()->format('m');
        $tahun  = $request->tahun  ?? now()->format('Y');
        $filter = $request->filter ?? 'semua';

        $periodeAwal  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $periodeAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // ── Query peminjaman sesuai periode & filter ─────────────
        $queryP = Peminjaman::with(['user', 'aset', 'denda', 'pengembalian'])
            ->whereBetween('tanggal_pengajuan', [
                $periodeAwal->toDateString(),
                $periodeAkhir->toDateString(),
            ]);

        if ($filter === 'aktif')   $queryP->where('status', 'Disetujui');
        if ($filter === 'selesai') $queryP->where('status', 'Selesai');
        if ($filter === 'denda')   $queryP->whereHas('denda');

        $peminjaman = $queryP->latest()->get();

        // ── Summary stats ─────────────────────────────────────────
        $summary = [
            'total'       => $peminjaman->count(),
            'selesai'     => $peminjaman->where('status', 'Selesai')->count(),
            'aktif'       => $peminjaman->where('status', 'Disetujui')->count(),
            'terlambat'   => $peminjaman->filter(fn($p) => $p->isTerlambat())->count(),
            'ditolak'     => $peminjaman->where('status', 'Ditolak')->count(),
            'total_denda' => $peminjaman->sum(fn($p) => $p->denda?->total_denda ?? 0),
            'denda_lunas' => $peminjaman->sum(fn($p) =>
                $p->denda?->status_bayar === 'Lunas' ? ($p->denda->total_denda ?? 0) : 0
            ),
        ];

        // ── Top 5 aset bulan ini ──────────────────────────────────
        $topAset = Aset::withCount(['peminjaman as jumlah_pinjam' => fn($q) =>
                $q->whereBetween('tanggal_pengajuan', [
                    $periodeAwal->toDateString(),
                    $periodeAkhir->toDateString(),
                ])
            ])
            ->orderByDesc('jumlah_pinjam')
            ->limit(5)
            ->get();

        // ── Laporan tersimpan ─────────────────────────────────────
        $laporanList = Laporan::with('admin')
            ->latest()
            ->paginate(10);

        // ── Opsi tahun (5 tahun ke belakang) ─────────────────────
        $tahunOptions = range(now()->year, now()->year - 4);

        return view('admin.laporan', compact(
            'peminjaman', 'summary', 'topAset',
            'laporanList', 'bulan', 'tahun', 'filter',
            'periodeAwal', 'periodeAkhir', 'tahunOptions'
        ));
    }

    // ── STORE — simpan laporan ke database ───────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'judul'      => ['required', 'string', 'max:200'],
            'periode'    => ['required', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string'],
            'status'     => ['required', Rule::in(['Draft', 'Final'])],
        ]);

        Laporan::create([
            'admin_id'   => auth()->id(),
            'judul'      => $request->judul,
            'periode'    => $request->periode,
            'keterangan' => $request->keterangan,
            'status'     => $request->status,
        ]);

        return redirect()->route('admin.laporan')
            ->with('success', 'Laporan berhasil disimpan!');
    }

    // ── UPDATE ────────────────────────────────────────────────────
    public function update(Request $request, Laporan $laporan)
    {
        $request->validate([
            'judul'      => ['required', 'string', 'max:200'],
            'periode'    => ['required', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string'],
            'status'     => ['required', Rule::in(['Draft', 'Final'])],
        ]);

        $laporan->update($request->only('judul', 'periode', 'keterangan', 'status'));

        return redirect()->route('admin.laporan')
            ->with('success', 'Laporan berhasil diperbarui!');
    }

    // ── DESTROY ───────────────────────────────────────────────────
    public function destroy(Laporan $laporan)
    {
        $laporan->delete();

        return redirect()->route('admin.laporan')
            ->with('success', 'Laporan berhasil dihapus!');
    }

    // ── EXPORT PDF — generate rekap bulanan ──────────────────────
    public function exportPdf(Request $request, Laporan $laporan)
    {
        // Parse periode dari laporan (format: "Januari 2026" atau "2026-01")
        $periode = $this->parsePeriode($laporan->periode);
        $bulan   = $periode['bulan'];
        $tahun   = $periode['tahun'];

        $periodeAwal  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $periodeAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // ── Ambil semua data periode --
        $peminjaman = Peminjaman::with(['user', 'aset'])
            ->whereBetween('tanggal_pengajuan', [
                $periodeAwal->toDateString(),
                $periodeAkhir->toDateString(),
            ])
            ->latest()
            ->get();

        $pengembalian = Pengembalian::with(['peminjaman.user', 'peminjaman.aset'])
            ->whereHas('peminjaman', fn($q) =>
                $q->whereBetween('tanggal_pengajuan', [
                    $periodeAwal->toDateString(),
                    $periodeAkhir->toDateString(),
                ])
            )
            ->latest()
            ->get();

        $dendas = Denda::with(['peminjaman.user', 'peminjaman.aset'])
            ->whereHas('peminjaman', fn($q) =>
                $q->whereBetween('tanggal_pengajuan', [
                    $periodeAwal->toDateString(),
                    $periodeAkhir->toDateString(),
                ])
            )
            ->latest()
            ->get();

        $topAset = Aset::withCount(['peminjaman as jumlah_pinjam' => fn($q) =>
                $q->whereBetween('tanggal_pengajuan', [
                    $periodeAwal->toDateString(),
                    $periodeAkhir->toDateString(),
                ])
            ])
            ->orderByDesc('jumlah_pinjam')
            ->limit(5)
            ->get();

        // ── Summary ──────────────────────────────────────────────
        $summary = [
            'total'       => $peminjaman->count(),
            'selesai'     => $peminjaman->where('status', 'Selesai')->count(),
            'aktif'       => $peminjaman->where('status', 'Disetujui')->count(),
            'terlambat'   => $peminjaman->filter(fn($p) => $p->isTerlambat())->count(),
            'ditolak'     => $peminjaman->where('status', 'Ditolak')->count(),
            'total_denda' => $dendas->sum('total_denda'),
            'denda_lunas' => $dendas->where('status_bayar', 'Lunas')->sum('total_denda'),
        ];

        $bulanLabel = $periodeAwal->translatedFormat('F'); // Januari, Februari, dst

        // ── Generate PDF ──────────────────────────────────────────
        $pdf = Pdf::loadView('admin.rekap-bulanan', compact(
            'laporan', 'peminjaman', 'pengembalian', 'dendas',
            'topAset', 'summary', 'bulan', 'tahun',
            'bulanLabel', 'periodeAwal', 'periodeAkhir'
        ))
        ->setPaper('a4', 'landscape') // landscape agar tabel tidak terpotong
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'defaultFont'          => 'sans-serif',
            'dpi'                  => 120,
            'defaultMediaType'     => 'print',
        ]);

        // Nama file: Laporan_Rekap_Januari_2026.pdf
        $filename = 'Laporan_Rekap_' . $bulanLabel . '_' . $tahun . '.pdf';

        return $pdf->download($filename);
    }

    // ── PREVIEW PDF (buka di browser, bukan download) ────────────
    public function previewPdf(Laporan $laporan)
    {
        $periode = $this->parsePeriode($laporan->periode);
        $bulan   = $periode['bulan'];
        $tahun   = $periode['tahun'];

        $periodeAwal  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $periodeAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $peminjaman = Peminjaman::with(['user', 'aset'])
            ->whereBetween('tanggal_pengajuan', [
                $periodeAwal->toDateString(),
                $periodeAkhir->toDateString(),
            ])
            ->latest()->get();

        $pengembalian = Pengembalian::with(['peminjaman.user', 'peminjaman.aset'])
            ->whereHas('peminjaman', fn($q) =>
                $q->whereBetween('tanggal_pengajuan', [
                    $periodeAwal->toDateString(),
                    $periodeAkhir->toDateString(),
                ])
            )->latest()->get();

        $dendas = Denda::with(['peminjaman.user', 'peminjaman.aset'])
            ->whereHas('peminjaman', fn($q) =>
                $q->whereBetween('tanggal_pengajuan', [
                    $periodeAwal->toDateString(),
                    $periodeAkhir->toDateString(),
                ])
            )->latest()->get();

        $topAset = Aset::withCount(['peminjaman as jumlah_pinjam' => fn($q) =>
                $q->whereBetween('tanggal_pengajuan', [
                    $periodeAwal->toDateString(),
                    $periodeAkhir->toDateString(),
                ])
            ])->orderByDesc('jumlah_pinjam')->limit(5)->get();

        $summary = [
            'total'       => $peminjaman->count(),
            'selesai'     => $peminjaman->where('status', 'Selesai')->count(),
            'aktif'       => $peminjaman->where('status', 'Disetujui')->count(),
            'terlambat'   => $peminjaman->filter(fn($p) => $p->isTerlambat())->count(),
            'ditolak'     => $peminjaman->where('status', 'Ditolak')->count(),
            'total_denda' => $dendas->sum('total_denda'),
            'denda_lunas' => $dendas->where('status_bayar', 'Lunas')->sum('total_denda'),
        ];

        $bulanLabel = $periodeAwal->translatedFormat('F');

        $pdf = Pdf::loadView('admin.rekap-bulanan', compact(
            'laporan', 'peminjaman', 'pengembalian', 'dendas',
            'topAset', 'summary', 'bulan', 'tahun',
            'bulanLabel', 'periodeAwal', 'periodeAkhir'
        ))
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'defaultFont'          => 'sans-serif',
            'dpi'                  => 120,
        ]);

        return $pdf->stream('preview_laporan.pdf');
    }

    // ── QUICK EXPORT — export langsung tanpa simpan dulu ─────────
    public function quickExport(Request $request)
    {
        $request->validate([
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'min:2020'],
        ]);

        // Buat record laporan sementara tanpa disimpan
        $laporan = new Laporan([
            'judul'    => 'Laporan Rekap ' . Carbon::create($request->tahun, $request->bulan, 1)->translatedFormat('F Y'),
            'periode'  => $request->bulan . '-' . $request->tahun,
            'status'   => 'Final',
            'admin_id' => auth()->id(),
        ]);
        $laporan->admin = auth()->user(); // inject relasi manual

        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;

        $periodeAwal  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $periodeAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $peminjaman = Peminjaman::with(['user', 'aset'])
            ->whereBetween('tanggal_pengajuan', [
                $periodeAwal->toDateString(),
                $periodeAkhir->toDateString(),
            ])->latest()->get();

        $pengembalian = Pengembalian::with(['peminjaman.user', 'peminjaman.aset'])
            ->whereHas('peminjaman', fn($q) =>
                $q->whereBetween('tanggal_pengajuan', [
                    $periodeAwal->toDateString(),
                    $periodeAkhir->toDateString(),
                ])
            )->latest()->get();

        $dendas = Denda::with(['peminjaman.user', 'peminjaman.aset'])
            ->whereHas('peminjaman', fn($q) =>
                $q->whereBetween('tanggal_pengajuan', [
                    $periodeAwal->toDateString(),
                    $periodeAkhir->toDateString(),
                ])
            )->latest()->get();

        $topAset = Aset::withCount(['peminjaman as jumlah_pinjam' => fn($q) =>
                $q->whereBetween('tanggal_pengajuan', [
                    $periodeAwal->toDateString(),
                    $periodeAkhir->toDateString(),
                ])
            ])->orderByDesc('jumlah_pinjam')->limit(5)->get();

        $summary = [
            'total'       => $peminjaman->count(),
            'selesai'     => $peminjaman->where('status', 'Selesai')->count(),
            'aktif'       => $peminjaman->where('status', 'Disetujui')->count(),
            'terlambat'   => $peminjaman->filter(fn($p) => $p->isTerlambat())->count(),
            'ditolak'     => $peminjaman->where('status', 'Ditolak')->count(),
            'total_denda' => $dendas->sum('total_denda'),
            'denda_lunas' => $dendas->where('status_bayar', 'Lunas')->sum('total_denda'),
        ];

        $bulanLabel = $periodeAwal->translatedFormat('F');

        $pdf = Pdf::loadView('admin.rekap-bulanan', compact(
            'laporan', 'peminjaman', 'pengembalian', 'dendas',
            'topAset', 'summary', 'bulan', 'tahun',
            'bulanLabel', 'periodeAwal', 'periodeAkhir'
        ))
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
            'defaultFont'          => 'sans-serif',
            'dpi'                  => 120,
        ]);

        $filename = 'Laporan_Rekap_' . $bulanLabel . '_' . $tahun . '.pdf';
        return $pdf->download($filename);
    }

    // ── Helper: parse periode string ─────────────────────────────
    private function parsePeriode(string $periode): array
    {
        // Format bisa: "1-2026", "01-2026", "Januari 2026", "2026-01"
        if (preg_match('/^(\d{1,2})-(\d{4})$/', $periode, $m)) {
            return ['bulan' => (int)$m[1], 'tahun' => (int)$m[2]];
        }
        if (preg_match('/^(\d{4})-(\d{2})$/', $periode, $m)) {
            return ['bulan' => (int)$m[2], 'tahun' => (int)$m[1]];
        }
        // Fallback: bulan ini
        return ['bulan' => now()->month, 'tahun' => now()->year];
    }
}
