@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-subtitle', 'Laporan & Rekap')

@push('styles')
<style>
    @media print {
        nav, aside, footer, .no-print { display: none !important; }
        main { padding: 0 !important; }
        .print-area { border: none !important; box-shadow: none !important; }
    }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[#dbeafe] flex items-center justify-center text-[#3b82f6] text-lg">
            <i class="fas fa-file-alt"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-[#1e293b]">Laporan</h2>
            <p class="text-xs text-[#64748b]">Rekap data peminjaman, pengembalian, dan denda</p>
        </div>
    </div>
    <div class="flex gap-2 no-print">
        <button onclick="window.print()"
            class="px-4 py-2 bg-white border border-[#e2e8f0] text-[#475569] rounded-lg text-sm font-semibold flex items-center gap-2 hover:bg-[#f8fafc] transition-colors">
            <i class="fas fa-print"></i> <span class="hidden sm:inline">Print</span>
        </button>
        <button onclick="exportPDF()"
            class="px-4 py-2 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white rounded-lg text-sm font-semibold flex items-center gap-2 hover:-translate-y-0.5 hover:shadow-lg transition-all">
            <i class="fas fa-file-pdf"></i> <span class="hidden sm:inline">Export PDF</span>
        </button>
    </div>
</div>

{{-- Filter Period --}}
<div class="bg-white rounded-xl p-4 shadow-sm border border-[#e2e8f0] mb-6 no-print">
    <div class="flex flex-col sm:flex-row gap-3 items-end">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-[#64748b] mb-1 uppercase tracking-wider">Dari Tanggal</label>
            <input type="date" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                class="w-full px-3 py-2 border border-[#e2e8f0] rounded-lg text-sm focus:outline-none focus:border-[#3b82f6] bg-[#f8fafc]">
        </div>
        <div class="flex-1">
            <label class="block text-xs font-semibold text-[#64748b] mb-1 uppercase tracking-wider">Sampai Tanggal</label>
            <input type="date" value="{{ now()->format('Y-m-d') }}"
                class="w-full px-3 py-2 border border-[#e2e8f0] rounded-lg text-sm focus:outline-none focus:border-[#3b82f6] bg-[#f8fafc]">
        </div>
        <div class="flex-1">
            <label class="block text-xs font-semibold text-[#64748b] mb-1 uppercase tracking-wider">Kategori</label>
            <select class="w-full px-3 py-2 border border-[#e2e8f0] rounded-lg text-sm focus:outline-none focus:border-[#3b82f6] bg-[#f8fafc]">
                <option>Semua Transaksi</option>
                <option>Peminjaman Aktif</option>
                <option>Pengembalian</option>
                <option>Denda</option>
            </select>
        </div>
        <button class="px-5 py-2 bg-[#3b82f6] text-white rounded-lg text-sm font-semibold hover:bg-[#2563eb] transition-colors flex items-center gap-2">
            <i class="fas fa-filter"></i> Filter
        </button>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6 print-area">
    @php
    $summary = [
        ['label'=>'Total Peminjaman','val'=>'48','icon'=>'fas fa-exchange-alt','bg'=>'bg-[#dbeafe]','c'=>'text-[#3b82f6]','trend'=>'+12%'],
        ['label'=>'Tepat Waktu','val'=>'40','icon'=>'fas fa-check-circle','bg'=>'bg-[#d1fae5]','c'=>'text-[#10b981]','trend'=>'83%'],
        ['label'=>'Terlambat','val'=>'8','icon'=>'fas fa-clock','bg'=>'bg-[#fecaca]','c'=>'text-[#ef4444]','trend'=>'17%'],
        ['label'=>'Total Denda','val'=>'Rp 452.000','icon'=>'fas fa-coins','bg'=>'bg-[#fef3c7]','c'=>'text-[#d97706]','trend'=>'+5%'],
    ];
    @endphp
    @foreach($summary as $s)
    <div class="bg-white rounded-xl p-4 shadow-sm border border-[#e2e8f0]">
        <div class="flex items-center justify-between mb-2">
            <div class="w-9 h-9 rounded-xl {{ $s['bg'] }} flex items-center justify-center {{ $s['c'] }} text-base">
                <i class="{{ $s['icon'] }}"></i>
            </div>
            <span class="text-xs text-[#94a3b8]">{{ $s['trend'] }}</span>
        </div>
        <div class="text-lg font-bold text-[#1e293b]">{{ $s['val'] }}</div>
        <div class="text-xs text-[#64748b]">{{ $s['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Laporan Table --}}
<div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-[#e2e8f0] mb-6 print-area">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-semibold text-[#1e293b] flex items-center gap-2">
            <i class="fas fa-table text-[#3b82f6]"></i> Rekap Transaksi Februari 2026
        </h3>
    </div>
    <div class="overflow-x-auto -mx-5 md:mx-0">
        <div class="inline-block min-w-full align-middle px-5 md:px-0">
            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-[#f8fafc]">
                    <tr>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">No</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Peminjam</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden sm:table-cell">Aset</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden md:table-cell">Pinjam</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden md:table-cell">Kembali</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Status</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $reports = [
                        ['no'=>1,'name'=>'Budi Santoso','aset'=>'Laptop Lenovo','pinjam'=>'07 Feb','kembali'=>'10 Feb','status'=>'Menunggu','denda'=>'-'],
                        ['no'=>2,'name'=>'Siti Nurhaliza','aset'=>'Proyektor Epson','pinjam'=>'07 Feb','kembali'=>'09 Feb','status'=>'Disetujui','denda'=>'-'],
                        ['no'=>3,'name'=>'Ahmad Ridwan','aset'=>'Kamera Canon','pinjam'=>'05 Feb','kembali'=>'07 Feb','status'=>'Terlambat','denda'=>'Rp 15.000'],
                        ['no'=>4,'name'=>'Dewi Lestari','aset'=>'Sound System','pinjam'=>'06 Feb','kembali'=>'08 Feb','status'=>'Selesai','denda'=>'-'],
                        ['no'=>5,'name'=>'Eko Prasetyo','aset'=>'iPad Pro','pinjam'=>'05 Feb','kembali'=>'10 Feb','status'=>'Disetujui','denda'=>'-'],
                        ['no'=>6,'name'=>'Fitri Handayani','aset'=>'Laptop Lenovo','pinjam'=>'03 Feb','kembali'=>'05 Feb','status'=>'Selesai','denda'=>'-'],
                        ['no'=>7,'name'=>'Galih Pratama','aset'=>'Drone DJI','pinjam'=>'01 Feb','kembali'=>'03 Feb','status'=>'Terlambat','denda'=>'Rp 50.000'],
                    ];
                    @endphp
                    @foreach($reports as $r)
                    @php
                        $sc = match($r['status']) {
                            'Menunggu' => 'bg-[#fed7aa] text-[#c2410c]',
                            'Disetujui' => 'bg-[#d1fae5] text-[#065f46]',
                            'Terlambat' => 'bg-[#fecaca] text-[#991b1b]',
                            'Selesai' => 'bg-[#e0e7ff] text-[#3730a3]',
                            default => 'bg-[#f1f5f9] text-[#64748b]',
                        };
                    @endphp
                    <tr class="hover:bg-[#f8fafc] transition-colors">
                        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9]">{{ $r['no'] }}</td>
                        <td class="p-3 text-xs text-[#1e293b] border-b border-[#f1f5f9] font-medium">{{ $r['name'] }}</td>
                        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden sm:table-cell">{{ $r['aset'] }}</td>
                        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden md:table-cell">{{ $r['pinjam'] }}</td>
                        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden md:table-cell">{{ $r['kembali'] }}</td>
                        <td class="p-3 border-b border-[#f1f5f9]">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc }}">{{ $r['status'] }}</span>
                        </td>
                        <td class="p-3 text-xs border-b border-[#f1f5f9] {{ $r['denda'] !== '-' ? 'text-[#ef4444] font-semibold' : 'text-[#94a3b8]' }}">{{ $r['denda'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-[#f8fafc]">
                    <tr>
                        <td colspan="6" class="p-3 text-xs font-bold text-[#1e293b] border-t-2 border-[#e2e8f0] text-right">Total Denda:</td>
                        <td class="p-3 text-sm font-bold text-[#ef4444] border-t-2 border-[#e2e8f0]">Rp 65.000</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- Top Asset Chart (Simple Visual) --}}
<div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-[#e2e8f0] print-area">
    <h3 class="text-base font-semibold text-[#1e293b] mb-4 flex items-center gap-2">
        <i class="fas fa-chart-bar text-[#3b82f6]"></i> Aset Paling Sering Dipinjam
    </h3>
    @php
    $topAssets = [
        ['nama'=>'Laptop Lenovo ThinkPad','count'=>18,'pct'=>90],
        ['nama'=>'Proyektor Epson EB-X41','count'=>14,'pct'=>70],
        ['nama'=>'Kamera Canon EOS 1500D','count'=>12,'pct'=>60],
        ['nama'=>'iPad Pro 12.9"','count'=>9,'pct'=>45],
        ['nama'=>'Sound System Simbadda','count'=>6,'pct'=>30],
    ];
    @endphp
    <div class="space-y-3">
        @foreach($topAssets as $a)
        <div>
            <div class="flex justify-between text-xs mb-1">
                <span class="text-[#475569] font-medium">{{ $a['nama'] }}</span>
                <span class="text-[#3b82f6] font-semibold">{{ $a['count'] }}x</span>
            </div>
            <div class="w-full bg-[#e2e8f0] rounded-full h-2">
                <div class="bg-gradient-to-r from-[#3b82f6] to-[#2563eb] h-2 rounded-full transition-all duration-500" style="width:{{ $a['pct'] }}%"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection

@push('scripts')
<script>
    function exportPDF() {
        window.print();
    }
</script>
@endpush
