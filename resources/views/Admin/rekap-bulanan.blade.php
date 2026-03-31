<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekap Bulanan — {{ $bulanLabel }} {{ $tahun }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: white;
            padding: 0;
        }

        /* ── HEADER ─────────────────────────────────────────── */
        .header {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: white;
            padding: 20px 30px;
            margin-bottom: 0;
        }
        .header-inner {
            display: table;
            width: 100%;
        }
        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
        }
        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 40%;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .header p {
            font-size: 11px;
            opacity: 0.85;
        }
        .badge-periode {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.35);
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        /* ── META INFO ──────────────────────────────────────── */
        .meta-bar {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 10px 30px;
            display: table;
            width: 100%;
        }
        .meta-item {
            display: table-cell;
            text-align: center;
            border-right: 1px solid #e2e8f0;
            padding: 0 16px;
        }
        .meta-item:last-child { border-right: none; }
        .meta-item .meta-label { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        .meta-item .meta-val   { font-size: 13px; font-weight: bold; color: #1e293b; margin-top: 2px; }
        .meta-item .meta-val.red  { color: #ef4444; }
        .meta-item .meta-val.green{ color: #10b981; }
        .meta-item .meta-val.blue { color: #3b82f6; }

        /* ── SECTION TITLE ──────────────────────────────────── */
        .section {
            padding: 16px 30px 0;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e293b;
            border-left: 4px solid #3b82f6;
            padding-left: 10px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* ── TABLE ──────────────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        thead tr {
            background: #1e293b;
            color: white;
        }
        thead th {
            padding: 8px 10px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            font-weight: bold;
        }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:last-child { border-bottom: 2px solid #e2e8f0; }
        tbody td {
            padding: 7px 10px;
            vertical-align: middle;
            color: #475569;
        }
        tbody td.bold { font-weight: bold; color: #1e293b; }
        tfoot tr { background: #e2e8f0; }
        tfoot td {
            padding: 8px 10px;
            font-weight: bold;
            font-size: 10px;
            color: #1e293b;
        }

        /* ── BADGES ─────────────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-menunggu  { background: #fef3c7; color: #92400e; }
        .badge-disetujui { background: #d1fae5; color: #065f46; }
        .badge-ditolak   { background: #fee2e2; color: #991b1b; }
        .badge-selesai   { background: #e0e7ff; color: #3730a3; }
        .badge-telat     { background: #fef3c7; color: #92400e; }
        .badge-rusak     { background: #ede9fe; color: #5b21b6; }
        .badge-hilang    { background: #fee2e2; color: #991b1b; }
        .badge-lunas     { background: #d1fae5; color: #065f46; }
        .badge-belum     { background: #fee2e2; color: #991b1b; }

        /* ── SUMMARY BOXES ──────────────────────────────────── */
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-spacing: 0;
        }
        .summary-box {
            display: table-cell;
            width: 25%;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .summary-box:not(:last-child) { border-right: none; }
        .summary-box .s-label { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.4px; }
        .summary-box .s-val   { font-size: 16px; font-weight: bold; color: #1e293b; margin: 3px 0 1px; }
        .summary-box .s-sub   { font-size: 9px; color: #64748b; }
        .summary-box .s-dot   { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 4px; vertical-align: middle; }

        /* ── TOP ASET BARS ──────────────────────────────────── */
        .bar-row {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }
        .bar-label { display: table-cell; width: 35%; font-size: 10px; color: #475569; vertical-align: middle; padding-right: 8px; }
        .bar-track { display: table-cell; vertical-align: middle; }
        .bar-outer { background: #e2e8f0; border-radius: 4px; height: 10px; width: 100%; }
        .bar-inner { background: linear-gradient(to right, #3b82f6, #2563eb); border-radius: 4px; height: 10px; }
        .bar-count { display: table-cell; width: 30px; text-align: right; font-size: 10px; font-weight: bold; color: #3b82f6; vertical-align: middle; padding-left: 6px; }

        /* ── DIVIDER ─────────────────────────────────────────── */
        .divider { border: none; border-top: 1px dashed #e2e8f0; margin: 6px 30px 16px; }

        /* ── FOOTER ─────────────────────────────────────────── */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 8px 30px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: table;
            width: 100%;
        }
        .footer-left  { display: table-cell; font-size: 9px; color: #94a3b8; vertical-align: middle; }
        .footer-right { display: table-cell; text-align: right; font-size: 9px; color: #94a3b8; vertical-align: middle; }

        /* ── TANDA TANGAN ───────────────────────────────────── */
        .ttd-wrap {
            display: table;
            width: 100%;
            margin-top: 20px;
            padding: 0 30px;
        }
        .ttd-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 0 10px;
        }
        .ttd-box .ttd-title { font-size: 10px; color: #64748b; margin-bottom: 50px; }
        .ttd-box .ttd-line  { border-bottom: 1px solid #1e293b; margin-bottom: 4px; }
        .ttd-box .ttd-name  { font-size: 10px; font-weight: bold; color: #1e293b; }
        .ttd-box .ttd-role  { font-size: 9px; color: #94a3b8; }

        /* ── WATERMARK (jika draft) ─────────────────────────── */
        @if($laporan->status === 'Draft')
        .watermark {
            position: fixed;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 80px;
            font-weight: 900;
            color: rgba(239,68,68,0.08);
            letter-spacing: 8px;
            pointer-events: none;
            z-index: 0;
            white-space: nowrap;
        }
        @endif

        /* Page break */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    @if($laporan->status === 'Draft')
    <div class="watermark">DRAFT</div>
    @endif

    {{-- ===== HEADER ===== --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-left">
                <h1>LAPORAN REKAP BULANAN</h1>
                <p>Smart Asset Booking — Sistem Peminjaman Aset Sekolah</p>
                <p style="margin-top:4px; opacity:0.7;">Dicetak: {{ now()->format('d M Y, H:i') }} WIB</p>
            </div>
            <div class="header-right">
                <div class="badge-periode">{{ $bulanLabel }} {{ $tahun }}</div>
                <p style="margin-top:6px; opacity:0.75;">Periode {{ $periodeAwal }} — {{ $periodeAkhir }}</p>
                <p style="opacity:0.75;">Dibuat oleh: {{ $laporan->admin->nama }}</p>
            </div>
        </div>
    </div>

    {{-- ===== META STATS BAR ===== --}}
    <div class="meta-bar">
        <div class="meta-item">
            <div class="meta-label">Total Peminjaman</div>
            <div class="meta-val blue">{{ $summary['total'] }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Selesai</div>
            <div class="meta-val green">{{ $summary['selesai'] }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Masih Aktif</div>
            <div class="meta-val blue">{{ $summary['aktif'] }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Terlambat</div>
            <div class="meta-val red">{{ $summary['terlambat'] }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Ditolak</div>
            <div class="meta-val">{{ $summary['ditolak'] }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Total Denda</div>
            <div class="meta-val red">Rp {{ number_format($summary['total_denda'], 0, ',', '.') }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Denda Terkumpul</div>
            <div class="meta-val green">Rp {{ number_format($summary['denda_lunas'], 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- ===== SECTION 1: REKAP PEMINJAMAN ===== --}}
    <div class="section">
        <div class="section-title">1. Rekap Transaksi Peminjaman</div>
        <table>
            <thead>
                <tr>
                    <th style="width:4%">No</th>
                    <th style="width:18%">Peminjam</th>
                    <th style="width:8%">Kelas</th>
                    <th style="width:20%">Nama Aset</th>
                    <th style="width:8%">Kode</th>
                    <th style="width:11%">Tgl Pengajuan</th>
                    <th style="width:11%">Tgl Kembali</th>
                    <th style="width:11%">Keperluan</th>
                    <th style="width:9%">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjaman as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    {{-- kolom 'nama' dari users --}}
                    <td class="bold">{{ $p->user->nama }}</td>
                    <td>{{ $p->user->kelas ?? '-' }}</td>
                    {{-- kolom 'nama_aset' dari asets --}}
                    <td class="bold">{{ $p->aset->nama_aset }}</td>
                    {{-- kolom 'kode_aset' --}}
                    <td style="font-family: monospace; font-size:9px;">{{ $p->aset->kode_aset }}</td>
                    {{-- kolom 'tanggal_pengajuan' --}}
                    <td>{{ $p->tanggal_pengajuan->format('d/m/Y') }}</td>
                    {{-- kolom 'tanggal_kembali' --}}
                    <td @if($p->isTerlambat()) style="color:#ef4444;font-weight:bold;" @endif>
                        {{ $p->tanggal_kembali->format('d/m/Y') }}
                    </td>
                    <td style="font-size:9px;">{{ Str::limit($p->keperluan ?? '-', 20) }}</td>
                    {{-- status enum: Menunggu, Disetujui, Ditolak, Selesai --}}
                    <td>
                        <span class="badge badge-{{ strtolower($p->status) }}">{{ $p->status }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center; color:#94a3b8; padding:16px;">
                        Tidak ada data peminjaman pada periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($peminjaman->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="8" style="text-align:right;">Total Transaksi:</td>
                    <td>{{ $peminjaman->count() }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <hr class="divider">

    {{-- ===== SECTION 2: REKAP PENGEMBALIAN ===== --}}
    <div class="section">
        <div class="section-title">2. Rekap Pengembalian Aset</div>
        <table>
            <thead>
                <tr>
                    <th style="width:4%">No</th>
                    <th style="width:18%">Peminjam</th>
                    <th style="width:20%">Nama Aset</th>
                    <th style="width:11%">Tgl Pinjam</th>
                    <th style="width:11%">Tgl Kembali</th>
                    <th style="width:11%">Tgl Pengembalian</th>
                    <th style="width:12%">Kondisi</th>
                    <th style="width:13%">Status Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengembalian as $i => $pg)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="bold">{{ $pg->peminjaman->user->nama }}</td>
                    <td class="bold">{{ $pg->peminjaman->aset->nama_aset }}</td>
                    <td>{{ $pg->peminjaman->tanggal_pengajuan->format('d/m/Y') }}</td>
                    <td>{{ $pg->peminjaman->tanggal_kembali->format('d/m/Y') }}</td>
                    {{-- kolom 'tanggal_pengembalian' --}}
                    <td>{{ $pg->tanggal_pengembalian->format('d/m/Y') }}</td>
                    {{-- kolom 'kondisi_barang': Baik, Rusak Ringan, Rusak Berat, Hilang --}}
                    <td>
                        @php
                            $kc = match($pg->kondisi_barang) {
                                'Baik'        => 'color:#10b981;font-weight:bold;',
                                'Rusak Ringan'=> 'color:#f59e0b;font-weight:bold;',
                                'Rusak Berat' => 'color:#ef4444;font-weight:bold;',
                                'Hilang'      => 'color:#991b1b;font-weight:bold;',
                                default       => '',
                            };
                        @endphp
                        <span style="{{ $kc }}">{{ $pg->kondisi_barang }}</span>
                    </td>
                    {{-- kolom 'status_verifikasi': Menunggu, Diterima, Ditolak --}}
                    <td>
                        <span class="badge {{ $pg->status_verifikasi === 'Diterima' ? 'badge-selesai' : ($pg->status_verifikasi === 'Ditolak' ? 'badge-ditolak' : 'badge-menunggu') }}">
                            {{ $pg->status_verifikasi }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; color:#94a3b8; padding:16px;">
                        Tidak ada data pengembalian pada periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <hr class="divider">

    {{-- ===== SECTION 3: REKAP DENDA ===== --}}
    <div class="section">
        <div class="section-title">3. Rekap Denda</div>
        <table>
            <thead>
                <tr>
                    <th style="width:4%">No</th>
                    <th style="width:18%">Peminjam</th>
                    <th style="width:20%">Nama Aset</th>
                    {{-- kolom 'jenis_denda': Telat, Rusak Berat, Hilang --}}
                    <th style="width:10%">Jenis Denda</th>
                    {{-- kolom 'jumlah_hari' --}}
                    <th style="width:8%">Hari Telat</th>
                    {{-- kolom 'tarif_per_hari' --}}
                    <th style="width:12%">Tarif/Hari</th>
                    {{-- kolom 'total_denda' --}}
                    <th style="width:13%">Total Denda</th>
                    {{-- kolom 'status_bayar': Belum Lunas, Lunas --}}
                    <th style="width:15%">Status Bayar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dendas as $i => $d)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="bold">{{ $d->peminjaman->user->nama }}</td>
                    <td>{{ $d->peminjaman->aset->nama_aset }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower(str_replace(' ','-',$d->jenis_denda)) }}">
                            {{ $d->jenis_denda }}
                        </span>
                    </td>
                    <td style="text-align:center;">{{ $d->jumlah_hari > 0 ? $d->jumlah_hari.' hari' : '-' }}</td>
                    <td>{{ $d->tarif_per_hari > 0 ? 'Rp '.number_format($d->tarif_per_hari,0,',','.') : '-' }}</td>
                    <td style="font-weight:bold; color:#ef4444;">{{ $d->total_format }}</td>
                    <td>
                        <span class="badge {{ $d->status_bayar === 'Lunas' ? 'badge-lunas' : 'badge-belum' }}">
                            {{ $d->status_bayar }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; color:#94a3b8; padding:16px;">
                        Tidak ada denda pada periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($dendas->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align:right;">Total Denda Terkumpul (Lunas):</td>
                    <td colspan="2" style="color:#10b981;">
                        Rp {{ number_format($dendas->where('status_bayar','Lunas')->sum('total_denda'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <hr class="divider">

    {{-- ===== SECTION 4: ASET TERPOPULER ===== --}}
    <div class="section">
        <div class="section-title">4. Aset Paling Sering Dipinjam</div>
        @php $maxVal = $topAset->max('jumlah_pinjam') ?: 1; @endphp
        @foreach($topAset as $idx => $a)
        <div class="bar-row">
            <div class="bar-label">
                <strong>{{ $idx + 1 }}.</strong> {{ Str::limit($a->nama_aset, 28) }}
            </div>
            <div class="bar-track">
                <div class="bar-outer">
                    <div class="bar-inner" style="width:{{ round($a->jumlah_pinjam / $maxVal * 100) }}%"></div>
                </div>
            </div>
            <div class="bar-count">{{ $a->jumlah_pinjam }}x</div>
        </div>
        @endforeach
    </div>

    <hr class="divider">

    {{-- ===== TANDA TANGAN ===== --}}
    <div class="ttd-wrap">
        <div class="ttd-box">
            <div class="ttd-title">Mengetahui,<br>Kepala Sekolah</div>
            <div class="ttd-line"></div>
            <div class="ttd-name">___________________________</div>
            <div class="ttd-role">NIP. ______________________</div>
        </div>
        <div class="ttd-box">
            <div class="ttd-title">Dibuat oleh,<br>Admin Sistem</div>
            <div class="ttd-line"></div>
            <div class="ttd-name">{{ $laporan->admin->nama }}</div>
            <div class="ttd-role">Administrator</div>
        </div>
        <div class="ttd-box">
            <div class="ttd-title">Diperiksa,<br>Penanggung Jawab</div>
            <div class="ttd-line"></div>
            <div class="ttd-name">___________________________</div>
            <div class="ttd-role">NIP. ______________________</div>
        </div>
    </div>

    {{-- ===== FOOTER ===== --}}
    <div class="footer">
        <div class="footer-left">
            Smart Asset Booking — Nexora Web © {{ date('Y') }} |
            Laporan: {{ $laporan->judul }} |
            Status: <strong>{{ $laporan->status }}</strong>
        </div>
        <div class="footer-right">
            Dicetak: {{ now()->format('d/m/Y H:i') }} WIB
        </div>
    </div>

</body>
</html>
