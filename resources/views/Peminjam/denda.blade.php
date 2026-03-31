@extends('layouts.peminjam')
@section('title', 'Denda Saya')
@section('page-subtitle', 'Denda & Tagihan')

@push('styles')
<style>
    .table-wrapper { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-wrapper::-webkit-scrollbar { height: 6px; }
    .table-wrapper::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .table-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .table-wrapper::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    .filter-tab { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 500; cursor: pointer; transition: all 0.2s; border: 1.5px solid #e2e8f0; color: #64748b; background: white; }
    .filter-tab.active { background: linear-gradient(to right, #ef4444, #dc2626); color: white; border-color: transparent; }

    /* Modal */
    .modal-box { background: white; border-radius: 20px; max-width: 480px; width: 100%; max-height: 90vh; overflow-y: auto; }

    @keyframes pulse-ring {
        0%   { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239,68,68,.5); }
        70%  { transform: scale(1);    box-shadow: 0 0 0 10px rgba(239,68,68,0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239,68,68,0); }
    }
    .pulse-red { animation: pulse-ring 2s infinite; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
    <div>
        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-[#1e293b] mb-1">Denda Saya</h2>
        <div class="flex gap-2 items-center text-xs sm:text-sm text-[#64748b]">
            <a href="{{ route('peminjam.dashboard') }}" class="text-[#3b82f6] hover:underline">Dashboard</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span>Denda</span>
        </div>
    </div>
</div>

{{-- Alert Denda Aktif --}}
@if($dendaBelumLunas->count() > 0)
<div class="bg-[#fef2f2] border border-[#fecaca] rounded-xl p-4 mb-6 flex items-start gap-3">
    <div class="w-9 h-9 rounded-full bg-[#fecaca] flex items-center justify-center flex-shrink-0 pulse-red">
        <i class="fas fa-exclamation-triangle text-[#ef4444] text-sm"></i>
    </div>
    <div>
        <p class="text-sm font-semibold text-[#991b1b]">
            Kamu memiliki {{ $dendaBelumLunas->count() }} denda yang belum lunas!
        </p>
        <p class="text-xs text-[#b91c1c] mt-0.5">
            Total tagihan: <strong>Rp {{ number_format($dendaBelumLunas->sum('total_denda'), 0, ',', '.') }}</strong>.
            Segera lunasi agar tidak mempengaruhi pengajuan berikutnya.
        </p>
    </div>
</div>
@endif

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    @php
    $cards = [
        ['label'=>'Belum Lunas',     'val'=>$stats['belum_lunas'],    'icon'=>'fas fa-exclamation-circle', 'bg'=>'bg-[#fecaca]',  'c'=>'text-[#ef4444]'],
        ['label'=>'Total Tagihan',   'val'=>'Rp '.number_format($stats['total_nominal'],0,',','.'), 'icon'=>'fas fa-coins','bg'=>'bg-[#fed7aa]','c'=>'text-[#f59e0b]'],
        ['label'=>'Sudah Lunas',     'val'=>$stats['sudah_lunas'],    'icon'=>'fas fa-check-circle',       'bg'=>'bg-[#d1fae5]',  'c'=>'text-[#10b981]'],
        ['label'=>'Total Dibayar',   'val'=>'Rp '.number_format($stats['total_lunas'],0,',','.'),   'icon'=>'fas fa-wallet',     'bg'=>'bg-[#dbeafe]',  'c'=>'text-[#3b82f6]'],
    ];
    @endphp
    @foreach($cards as $c)
    <div class="bg-white rounded-xl p-4 shadow-sm border border-[#e2e8f0] flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} flex items-center justify-center {{ $c['c'] }} flex-shrink-0">
            <i class="{{ $c['icon'] }}"></i>
        </div>
        <div class="min-w-0">
            <div class="text-base font-bold text-[#1e293b] truncate">{{ $c['val'] }}</div>
            <div class="text-xs text-[#64748b]">{{ $c['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Denda Table --}}
<div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-[#e2e8f0]">

    {{-- Filter --}}
    <form method="GET" action="{{ route('peminjam.denda') }}" class="flex flex-wrap gap-2 mb-4">
        @foreach(['' => 'Semua', 'Belum Lunas' => 'Belum Lunas', 'Lunas' => 'Sudah Lunas'] as $val => $label)
        <button type="submit" name="status" value="{{ $val }}"
            class="filter-tab {{ request('status') === $val ? 'active' : '' }}">
            {{ $label }}
        </button>
        @endforeach
    </form>

    <div class="table-wrapper">
        <table class="min-w-full border-collapse text-sm">
            <thead class="bg-[#f8fafc]">
                <tr>
                    <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] whitespace-nowrap">No</th>
                    <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] whitespace-nowrap">Aset</th>
                    <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] whitespace-nowrap hidden sm:table-cell">Jenis Denda</th>
                    <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] whitespace-nowrap hidden md:table-cell">Hari Telat</th>
                    <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] whitespace-nowrap">Total Denda</th>
                    <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] whitespace-nowrap">Status</th>
                    <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dendas as $d)
                @php
                    $jenisCls = match($d->jenis_denda) {
                        'Telat'       => 'bg-[#fed7aa] text-[#c2410c]',
                        'Rusak Berat' => 'bg-[#e9d5ff] text-[#7e22ce]',
                        'Hilang'      => 'bg-[#fecaca] text-[#991b1b]',
                        default       => 'bg-[#f1f5f9] text-[#64748b]',
                    };
                @endphp
                <tr class="hover:bg-[#f8fafc] transition-colors">
                    <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9]">{{ $dendas->firstItem() + $loop->index }}</td>
                    <td class="p-3 border-b border-[#f1f5f9]">
                        {{-- nama_aset dari relasi --}}
                        <p class="text-xs font-medium text-[#1e293b]">{{ $d->peminjaman->aset->nama_aset }}</p>
                        <p class="text-[10px] text-[#94a3b8]">{{ $d->peminjaman->aset->kode_aset }}</p>
                    </td>
                    {{-- jenis_denda: Telat | Rusak Berat | Hilang --}}
                    <td class="p-3 border-b border-[#f1f5f9] hidden sm:table-cell">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $jenisCls }}">{{ $d->jenis_denda }}</span>
                    </td>
                    {{-- jumlah_hari --}}
                    <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden md:table-cell">
                        {{ $d->jumlah_hari > 0 ? $d->jumlah_hari . ' hari' : '-' }}
                    </td>
                    {{-- total_denda --}}
                    <td class="p-3 border-b border-[#f1f5f9]">
                        <span class="text-sm font-bold {{ $d->status_bayar === 'Belum Lunas' ? 'text-[#ef4444]' : 'text-[#10b981]' }}">
                            {{ $d->total_format }}
                        </span>
                    </td>
                    {{-- status_bayar: Belum Lunas | Lunas --}}
                    <td class="p-3 border-b border-[#f1f5f9]">
                        @if($d->status_bayar === 'Belum Lunas')
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-[#fecaca] text-[#991b1b]">Belum Lunas</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-[#d1fae5] text-[#065f46]">Lunas</span>
                        @endif
                    </td>
                    <td class="p-3 border-b border-[#f1f5f9]">
                        <button onclick="openDetailDenda({{ $d->id }})"
                            class="w-7 h-7 rounded-md bg-[#dbeafe] text-[#3b82f6] hover:bg-[#3b82f6] hover:text-white transition-all flex items-center justify-center text-xs" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-12 text-center">
                        <i class="fas fa-smile text-4xl text-[#d1fae5] block mb-3"></i>
                        <p class="text-sm font-semibold text-[#1e293b]">Tidak ada denda! 🎉</p>
                        <p class="text-xs text-[#94a3b8] mt-1">Kamu selalu tepat waktu mengembalikan aset.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($dendas->hasPages())
    <div class="mt-4">{{ $dendas->links() }}</div>
    @endif
</div>

{{-- Info Cara Bayar --}}
@if($stats['belum_lunas'] > 0)
<div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-[#e2e8f0] mt-5">
    <h3 class="text-sm font-semibold text-[#1e293b] mb-4 flex items-center gap-2">
        <i class="fas fa-info-circle text-[#3b82f6]"></i> Cara Melunasi Denda
    </h3>
    <div class="space-y-3">
        <div class="flex items-start gap-3 p-3 bg-[#f8fafc] rounded-xl border border-[#e2e8f0]">
            <div class="w-8 h-8 rounded-full bg-[#dbeafe] flex items-center justify-center text-[#3b82f6] font-bold text-sm flex-shrink-0">1</div>
            <div>
                <p class="text-xs font-semibold text-[#1e293b]">Datang ke Admin Sekolah</p>
                <p class="text-xs text-[#64748b] mt-0.5">Bawa bukti denda dari aplikasi ini (screenshot halaman ini) ke admin/petugas sekolah.</p>
            </div>
        </div>
        <div class="flex items-start gap-3 p-3 bg-[#f8fafc] rounded-xl border border-[#e2e8f0]">
            <div class="w-8 h-8 rounded-full bg-[#dbeafe] flex items-center justify-center text-[#3b82f6] font-bold text-sm flex-shrink-0">2</div>
            <div>
                <p class="text-xs font-semibold text-[#1e293b]">Lakukan Pembayaran</p>
                <p class="text-xs text-[#64748b] mt-0.5">Bayar secara tunai, transfer bank, atau QRIS sesuai arahan admin. Simpan bukti pembayaran.</p>
            </div>
        </div>
        <div class="flex items-start gap-3 p-3 bg-[#f8fafc] rounded-xl border border-[#e2e8f0]">
            <div class="w-8 h-8 rounded-full bg-[#dbeafe] flex items-center justify-center text-[#3b82f6] font-bold text-sm flex-shrink-0">3</div>
            <div>
                <p class="text-xs font-semibold text-[#1e293b]">Status Otomatis Berubah</p>
                <p class="text-xs text-[#64748b] mt-0.5">Setelah admin mengkonfirmasi, status denda kamu akan berubah menjadi <strong class="text-[#10b981]">Lunas</strong> secara otomatis.</p>
            </div>
        </div>
    </div>

    <div class="mt-4 p-3 bg-[#fef3c7] border border-[#fde68a] rounded-xl">
        <p class="text-xs text-[#92400e] flex items-start gap-2">
            <i class="fas fa-exclamation-triangle mt-0.5 flex-shrink-0"></i>
            <span>Denda yang belum lunas dapat mempengaruhi pengajuan peminjaman berikutnya. Segera hubungi admin jika ada pertanyaan.</span>
        </p>
    </div>
</div>
@endif

{{-- Modal Detail Denda --}}
<div class="modal-overlay" id="detailDendaModal" onclick="if(event.target===this)closeModal('detailDendaModal')">
    <div class="modal-box">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#ef4444] to-[#dc2626] px-6 py-4 rounded-t-2xl flex items-center justify-between">
            <h3 class="text-white font-semibold text-base flex items-center gap-2">
                <i class="fas fa-receipt"></i> Detail Denda
            </h3>
            <button onclick="closeModal('detailDendaModal')" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
        </div>

        {{-- Body --}}
        <div class="p-6">
            {{-- Status Badge besar --}}
            <div class="text-center mb-5">
                <div id="ddStatusIcon" class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2"></div>
                <p id="ddStatusLabel" class="text-sm font-bold"></p>
            </div>

            {{-- Info rows --}}
            <div class="space-y-2.5 mb-5">
                <div class="flex justify-between text-sm border-b border-[#f1f5f9] pb-2">
                    <span class="text-[#64748b]">Aset</span>
                    <span id="ddAset" class="font-medium text-[#1e293b] text-right max-w-[55%]"></span>
                </div>
                <div class="flex justify-between text-sm border-b border-[#f1f5f9] pb-2">
                    <span class="text-[#64748b]">Jenis Denda</span>
                    <span id="ddJenis" class="font-semibold px-2 py-0.5 rounded-full text-xs"></span>
                </div>
                <div class="flex justify-between text-sm border-b border-[#f1f5f9] pb-2">
                    <span class="text-[#64748b]">Hari Terlambat</span>
                    <span id="ddHari" class="font-medium text-[#1e293b]"></span>
                </div>
                <div class="flex justify-between text-sm border-b border-[#f1f5f9] pb-2">
                    <span class="text-[#64748b]">Tarif/Hari</span>
                    <span id="ddTarif" class="font-medium text-[#1e293b]"></span>
                </div>
                <div class="flex justify-between text-sm border-b border-[#f1f5f9] pb-2">
                    <span class="text-[#64748b]">Total Denda</span>
                    <span id="ddTotal" class="font-bold text-[#ef4444] text-base"></span>
                </div>
                <div class="flex justify-between text-sm border-b border-[#f1f5f9] pb-2">
                    <span class="text-[#64748b]">Tgl Peminjaman</span>
                    <span id="ddTglPinjam" class="font-medium text-[#1e293b]"></span>
                </div>
                <div class="flex justify-between text-sm pb-2">
                    <span class="text-[#64748b]">Catatan Admin</span>
                    <span id="ddCatatan" class="font-medium text-[#1e293b] text-right max-w-[55%]"></span>
                </div>
            </div>

            {{-- Info cara bayar jika belum lunas --}}
            <div id="ddBayarInfo" class="hidden bg-[#fef3c7] border border-[#fde68a] rounded-xl p-3 mb-4">
                <p class="text-xs text-[#92400e] font-semibold mb-1"><i class="fas fa-info-circle mr-1"></i>Cara Melunasi</p>
                <p class="text-xs text-[#92400e]">Datang ke admin sekolah dengan membawa bukti ini untuk melunasi denda.</p>
            </div>

            <button onclick="closeModal('detailDendaModal')"
                class="w-full py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-xl text-sm font-semibold hover:bg-[#e2e8f0] transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Data denda di-pass dari controller sebagai JSON
    const dendaData = @json($dendaJson);

    function openDetailDenda(id) {
        const d = dendaData[id];
        if (!d) return;

        // Status icon & label
        const isLunas = d.status_bayar === 'Lunas';
        const icon    = document.getElementById('ddStatusIcon');
        const label   = document.getElementById('ddStatusLabel');
        if (isLunas) {
            icon.className  = 'w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2 bg-[#d1fae5]';
            icon.innerHTML  = '<i class="fas fa-check-circle text-[#10b981] text-3xl"></i>';
            label.textContent = 'Denda Sudah Lunas';
            label.className   = 'text-sm font-bold text-[#10b981]';
        } else {
            icon.className  = 'w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2 bg-[#fecaca] pulse-red';
            icon.innerHTML  = '<i class="fas fa-exclamation-circle text-[#ef4444] text-3xl"></i>';
            label.textContent = 'Denda Belum Lunas';
            label.className   = 'text-sm font-bold text-[#ef4444]';
        }

        // Isi data
        document.getElementById('ddAset').textContent     = d.aset;
        document.getElementById('ddHari').textContent     = d.jumlah_hari > 0 ? d.jumlah_hari + ' hari' : '-';
        document.getElementById('ddTarif').textContent    = d.tarif_per_hari > 0 ? 'Rp ' + d.tarif_per_hari.toLocaleString('id') : '-';
        document.getElementById('ddTotal').textContent    = d.total_format;
        document.getElementById('ddTglPinjam').textContent= d.tgl_pengajuan;
        document.getElementById('ddCatatan').textContent  = d.catatan_admin || '-';

        // Jenis badge
        const jenisBadge = document.getElementById('ddJenis');
        jenisBadge.textContent = d.jenis_denda;
        const jenisClass = {
            'Telat':       'bg-[#fed7aa] text-[#c2410c]',
            'Rusak Berat': 'bg-[#e9d5ff] text-[#7e22ce]',
            'Hilang':      'bg-[#fecaca] text-[#991b1b]',
        };
        jenisBadge.className = 'font-semibold px-2 py-0.5 rounded-full text-xs ' + (jenisClass[d.jenis_denda] || 'bg-[#f1f5f9] text-[#64748b]');

        // Tampilkan info bayar jika belum lunas
        document.getElementById('ddBayarInfo').classList.toggle('hidden', isLunas);

        openModal('detailDendaModal');
    }
</script>
@endpush
