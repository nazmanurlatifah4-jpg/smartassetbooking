@extends('layouts.admin')
@section('title', 'Laporan')
@section('page-subtitle', 'Laporan & Export PDF')

@push('styles')
<style>
    .fl { display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #64748b; margin-bottom: 5px; }
    .fi { width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 13px; color: #1e293b; background: #f8fafc; outline: none; transition: border-color .2s; }
    .fi:focus { border-color: #3b82f6; background: white; }
    select.fi { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; background-size: 16px; padding-right: 32px; }
    @media print {
        nav, aside, footer, .no-print { display: none !important; }
        main { padding: 0 !important; }
    }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 no-print">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[#dbeafe] flex items-center justify-center text-[#3b82f6] text-lg">
            <i class="fas fa-file-alt"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-[#1e293b]">Laporan & Export PDF</h2>
            <p class="text-xs text-[#64748b]">Rekap bulanan peminjaman, pengembalian, dan denda</p>
        </div>
    </div>
</div>

{{-- ===== QUICK EXPORT CARD ===== --}}
<div class="bg-gradient-to-br from-[#3b82f6] to-[#2563eb] rounded-2xl p-5 md:p-6 text-white mb-6 no-print relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4 pointer-events-none"></div>
    <h3 class="text-base font-bold mb-1 flex items-center gap-2 z-10 relative">
        <i class="fas fa-file-pdf"></i> Export PDF Cepat
    </h3>
    <p class="text-xs text-white/75 mb-4 z-10 relative">Pilih bulan dan tahun, langsung download tanpa perlu simpan laporan terlebih dahulu.</p>
    <form method="POST" action="{{ route('admin.laporan.quick-export') }}" class="flex flex-col sm:flex-row gap-3 z-10 relative">
        @csrf
        <div class="flex-1">
            <label class="fl text-white/70">Bulan</label>
            <select name="bulan" class="fi bg-white/10 border-white/30 text-white" style="color-scheme:dark;">
                @php
                $bulanNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                @endphp
                @foreach($bulanNames as $idx => $bName)
                <option value="{{ $idx + 1 }}" {{ (int)$bulan === $idx+1 ? 'selected' : '' }}>{{ $bName }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label class="fl text-white/70">Tahun</label>
            <select name="tahun" class="fi bg-white/10 border-white/30 text-white">
                @foreach($tahunOptions as $t)
                <option value="{{ $t }}" {{ (int)$tahun === $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit"
                class="w-full sm:w-auto px-6 py-2.5 bg-white text-[#2563eb] rounded-xl font-bold text-sm flex items-center justify-center gap-2 hover:-translate-y-0.5 hover:shadow-xl transition-all shadow-lg whitespace-nowrap">
                <i class="fas fa-download"></i> Download PDF
            </button>
        </div>
    </form>
</div>

{{-- ===== FILTER PREVIEW ===== --}}
<div class="bg-white rounded-2xl p-5 shadow-sm border border-[#e2e8f0] mb-6 no-print">
    <form method="GET" action="{{ route('admin.laporan') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
        {{-- Input Bulan --}}
        <div class="w-full">
            <label class="block text-[10px] font-bold text-[#64748b] uppercase tracking-wider mb-1.5 ml-1">Bulan</label>
            <select name="bulan" class="w-full bg-[#f8fafc] border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm font-medium text-[#1e293b] focus:ring-2 focus:ring-[#3b82f6] focus:border-transparent outline-none transition-all cursor-pointer hover:bg-white">
                @foreach($bulanNames as $idx => $bName)
                    <option value="{{ $idx + 1 }}" {{ (int)$bulan === $idx+1 ? 'selected' : '' }}>{{ $bName }}</option>
                @endforeach
            </select>
        </div>

        {{-- Input Tahun --}}
        <div class="w-full">
            <label class="block text-[10px] font-bold text-[#64748b] uppercase tracking-wider mb-1.5 ml-1">Tahun</label>
            <select name="tahun" class="w-full bg-[#f8fafc] border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm font-medium text-[#1e293b] focus:ring-2 focus:ring-[#3b82f6] focus:border-transparent outline-none transition-all cursor-pointer hover:bg-white">
                @foreach($tahunOptions as $t)
                    <option value="{{ $t }}" {{ (int)$tahun === $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        {{-- Input Status --}}
        <div class="w-full">
            <label class="block text-[10px] font-bold text-[#64748b] uppercase tracking-wider mb-1.5 ml-1">Filter Status</label>
            <select name="filter" class="w-full bg-[#f8fafc] border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm font-medium text-[#1e293b] focus:ring-2 focus:ring-[#3b82f6] focus:border-transparent outline-none transition-all cursor-pointer hover:bg-white">
                <option value="semua"   {{ $filter==='semua'   ? 'selected':'' }}>Semua Transaksi</option>
                <option value="aktif"   {{ $filter==='aktif'   ? 'selected':'' }}>Aktif (Disetujui)</option>
                <option value="selesai" {{ $filter==='selesai' ? 'selected':'' }}>Selesai</option>
                <option value="denda"   {{ $filter==='denda'   ? 'selected':'' }}>Ada Denda</option>
            </select>
        </div>

        {{-- Tombol Submit --}}
        <div class="w-full">
            <button type="submit" class="w-full bg-[#3b82f6] hover:bg-[#2563eb] text-white font-bold py-2.5 rounded-xl text-sm shadow-md shadow-blue-100 hover:shadow-lg transition-all flex items-center justify-center gap-2 border border-transparent active:scale-[0.98]">
                <i class="fas fa-filter text-xs"></i> 
                <span>Tampilkan</span>
            </button>
        </div>
    </form>
</div>
{{-- ===== SUMMARY STATS ===== --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
    @php
    $summaryCards = [
        ['label'=>'Total Transaksi','val'=>$summary['total'],    'icon'=>'fas fa-exchange-alt','bg'=>'bg-[#dbeafe]','c'=>'text-[#3b82f6]'],
        ['label'=>'Selesai',        'val'=>$summary['selesai'],  'icon'=>'fas fa-check-circle','bg'=>'bg-[#d1fae5]','c'=>'text-[#10b981]'],
        ['label'=>'Terlambat',      'val'=>$summary['terlambat'],'icon'=>'fas fa-clock',       'bg'=>'bg-[#fecaca]','c'=>'text-[#ef4444]'],
        ['label'=>'Total Denda',    'val'=>'Rp '.number_format($summary['total_denda'],0,',','.'),'icon'=>'fas fa-coins','bg'=>'bg-[#fef3c7]','c'=>'text-[#d97706]'],
    ];
    @endphp
    @foreach($summaryCards as $s)
    <div class="bg-white rounded-xl p-4 shadow-sm border border-[#e2e8f0]">
        <div class="flex items-center justify-between mb-2">
            <div class="w-9 h-9 rounded-xl {{ $s['bg'] }} flex items-center justify-center {{ $s['c'] }} text-sm">
                <i class="{{ $s['icon'] }}"></i>
            </div>
        </div>
        <div class="text-lg font-bold text-[#1e293b]">{{ $s['val'] }}</div>
        <div class="text-xs text-[#64748b]">{{ $s['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- ===== REKAP TABEL ===== --}}
<div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-[#e2e8f0] mb-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-[#1e293b] flex items-center gap-2">
            <i class="fas fa-table text-[#3b82f6]"></i>
            Rekap Periode {{ $periodeAwal->translatedFormat('F Y') }}
        </h3>
        <span class="text-xs text-[#94a3b8]">{{ $peminjaman->count() }} transaksi</span>
    </div>
    <div class="overflow-x-auto -mx-5 md:mx-0">
        <div class="inline-block min-w-full align-middle px-5 md:px-0">
            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-[#f8fafc]">
                    <tr>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">No</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Peminjam</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden sm:table-cell">Aset</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden md:table-cell">Tgl Pengajuan</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden md:table-cell">Tgl Kembali</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Status</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjaman as $i => $p)
                    @php
                        $bc = match($p->status) {
                            'Menunggu'  => 'bg-[#fed7aa] text-[#c2410c]',
                            'Disetujui' => 'bg-[#d1fae5] text-[#065f46]',
                            'Ditolak'   => 'bg-[#fecaca] text-[#991b1b]',
                            'Selesai'   => 'bg-[#e0e7ff] text-[#3730a3]',
                            default     => 'bg-[#f1f5f9] text-[#64748b]',
                        };
                    @endphp
                    <tr class="hover:bg-[#f8fafc] transition-colors">
                        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9]">{{ $i + 1 }}</td>
                        <td class="p-3 border-b border-[#f1f5f9]">
                            <p class="text-xs font-medium text-[#1e293b]">{{ $p->user->nama }}</p>
                            <p class="text-[10px] text-[#94a3b8]">{{ $p->user->kelas }}</p>
                        </td>
                        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden sm:table-cell">{{ $p->aset->nama_aset }}</td>
                        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden md:table-cell">{{ $p->tanggal_pengajuan->format('d M Y') }}</td>
                        <td class="p-3 text-xs border-b border-[#f1f5f9] hidden md:table-cell {{ $p->isTerlambat() ? 'text-[#ef4444] font-semibold' : 'text-[#475569]' }}">
                            {{ $p->tanggal_kembali->format('d M Y') }}
                        </td>
                        <td class="p-3 border-b border-[#f1f5f9]">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $bc }}">{{ $p->status }}</span>
                        </td>
                        <td class="p-3 text-xs border-b border-[#f1f5f9] {{ $p->denda ? 'text-[#ef4444] font-semibold' : 'text-[#94a3b8]' }}">
                            {{ $p->denda ? $p->denda->total_format : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-xs text-[#94a3b8]">Belum ada data untuk periode ini</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($peminjaman->count() > 0)
                <tfoot class="bg-[#f8fafc]">
                    <tr>
                        <td colspan="6" class="p-3 text-xs font-bold text-right text-[#1e293b] border-t-2 border-[#e2e8f0]">Total Denda Periode:</td>
                        <td class="p-3 text-sm font-bold text-[#ef4444] border-t-2 border-[#e2e8f0]">
                            Rp {{ number_format($summary['total_denda'], 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- ===== TOP ASET ===== --}}
<div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-[#e2e8f0] mb-5">
    <h3 class="text-sm font-semibold text-[#1e293b] mb-4 flex items-center gap-2">
        <i class="fas fa-chart-bar text-[#3b82f6]"></i> Aset Paling Sering Dipinjam — {{ $periodeAwal->translatedFormat('F Y') }}
    </h3>
    @php $maxVal = $topAset->max('jumlah_pinjam') ?: 1; @endphp
    <div class="space-y-3">
        @forelse($topAset as $a)
        <div>
            <div class="flex justify-between text-xs mb-1">
                <span class="font-medium text-[#475569]">{{ $a->nama_aset }}</span>
                <span class="font-semibold text-[#3b82f6]">{{ $a->jumlah_pinjam }}x</span>
            </div>
            <div class="w-full bg-[#e2e8f0] rounded-full h-2">
                <div class="bg-gradient-to-r from-[#3b82f6] to-[#2563eb] h-2 rounded-full transition-all"
                    style="width:{{ round($a->jumlah_pinjam / $maxVal * 100) }}%"></div>
            </div>
        </div>
        @empty
        <p class="text-xs text-[#94a3b8] text-center py-4">Belum ada data</p>
        @endforelse
    </div>
</div>

{{-- ===== LAPORAN TERSIMPAN ===== --}}
<div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-[#e2e8f0] no-print">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-[#1e293b] flex items-center gap-2">
            <i class="fas fa-folder text-[#3b82f6]"></i> Laporan Tersimpan
        </h3>
        <button onclick="openModal('simpanLaporanModal')"
            class="px-3 py-1.5 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <i class="fas fa-plus"></i> Simpan Laporan
        </button>
    </div>

    @if($laporanList->count() > 0)
    <div class="overflow-x-auto -mx-5 md:mx-0">
        <div class="inline-block min-w-full align-middle px-5 md:px-0">
            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-[#f8fafc]">
                    <tr>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Judul</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden sm:table-cell">Periode</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden md:table-cell">Dibuat Oleh</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Status</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporanList as $lap)
                    <tr class="hover:bg-[#f8fafc] transition-colors">
                        <td class="p-3 border-b border-[#f1f5f9]">
                            <p class="text-xs font-semibold text-[#1e293b]">{{ $lap->judul }}</p>
                            @if($lap->keterangan)
                            <p class="text-[10px] text-[#94a3b8]">{{ Str::limit($lap->keterangan, 50) }}</p>
                            @endif
                        </td>
                        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden sm:table-cell">{{ $lap->periode }}</td>
                        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden md:table-cell">{{ $lap->admin->nama }}</td>
                        <td class="p-3 border-b border-[#f1f5f9]">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $lap->status === 'Final' ? 'bg-[#d1fae5] text-[#065f46]' : 'bg-[#fef3c7] text-[#92400e]' }}">
                                {{ $lap->status }}
                            </span>
                        </td>
                        <td class="p-3 border-b border-[#f1f5f9]">
                            <div class="flex gap-1">
                                {{-- Preview PDF --}}
                                <a href="{{ route('admin.laporan.preview', $lap) }}" target="_blank"
                                    class="w-7 h-7 rounded-md bg-[#dbeafe] text-[#3b82f6] hover:bg-[#3b82f6] hover:text-white transition-all flex items-center justify-center text-xs" title="Preview PDF">
                                    <i class="fas fa-eye"></i>
                                </a>
                                {{-- Download PDF --}}
                                <a href="{{ route('admin.laporan.export', $lap) }}"
                                    class="w-7 h-7 rounded-md bg-[#d1fae5] text-[#10b981] hover:bg-[#10b981] hover:text-white transition-all flex items-center justify-center text-xs" title="Download PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                {{-- Edit --}}
                                <button onclick="openEditModal({{ $lap->id }}, '{{ addslashes($lap->judul) }}', '{{ $lap->periode }}', '{{ addslashes($lap->keterangan ?? '') }}', '{{ $lap->status }}')"
                                    class="w-7 h-7 rounded-md bg-[#fef3c7] text-[#d97706] hover:bg-[#d97706] hover:text-white transition-all flex items-center justify-center text-xs" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                {{-- Hapus --}}
                                <form method="POST" action="{{ route('admin.laporan.destroy', $lap) }}"
                                    onsubmit="return confirm('Hapus laporan ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-md bg-[#fee2e2] text-[#ef4444] hover:bg-[#ef4444] hover:text-white transition-all flex items-center justify-center text-xs" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if($laporanList->hasPages())
    <div class="mt-4">{{ $laporanList->links() }}</div>
    @endif
    @else
    <div class="text-center py-8 text-[#94a3b8]">
        <i class="fas fa-folder-open text-3xl block mb-2 opacity-30"></i>
        <p class="text-sm">Belum ada laporan tersimpan</p>
    </div>
    @endif
</div>

{{-- ===== MODAL SIMPAN LAPORAN ===== --}}
<div id="simpanLaporanModal" class="modal-overlay" onclick="if(event.target===this)closeModal('simpanLaporanModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="bg-gradient-to-r from-[#3b82f6] to-[#2563eb] px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-white font-semibold text-sm"><i class="fas fa-save mr-2"></i>Simpan Laporan</h3>
            <button onclick="closeModal('simpanLaporanModal')" class="text-white/80 hover:text-white text-xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.laporan.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="fl">Judul Laporan</label>
                <input type="text" name="judul"
                    value="Laporan Rekap {{ $periodeAwal->translatedFormat('F Y') }}"
                    class="fi" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="fl">Periode (Bulan)</label>
                    <select name="bulan_simpan" id="bulanSimpan" class="fi">
                        @foreach($bulanNames as $idx => $bName)
                        <option value="{{ $idx + 1 }}" {{ (int)$bulan === $idx+1 ? 'selected' : '' }}>{{ $bName }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="fl">Tahun</label>
                    <select name="tahun_simpan" id="tahunSimpan" class="fi">
                        @foreach($tahunOptions as $t)
                        <option value="{{ $t }}" {{ (int)$tahun === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            {{-- Hidden: format periode untuk disimpan ke DB --}}
            <input type="hidden" name="periode" id="periodeHidden" value="{{ $bulan }}-{{ $tahun }}">
            <div>
                <label class="fl">Keterangan (Opsional)</label>
                <textarea name="keterangan" rows="2" placeholder="Catatan tambahan laporan..." class="fi resize-none"></textarea>
            </div>
            <div>
                <label class="fl">Status</label>
                <select name="status" class="fi">
                    <option value="Draft">Draft — Masih bisa diedit</option>
                    <option value="Final">Final — Laporan resmi</option>
                </select>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="submit" class="flex-1 py-2.5 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white rounded-lg text-sm font-semibold">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
                <button type="button" onclick="closeModal('simpanLaporanModal')" class="px-4 py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL EDIT LAPORAN ===== --}}
<div id="editLaporanModal" class="modal-overlay" onclick="if(event.target===this)closeModal('editLaporanModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="bg-gradient-to-r from-[#f59e0b] to-[#d97706] px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-white font-semibold text-sm"><i class="fas fa-edit mr-2"></i>Edit Laporan</h3>
            <button onclick="closeModal('editLaporanModal')" class="text-white/80 hover:text-white text-xl">&times;</button>
        </div>
        <form id="editLaporanForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="fl">Judul Laporan</label>
                <input type="text" id="elJudul" name="judul" class="fi" required>
            </div>
            <div>
                <label class="fl">Periode</label>
                <input type="text" id="elPeriode" name="periode" class="fi" required placeholder="contoh: 1-2026">
            </div>
            <div>
                <label class="fl">Keterangan</label>
                <textarea id="elKet" name="keterangan" rows="2" class="fi resize-none"></textarea>
            </div>
            <div>
                <label class="fl">Status</label>
                <select id="elStatus" name="status" class="fi">
                    <option value="Draft">Draft</option>
                    <option value="Final">Final</option>
                </select>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="submit" class="flex-1 py-2.5 bg-gradient-to-r from-[#f59e0b] to-[#d97706] text-white rounded-lg text-sm font-semibold">
                    <i class="fas fa-save mr-1"></i> Update
                </button>
                <button type="button" onclick="closeModal('editLaporanModal')" class="px-4 py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold">Batal</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Sync hidden periode field saat bulan/tahun berubah
    ['bulanSimpan','tahunSimpan'].forEach(id => {
        document.getElementById(id)?.addEventListener('change', () => {
            const b = document.getElementById('bulanSimpan').value;
            const t = document.getElementById('tahunSimpan').value;
            document.getElementById('periodeHidden').value = b + '-' + t;
        });
    });

    function openEditModal(id, judul, periode, keterangan, status) {
        document.getElementById('editLaporanForm').action = '{{ url("admin/laporan") }}/' + id;
        document.getElementById('elJudul').value   = judul;
        document.getElementById('elPeriode').value = periode;
        document.getElementById('elKet').value     = keterangan;
        document.getElementById('elStatus').value  = status;
        openModal('editLaporanModal');
    }
</script>
@endpush
