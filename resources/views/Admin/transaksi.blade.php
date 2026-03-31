@extends('layouts.admin')

@section('title', 'Transaksi')
@section('page-subtitle', 'Transaksi Peminjaman')

@push('styles')
<style>
    .field-label { display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 5px; }
    .field-input { width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 13px; color: #1e293b; background: #f8fafc; outline: none; transition: border-color 0.2s; }
    .field-input:focus { border-color: #3b82f6; background: white; }
    select.field-input { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; background-size: 16px; padding-right: 32px; }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[#dbeafe] flex items-center justify-center text-[#3b82f6] text-lg">
            <i class="fas fa-exchange-alt"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-[#1e293b]">Transaksi</h2>
            <p class="text-xs text-[#64748b]">Kelola semua aktivitas peminjaman aset</p>
        </div>
    </div>
    <button onclick="openModal('addTransaksiModal')"
        class="px-4 py-2 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white rounded-lg text-sm font-semibold flex items-center gap-2 hover:-translate-y-0.5 hover:shadow-lg transition-all w-fit">
        <i class="fas fa-plus"></i> Buat Peminjaman
    </button>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    @php
    $statCards = [
        ['label'=>'Total','val'=>$stats->total,'icon'=>'fas fa-list','bg'=>'bg-[#dbeafe]','c'=>'text-[#3b82f6]'],
        ['label'=>'Menunggu','val'=>$stats->menunggu,'icon'=>'fas fa-clock','bg'=>'bg-[#fed7aa]','c'=>'text-[#f59e0b]'],
        ['label'=>'Aktif','val'=>$stats->dipinjam,'icon'=>'fas fa-hand-holding','bg'=>'bg-[#d1fae5]','c'=>'text-[#10b981]'],
        ['label'=>'Terlambat','val'=>$stats->terlambat,'icon'=>'fas fa-exclamation-triangle','bg'=>'bg-[#fecaca]','c'=>'text-[#ef4444]'],
    ];
    @endphp
    @foreach($statCards as $sc)
    <div class="bg-white rounded-xl p-4 shadow-sm border border-[#e2e8f0] flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl {{ $sc['bg'] }} flex items-center justify-center {{ $sc['c'] }} flex-shrink-0">
            <i class="{{ $sc['icon'] }}"></i>
        </div>
        <div>
            <div class="text-xl font-bold text-[#1e293b]">{{ $sc['val'] }}</div>
            <div class="text-xs text-[#64748b]">{{ $sc['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-[#e2e8f0]">
    <form method="GET" action="{{ route('admin.transaksi') }}" class="flex flex-col sm:flex-row gap-3 mb-4">
        <div class="flex flex-wrap gap-2">
            @foreach([''=>'Semua','menunggu'=>'Menunggu','disetujui'=>'Disetujui','terlambat'=>'Terlambat','ditolak'=>'Ditolak','selesai'=>'Selesai'] as $val => $label)
            <button type="submit" name="status" value="{{ $val }}"
                class="px-3 py-1.5 rounded-full text-xs font-medium border transition-all {{ request('status')===$val ? 'bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white border-transparent' : 'border-[#e2e8f0] text-[#64748b] bg-white hover:border-[#3b82f6]' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
        <div class="relative sm:ml-auto">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#94a3b8] text-xs"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari peminjam / aset..."
                class="pl-8 pr-3 py-2 border border-[#e2e8f0] rounded-lg text-xs focus:outline-none focus:border-[#3b82f6] bg-[#f8fafc] w-full sm:w-52">
        </div>
    </form>

    <div class="overflow-x-auto -mx-5 md:mx-0">
        <div class="inline-block min-w-full align-middle px-5 md:px-0">
            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-[#f8fafc]">
                    <tr>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">No</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Peminjam</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden sm:table-cell">Aset</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden md:table-cell">Tgl Pinjam</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden md:table-cell">Tgl Kembali</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Status</th>
                        <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjaman as $p)
                    @php
                        $bc = match($p->status) {
                            'menunggu'  => 'bg-[#fed7aa] text-[#c2410c]',
                            'disetujui' => 'bg-[#d1fae5] text-[#065f46]',
                            'dipinjam'  => 'bg-[#dbeafe] text-[#1d4ed8]',
                            'terlambat' => 'bg-[#fecaca] text-[#991b1b]',
                            'selesai'   => 'bg-[#e0e7ff] text-[#3730a3]',
                            default     => 'bg-[#f1f5f9] text-[#64748b]',
                        };
                    @endphp
                    <tr class="hover:bg-[#f8fafc] transition-colors">
                        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9]">{{ $peminjaman->firstItem() + $loop->index }}</td>
                        <td class="p-3 border-b border-[#f1f5f9]">
                            <div class="text-xs font-medium text-[#1e293b]">{{ $p->user->name }}</div>
                            <div class="text-[10px] text-[#94a3b8]">{{ $p->user->jurusan }}</div>
                        </td>
                        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden sm:table-cell">{{ $p->aset->nama }}</td>
                        <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden md:table-cell">{{ $p->tanggal_pinjam->format('d M Y') }}</td>
                        <td class="p-3 text-xs border-b border-[#f1f5f9] hidden md:table-cell {{ $p->isTerlambat() ? 'text-[#ef4444] font-semibold' : 'text-[#475569]' }}">
                            {{ $p->tanggal_kembali_rencana->format('d M Y') }}
                            @if($p->isTerlambat())<div class="text-[10px]">+{{ $p->hari_terlambat }} hari</div>@endif
                        </td>
                        <td class="p-3 border-b border-[#f1f5f9]">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $bc }}">{{ ucfirst($p->status) }}</span>
                        </td>
                        <td class="p-3 border-b border-[#f1f5f9]">
                            <div class="flex gap-1 flex-wrap">
                                <a href="{{ route('admin.peminjaman.show', $p) }}"
                                    class="w-7 h-7 rounded-md bg-[#dbeafe] text-[#3b82f6] hover:bg-[#3b82f6] hover:text-white transition-all flex items-center justify-center text-xs" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($p->status === 'menunggu')
                                <form method="POST" action="{{ route('admin.peminjaman.approve', $p) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="w-7 h-7 rounded-md bg-[#d1fae5] text-[#10b981] hover:bg-[#10b981] hover:text-white transition-all flex items-center justify-center text-xs" title="Setujui">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <button onclick="openTolakModal({{ $p->id }})"
                                    class="w-7 h-7 rounded-md bg-[#fee2e2] text-[#ef4444] hover:bg-[#ef4444] hover:text-white transition-all flex items-center justify-center text-xs" title="Tolak">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                                @if(in_array($p->status, ['disetujui','dipinjam','terlambat']))
                                <button onclick="openKembaliModal({{ $p->id }})"
                                    class="w-7 h-7 rounded-md bg-[#e0e7ff] text-[#3730a3] hover:bg-[#3730a3] hover:text-white transition-all flex items-center justify-center text-xs" title="Konfirmasi Kembali">
                                    <i class="fas fa-undo"></i>
                                </button>
                                @endif
                                @if(in_array($p->status, ['selesai','ditolak']))
                                <form method="POST" action="{{ route('admin.peminjaman.destroy', $p) }}" class="inline"
                                    onsubmit="return confirm('Yakin hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-md bg-[#fee2e2] text-[#ef4444] hover:bg-[#ef4444] hover:text-white transition-all flex items-center justify-center text-xs" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-10 text-center text-[#94a3b8] text-sm">
                            <i class="fas fa-inbox text-4xl block mb-3 opacity-20"></i>
                            Belum ada data transaksi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($peminjaman->hasPages())
    <div class="mt-4">{{ $peminjaman->links() }}</div>
    @endif
</div>

{{-- Modal Buat Peminjaman --}}
<div id="addTransaksiModal" class="modal-overlay" onclick="if(event.target===this)closeModal('addTransaksiModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="bg-gradient-to-r from-[#3b82f6] to-[#2563eb] px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-white font-semibold text-base"><i class="fas fa-plus-circle mr-2"></i>Buat Peminjaman</h3>
            <button onclick="closeModal('addTransaksiModal')" class="text-white/80 hover:text-white text-xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.peminjaman.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="field-label">Peminjam</label>
                <select name="user_id" class="field-input" required>
                    <option value="">-- Pilih User --</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->jurusan ?? 'Staff' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-label">Aset</label>
                <select name="aset_id" class="field-input" required>
                    <option value="">-- Pilih Aset --</option>
                    @foreach($asets as $a)
                    <option value="{{ $a->id }}">{{ $a->nama }} (Stok: {{ $a->stok_tersedia }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="field-label">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_pinjam" class="field-input" value="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="field-label">Tanggal Kembali</label>
                    <input type="date" name="tanggal_kembali_rencana" class="field-input" required>
                </div>
            </div>
            <div>
                <label class="field-label">Keperluan</label>
                <textarea name="keperluan" rows="2" placeholder="Tujuan peminjaman..." class="field-input resize-none"></textarea>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="submit" class="flex-1 py-2.5 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white rounded-lg text-sm font-semibold">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
                <button type="button" onclick="closeModal('addTransaksiModal')" class="px-4 py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tolak --}}
<div id="tolakModal" class="modal-overlay" onclick="if(event.target===this)closeModal('tolakModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4">
        <div class="bg-gradient-to-r from-[#ef4444] to-[#dc2626] px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-white font-semibold text-base"><i class="fas fa-times-circle mr-2"></i>Tolak Pengajuan</h3>
            <button onclick="closeModal('tolakModal')" class="text-white/80 hover:text-white text-xl">&times;</button>
        </div>
        <form id="tolakForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="field-label">Alasan Penolakan (Opsional)</label>
                <textarea name="catatan_admin" rows="3" placeholder="Contoh: Aset sedang dalam perbaikan..."
                    class="field-input resize-none"></textarea>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-[#ef4444] text-white rounded-lg text-sm font-semibold hover:bg-[#dc2626]">Ya, Tolak</button>
                <button type="button" onclick="closeModal('tolakModal')" class="flex-1 py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Konfirmasi Kembali --}}
<div id="kembaliModal" class="modal-overlay" onclick="if(event.target===this)closeModal('kembaliModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4">
        <div class="bg-gradient-to-r from-[#10b981] to-[#059669] px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-white font-semibold text-base"><i class="fas fa-undo mr-2"></i>Konfirmasi Pengembalian</h3>
            <button onclick="closeModal('kembaliModal')" class="text-white/80 hover:text-white text-xl">&times;</button>
        </div>
        <form id="kembaliForm" method="POST" class="p-6 space-y-4">
            @csrf
            <p class="text-sm text-[#64748b]">Konfirmasi aset telah dikembalikan. Jika terlambat, denda akan otomatis dibuat.</p>
            <div>
                <label class="field-label">Kondisi Saat Kembali</label>
                <select name="catatan_kondisi" class="field-input">
                    <option value="Baik — tidak ada kerusakan">Baik — Tidak ada kerusakan</option>
                    <option value="Cukup — ada sedikit kerusakan">Cukup — Ada sedikit kerusakan</option>
                    <option value="Rusak — perlu perbaikan">Rusak — Perlu perbaikan</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-gradient-to-r from-[#10b981] to-[#059669] text-white rounded-lg text-sm font-semibold">
                    <i class="fas fa-check mr-1"></i> Konfirmasi
                </button>
                <button type="button" onclick="closeModal('kembaliModal')" class="flex-1 py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold">Batal</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openTolakModal(id) {
        document.getElementById('tolakForm').action = '{{ url("admin/transaksi") }}/' + id + '/reject';
        openModal('tolakModal');
    }
    function openKembaliModal(id) {
        document.getElementById('kembaliForm').action = '{{ url("admin/transaksi") }}/' + id + '/kembali';
        openModal('kembaliModal');
    }
</script>
@endpush
