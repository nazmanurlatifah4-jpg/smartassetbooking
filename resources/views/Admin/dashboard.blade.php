@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-subtitle', 'Dashboard Admin')

@section('content')

{{-- Greeting Banner --}}
<div class="bg-gradient-to-br from-[#3b82f6] to-[#2563eb] rounded-2xl p-6 md:p-8 text-white mb-6 flex flex-col md:flex-row items-center justify-center gap-6 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3"></div>
    <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>
    <img src="{{ asset('assets/logo-dashboard-admin.png') }}" class="w-24 sm:w-32 md:w-36 drop-shadow-xl z-10" alt="Admin Hero">
    <div class="text-center z-10">
        <p class="text-sm text-white/80 mb-1">Selamat datang kembali 👋</p>
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-2">Halo, {{ auth()->user()->nama ?? 'Admin' }}!</h2>
        <p class="text-sm opacity-90 mb-4 max-w-md">Kelola sistem peminjaman aset sekolah dengan mudah, cepat, dan transparan.</p>
        <a href="{{ route('admin.transaksi') }}" class="inline-block bg-white text-[#3b82f6] px-6 py-2.5 rounded-full font-semibold text-sm shadow-lg hover:-translate-y-0.5 hover:shadow-xl transition-all">
            <i class="fas fa-tasks mr-2"></i>Kelola Transaksi
        </a>
    </div>
</div>

