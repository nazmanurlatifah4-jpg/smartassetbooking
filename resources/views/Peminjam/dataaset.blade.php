@extends('layouts.peminjam')
@section('title', 'Data Aset')
@section('page-subtitle', 'Data Aset')
@section('show-cart') {{-- aktifkan cart icon di navbar --}} @endsection

@push('styles')
<style>
    .cat-btn {
        padding: 8px 14px; background: white; border: 2px solid #e2e8f0;
        border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 12px;
        transition: all 0.3s; color: #475569; white-space: nowrap;
    }
    @media (min-width: 640px) { .cat-btn { padding: 9px 16px; font-size: 13px; } }
    .cat-btn:hover { border-color: #3b82f6; color: #3b82f6; }
    .cat-btn.active { background: #3b82f6; color: white; border-color: #3b82f6; }

    .cart-badge {
        position: absolute; top: -8px; right: -8px;
        background: #94b910; color: white; font-size: 11px;
        font-weight: 700; padding: 2px 6px; border-radius: 50%;
        min-width: 20px; text-align: center;
    }
    .img-container { aspect-ratio: 4/3; width: 100%; overflow: hidden; background-color: #f1f5f9; }
    .img-container img { width: 100%; height: 100%; object-fit: cover; object-position: center; }
    @media (max-width: 640px) { .img-container { aspect-ratio: 16/9; } }

    .cart-float { position: fixed; bottom: 30px; right: 30px; z-index: 150; display: none; }
    .cart-float.show { display: block; }
    .cart-float-btn {
        background: linear-gradient(135deg, #10b981, #059669); color: white; border: none;
        padding: 14px 22px; border-radius: 50px; cursor: pointer; font-size: 15px;
        font-weight: 700; display: flex; align-items: center; gap: 10px;
        box-shadow: 0 6px 20px rgba(16,185,129,.4); transition: all 0.3s;
    }
    @media (max-width: 640px) { .cart-float { bottom: 15px; right: 15px; } .cart-float-btn { padding: 12px 18px; font-size: 14px; } }

    .modal-box { background: white; border-radius: 20px; max-width: 480px; width: 100%; max-height: 90vh; overflow-y: auto; }
    .modal-img { width: 100%; height: 210px; object-fit: cover; border-radius: 20px 20px 0 0; }
    @media (max-width: 640px) { .modal-img { height: 180px; } }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4 sm:mb-6">
    <div>
        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-[#1e293b] mb-1">Data Aset Tersedia</h2>
        <div class="flex gap-2 items-center text-xs sm:text-sm text-[#64748b]">
            <a href="{{ route('peminjam.dashboard') }}" class="text-[#3b82f6] hover:underline">Dashboard</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span>Data Aset</span>
        </div>
    </div>
</div>

{{-- Search & Filter --}}
<div class="mb-4 sm:mb-6 space-y-3 sm:space-y-4">
    <div class="relative">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" id="searchInput" placeholder="Cari nama aset..." oninput="applyFilter()"
            class="w-full pl-10 pr-4 py-2.5 sm:py-3 border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
    </div>
    <div class="flex flex-nowrap sm:flex-wrap gap-2 overflow-x-auto pb-1 sm:pb-0 -mx-1 px-1 sm:mx-0 sm:px-0">
        <button class="cat-btn flex-shrink-0 active" onclick="filterCat('all',this)">Semua</button>
        @foreach($kategoris as $kat)
        <button class="cat-btn flex-shrink-0" onclick="filterCat('{{ $kat }}',this)">{{ $kat }}</button>
        @endforeach
    </div>
</div>

{{-- Not Found --}}
<div class="hidden text-center py-12 bg-white rounded-xl border border-gray-200 mb-6" id="notFound">
    <i class="fas fa-search text-5xl text-gray-300 mb-3"></i>
    <p class="text-gray-500" id="notFoundMsg">Data tidak ditemukan</p>
</div>

{{-- Aset Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 md:gap-5" id="asetGrid">
    @forelse($asets as $aset)
    @php
        $tersedia = $aset->stokTersedia();
        $habis    = $tersedia <= 0;
    @endphp
    <div class="bg-white rounded-xl overflow-hidden border border-gray-200 hover:-translate-y-1 hover:shadow-lg transition-all flex flex-col"
         data-cat="{{ $aset->kategori }}"
         data-name="{{ $aset->nama_aset }}"
         data-stok="{{ $tersedia }}"
         data-kondisi="{{ $aset->kondisi }}"
         data-desk="{{ $aset->deskripsi ?? '-' }}"
         data-id="{{ $aset->id }}"
         data-kode="{{ $aset->kode_aset }}">

        <div class="img-container">
            @if($aset->foto)
            <img src="{{ asset('storage/' . $aset->foto) }}" alt="{{ $aset->nama_aset }}" class="w-full h-full object-cover">
            @else
            <div class="w-full h-full flex items-center justify-center bg-[#e2e8f0]">
                <i class="fas fa-box text-4xl text-[#94a3b8]"></i>
            </div>
            @endif
        </div>

        <div class="p-3 sm:p-4 flex flex-col flex-1">
            <h3 class="font-bold text-sm sm:text-base mb-2 line-clamp-1">{{ $aset->nama_aset }}</h3>
            <div class="space-y-1 mb-2 flex-1">
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-tag w-4"></i><span>{{ $aset->kategori }}</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-check-circle text-green-500 w-4"></i>
                    <span>Kondisi: {{ $aset->kondisi }}</span>
                </div>
            </div>

            @if($habis)
            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 mb-3 w-fit">Stok Habis</span>
            @else
            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 mb-3 w-fit">Stok: {{ $tersedia }}</span>
            @endif

            <button class="w-full py-2 bg-gray-100 text-gray-700 rounded-lg text-xs sm:text-sm font-semibold hover:bg-gray-200 transition-all flex items-center justify-center gap-1 mb-2" onclick="openDetail(this)">
                <i class="fas fa-eye"></i> Detail
            </button>

            @if($habis)
            <button class="w-full py-2 bg-gray-300 text-gray-600 rounded-lg text-xs sm:text-sm font-semibold cursor-not-allowed flex items-center justify-center gap-1" disabled>
                <i class="fas fa-ban"></i> Habis
            </button>
            @else
            <div class="flex gap-2">
                <button class="flex-1 py-2 bg-blue-500 text-white rounded-lg text-xs sm:text-sm font-semibold hover:bg-blue-600 hover:-translate-y-0.5 hover:shadow-md transition-all flex items-center justify-center gap-1"
                    onclick="pinjamSekarang({{ $aset->id }}, '{{ addslashes($aset->nama_aset) }}', {{ $tersedia }}, this)">
                    <i class="fas fa-bolt"></i> Sekarang
                </button>
                <button class="flex-1 py-2 bg-green-500 text-white rounded-lg text-xs sm:text-sm font-semibold hover:bg-green-600 hover:-translate-y-0.5 hover:shadow-md transition-all flex items-center justify-center gap-1"
                    id="btn-{{ $aset->id }}"
                    onclick="addToCart('{{ $aset->id }}', '{{ $aset->nama_aset }}', {{ $tersedia }}, '{{ $aset->foto }}', this)">
                    <i class="fas fa-cart-plus"></i> +Keranjang
                </button>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12 bg-white rounded-xl border border-gray-200">
        <i class="fas fa-box-open text-5xl text-gray-300 mb-3 block"></i>
        <p class="text-gray-500">Belum ada aset tersedia</p>
    </div>
    @endforelse
</div>

{{-- Floating Cart --}}
<div class="cart-float" id="cartFloat">
    <button class="cart-float-btn" onclick="goToKeranjang()">
        <i class="fas fa-shopping-cart"></i>
        Lihat Keranjang (<span id="cartFloatCount">0</span>)
    </button>
</div>

{{-- Modal Detail Aset --}}
<div class="modal-overlay" id="modalDetail" onclick="if(event.target===this)closeDetail()">
    <div class="modal-box">
        <img id="detailImg" src="" class="modal-img" onerror="this.style.display='none'">
        <div class="p-4 sm:p-6">
            <div class="text-lg sm:text-xl font-bold text-[#1e293b] mb-3" id="detailNama"></div>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Kategori</span>
                    <span class="text-sm font-semibold text-gray-800" id="detailKat"></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Kondisi</span>
                    <span class="text-sm font-semibold text-gray-800" id="detailKondisi"></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Stok Tersedia</span>
                    <span class="text-sm font-semibold text-gray-800" id="detailStok"></span>
                </div>
                <div class="flex justify-between items-start py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Deskripsi</span>
                    <span class="text-sm font-semibold text-gray-800 text-right max-w-[60%]" id="detailDesk"></span>
                </div>
            </div>
            <div class="flex gap-2 sm:gap-3 mt-5 sm:mt-6">
                <button class="flex-1 px-3 py-2 sm:px-4 sm:py-2.5 bg-gray-200 text-gray-700 rounded-lg text-xs sm:text-sm font-semibold hover:bg-gray-300 transition-all" onclick="closeDetail()">
                    <i class="fas fa-times"></i> Tutup
                </button>
                <button class="flex-1 px-3 py-2 sm:px-4 sm:py-2.5 bg-blue-500 text-white rounded-lg text-xs sm:text-sm font-semibold hover:bg-blue-600 hover:-translate-y-0.5 hover:shadow-md transition-all flex items-center justify-center gap-2"
                    id="detailPinjamBtn" onclick="addFromDetail()">
                    <i class="fas fa-cart-plus"></i> Tambah
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let cart = JSON.parse(localStorage.getItem('nexora_cart')) || [];
    let activeCat = 'all';
    let currentDetailCard = null;

    function updateCartUI() {
        const n = cart.length;
        document.getElementById('cartCount').textContent = n;
        document.getElementById('cartFloatCount').textContent = n;
        document.getElementById('cartFloat').classList.toggle('show', n > 0);
    }

    function pinjamSekarang(id, name, stok, btn) {
        if (stok === 0) { showToast('Stok habis!', 'error'); return; }
        showToast('Mengarahkan ke halaman peminjaman...', 'success');
        setTimeout(() => { location.href = '{{ route("peminjam.peminjaman") }}?aset=' + id; }, 1000);
    }

    function addToCart(id, name, stok, foto, btn) {
        if (stok === 0) { showToast('Stok habis!', 'error'); return; }
        if (cart.find(i => i.id === id)) { showToast('Sudah ada di keranjang!', 'error'); return; }
        cart.push({ id, name, stok, foto, jumlah: 1 });
        localStorage.setItem('nexora_cart', JSON.stringify(cart));
        updateCartUI();
        btn.innerHTML = '<i class="fas fa-check"></i> +Keranjang';
        btn.classList.remove('bg-green-500', 'hover:bg-green-600');
        btn.classList.add('bg-green-600', 'hover:bg-green-700');
        btn.disabled = true;
        showToast(name + ' ditambahkan ke keranjang!', 'success');
    }

    function applyFilter() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        let visible = 0;
        document.querySelectorAll('[data-cat]').forEach(card => {
            const matchCat  = activeCat === 'all' || card.dataset.cat === activeCat;
            const matchName = card.dataset.name.toLowerCase().includes(q);
            const show      = matchCat && matchName;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        const nf = document.getElementById('notFound');
        nf.classList.toggle('hidden', visible > 0);
        document.getElementById('notFoundMsg').textContent = q ? `Aset "${q}" tidak ditemukan` : 'Data tidak ditemukan';
    }

    function filterCat(cat, btn) {
        activeCat = cat;
        document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        applyFilter();
    }

    function openDetail(btn) {
        currentDetailCard = btn;
        const card = btn.closest('[data-cat]');
        const img  = card.querySelector('img');
        document.getElementById('detailImg').src = img ? img.src : '';
        document.getElementById('detailNama').textContent    = card.dataset.name;
        document.getElementById('detailKat').textContent     = card.dataset.cat;
        document.getElementById('detailKondisi').textContent = card.dataset.kondisi;
        const stok = parseInt(card.dataset.stok);
        document.getElementById('detailStok').textContent = stok > 0 ? stok + ' unit' : 'Habis';
        document.getElementById('detailDesk').textContent = card.dataset.desk;
        const pBtn      = document.getElementById('detailPinjamBtn');
        const cartBtn   = document.getElementById('btn-' + card.dataset.id);
        if (stok === 0) {
            pBtn.innerHTML = '<i class="fas fa-ban"></i> Stok Habis'; pBtn.disabled = true;
        } else if (cartBtn && cartBtn.disabled) {
            pBtn.innerHTML = '<i class="fas fa-check"></i> Di Keranjang'; pBtn.disabled = true;
        } else {
            pBtn.innerHTML = '<i class="fas fa-cart-plus"></i> Tambah'; pBtn.disabled = false;
        }
        openModal('modalDetail');
    }

    function addFromDetail() {
        if (!currentDetailCard) return;
        const card    = currentDetailCard.closest('[data-cat]');
        const cartBtn = document.getElementById('btn-' + card.dataset.id);
        if (cartBtn && !cartBtn.disabled) cartBtn.click();
        closeDetail();
    }

    function closeDetail() { closeModal('modalDetail'); }

    // Restore cart state
    cart.forEach(item => {
        const btn = document.getElementById('btn-' + item.id);
        if (btn) {
            btn.innerHTML = '<i class="fas fa-check"></i> +Keranjang';
            btn.classList.remove('bg-green-500','hover:bg-green-600');
            btn.classList.add('bg-green-600','hover:bg-green-700');
            btn.disabled = true;
        }
    });
    updateCartUI();
</script>
@endpush
