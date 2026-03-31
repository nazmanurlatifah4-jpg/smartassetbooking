@extends('layouts.peminjam')
@section('title', 'Riwayat')
@section('page-subtitle', 'Riwayat Transaksi')

@push('styles')
<style>
    .table-wrapper { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-wrapper::-webkit-scrollbar { height: 6px; }
    .table-wrapper::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .table-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .table-wrapper::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .filter-tab { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 500; cursor: pointer; transition: all 0.2s; border: 1.5px solid #e2e8f0; color: #64748b; background: white; }
    .filter-tab.active { background: linear-gradient(to right, #3b82f6, #2563eb); color: white; border-color: transparent; }
</style>
@endpush

@section('content')

<div class="mb-4 sm:mb-6">
    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-[#1e293b] mb-1">Riwayat Transaksi Saya</h2>
    <div class="flex gap-2 items-center text-xs sm:text-sm text-[#64748b]">
        <a href="{{ route('peminjam.dashboard') }}" class="text-[#3b82f6] hover:underline">Dashboard</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <span>Riwayat</span>
    </div>
</div>

{{-- Filter Tabs --}}
<div class="bg-white rounded-xl p-4 sm:p-5 md:p-6 shadow-sm border border-gray-200 mb-5">
    <form method="GET" action="{{ route('peminjam.riwayat') }}" class="flex flex-col sm:flex-row gap-3 mb-4">
        <div class="flex flex-wrap gap-2">
            @foreach(['' => 'Semua', 'Menunggu' => 'Pending', 'Disetujui' => 'Aktif', 'Selesai' => 'Selesai', 'Ditolak' => 'Ditolak'] as $val => $label)
            <button type="submit" name="status" value="{{ $val }}"
                class="filter-tab {{ request('status') === $val ? 'active' : '' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
        <div class="relative sm:ml-auto">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#94a3b8] text-xs"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aset..."
                class="pl-8 pr-3 py-2 border border-[#e2e8f0] rounded-full text-xs focus:outline-none focus:border-[#3b82f6] bg-[#f8fafc] w-full sm:w-44">
        </div>
    </form>

    <div class="table-wrapper">
        <table class="min-w-full border-collapse text-sm" id="riwayatTable">
            <thead class="bg-[#f8fafc]">
                <tr>
                    <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap">No</th>
                    <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap">Jenis</th>
                    <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap">Nama Aset</th>
                    <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap hidden sm:table-cell">Jumlah</th>
                    <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap">Tanggal</th>
                    <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap hidden md:table-cell">Keterangan</th>
                    <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $r)
                @php
                    // Tentukan label status dan warnanya
                    $statusLabel = match($r->status) {
                        'Menunggu'  => 'Pending',
                        'Disetujui' => 'Aktif',
                        'Ditolak'   => 'Ditolak',
                        'Selesai'   => 'Selesai',
                        default     => $r->status,
                    };
                    $statusClass = match($r->status) {
                        'Menunggu'  => 'bg-yellow-100 text-yellow-800',
                        'Disetujui' => 'bg-green-100 text-green-800',
                        'Ditolak'   => 'bg-red-100 text-red-800',
                        'Selesai'   => 'bg-blue-100 text-blue-800',
                        default     => 'bg-gray-100 text-gray-600',
                    };
                    // Jenis transaksi: pinjam atau kembali
                    $isPengembalian = $r->pengembalian !== null && $r->status === 'Selesai';
                @endphp
                <tr class="hover:bg-[#f8fafc] transition-colors">
                    <td class="p-3 text-xs md:text-sm text-gray-600 border-b border-gray-100">{{ $riwayat->firstItem() + $loop->index }}</td>
                    <td class="p-3 border-b border-gray-100">
                        @if($isPengembalian)
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-[#3b82f6]">
                            <i class="fas fa-undo"></i> Pengembalian
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-[#f59e0b]">
                            <i class="fas fa-hand-holding"></i> Peminjaman
                        </span>
                        @endif
                    </td>
                    {{-- kolom nama_aset --}}
                    <td class="p-3 text-xs md:text-sm text-gray-600 border-b border-gray-100 font-medium">{{ $r->aset->nama_aset }}</td>
                    <td class="p-3 text-xs md:text-sm text-gray-600 border-b border-gray-100 hidden sm:table-cell">1</td>
                    {{-- kolom tanggal_pengajuan --}}
                    <td class="p-3 text-xs md:text-sm text-gray-600 border-b border-gray-100">{{ $r->tanggal_pengajuan->format('d M Y') }}</td>
                    <td class="p-3 text-xs md:text-sm text-gray-600 border-b border-gray-100 hidden md:table-cell">
                        {{ $r->keperluan ?? ($r->pengembalian?->catatan_admin ?? '-') }}
                    </td>
                    <td class="p-3 border-b border-gray-100">
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-10 text-center text-xs text-[#94a3b8]">
                        <i class="fas fa-inbox text-3xl block mb-2 opacity-20"></i>
                        Belum ada riwayat transaksi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($riwayat->hasPages())
    <div class="mt-4">{{ $riwayat->links() }}</div>
    @endif
</div>

@endsection
