@extends('layouts.admin')

@section('title', 'Denda')
@section('page-subtitle', 'Manajemen Denda')

@push('styles')
<style>
    .field-label { display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 5px; }
    .field-input { width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 13px; color: #1e293b; background: #f8fafc; outline: none; transition: border-color 0.2s; }
    .field-input:focus { border-color: #3b82f6; background: white; }
    select.field-input { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; background-size: 16px; padding-right: 32px; }
    .filter-tab { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 500; cursor: pointer; transition: all 0.2s; border: 1.5px solid #e2e8f0; color: #64748b; background: white; }
    .filter-tab.active { background: linear-gradient(to right, #ef4444, #dc2626); color: white; border-color: transparent; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[#fecaca] flex items-center justify-center text-[#ef4444] text-lg">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-[#1e293b]">Manajemen Denda</h2>
            <p class="text-xs text-[#64748b]">Pantau dan kelola denda keterlambatan pengembalian aset</p>
        </div>
    </div>
    <button onclick="openModal('aturDendaModal')"
        class="px-4 py-2 bg-gradient-to-r from-[#ef4444] to-[#dc2626] text-white rounded-lg text-sm font-semibold flex items-center gap-2 hover:-translate-y-0.5 hover:shadow-lg transition-all w-fit">
        <i class="fas fa-cog"></i> Atur Tarif Denda
    </button>
</div>

@if($stats['terlambat_count'] > 0)
{{-- Alert Banner --}}
<div class="bg-[#fef3c7] border border-[#fde68a] rounded-xl p-4 mb-6 flex items-start gap-3">
    <div class="w-8 h-8 rounded-full bg-[#fde68a] flex items-center justify-center text-[#d97706] flex-shrink-0 mt-0.5">
        <i class="fas fa-exclamation-triangle text-sm"></i>
    </div>
    <div>
        <p class="text-sm font-semibold text-[#92400e]">Perhatian: Ada {{ $stats['terlambat_count'] }} peminjaman yang melewati batas waktu pengembalian!</p>
        <p class="text-xs text-[#b45309] mt-0.5">Segera tindak lanjuti denda agar tidak menumpuk. Notifikasi otomatis sudah dikirim ke peminjam.</p>
    </div>
</div>
@endif

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    @php
    $ds = [
        ['label'=>'Total Denda Aktif','val'=>$stats['aktif'],'icon'=>'fas fa-exclamation-circle','bg'=>'bg-[#fecaca]','c'=>'text-[#ef4444]'],
        ['label'=>'Total Nominal','val'=>'Rp '.number_format($stats['total_nominal'],0,',','.'),'icon'=>'fas fa-coins','bg'=>'bg-[#fed7aa]','c'=>'text-[#f59e0b]'],
        ['label'=>'Sudah Dibayar','val'=>$stats['sudah_bayar'],'icon'=>'fas fa-check-circle','bg'=>'bg-[#d1fae5]','c'=>'text-[#10b981]'],
        ['label'=>'Total Terkumpul','val'=>'Rp '.number_format($stats['total_terkumpul'],0,',','.'),'icon'=>'fas fa-wallet','bg'=>'bg-[#dbeafe]','c'=>'text-[#3b82f6]'],
    ];
    @endphp
    @foreach($ds as $d)
    <div class="bg-white rounded-xl p-4 shadow-sm border border-[#e2e8f0] flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl {{ $d['bg'] }} flex items-center justify-center {{ $d['c'] }} flex-shrink-0">
            <i class="{{ $d['icon'] }}"></i>
        </div>
        <div>
            <div class="text-base md:text-lg font-bold text-[#1e293b]">{{ $d['val'] }}</div>
            <div class="text-xs text-[#64748b] leading-tight">{{ $d['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Denda Table --}}
<div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-[#e2e8f0]">
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <h3 class="text-base font-semibold text-[#1e293b] flex items-center gap-2"><i class="fas fa-list text-[#ef4444]"></i> Daftar Denda</h3>
        <div class="flex flex-wrap gap-2 sm:ml-auto">
            <button class="filter-tab active" onclick="filterDenda(this,'all')">Semua</button>
            <button class="filter-tab" onclick="filterDenda(this,'Belum Bayar')">Belum Bayar</button>
            <button class="filter-tab" onclick="filterDenda(this,'Sudah Bayar')">Sudah Bayar</button>
        </div>
    </div>

    <div class="overflow-x-auto -mx-5 md:mx-0">
        <div class="inline-block min-w-full align-middle px-5 md:px-0">
            <table class="min-w-full text-sm border-collapse" id="dendaTable">
                <thead class="bg-[#f8fafc]">
                    <tr>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">No</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Peminjam</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden sm:table-cell">Aset</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden md:table-cell">Tgl Jatuh Tempo</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden md:table-cell">Hari Telat</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Nominal</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Status</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Aksi</th>
                    </tr>
                </thead>
                <tbody id="dendaBody">
                    @forelse($dendas as $d)
                    <tr class="hover:bg-[#f8fafc] transition-colors" data-status="{{ $d->status === 'belum_bayar' ? 'Belum Bayar' : 'Sudah Bayar' }}">
                        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9]">{{ $dendas->firstItem() + $loop->index }}</td>
                        <td class="p-3 border-b border-[#f1f5f9]">
                            <div class="text-xs font-medium text-[#1e293b]">{{ $d->peminjaman->user->nama ?? 'User' }}</div>
                            <div class="text-[10px] text-[#94a3b8]">{{ $d->peminjaman->user->role ?? '-' }}</div>
                        </td>
                        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden sm:table-cell">{{ $d->peminjaman->aset->nama_aset ?? 'Aset' }}</td>
                        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden md:table-cell">
                            {{ $d->peminjaman->tanggal_kembali_rencana->format('d M Y') }}
                        </td>
                        <td class="p-3 border-b border-[#f1f5f9] hidden md:table-cell">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-[#fecaca] text-[#991b1b]">{{ $d->hari_terlambat }} hari</span>
                        </td>
                        <td class="p-3 text-xs font-bold text-[#ef4444] border-b border-[#f1f5f9]">{{ $d->total_format }}</td>
                        <td class="p-3 border-b border-[#f1f5f9]">
                            @if($d->status === 'belum_bayar')
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-[#fecaca] text-[#991b1b]">Belum Bayar</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-[#d1fae5] text-[#065f46]">Sudah Bayar</span>
                            @endif
                        </td>
                        <td class="p-3 border-b border-[#f1f5f9]">
                            <div class="flex gap-1" @php
                                $uNama = addslashes($d->peminjaman->user->nama ?? 'User');
                                $aNama = addslashes($d->peminjaman->aset->nama_aset ?? 'Aset');
                                $statusLbl = $d->status === 'belum_bayar' ? 'Belum Bayar' : 'Sudah Bayar';
                            @endphp>
                                <button onclick="openDendaDetail('{{ $uNama }}','{{ $aNama }}','{{ $d->total_format }}','{{ $d->hari_terlambat }}','{{ $statusLbl }}')"
                                    class="w-7 h-7 rounded-md bg-[#dbeafe] text-[#3b82f6] hover:bg-[#3b82f6] hover:text-white transition-all flex items-center justify-center text-xs" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($d->status === 'belum_bayar')
                                <button onclick="openBayarModal('{{ $d->id }}')"
                                    class="w-7 h-7 rounded-md bg-[#d1fae5] text-[#10b981] hover:bg-[#10b981] hover:text-white transition-all flex items-center justify-center text-xs" title="Konfirmasi Bayar">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-10 text-center text-[#94a3b8] text-sm">
                            <i class="fas fa-inbox text-4xl block mb-3 opacity-20"></i>
                            Belum ada data denda
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===== MODALS ===== --}}

{{-- Detail Denda Modal --}}
<div id="detailDendaModal" class="modal-overlay" onclick="if(event.target===this)closeModal('detailDendaModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="bg-gradient-to-r from-[#ef4444] to-[#dc2626] px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-white font-semibold text-base"><i class="fas fa-receipt mr-2"></i>Detail Denda</h3>
            <button onclick="closeModal('detailDendaModal')" class="text-white/80 hover:text-white text-xl">&times;</button>
        </div>
        <div class="p-6 space-y-2.5">
            <div class="flex justify-between text-sm border-b border-[#f1f5f9] pb-2">
                <span class="text-[#64748b]">Peminjam</span>
                <span id="ddNama" class="font-medium text-[#1e293b]"></span>
            </div>
            <div class="flex justify-between text-sm border-b border-[#f1f5f9] pb-2">
                <span class="text-[#64748b]">Aset</span>
                <span id="ddAset" class="font-medium text-[#1e293b]"></span>
            </div>
            <div class="flex justify-between text-sm border-b border-[#f1f5f9] pb-2">
                <span class="text-[#64748b]">Keterlambatan</span>
                <span id="ddHari" class="font-semibold text-[#ef4444]"></span>
            </div>
            <div class="flex justify-between text-sm border-b border-[#f1f5f9] pb-2">
                <span class="text-[#64748b]">Total Denda</span>
                <span id="ddNominal" class="font-bold text-[#ef4444] text-base"></span>
            </div>
            <div class="flex justify-between text-sm pb-2">
                <span class="text-[#64748b]">Status Pembayaran</span>
                <span id="ddStatus" class="px-2 py-0.5 rounded-full text-xs font-semibold"></span>
            </div>
        </div>
        <div class="px-6 pb-5">
            <button onclick="closeModal('detailDendaModal')" class="w-full py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold hover:bg-[#e2e8f0] transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- Konfirmasi Bayar Modal --}}
<div id="konfirmasiBayarModal" class="modal-overlay" onclick="if(event.target===this)closeModal('konfirmasiBayarModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4">
        <div class="bg-gradient-to-r from-[#10b981] to-[#059669] px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-white font-semibold text-base"><i class="fas fa-hand-holding-usd mr-2"></i>Konfirmasi Pembayaran</h3>
            <button onclick="closeModal('konfirmasiBayarModal')" class="text-white/80 hover:text-white text-xl">&times;</button>
        </div>
        <div class="p-6">
            <p class="text-sm text-[#475569] mb-4">Konfirmasi bahwa peminjam telah membayar denda:</p>
            <div class="bg-[#f8fafc] rounded-xl p-4 mb-4 text-center">
                <p class="text-xs text-[#64748b] mb-1">Total Pembayaran</p>
                <p class="text-2xl font-bold text-[#ef4444]">Rp 15.000</p>
            </div>
            <div class="mb-4">
                <label class="field-label">Metode Pembayaran</label>
                <select class="field-input">
                    <option>Tunai / Cash</option>
                    <option>Transfer Bank</option>
                    <option>QRIS</option>
                    <option>Lainnya</option>
                </select>
            </div>
            <div>
                <label class="field-label">Catatan</label>
                <textarea rows="2" placeholder="Catatan pembayaran..." class="field-input resize-none"></textarea>
            </div>
        </div>
        <div class="px-6 pb-5 flex gap-2">
            <button class="flex-1 py-2.5 bg-gradient-to-r from-[#10b981] to-[#059669] text-white rounded-lg text-sm font-semibold hover:opacity-90 transition-opacity">
                <i class="fas fa-check mr-1"></i> Konfirmasi Lunas
            </button>
            <button onclick="closeModal('konfirmasiBayarModal')" class="px-4 py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold hover:bg-[#e2e8f0]">
                Batal
            </button>
        </div>
    </div>
</div>

{{-- Atur Tarif Denda Modal --}}
<div id="aturDendaModal" class="modal-overlay" onclick="if(event.target===this)closeModal('aturDendaModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="bg-gradient-to-r from-[#7c3aed] to-[#6d28d9] px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-white font-semibold text-base"><i class="fas fa-cog mr-2"></i>Atur Tarif Denda</h3>
            <button onclick="closeModal('aturDendaModal')" class="text-white/80 hover:text-white text-xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.denda.settings') }}" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="bg-[#f5f3ff] border border-[#ddd6fe] rounded-xl p-4 text-sm text-[#5b21b6]">
                <i class="fas fa-info-circle mr-2"></i>
                Tarif denda berlaku untuk semua aset secara default. Kamu bisa override per aset di menu Master Data.
            </div>
            <div>
                <label class="field-label">Tarif Denda Per Hari (Rp)</label>
                <input type="number" name="tarif_per_hari" value="5000" class="field-input" required>
            </div>
            <div>
                <label class="field-label">Batas Maksimal Denda (Rp)</label>
                <input type="number" name="max_denda" value="100000" class="field-input">
            </div>
            <div>
                <label class="field-label">Notifikasi Otomatis</label>
                <select name="notif_auto" class="field-input">
                    <option value="1">Aktif — Kirim otomatis saat jatuh tempo</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-gradient-to-r from-[#7c3aed] to-[#6d28d9] text-white rounded-lg text-sm font-semibold hover:opacity-90 transition-opacity">
                    <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                </button>
                <button type="button" onclick="closeModal('aturDendaModal')" class="px-4 py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold hover:bg-[#e2e8f0]">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openBayarModal(id) {
        document.getElementById('bayarForm').action = '{{ url("admin/denda") }}/' + id + '/bayar';
        openModal('konfirmasiBayarModal');
    }

    function filterDenda(btn, status) {
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('#dendaBody tr').forEach(row => {
            if (status === 'all') { row.style.display = ''; return; }
            row.style.display = row.dataset.status === status ? '' : 'none';
        });
    }

    function openDendaDetail(nama, aset, nominal, hari, status) {
        document.getElementById('ddNama').textContent = nama;
        document.getElementById('ddAset').textContent = aset;
        document.getElementById('ddNominal').textContent = nominal;
        document.getElementById('ddHari').textContent = hari + ' hari keterlambatan';
        const ddStatus = document.getElementById('ddStatus');
        ddStatus.textContent = status;
        if (status === 'Belum Bayar') {
            ddStatus.className = 'px-2 py-0.5 rounded-full text-xs font-semibold bg-[#fecaca] text-[#991b1b]';
        } else {
            ddStatus.className = 'px-2 py-0.5 rounded-full text-xs font-semibold bg-[#d1fae5] text-[#065f46]';
        }
        openModal('detailDendaModal');
    }
</script>
@endpush
