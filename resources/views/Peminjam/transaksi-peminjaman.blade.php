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
    // 1. Ambil data keranjang dari memori browser
    let cart = JSON.parse(localStorage.getItem('nexora_cart')) || [];

    // Fungsi format tanggal (opsional)
    function formatTgl(str) {
        if (!str) return '-';
        const d = new Date(str);
        return d.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
    }

    // 2. FUNGSI MENAMPILKAN KERANJANG
    function renderCart() {
        const container = document.getElementById('cartItemsContainer');
        const emptyMsg  = document.getElementById('emptyCartMsg');
        const dateFields= document.getElementById('dateFields');
        const actionBtn = document.getElementById('actionButtons');

        if (cart.length === 0) {
            container.innerHTML = '';
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
                <div class="w-12 h-12 rounded-xl bg-[#dbeafe] overflow-hidden flex-shrink-0 border border-[#e2e8f0]">
                    <img src="${item.foto ? '/storage/' + item.foto : 'https://ui-avatars.com/api/?name=' + item.name}" 
                         class="w-full h-full object-cover" 
                         onerror="this.src='https://ui-avatars.com/api/?name=${item.name}&background=dbeafe&color=3b82f6'">
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-[#1e293b] truncate">${item.name}</p>
                    <p class="text-[10px] text-[#64748b]">Stok tersedia: ${item.stok}</p>
                </div>
                
                <div class="flex items-center gap-2">
                    <button type="button" onclick="changeQty(${idx}, -1)" class="w-8 h-8 rounded-lg bg-white border border-[#e2e8f0] flex items-center justify-center text-sm font-bold shadow-sm active:scale-95">-</button>
                    <input type="number" id="qty-input-${idx}" class="w-12 text-center border-none bg-transparent font-bold text-sm" value="${item.jumlah}" readonly>
                    <button type="button" onclick="changeQty(${idx}, 1)" class="w-8 h-8 rounded-lg bg-white border border-[#e2e8f0] flex items-center justify-center text-sm font-bold shadow-sm active:scale-95">+</button>
                </div>

                <button type="button" onclick="removeFromCart(${idx})" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center ml-2">
                    <i class="fas fa-times"></i>
                </button>
            </div>`;
        });
        
        container.innerHTML = html;
    }

    // 3. FUNGSI UBAH JUMLAH (SINKRON KE STOK)
    function changeQty(idx, delta) {
        let currentQty = parseInt(cart[idx].jumlah) || 1;
        let newQty = currentQty + delta;
        
        if (newQty >= 1 && newQty <= cart[idx].stok) {
            cart[idx].jumlah = newQty;
            localStorage.setItem('nexora_cart', JSON.stringify(cart));
            
            // Update angka di layar tanpa reload
            const inputEl = document.getElementById(`qty-input-${idx}`);
            if (inputEl) { inputEl.value = newQty; }
        } else if (newQty > cart[idx].stok) {
            alert('Stok tidak mencukupi, Boss!');
        }
    }

    // 4. FUNGSI HAPUS DARI KERANJANG
    function removeFromCart(idx) {
    if(confirm('Hapus aset ini dari daftar?')) {
        // 1. Hapus item dari array berdasarkan index
        cart.splice(idx, 1);
        
        // 2. Simpan perubahan ke localStorage
        localStorage.setItem('nexora_cart', JSON.stringify(cart));
        
        // 3. JALANKAN ULANG RENDER (Ini yang bikin Real-time)
        renderCart(); 
        
        // 4. Update angka di icon keranjang (Badge)
        if(typeof updateCartBadge === "function") {
            updateCartBadge();
        }

        showToast('Aset berhasil dihapus', 'info');
    }
}

    // 5. FUNGSI KIRIM KE CONTROLLER (PROSES STOK)
    function submitPeminjaman() {
        const tglP   = document.getElementById('tglPinjam').value;
        const tglK   = document.getElementById('tglKembali').value;
        const tujuan = document.getElementById('tujuan').value.trim();

        if (!tglP || !tglK || !tujuan) { 
            alert('Lengkapi tanggal dan tujuan peminjaman dulu ya!'); 
            return; 
        }
        
        if (cart.length === 0) { 
            alert('Keranjangnya masih kosong nih!'); 
            return; 
        }

        // Bikin form bayangan untuk kirim data ke Laravel
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("peminjam.peminjaman.store") }}';
        // Cari bagian ini di script Blade kamu:
form.innerHTML = `
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="tanggal_pengajuan" value="${tglP}">
    <input type="hidden" name="tanggal_kembali" value="${tglK}">
    <input type="hidden" name="keperluan" value="${tujuan}">
    <input type="hidden" name="cart" value='${JSON.stringify(cart)}'>`; 
        document.body.appendChild(form);
        
        // Bersihkan keranjang di browser sebelum pindah halaman
        localStorage.removeItem('nexora_cart');
        
        form.submit();
    }

    // Jalankan render otomatis pas halaman dibuka
    renderCart();
</script>
@endpush
