@extends('layouts.peminjam')
@section('title', 'Transaksi Peminjaman')
@section('page-subtitle', 'Transaksi Peminjaman')

@push('styles')
<style>
    .table-wrapper { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-wrapper::-webkit-scrollbar { height: 6px; }
    .table-wrapper::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .table-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .qty-input {
        width: 60px; text-align: center; border: 2px solid #e2e8f0;
        border-radius: 6px; padding: 4px 6px; font-size: 14px; outline: none;
    }
    .qty-input:focus { border-color: #3b82f6; }
</style>
@endpush

@section('content')

<div class="mb-4 sm:mb-6">
    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-[#1e293b] mb-1">Transaksi Peminjaman</h2>
    <div class="flex gap-2 items-center text-xs sm:text-sm text-[#64748b]">
        <a href="{{ route('peminjam.dashboard') }}" class="text-[#3b82f6] hover:underline">Dashboard</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <span>Transaksi Peminjaman</span>
    </div>
</div>

{{-- Form Section --}}
<div id="formSection">
    {{-- Info Peminjam --}}
    <div class="bg-white rounded-xl p-4 sm:p-5 md:p-6 shadow-sm border border-gray-200 mb-6">
        <h3 class="text-base md:text-lg font-semibold text-[#1e293b] mb-4 flex items-center gap-2">
            <i class="fas fa-user-circle text-blue-500"></i> Informasi Peminjam
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-[#64748b] mb-1 uppercase tracking-wider">Nama</label>
                {{-- kolom 'nama' sesuai migration --}}
                <p class="text-sm font-medium text-[#1e293b] bg-[#f8fafc] px-3 py-2 rounded-lg border border-[#e2e8f0]">{{ auth()->user()->nama }}</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#64748b] mb-1 uppercase tracking-wider">Kelas / Jurusan</label>
                {{-- kolom 'kelas', 'jurusan' --}}
                <p class="text-sm font-medium text-[#1e293b] bg-[#f8fafc] px-3 py-2 rounded-lg border border-[#e2e8f0]">{{ auth()->user()->kelas ?? '-' }} / {{ auth()->user()->jurusan ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Form Detail Peminjaman --}}
    <div class="bg-white rounded-xl p-4 sm:p-5 md:p-6 shadow-sm border border-gray-200 mb-6" id="formDetail">
        <h3 class="text-base md:text-lg font-semibold text-[#1e293b] mb-4 flex items-center gap-2">
            <i class="fas fa-clipboard-list text-blue-500"></i> Detail Peminjaman
        </h3>
        <div id="cartItemsContainer">
            {{-- Diisi oleh JS dari localStorage --}}
            <p class="text-sm text-[#94a3b8] text-center py-6" id="emptyCartMsg">
                <i class="fas fa-shopping-cart text-3xl block mb-2 opacity-30"></i>
                Keranjang kosong. <a href="{{ route('peminjam.aset') }}" class="text-[#3b82f6] hover:underline">Pilih aset terlebih dahulu.</a>
            </p>
        </div>

        {{-- Field Tanggal & Tujuan (muncul setelah ada item keranjang) --}}
        <div id="dateFields" class="hidden mt-4 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#64748b] mb-1 uppercase tracking-wider">Tanggal Pinjam</label>
                    <input type="date" id="tglPinjam" oninput="validateDurasi()"
                        class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                        min="{{ date('Y-m-d') }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#64748b] mb-1 uppercase tracking-wider">Tanggal Kembali</label>
                    <input type="date" id="tglKembali" oninput="validateDurasi()"
                        class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <p id="durasiInfo" class="text-xs text-[#64748b] mt-1"></p>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#64748b] mb-1 uppercase tracking-wider">Tujuan Peminjaman</label>
                <input type="text" id="tujuan" placeholder="Contoh: Presentasi kelas XII TKJ"
                    class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
        </div>
    </div>

    {{-- Buttons --}}
    <div id="actionButtons" class="hidden flex flex-col sm:flex-row gap-3 mb-6">
        <a href="{{ route('peminjam.aset') }}"
            class="flex-1 py-3 bg-white border-2 border-[#3b82f6] text-[#3b82f6] rounded-xl font-semibold text-sm flex items-center justify-center gap-2 hover:bg-[#eff6ff] transition-all">
            <i class="fas fa-plus"></i> Tambah Aset Lagi
        </a>
        <button onclick="submitPeminjaman()"
            class="flex-1 py-3 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white rounded-xl font-semibold text-sm flex items-center justify-center gap-2 hover:-translate-y-0.5 hover:shadow-lg transition-all">
            <i class="fas fa-paper-plane"></i> Kirim ke Admin
        </button>
    </div>
</div>

{{-- Table Section (setelah submit) --}}
<div id="tableSection" class="hidden">
    <div class="bg-white rounded-xl p-4 sm:p-5 md:p-6 shadow-sm border border-gray-200 mb-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <div>
                <h3 class="text-base font-semibold text-[#1e293b]">Pengajuan Berhasil Dikirim!</h3>
                <p class="text-xs text-[#64748b]">Admin akan memproses dalam 1–2 hari kerja</p>
            </div>
        </div>
        <div class="table-wrapper">
            <table class="min-w-full border-collapse text-sm">
                <thead class="bg-[#f8fafc]">
                    <tr>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap">No</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap">Nama Aset</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap hidden sm:table-cell">Jumlah</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap hidden md:table-cell">Tgl Pinjam</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap hidden md:table-cell">Tgl Kembali</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap hidden lg:table-cell">Tujuan</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 border-b-2 border-gray-200 whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody id="resultBody"></tbody>
            </table>
        </div>
    </div>
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('peminjam.aset') }}"
            class="flex-1 py-3 bg-white border-2 border-[#3b82f6] text-[#3b82f6] rounded-xl font-semibold text-sm flex items-center justify-center gap-2 hover:bg-[#eff6ff] transition-all">
            <i class="fas fa-plus"></i> Ajukan Lagi
        </a>
        <a href="{{ route('peminjam.riwayat') }}"
            class="flex-1 py-3 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white rounded-xl font-semibold text-sm flex items-center justify-center gap-2 hover:-translate-y-0.5 hover:shadow-lg transition-all">
            <i class="fas fa-history"></i> Lihat Riwayat
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let cart = JSON.parse(localStorage.getItem('nexora_cart')) || [];

    function formatTgl(str) {
        if (!str) return '-';
        const d = new Date(str);
        return d.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
    }

    function renderCart() {
        const container = document.getElementById('cartItemsContainer');
        const emptyMsg  = document.getElementById('emptyCartMsg');
        const dateFields= document.getElementById('dateFields');
        const actionBtn = document.getElementById('actionButtons');

        // Pre-fill aset dari query param ?aset=id
        const params = new URLSearchParams(location.search);
        const asetId = params.get('aset');
        if (asetId && !cart.find(i => i.id == asetId)) {
            // cari dari aset grid data yang di-pass blade (via JSON)
            const asetData = window.asetData?.find(a => a.id == asetId);
            if (asetData) cart.push({ id: asetData.id, name: asetData.nama_aset, stok: asetData.stok, jumlah: 1 });
            localStorage.setItem('nexora_cart', JSON.stringify(cart));
        }

        if (cart.length === 0) {
            emptyMsg.classList.remove('hidden');
            dateFields.classList.add('hidden');
            actionBtn.classList.add('hidden');
            return;
        }
        emptyMsg.classList.add('hidden');
        dateFields.classList.remove('hidden');
        actionBtn.classList.remove('hidden');

        let html = '';
        cart.forEach((item, idx) => {
            html += `
            <div class="flex items-center gap-3 p-3 bg-[#f8fafc] rounded-xl mb-2 border border-[#e2e8f0]">
                <div class="w-10 h-10 rounded-xl bg-[#dbeafe] flex items-center justify-center text-[#3b82f6] flex-shrink-0">
                    <i class="fas fa-box"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-[#1e293b] truncate">${item.name}</p>
                    <p class="text-xs text-[#64748b]">Stok tersedia: ${item.stok}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="changeQty(${idx}, -1)" class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold">-</button>
                    <input type="number" class="qty-input" value="${item.jumlah}" min="1" max="${item.stok}" onchange="setQty(${idx}, this.value)">
                    <button onclick="changeQty(${idx}, 1)" class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold">+</button>
                </div>
                <button onclick="removeFromCart(${idx})" class="w-7 h-7 rounded-full bg-red-100 hover:bg-red-200 text-red-500 flex items-center justify-center text-xs ml-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>`;
        });
        container.innerHTML = html;
    }

    function changeQty(idx, delta) {
        cart[idx].jumlah = Math.max(1, Math.min(cart[idx].stok, cart[idx].jumlah + delta));
        localStorage.setItem('nexora_cart', JSON.stringify(cart));
        renderCart();
    }
    function setQty(idx, val) {
        cart[idx].jumlah = Math.max(1, Math.min(cart[idx].stok, parseInt(val) || 1));
        localStorage.setItem('nexora_cart', JSON.stringify(cart));
    }
    function removeFromCart(idx) {
        cart.splice(idx, 1);
        localStorage.setItem('nexora_cart', JSON.stringify(cart));
        renderCart();
    }
    function validateDurasi() {
        const p = document.getElementById('tglPinjam').value;
        const k = document.getElementById('tglKembali').value;
        const info = document.getElementById('durasiInfo');
        if (p && k) {
            const diff = Math.round((new Date(k) - new Date(p)) / 86400000);
            if (diff < 0) { info.textContent = '⚠ Tanggal kembali tidak valid'; info.className = 'text-xs text-red-500 mt-1'; }
            else if (diff > 7) { info.textContent = '⚠ Maksimal 7 hari peminjaman'; info.className = 'text-xs text-red-500 mt-1'; }
            else { info.textContent = `✓ Durasi: ${diff} hari`; info.className = 'text-xs text-green-600 mt-1'; }
        }
    }

    function submitPeminjaman() {
        const tglP   = document.getElementById('tglPinjam').value;
        const tglK   = document.getElementById('tglKembali').value;
        const tujuan = document.getElementById('tujuan').value.trim();

        if (!tglP || !tglK)   { showToast('Isi tanggal pinjam dan kembali!', 'error'); return; }
        if (!tujuan)           { showToast('Isi tujuan peminjaman!', 'error'); return; }
        if (cart.length === 0) { showToast('Keranjang kosong!', 'error'); return; }

        const diff = Math.round((new Date(tglK) - new Date(tglP)) / 86400000);
        if (diff < 0 || diff > 7) { showToast('Durasi peminjaman harus 1–7 hari!', 'error'); return; }

        // Submit ke server via POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("peminjam.peminjaman.store") }}';
        form.innerHTML = `<input name="_token" value="{{ csrf_token() }}">
            <input name="tanggal_pengajuan" value="${tglP}">
            <input name="tanggal_kembali" value="${tglK}">
            <input name="keperluan" value="${tujuan}">
            <input name="cart" value='${JSON.stringify(cart)}'>`;
        document.body.appendChild(form);
        form.submit();
    }

    // Jalankan saat load
    renderCart();
</script>
@endpush
