@extends('layouts.peminjam')
@section('title', 'Dashboard Peminjam')
@section('page-subtitle', 'Dashboard Peminjam')

@section('content')

@if($stats['terlambat'] > 0)
<div class="bg-[#fef3c7] border border-[#fde68a] rounded-xl p-4 mb-6 flex items-start gap-3 animate-pulse">
    <div class="w-8 h-8 rounded-full bg-[#fde68a] flex items-center justify-center text-[#d97706] flex-shrink-0 mt-0.5">
        <i class="fas fa-exclamation-triangle text-sm"></i>
    </div>
    <div>
        <p class="text-sm font-semibold text-[#92400e]">Peringatan: Kamu memiliki {{ $stats['terlambat'] }} pinjaman yang terlambat!</p>
        <p class="text-xs text-[#b45309] mt-0.5">Segera kembalikan aset ke perpustakaan/lab agar denda tidak terus bertambah.</p>
    </div>
</div>
@endif

{{-- Greeting Card --}}
<div class="bg-gradient-to-br from-[#3b82f6] to-[#2563eb] rounded-xl md:rounded-2xl p-6 md:p-8 text-white mb-6 relative overflow-hidden">
    <div class="flex flex-col items-center text-center">
        <div class="max-w-[600px] mb-4 md:mb-6">
            <h2 class="text-xl sm:text-2xl md:text-3xl font-bold mb-2 md:mb-3">Selamat Datang, {{ auth()->user()->nama }}!</h2>
            <p class="text-sm sm:text-base opacity-95">Ajukan peminjaman aset dengan mudah dan cepat</p>
        </div>
        <div class="mb-4 md:mb-6">
            <img src="{{ asset('assets/gtpeminjam.png') }}" alt="hero" class="w-36 sm:w-44 md:w-52 lg:w-56 h-auto mx-auto">
        </div>
        <div>
            <a href="{{ route('peminjam.aset') }}" class="inline-block bg-white text-[#3b82f6] px-6 md:px-8 py-2.5 md:py-3 rounded-full font-semibold text-sm md:text-base shadow-lg hover:-translate-y-0.5 hover:shadow-xl transition-all">
                Ajukan Peminjaman
            </a>
        </div>
    </div>
</div>

{{-- Stats Cards --}}
<div class="flex gap-3 md:gap-4 overflow-x-auto pb-2 stats-scroll mb-6">
    @php
    $cards = [
        ['icon'=>'fas fa-clock',          'bg'=>'bg-[#dbeafe]','c'=>'text-[#3b82f6]', 'val'=>$stats['aktif'],     'label'=>'Aktif'],
        ['icon'=>'fas fa-hourglass-half', 'bg'=>'bg-[#fed7aa]','c'=>'text-[#f59e0b]', 'val'=>$stats['diproses'],  'label'=>'Diproses'],
        ['icon'=>'fas fa-exclamation-circle','bg'=>'bg-[#fecaca]','c'=>'text-[#ef4444]','val'=>$stats['terlambat'],'label'=>'Terlambat'],
        ['icon'=>'fas fa-check-double',    'bg'=>'bg-[#d1fae5]','c'=>'text-[#10b981]', 'val'=>$stats['selesai'],   'label'=>'Selesai'],
        ['icon'=>'fas fa-list-alt',        'bg'=>'bg-[#e9d5ff]','c'=>'text-[#a855f7]', 'val'=>$stats['total'],     'label'=>'Total'],
    ];
    @endphp
    @foreach($cards as $card)
    <div class="min-w-[160px] bg-white rounded-xl p-4 shadow-sm border border-[#e2e8f0]">
        <div class="w-10 h-10 rounded-xl {{ $card['bg'] }} flex items-center justify-center mb-3 {{ $card['c'] }}">
            <i class="{{ $card['icon'] }}"></i>
        </div>
        <div class="text-2xl font-bold text-[#1e293b]">{{ $card['val'] }}</div>
        <div class="text-xs text-[#64748b]">{{ $card['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Tabel Riwayat --}}
<div class="bg-white rounded-xl p-6 shadow-sm border border-[#e2e8f0] mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-semibold text-[#1e293b]">Riwayat Terbaru</h3>
        <a href="{{ route('peminjam.riwayat') }}" class="text-xs text-[#3b82f6]">Lihat semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-[#f8fafc]">
                <tr>
                    <th class="p-3 text-[#64748b]">Aset</th>
                    <th class="p-3 text-[#64748b]">Tgl Pinjam</th>
                    <th class="p-3 text-[#64748b]">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatTerbaru as $r)
                <tr class="border-b border-[#f8fafc]">
                    <td class="p-3 font-medium">{{ $r->aset->nama_aset }}</td>
                    <td class="p-3">{{ \Carbon\Carbon::parse($r->tanggal_pengajuan)->format('d/m/Y') }}</td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded-md text-[10px] font-bold 
                            {{ $r->status == 'Disetujui' ? 'bg-blue-100 text-blue-600' : ($r->status == 'Menunggu' ? 'bg-orange-100 text-orange-600' : 'bg-green-100 text-green-600') }}">
                            {{ $r->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center py-4 text-gray-400">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('right-sidebar')
<aside class="w-[300px] p-6 bg-white border-l border-[#e2e8f0] hidden xl:block">
    {{-- Riwayat Sidebar --}}
    <div class="mb-8">
        <div class="font-semibold mb-4 flex items-center gap-2">
            <i class="fas fa-history text-[#3b82f6]"></i> Riwayat Selesai
        </div>
        @foreach($riwayatSidebar as $rs)
        <div class="p-3 bg-[#f8fafc] rounded-lg mb-2 border-l-4 border-[#3b82f6]">
            <p class="text-xs font-bold">{{ $rs->aset->nama_aset }}</p>
            <span class="text-[10px] text-[#94a3b8]">{{ \Carbon\Carbon::parse($rs->tanggal_kembali)->format('d M Y') }}</span>
        </div>
        @endforeach
    </div>

    {{-- Notifikasi Sidebar --}}
    <div class="mb-6 lg:mb-8">
        <div class="text-sm lg:text-base font-semibold text-[#1e293b] mb-3 lg:mb-4 flex items-center gap-2">
            <i class="fas fa-bell text-[#3b82f6]"></i> Notifikasi
        </div>
        @foreach(auth()->user()->notifikasi()->latest('tanggal_kirim')->limit(3)->get() as $notif)
        <div class="p-3 lg:p-4 bg-[#f8fafc] rounded-lg mb-2 lg:mb-3 border-l-4 border-[#3b82f6]">
            <p class="text-xs lg:text-sm text-[#475569] mb-1"><strong>{{ $notif->judul }}</strong></p>
            <p class="text-xs lg:text-sm text-[#475569] mb-1">{{ Str::limit($notif->pesan, 50) }}</p>
            <span class="text-[10px] lg:text-xs text-[#94a3b8]">{{ $notif->tanggal_kirim->diffForHumans() }}</span>
        </div>
        @endforeach
    </div>
</aside>
@endsection