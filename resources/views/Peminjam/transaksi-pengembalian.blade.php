@extends('layouts.peminjam')
@section('title', 'Transaksi Pengembalian')
@section('page-subtitle', 'Transaksi Pengembalian')

@push('styles')
<style>
    .table-wrapper { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-wrapper::-webkit-scrollbar { height: 6px; }
    .table-wrapper::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .table-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .upload-area {
        border: 2px dashed #e2e8f0; border-radius: 12px; padding: 24px;
        text-align: center; transition: all 0.3s; cursor: pointer;
    }
    .upload-area:hover { border-color: #3b82f6; background: #eff6ff; }
    .upload-area.has-file { border-color: #10b981; background: #f0fdf4; }
</style>
@endpush

@section('content')

<div class="mb-4 sm:mb-6">
    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-[#1e293b] mb-1">Transaksi Pengembalian</h2>
    <div class="flex gap-2 items-center text-xs sm:text-sm text-[#64748b]">
        <a href="{{ route('peminjam.dashboard') }}" class="text-[#3b82f6] hover:underline">Dashboard</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <span>Transaksi Pengembalian</span>
    </div>
</div>

{{-- Form Section --}}
<div id="formSection">
    <form onsubmit="event.preventDefault(); submitPengembalian();" class="space-y-5" enctype="multipart/form-data" id="returnForm">
        @csrf
        {{-- Pilih Aset --}}
        <div class="bg-white rounded-xl p-4 sm:p-5 md:p-6 shadow-sm border border-gray-200">
            <h3 class="text-base md:text-lg font-semibold text-[#1e293b] mb-4 flex items-center gap-2">
                <i class="fas fa-box text-blue-500"></i> Aset yang Dikembalikan
            </h3>

            @if($peminjamansAktif->isEmpty())
            <div class="text-center py-8 text-[#94a3b8]">
                <i class="fas fa-inbox text-4xl block mb-2 opacity-30"></i>
                <p class="text-sm">Tidak ada peminjaman aktif untuk dikembalikan.</p>
                <a href="{{ route('peminjam.aset') }}" class="mt-2 inline-block text-sm text-[#3b82f6] hover:underline">Pinjam aset sekarang →</a>
            </div>
            @else
            <div>
                <label class="block text-xs font-semibold text-[#64748b] mb-1.5 uppercase tracking-wider">Pilih Peminjaman</label>
                {{-- Select dari peminjamans yang aktif milik user ini --}}
                <select id="asetPinjam" name="peminjaman_id" required
                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">-- Pilih aset yang akan dikembalikan --</option>
                    @foreach($peminjamansAktif as $p)
                    <option value="{{ $p->id }}">
                        {{ $p->aset->nama_aset }} ({{ $p->aset->kode_aset }}) — Kembali: {{ $p->tanggal_kembali->format('d M Y') }}
                        @if($p->isTerlambat()) ⚠ TERLAMBAT {{ $p->hari_terlambat }} hari @endif
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>

        @if(!$peminjamansAktif->isEmpty())
        {{-- Detail Pengembalian --}}
        <div class="bg-white rounded-xl p-4 sm:p-5 md:p-6 shadow-sm border border-gray-200">
            <h3 class="text-base md:text-lg font-semibold text-[#1e293b] mb-4 flex items-center gap-2">
                <i class="fas fa-clipboard-check text-blue-500"></i> Detail Pengembalian
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-[#64748b] mb-1.5 uppercase tracking-wider">Jumlah yang Dikembalikan</label>
                    <input type="number" id="jumlah" name="jumlah" min="1" value="1" required
                        class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#64748b] mb-1.5 uppercase tracking-wider">Kondisi Aset Saat Dikembalikan</label>
                    {{-- enum kondisi_barang: Baik, Rusak Ringan, Rusak Berat, Hilang --}}
                    <select id="kondisi" name="kondisi_barang" required
                        class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <option value="">-- Pilih kondisi --</option>
                        <option value="Baik">Baik — Tidak ada kerusakan</option>
                        <option value="Rusak Ringan">Rusak Ringan — Masih bisa digunakan</option>
                        <option value="Rusak Berat">Rusak Berat — Perlu perbaikan</option>
                        <option value="Hilang">Hilang — Tidak ada</option>
                    </select>
                </div>

                {{-- Upload Foto Wajib --}}
                <div>
                    <label class="block text-xs font-semibold text-[#64748b] mb-1.5 uppercase tracking-wider">
                        Foto Bukti Kondisi <span class="text-red-500">*Wajib</span>
                    </label>
                    <div class="upload-area" id="uploadArea" onclick="document.getElementById('fotoInput').click()">
                        <i class="fas fa-camera text-3xl text-[#94a3b8] mb-2 block"></i>
                        <p class="text-sm text-[#64748b]">Klik untuk ambil/upload foto kondisi aset</p>
                        <p class="text-xs text-[#94a3b8] mt-1">JPG, PNG, WebP maks. 5MB</p>
                    </div>
                    <input type="file" id="fotoInput" name="foto" accept="image/*" capture="environment" class="hidden" onchange="onFotoChange(this)">
                    <div id="previewContainer" class="hidden mt-2">
                        <img id="fotoPreview" class="w-full max-h-48 object-cover rounded-xl border border-[#e2e8f0]">
                        <button type="button" onclick="resetFoto()" class="mt-1 text-xs text-red-500 hover:underline">
                            <i class="fas fa-times mr-1"></i>Ganti foto
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#64748b] mb-1.5 uppercase tracking-wider">Catatan (Opsional)</label>
                    <textarea name="catatan_admin" rows="2" placeholder="Tambahkan catatan jika ada kerusakan atau keterangan lain..."
                        class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 resize-none"></textarea>
                </div>
            </div>
        </div>

        {{-- Submit Buttons --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('peminjam.dashboard') }}"
                class="flex-1 py-3 bg-white border-2 border-gray-200 text-gray-600 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 hover:bg-gray-50 transition-all">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button type="submit"
                class="flex-1 py-3 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white rounded-xl font-semibold text-sm flex items-center justify-center gap-2 hover:-translate-y-0.5 hover:shadow-lg transition-all">
                <i class="fas fa-paper-plane"></i> Kirim ke Admin
            </button>
        </div>
        @endif
    </form>
</div>

{{-- Table Section (setelah submit berhasil) --}}
<div id="tableSection" class="hidden">
    <div class="bg-white rounded-xl p-4 sm:p-5 md:p-6 shadow-sm border border-gray-200 mb-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <div>
                <h3 class="text-base font-semibold text-[#1e293b]">Pengembalian Berhasil Dikirim!</h3>
                <p class="text-xs text-[#64748b]">Admin akan memverifikasi pengembalian kamu</p>
            </div>
        </div>
        <div class="table-wrapper">
            <table class="min-w-full border-collapse text-sm">
                <thead class="bg-[#f8fafc]">
                    <tr>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap">No</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap">Nama Aset</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap hidden sm:table-cell">Jumlah</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap hidden md:table-cell">Kondisi</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
        </div>
    </div>
    <div class="flex flex-col sm:flex-row gap-3">
        <button onclick="resetForm()"
            class="flex-1 py-3 bg-white border-2 border-[#3b82f6] text-[#3b82f6] rounded-xl font-semibold text-sm flex items-center justify-center gap-2 hover:bg-[#eff6ff] transition-all">
            <i class="fas fa-plus"></i> Kembalikan Lagi
        </button>
        <a href="{{ route('peminjam.riwayat') }}"
            class="flex-1 py-3 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white rounded-xl font-semibold text-sm flex items-center justify-center gap-2 hover:-translate-y-0.5 hover:shadow-lg transition-all">
            <i class="fas fa-history"></i> Lihat Riwayat
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function onFotoChange(input) {
        if (!input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('fotoPreview').src = e.target.result;
            document.getElementById('previewContainer').classList.remove('hidden');
            document.getElementById('uploadArea').classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
    function resetFoto() {
        document.getElementById('fotoInput').value = '';
        document.getElementById('previewContainer').classList.add('hidden');
        document.getElementById('uploadArea').classList.remove('hidden');
        document.getElementById('uploadArea').classList.remove('has-file');
    }

    function submitPengembalian() {
        const peminjaman = document.getElementById('asetPinjam').value;
        const kondisi    = document.getElementById('kondisi').value;
        const foto       = document.getElementById('fotoInput').files[0];

        if (!peminjaman) { showToast('Pilih aset yang akan dikembalikan!', 'error'); return; }
        if (!kondisi)    { showToast('Pilih kondisi aset!', 'error'); return; }
        if (!foto)       { showToast('Upload foto kondisi aset wajib!', 'error'); return; }

        // Submit form langsung dengan file upload
        document.getElementById('returnForm').action = '{{ route("peminjam.pengembalian.store") }}';
        document.getElementById('returnForm').method = 'POST';
        document.getElementById('returnForm').submit();
    }

    function resetForm() {
        document.getElementById('tableSection').classList.add('hidden');
        document.getElementById('formSection').classList.remove('hidden');
        document.getElementById('returnForm').reset();
        resetFoto();
    }
</script>
@endpush
