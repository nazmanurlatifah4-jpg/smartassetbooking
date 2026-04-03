@extends('layouts.admin')

@section('title', 'Detail Peminjaman')
@section('page-subtitle', 'Detail Transaksi')

@section('content')

<div class="mb-5">
    <a href="{{ route('admin.transaksi') }}" class="inline-flex items-center gap-2 text-sm text-[#64748b] hover:text-[#3b82f6] transition-colors">
        <i class="fas fa-arrow-left"></i> Kembali ke Transaksi
    </a>
</div>

@php
$bc = match($peminjaman->status) {
    'menunggu'  => 'bg-[#fed7aa] text-[#c2410c]',
    'disetujui' => 'bg-[#d1fae5] text-[#065f46]',
    'dipinjam'  => 'bg-[#dbeafe] text-[#1d4ed8]',
    'terlambat' => 'bg-[#fecaca] text-[#991b1b]',
    'selesai'   => 'bg-[#e0e7ff] text-[#3730a3]',
    default     => 'bg-[#f1f5f9] text-[#64748b]',
};
@endphp

<div class="grid md:grid-cols-2 gap-5">

    {{-- Info Peminjaman --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e2e8f0]">
        <h3 class="text-base font-bold text-[#1e293b] mb-4 flex items-center gap-2">
            <i class="fas fa-info-circle text-[#3b82f6]"></i> Detail Peminjaman
        </h3>
        <div class="space-y-3">
            @php
            $fields = [
    'ID Peminjaman'    => '#' . str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT),
    'Status'           => null,
    'Tanggal Pinjam'   => $peminjaman->tanggal_pinjam?->format('d F Y') ?? '-',
    'Rencana Kembali'  => $peminjaman->tanggal_kembali?->format('d F Y') ?? '-',
    'Aktual Kembali'   => $peminjaman->pengembalian?->tanggal_pengembalian?->format('d F Y') ?? '-',
    'Keperluan'        => $peminjaman->keperluan ?? '-',
    'Kondisi Kembali'  => $peminjaman->pengembalian?->kondisi_barang ?? '-',
    'Catatan Admin'    => $peminjaman->verifikasi?->catatan ?? '-',
];
            @endphp
            @foreach($fields as $label => $val)
            <div class="flex justify-between items-start gap-3 border-b border-[#f1f5f9] pb-2 last:border-0 last:pb-0">
                <span class="text-xs text-[#64748b] flex-shrink-0">{{ $label }}</span>
                @if($label === 'Status')
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $bc }}">{{ ucfirst($peminjaman->status) }}</span>
                @else
                <span class="text-xs font-medium text-[#1e293b] text-right">{{ $val }}</span>
                @endif
            </div>
            @endforeach
            @if($peminjaman->isTerlambat())
            <div class="bg-[#fef2f2] border border-[#fecaca] rounded-lg p-3 mt-2">
                <p class="text-xs font-semibold text-[#ef4444]">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Terlambat {{ $peminjaman->hari_terlambat }} hari
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- Info Peminjam + Aset --}}
    <div class="space-y-5">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e2e8f0]">
            <h3 class="text-base font-bold text-[#1e293b] mb-4 flex items-center gap-2">
                <i class="fas fa-user text-[#3b82f6]"></i> Data Peminjam
            </h3>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#3b82f6] to-[#2563eb] flex items-center justify-center text-white font-bold text-lg">
                    {{ substr($peminjaman->user->name, 0, 1) }}
                </div>
                <div>
                    <p class="font-semibold text-[#1e293b] text-sm">{{ $peminjaman->user->name }}</p>
                    <p class="text-xs text-[#64748b]">{{ $peminjaman->user->email }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="bg-[#f8fafc] rounded-lg p-2">
                    <p class="text-[#94a3b8]">Jurusan</p>
                    <p class="font-medium text-[#1e293b]">{{ $peminjaman->user->jurusan ?? '-' }}</p>
                </div>
                <div class="bg-[#f8fafc] rounded-lg p-2">
                    <p class="text-[#94a3b8]">Kelas</p>
                    <p class="font-medium text-[#1e293b]">{{ $peminjaman->user->kelas ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e2e8f0]">
            <h3 class="text-base font-bold text-[#1e293b] mb-4 flex items-center gap-2">
                <i class="fas fa-box text-[#3b82f6]"></i> Data Aset
            </h3>
            <p class="font-semibold text-[#1e293b] text-sm mb-1">{{ $peminjaman->aset->nama }}</p>
            <p class="text-xs text-[#64748b] mb-3"> {{ is_object($peminjaman->aset->kategori) ? $peminjaman->aset->kategori->nama : ($peminjaman->aset->kategori ?? '-') }} </p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="bg-[#f8fafc] rounded-lg p-2">
                    <p class="text-[#94a3b8]">Kondisi</p>
                    <p class="font-medium text-[#1e293b]">{{ ucfirst($peminjaman->aset->kondisi) }}</p>
                </div>
                <div class="bg-[#f8fafc] rounded-lg p-2">
                    <p class="text-[#94a3b8]">Denda/Hari</p>
                    <p class="font-medium text-[#ef4444]">Rp {{ number_format($peminjaman->aset->denda_per_hari ?? 5000, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Info Denda (jika ada) --}}
        @if($peminjaman->denda)
        <div class="bg-[#fef2f2] border border-[#fecaca] rounded-2xl p-5">
            <h3 class="text-sm font-bold text-[#991b1b] mb-3 flex items-center gap-2">
                <i class="fas fa-money-bill-wave"></i> Info Denda
            </h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-[#64748b]">Hari Terlambat</span>
                    <span class="font-semibold text-[#ef4444]">{{ $peminjaman->denda->hari_terlambat }} hari</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#64748b]">Total Denda</span>
                    <span class="font-bold text-[#ef4444] text-sm">{{ $peminjaman->denda->total_format }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#64748b]">Status Bayar</span>
                    @if($peminjaman->denda->sudahBayar())
                    <span class="px-2 py-0.5 rounded-full bg-[#d1fae5] text-[#065f46] font-semibold">Sudah Bayar</span>
                    @else
                    <span class="px-2 py-0.5 rounded-full bg-[#fecaca] text-[#991b1b] font-semibold">Belum Bayar</span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{{-- Action Buttons --}}
<div class="mt-5 bg-white rounded-2xl p-5 shadow-sm border border-[#e2e8f0]">
    <h3 class="text-sm font-semibold text-[#1e293b] mb-3">Aksi</h3>
    <div class="flex items-center gap-3 mt-6">
    @if($peminjaman->status === 'Menunggu')
        {{-- Tombol Setuju --}}
        <form action="{{ route('admin.transaksi.approve', $peminjaman->id) }}" method="POST" class="flex-1">
            @csrf
            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                <i class="fas fa-check"></i> Setujui
            </button>
        </form>

        {{-- Tombol Tolak --}}
        <form action="{{ route('admin.transaksi.reject', $peminjaman->id) }}" method="POST" class="flex-1">
            @csrf
            <button type="submit" onclick="return confirm('Yakin ingin menolak ini?')" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                <i class="fas fa-times"></i> Tolak
            </button>
        </form>
    @endif
        {{-- Tombol Kembali Selalu Ada --}}
        <a href="{{ route('admin.transaksi') }}" class="px-4 py-2 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold hover:bg-[#e2e8f0]">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>
</div>

{{-- Reuse modals dari transaksi --}}
<div id="tolakModal" class="modal-overlay" onclick="if(event.target===this)closeModal('tolakModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4">
        <div class="bg-gradient-to-r from-[#ef4444] to-[#dc2626] px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-white font-semibold text-base"><i class="fas fa-times-circle mr-2"></i>Tolak Pengajuan</h3>
            <button onclick="closeModal('tolakModal')" class="text-white/80 hover:text-white text-xl">&times;</button>
        </div>
        <form id="tolakForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-[#64748b] mb-1.5 uppercase tracking-wider">Alasan Penolakan</label>
                <textarea name="catatan_admin" rows="3" class="w-full px-3 py-2 border border-[#e2e8f0] rounded-lg text-sm focus:outline-none focus:border-[#3b82f6] bg-[#f8fafc] resize-none"></textarea>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-[#ef4444] text-white rounded-lg text-sm font-semibold">Ya, Tolak</button>
                <button type="button" onclick="closeModal('tolakModal')" class="flex-1 py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold">Batal</button>
            </div>
        </form>
    </div>
</div>

<div id="kembaliModal" class="modal-overlay" onclick="if(event.target===this)closeModal('kembaliModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4">
        <div class="bg-gradient-to-r from-[#10b981] to-[#059669] px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-white font-semibold text-base"><i class="fas fa-undo mr-2"></i>Konfirmasi Pengembalian</h3>
            <button onclick="closeModal('kembaliModal')" class="text-white/80 hover:text-white text-xl">&times;</button>
        </div>
        <form id="kembaliForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-[#64748b] mb-1.5 uppercase tracking-wider">Kondisi Saat Kembali</label>
                <select name="catatan_kondisi" class="w-full px-3 py-2 border border-[#e2e8f0] rounded-lg text-sm focus:outline-none focus:border-[#3b82f6] bg-[#f8fafc]">
                    <option>Baik — tidak ada kerusakan</option>
                    <option>Cukup — ada sedikit kerusakan</option>
                    <option>Rusak — perlu perbaikan</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-gradient-to-r from-[#10b981] to-[#059669] text-white rounded-lg text-sm font-semibold">Konfirmasi</button>
                <button type="button" onclick="closeModal('kembaliModal')" class="flex-1 py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold">Batal</button>
            </div>
        </form>
    </div>
</div>

@endsection
