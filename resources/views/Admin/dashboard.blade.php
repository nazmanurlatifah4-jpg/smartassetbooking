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
            [
                'icon'  => 'fas fa-users',
                'bg'    => 'bg-[#dbeafe]',
                'color' => 'text-[#3b82f6]',
                'val'   => $totalUser ?? 0, 
                'label' => 'Total User'
            ],
            [
                'icon'  => 'fas fa-box',
                'bg'    => 'bg-[#e9d5ff]',
                'color' => 'text-[#a855f7]',
                'val'   => $totalAset ?? 0,
                'label' => 'Total Aset'
            ],
            [
                'icon'  => 'fas fa-clock',
                'bg'    => 'bg-[#fed7aa]',
                'color' => 'text-[#f59e0b]',
                'val'   => $peminjamanAktif ?? 0,
                'label' => 'Peminjaman Aktif'
            ],
            [
                'icon'  => 'fas fa-exclamation-triangle',
                'bg'    => 'bg-[#fecaca]',
                'color' => 'text-[#ef4444]',
                'val'   => $terlambat ?? 0,
                'label' => 'Terlambat'
            ],
            [
                'icon'  => 'fas fa-money-bill-wave',
                'bg'    => 'bg-[#fde68a]',
                'color' => 'text-[#d97706]',
                'val'   => $dendaAktif ?? 0,
                'label' => 'Denda Aktif'
            ],
            [
                'icon'  => 'fas fa-check-circle',
                'bg'    => 'bg-[#d1fae5]',
                'color' => 'text-[#10b981]',
                'val'   => $selesaiHariIni ?? 0,
                'label' => 'Selesai Hari Ini'
            ],
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
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Status</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamanTerbaru as $index => $p)
                    <tr class="hover:bg-[#f8fafc] transition-colors text-[#475569]">
                        <td class="p-3 border-b border-[#f1f5f9]">{{ $index + 1 }}</td>
                        <td class="p-3 border-b border-[#f1f5f9] font-medium">{{ $p->user->nama ?? 'N/A' }}</td>
                        <td class="p-3 border-b border-[#f1f5f9] hidden sm:table-cell">{{ $p->aset->nama_aset ?? 'N/A' }}</td>
                        <td class="p-3 border-b border-[#f1f5f9]">
                            @php
                                $statusClasses = [
                                    'Menunggu' => 'bg-[#fed7aa] text-[#c2410c]',
                                    'Disetujui' => 'bg-[#d1fae5] text-[#065f46]',
                                    'Ditolak'   => 'bg-[#fecaca] text-[#991b1b]',
                                    'Selesai'   => 'bg-[#e0f2fe] text-[#0369a1]',
                                ];
                                $class = $statusClasses[$p->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $class }}">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td class="p-3 border-b border-[#f1f5f9]">
                            <button type="button" onclick="openModal('detailModal-{{ $p->id }}')" class="text-xs text-[#3b82f6] hover:underline font-semibold flex items-center gap-1">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                        </td>
                    </tr>

                    {{-- MODAL TIAP DATA --}}
                    <div id="detailModal-{{ $p->id }}" class="fixed inset-0 z-[999] hidden items-center justify-center bg-black/60 backdrop-blur-sm px-4">
                        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-auto overflow-hidden animate-in fade-in zoom-in duration-200">
                            {{-- Header Modal --}}
                            <div class="bg-gradient-to-r from-[#3b82f6] to-[#2563eb] px-6 py-4 flex items-center justify-between text-white">
                                <h3 class="font-semibold text-sm flex items-center gap-2">
                                    <i class="fas fa-info-circle"></i> Detail Peminjaman
                                </h3>
                                <button type="button" onclick="closeModal('detailModal-{{ $p->id }}')" class="hover:text-white/70 text-2xl leading-none">&times;</button>
                            </div>
                            
                            {{-- Isi Modal --}}
                            <div class="p-6 space-y-4 text-sm text-slate-600">
                                <div class="flex justify-between border-b border-slate-50 pb-2">
                                    <span>Peminjam</span>
                                    <span class="font-bold text-slate-800">{{ $p->user->nama }}</span>
                                </div>
                                <div class="flex justify-between border-b border-slate-50 pb-2">
                                    <span>Aset</span>
                                    <span class="font-bold text-slate-800">{{ $p->aset->nama_aset }}</span>
                                </div>
                                <div class="flex justify-between border-b border-slate-50 pb-2">
                                    <span>Tgl Pinjam</span>
                                    <span class="font-bold text-slate-800">{{ $p->tanggal_pengajuan->format('d M Y') }}</span>
                                </div>
                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    <span class="text-[10px] text-slate-400 font-bold block uppercase mb-1">Keperluan:</span>
                                    <p class="text-slate-700 italic text-xs leading-relaxed">"{{ $p->keperluan ?? '-' }}"</p>
                                </div>
                            </div>

                            {{-- Footer/Aksi --}}
                            <div class="px-6 pb-6 pt-2 flex flex-col gap-2">
                                @if($p->status == 'Menunggu')
                                    <div class="flex gap-2">
                                        <form action="{{ route('admin.transaksi.approve', $p->id) }}" method="POST" class="flex-1 m-0">
                                            @csrf
                                            <button type="submit" class="w-full py-2.5 bg-emerald-500 text-white rounded-xl text-xs font-bold hover:bg-emerald-600 shadow-sm transition-all">Setujui</button>
                                        </form>
                                        <button type="button" onclick="document.getElementById('formTolak-{{ $p->id }}').classList.toggle('hidden')" 
                                            class="flex-1 py-2.5 bg-rose-500 text-white rounded-xl text-xs font-bold hover:bg-rose-600 shadow-sm transition-all">Tolak</button>
                                    </div>
                                    <form id="formTolak-{{ $p->id }}" action="{{ route('admin.transaksi.reject', $p->id) }}" method="POST" class="hidden">
                                        @csrf
                                        <textarea name="catatan" required placeholder="Alasan penolakan..." class="w-full text-xs p-3 border border-slate-200 rounded-xl mt-2 focus:ring-2 focus:ring-rose-500 outline-none transition-all"></textarea>
                                        <button type="submit" class="w-full py-2 bg-slate-800 text-white rounded-xl text-xs font-bold mt-1 hover:bg-black transition-all">Kirim Penolakan</button>
                                    </form>
                                @endif
                                <button type="button" onclick="closeModal('detailModal-{{ $p->id }}')" 
                                    class="w-full py-2.5 rounded-xl bg-slate-100 text-slate-500 text-xs font-bold hover:bg-slate-200 transition-all">Tutup</button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="5" class="p-10 text-center text-slate-400 italic">Tidak ada data transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    function openModal(id) {
        console.log('Membuka modal:', id); // Cek di F12 Console
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        } else {
            console.error('Modal tidak ditemukan:', id);
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    }
</script>
@endpush