{{-- Stats Cards --}}
<div class="flex gap-3 md:gap-4 overflow-x-auto pb-2 stats-scroll mb-6">
    @php
        $stats = [
            ['icon'=>'fas fa-users','bg'=>'bg-[#dbeafe]','color'=>'text-[#3b82f6]','val'=>$totalUser ?? 156,'label'=>'Total User'],
            ['icon'=>'fas fa-box','bg'=>'bg-[#e9d5ff]','color'=>'text-[#a855f7]','val'=>$totalAset ?? 89,'label'=>'Total Aset'],
            ['icon'=>'fas fa-clock','bg'=>'bg-[#fed7aa]','color'=>'text-[#f59e0b]','val'=>$peminjaman ?? 24,'label'=>'Peminjaman Aktif'],
            ['icon'=>'fas fa-exclamation-triangle','bg'=>'bg-[#fecaca]','color'=>'text-[#ef4444]','val'=>$terlambat ?? 5,'label'=>'Terlambat'],
            ['icon'=>'fas fa-money-bill-wave','bg'=>'bg-[#fde68a]','color'=>'text-[#d97706]','val'=>$denda ?? 3,'label'=>'Denda Aktif'],
            ['icon'=>'fas fa-check-circle','bg'=>'bg-[#d1fae5]','color'=>'text-[#10b981]','val'=>$selesai ?? 12,'label'=>'Selesai Hari Ini'],
        ];
    @endphp
    @foreach($stats as $s)
    <div class="min-w-[170px] sm:min-w-[190px] bg-white rounded-2xl p-4 md:p-5 shadow-sm border border-[#e2e8f0] hover:-translate-y-1 hover:shadow-lg transition-all flex-shrink-0">
        <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl {{ $s['bg'] }} flex items-center justify-center text-xl {{ $s['color'] }} mb-3">
            <i class="{{ $s['icon'] }}"></i>
        </div>
        <div class="text-2xl md:text-3xl font-bold text-[#1e293b]">{{ $s['val'] }}</div>
        <div class="text-xs text-[#64748b] mt-0.5">{{ $s['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- Recent Transactions Table --}}
<div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-[#e2e8f0] mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <h3 class="text-base md:text-lg font-semibold text-[#1e293b] flex items-center gap-2">
            <i class="fas fa-list-alt text-[#3b82f6]"></i> Pengajuan Terbaru
        </h3>
        <a href="{{ route('admin.transaksi') }}" class="text-xs text-[#3b82f6] hover:underline flex items-center gap-1">
            Lihat semua <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div class="overflow-x-auto -mx-5 md:mx-0">
        <div class="inline-block min-w-full align-middle px-5 md:px-0">
            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-[#f8fafc]">
                    <tr>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">No</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Peminjam</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden sm:table-cell">Aset</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden md:table-cell">Tgl Pinjam</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Status</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Aksi</th>
                    </tr>
                </thead>
                <tbody>
    @forelse($peminjamanTerbaru as $index => $p)
    <tr class="hover:bg-[#f8fafc] transition-colors">
        {{-- Nomor Urut --}}
        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9]">
            {{ $index + 1 }}
        </td>
        
        {{-- Nama Peminjam --}}
        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] font-medium">
            {{ $p->user->nama ?? 'User Tidak Ada' }}
        </td>
        
        {{-- Nama Aset --}}
        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden sm:table-cell">
            {{ $p->aset->nama_aset ?? 'Aset Terhapus' }}
        </td>
        
        {{-- Tanggal Pinjam --}}
        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden md:table-cell">
            {{ $p->tanggal_pengajuan->format('d M Y') }}
        </td>
        
        {{-- Status dengan Warna Dinamis --}}
        <td class="p-3 border-b border-[#f1f5f9]">
            @php
                // Logika warna otomatis berdasarkan status
                $statusClasses = [
                    'Menunggu' => 'bg-[#fed7aa] text-[#c2410c]',
                    'Disetujui' => 'bg-[#d1fae5] text-[#065f46]',
                    'Ditolak'   => 'bg-[#fecaca] text-[#991b1b]',
                    'Selesai'   => 'bg-[#e0f2fe] text-[#0369a1]',
                ];
                $class = $statusClasses[$p->status] ?? 'bg-gray-100 text-gray-600';
            @endphp
            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $class }}">
                {{ $p->status }}
            </span>
        </td>
        
        {{-- Tombol Detail --}}
        <td class="p-3 border-b border-[#f1f5f9]">
            <button onclick="openModal('detailModal')" class="text-xs text-[#3b82f6] hover:underline flex items-center gap-1">
                <i class="fas fa-eye"></i> Detail
            </button>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="p-5 text-center text-gray-500 text-xs">
            Belum ada pengajuan peminjaman saat ini.
        </td>
    </tr>
    @endforelse
</tbody>
            </table>
        </div>
    </div>
</div>

{{-- Detail Modal --}}
<div id="detailModal" class="modal-overlay" onclick="if(event.target===this)closeModal('detailModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-[#3b82f6] to-[#2563eb] px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-semibold text-base flex items-center gap-2"><i class="fas fa-info-circle"></i> Detail Peminjaman</h3>
            <button onclick="closeModal('detailModal')" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
        </div>
        <div class="p-6 space-y-3">
            <div class="flex justify-between text-sm border-b border-[#f1f5f9] pb-2">
                <span class="text-[#64748b]">Nama Peminjam</span>
                <span class="font-semibold text-[#1e293b]">Budi Santoso</span>
            </div>
            <div class="flex justify-between text-sm border-b border-[#f1f5f9] pb-2">
                <span class="text-[#64748b]">Nama Aset</span>
                <span class="font-semibold text-[#1e293b]">Laptop Lenovo</span>
            </div>
            <div class="flex justify-between text-sm border-b border-[#f1f5f9] pb-2">
                <span class="text-[#64748b]">Tanggal Pinjam</span>
                <span class="font-semibold text-[#1e293b]">07 Feb 2026</span>
            </div>
            <div class="flex justify-between text-sm border-b border-[#f1f5f9] pb-2">
                <span class="text-[#64748b]">Tanggal Kembali</span>
                <span class="font-semibold text-[#1e293b]">10 Feb 2026</span>
            </div>
            <div class="flex justify-between text-sm pb-2">
                <span class="text-[#64748b]">Status</span>
                <span class="px-2 py-0.5 bg-[#fed7aa] text-[#c2410c] rounded-full text-xs font-semibold">Menunggu</span>
            </div>
        </div>

        <div class="flex gap-2 mt-4">
    <form action="{{ route('admin.transaksi.approve', $p->id) }}" method="POST" class="flex-1">
        @csrf
        <button type="submit" onclick="return confirm('Setujui peminjaman ini?')" 
            class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold transition-all shadow-sm">
        <div class="px-6 pb-5 flex gap-2">
    <form action="{{ route('admin.transaksi.approve', $p->id) }}" method="POST" class="flex-1">
        @csrf
        <button type="submit" class="w-full py-2 rounded-full bg-[#d1fae5] text-[#065f46] text-sm font-semibold hover:bg-[#a7f3d0] transition-colors">
            <i class="fas fa-check mr-1"></i> Setujui
        </button>
    </form>


    <form action="{{ route('admin.transaksi.reject', $p->id) }}" method="POST">
    @csrf
    <div class="flex flex-col gap-2">
        <textarea name="catatan" placeholder="Alasan ditolak..." class="text-xs p-2 border rounded-md"></textarea>
        
        <button type="submit" onclick="return confirm('Yakin ingin menolak peminjaman ini?')" 
            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold transition-all">
            <i class="fas fa-times mr-1"></i> Tolak Peminjaman
        </button>
    </div>
</form>

    <form action="{{ route('admin.transaksi.reject', $p->id) }}" method="POST" class="flex-1">
        @csrf
        <button type="submit" class="w-full py-2 rounded-full bg-[#fecaca] text-[#991b1b] text-sm font-semibold hover:bg-[#fca5a5] transition-colors">
            <i class="fas fa-times mr-1"></i> Tolak
        </button>
    </form>

    <button type="button" onclick="closeModal('detailModal')" class="px-4 py-2 rounded-full bg-[#f1f5f9] text-[#64748b] text-sm font-semibold hover:bg-[#e2e8f0] transition-colors">
        Tutup
    </button>
>>>>>>> 629c3b93746c4db7dc4d99dd101f6be6e3ca02f2
</div>
    </div>
</div>

@endsection
