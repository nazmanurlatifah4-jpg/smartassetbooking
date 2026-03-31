@extends('layouts.peminjam')
@section('title', 'Dashboard Peminjam')
@section('page-subtitle', 'Dashboard Peminjam')

@section('content')

{{-- Greeting Card --}}
<div class="bg-gradient-to-br from-[#3b82f6] to-[#2563eb] rounded-xl md:rounded-2xl p-6 md:p-8 text-white mb-6 relative overflow-hidden">
    <div class="flex flex-col items-center text-center">
        <div class="max-w-[600px] mb-4 md:mb-6">
            {{-- kolom 'nama' sesuai migration --}}
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

{{-- Stats Cards — data dari controller --}}
<div class="flex gap-3 md:gap-4 overflow-x-auto pb-2 stats-scroll mb-6">
    @php
    $cards = [
        ['icon'=>'fas fa-clock',       'bg'=>'bg-[#dbeafe]','c'=>'text-[#3b82f6]', 'val'=>$aktif,       'label'=>'Aktif (Dipinjam)'],
        ['icon'=>'fas fa-hourglass-half','bg'=>'bg-[#fed7aa]','c'=>'text-[#f59e0b]','val'=>$diproses,    'label'=>'Diproses'],
        ['icon'=>'fas fa-exclamation-circle','bg'=>'bg-[#fecaca]','c'=>'text-[#ef4444]','val'=>$terlambat,'label'=>'Terlambat'],
        ['icon'=>'fas fa-check-double', 'bg'=>'bg-[#d1fae5]','c'=>'text-[#10b981]', 'val'=>$selesai,     'label'=>'Selesai'],
        ['icon'=>'fas fa-list-alt',     'bg'=>'bg-[#e9d5ff]','c'=>'text-[#a855f7]', 'val'=>$total,       'label'=>'Total Riwayat'],
    ];
    @endphp
    @foreach($cards as $card)
    <div class="min-w-[160px] sm:min-w-[180px] md:min-w-[200px] bg-white rounded-xl md:rounded-2xl p-4 md:p-5 shadow-sm border border-[#e2e8f0] hover:-translate-y-1 hover:shadow-lg transition-all">
        <div class="flex justify-between items-center mb-2 md:mb-3">
            <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl {{ $card['bg'] }} flex items-center justify-center text-xl md:text-2xl {{ $card['c'] }}">
                <i class="{{ $card['icon'] }}"></i>
            </div>
        </div>
        <div class="text-xl md:text-2xl lg:text-3xl font-bold text-[#1e293b]">{{ $card['val'] }}</div>
        <div class="text-xs md:text-sm text-[#64748b]">{{ $card['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Riwayat Peminjaman Saya --}}
<div class="bg-white rounded-xl md:rounded-2xl p-4 md:p-5 lg:p-6 shadow-sm border border-[#e2e8f0] mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-base md:text-lg font-semibold text-[#1e293b]">Riwayat Peminjaman Saya</h3>
        <a href="{{ route('peminjam.riwayat') }}" class="text-xs text-[#3b82f6] hover:underline">Lihat semua</a>
    </div>
    <div class="overflow-x-auto -mx-4 md:mx-0">
        <div class="inline-block min-w-full align-middle px-4 md:px-0">
            <table class="min-w-full border-collapse text-sm">
                <thead class="bg-[#f8fafc]">
                    <tr>
                        <th class="p-2 md:p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] whitespace-nowrap">No</th>
                        <th class="p-2 md:p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] whitespace-nowrap">Nama Aset</th>
                        <th class="p-2 md:p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] whitespace-nowrap hidden sm:table-cell">Tgl Pinjam</th>
                        <th class="p-2 md:p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] whitespace-nowrap hidden md:table-cell">Tgl Kembali</th>
                        <th class="p-2 md:p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatTerbaru as $p)
                    @php
                        $bc = match($p->status) {
                            'Menunggu'  => 'bg-[#fed7aa] text-[#c2410c]',
                            'Disetujui' => 'bg-[#dbeafe] text-[#1e40af]',
                            'Ditolak'   => 'bg-[#fecaca] text-[#991b1b]',
                            'Selesai'   => 'bg-[#d1fae5] text-[#065f46]',
                            default     => 'bg-[#f1f5f9] text-[#64748b]',
                        };
                        $label = match($p->status) {
                            'Disetujui' => 'Aktif',
                            'Menunggu'  => 'Diproses',
                            default     => $p->status,
                        };
                    @endphp
                    <tr class="hover:bg-[#f8fafc]">
                        <td class="p-2 md:p-3 text-xs md:text-sm text-[#475569] border-b border-[#f1f5f9]">{{ $loop->iteration }}</td>
                        {{-- kolom 'nama_aset' dari tabel asets --}}
                        <td class="p-2 md:p-3 text-xs md:text-sm text-[#475569] border-b border-[#f1f5f9]">{{ $p->aset->nama_aset }}</td>
                        {{-- kolom 'tanggal_pengajuan' --}}
                        <td class="p-2 md:p-3 text-xs md:text-sm text-[#475569] border-b border-[#f1f5f9] hidden sm:table-cell">{{ $p->tanggal_pengajuan->format('d M Y') }}</td>
                        {{-- kolom 'tanggal_kembali' --}}
                        <td class="p-2 md:p-3 text-xs md:text-sm text-[#475569] border-b border-[#f1f5f9] hidden md:table-cell">{{ $p->tanggal_kembali->format('d M Y') }}</td>
                        <td class="p-2 md:p-3 border-b border-[#f1f5f9]">
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold {{ $bc }}">
                                {{ $label }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-xs text-[#94a3b8]">Belum ada riwayat peminjaman</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('right-sidebar')
<aside class="right-sidebar-hide w-[280px] lg:w-[300px] p-5 lg:p-6 bg-white border-l border-[#e2e8f0] sticky top-[73px] h-[calc(100vh-73px)] overflow-y-auto hidden xl:block">
    {{-- Riwayat Terakhir --}}
    <div class="mb-6 lg:mb-8">
        <div class="text-sm lg:text-base font-semibold text-[#1e293b] mb-3 lg:mb-4 flex items-center gap-2">
            <i class="fas fa-history text-[#3b82f6]"></i> Riwayat Terakhir
        </div>
        @foreach($riwayatSidebar as $r)
        <div class="p-3 lg:p-4 bg-[#f8fafc] rounded-lg mb-2 lg:mb-3 border-l-4 border-[#3b82f6]">
            <p class="text-xs lg:text-sm text-[#475569] mb-1"><strong>{{ $r->status }}</strong></p>
            <p class="text-xs lg:text-sm text-[#475569] mb-1">{{ $r->aset->nama_aset }}</p>
            <span class="text-[10px] lg:text-xs text-[#94a3b8]">{{ $r->tanggal_kembali->format('d M Y') }}</span>
        </div>
        @endforeach
    </div>
    {{-- Notifikasi --}}
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
