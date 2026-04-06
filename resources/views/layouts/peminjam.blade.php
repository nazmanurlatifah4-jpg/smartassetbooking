<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Smart Asset Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media (max-width: 768px) {
            .sidebar-mobile { left: -260px; transition: left 0.3s; }
            .sidebar-mobile.show { left: 0; }
        }
        @media (max-width: 1024px) { .right-sidebar-hide { display: none; } }

        .stats-scroll { scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; }
        .stats-scroll > div { scroll-snap-align: start; }
        .stats-scroll::-webkit-scrollbar { display: none; }

        /* Notification Dropdown */
        .notification-dropdown {
            position: absolute; top: 40px; right: -10px; width: 320px;
            background: white; border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            z-index: 1000; display: none; overflow: hidden;
            animation: slideDown 0.3s ease;
        }
        @media (max-width: 640px) { .notification-dropdown { width: 280px; right: -20px; } }
        .notification-dropdown.show { display: block; }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .notification-header { padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .notification-header h3 { font-size: 16px; font-weight: 600; color: #1e293b; }
        .notification-header span { font-size: 12px; color: #3b82f6; cursor: pointer; }
        .notification-list { max-height: 350px; overflow-y: auto; }
        .notification-item { padding: 15px 20px; border-bottom: 1px solid #f1f5f9; transition: background 0.3s; cursor: pointer; }
        .notification-item:hover { background: #f8fafc; }
        .notification-item.unread { background: #eff6ff; }
        .notification-item-title { font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 5px; }
        .notification-item-desc { font-size: 13px; color: #64748b; margin-bottom: 5px; }
        .notification-item-time { font-size: 11px; color: #94a3b8; }
        .notification-footer { padding: 12px 20px; text-align: center; border-top: 1px solid #e2e8f0; }
        .notification-footer a { font-size: 13px; color: #3b82f6; text-decoration: none; }
        .notification-footer a:hover { text-decoration: underline; }

        /* User Dropdown */
        .user-dropdown {
            position: absolute; top: 40px; right: 0; width: 200px;
            background: white; border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            z-index: 1000; display: none; overflow: hidden;
            animation: slideDown 0.3s ease;
        }
        .user-dropdown.show { display: block; }
        .user-dropdown-item { padding: 12px 20px; display: flex; align-items: center; gap: 12px; color: #1e293b; font-size: 14px; transition: background 0.3s; cursor: pointer; }
        .user-dropdown-item:hover { background: #f8fafc; }
        .user-dropdown-item i { width: 16px; color: #64748b; }
        .user-dropdown-divider { height: 1px; background: #e2e8f0; margin: 5px 0; }

        /* Toast */
        .toast {
            position: fixed; bottom: 100px; left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: #1e293b; color: white;
            padding: 13px 22px; border-radius: 10px;
            font-size: 14px; font-weight: 600; z-index: 9999;
            opacity: 0; transition: all 0.35s;
            display: flex; align-items: center; gap: 10px;
            white-space: nowrap; pointer-events: none;
        }
        .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
        .toast.success { background: linear-gradient(135deg, #10b981, #059669); }
        .toast.error   { background: linear-gradient(135deg, #ef4444, #dc2626); }

        /* Table wrapper scroll */
        .table-wrapper { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-wrapper::-webkit-scrollbar { height: 6px; }
        .table-wrapper::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .table-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .table-wrapper::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Modal overlay */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 9999;
            align-items: center; justify-content: center; padding: 20px;
        }
        .modal-overlay.show { display: flex; }
    </style>
    @stack('styles')
</head>
<body class="font-['Segoe_UI',Tahoma,Geneva,Verdana,sans-serif] bg-[#f8fafc] text-[#1e293b]">

{{-- ===== NAVBAR ===== --}}
<nav class="bg-white px-4 sm:px-6 md:px-8 py-3 md:py-4 shadow-[0_2px_10px_rgba(0,0,0,0.05)] flex justify-between items-center sticky top-0 z-50">
    <div onclick="toggleSidebar()" class="flex items-center gap-2 sm:gap-3 cursor-pointer">
        <img src="{{ asset('assets/logo-removebg-preview.png') }}" alt="logo" class="w-8 sm:w-10 h-auto">
        <div>
            <h1 class="text-sm sm:text-base md:text-xl font-semibold text-[#1e293b]">Smart Asset Booking</h1>
            <p class="text-xs sm:text-sm text-[#64748b]">@yield('page-subtitle', 'Dashboard Peminjam')</p>
        </div>
    </div>

    <div class="flex gap-3 sm:gap-4 md:gap-5 items-center relative">
        {{-- Notification Bell --}}
        <div class="relative">
            <div onclick="toggleNotification()" class="cursor-pointer text-[#64748b] hover:text-[#3b82f6] text-lg sm:text-xl relative">
                <i class="fas fa-bell"></i>
                @php $notifCount = auth()->user()->notifikasi()->where('tanda_baca','Belum Dibaca')->count(); @endphp
                @if($notifCount > 0)
                <span id="notifBadge" class="absolute -top-1 -right-1 bg-[#ef4444] text-white text-[10px] px-1.5 py-0.5 rounded-full leading-none">
                    {{ $notifCount > 9 ? '9+' : $notifCount }}
                </span>
                @endif
            </div>
            <div id="notificationDropdown" class="notification-dropdown">
                <div class="notification-header">
                    <h3>Notifikasi</h3>
                    <span onclick="markAllAsRead()">Tandai sudah dibaca</span>
                </div>
                <div class="notification-list">
                    @forelse(auth()->user()->notifikasi()->latest('tanggal_kirim')->limit(5)->get() as $notif)
                    <div class="notification-item {{ $notif->tanda_baca === 'Belum Dibaca' ? 'unread' : '' }}" onclick="markAsRead(this, {{ $notif->id }})">
                        <div class="notification-item-title">{{ $notif->judul }}</div>
                        <div class="notification-item-desc">{{ $notif->pesan }}</div>
                        <div class="notification-item-time">{{ $notif->tanggal_kirim->diffForHumans() }}</div>
                    </div>
                    @empty
                    <div class="notification-item">
                        <div class="notification-item-desc text-center py-4">Tidak ada notifikasi</div>
                    </div>
                    @endforelse
                </div>
                <div class="notification-footer">
                    <a href="#">Lihat semua notifikasi</a>
                </div>
            </div>
        </div>

        {{-- Cart Icon (hanya di halaman data aset) --}}
        @hasSection('show-cart')
        <div class="relative">
            <div onclick="goToKeranjang()" class="cursor-pointer text-[#64748b] hover:text-[#3b82f6] text-lg sm:text-xl relative">
                <i class="fas fa-shopping-cart"></i>
                <span class="absolute -top-1 -right-1 bg-[#94b910] text-white text-[10px] px-1.5 py-0.5 rounded-full leading-none" id="cartCount">0</span>
            </div>
        </div>
        @endif

        {{-- User Icon --}}
        <div class="relative">
            <div onclick="toggleUserDropdown()" class="cursor-pointer text-[#64748b] hover:text-[#3b82f6] text-lg sm:text-xl">
                <i class="fas fa-user-circle"></i>
            </div>
            <div id="userDropdown" class="user-dropdown">
                <div class="px-4 py-3 border-b border-[#e2e8f0]">
                    {{-- kolom 'nama' sesuai migration --}}
                    <p class="text-sm font-semibold text-[#1e293b]">{{ auth()->user()->nama }}</p>
                    <p class="text-xs text-[#64748b]">{{ auth()->user()->kelas ?? auth()->user()->jurusan }}</p>
                </div>
                <div class="user-dropdown-item">
                    <i class="fas fa-user"></i><span>Profil Saya</span>
                </div>
                <div class="user-dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="user-dropdown-item w-full text-left">
                        <i class="fas fa-sign-out-alt"></i><span style="color:#ef4444">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

{{-- Overlay --}}
<div id="overlay" class="hidden fixed inset-0 bg-black/50 z-40" onclick="toggleSidebar()"></div>

{{-- ===== LAYOUT ===== --}}
<div class="flex min-h-[calc(100vh-70px)]">

    {{-- ===== SIDEBAR ===== --}}
    <aside id="sidebar" class="sidebar-mobile fixed md:sticky top-0 md:top-[73px] left-[-260px] md:left-0 w-[260px] h-screen md:h-[calc(100vh-73px)] bg-gradient-to-b from-[#3b82f6] to-[#2563eb] text-white py-6 md:py-8 overflow-y-auto z-50 transition-all duration-300">
        <div class="md:hidden flex justify-end px-4 mb-4">
            <button onclick="toggleSidebar()" class="text-white/80 hover:text-white text-xl">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <ul class="px-3 space-y-1">
            @php
            $menus = [
                ['route'=>'peminjam.dashboard',   'icon'=>'fas fa-home',        'label'=>'Dashboard'],
                ['route'=>'peminjam.aset',         'icon'=>'fas fa-box',         'label'=>'Data Aset'],
                ['route'=>'peminjam.peminjaman',   'icon'=>'fas fa-exchange-alt','label'=>'Transaksi Peminjaman'],
                ['route'=>'peminjam.pengembalian', 'icon'=>'fas fa-undo',        'label'=>'Transaksi Pengembalian'],
                ['route'=>'peminjam.riwayat',      'icon'=>'fas fa-history',     'label'=>'Riwayat'],
                ['route' => 'peminjam.denda',        'icon' => 'fas fa-money-bill-wave','label' => 'Denda'],
                ['route'=>'peminjam.tentang',      'icon'=>'fas fa-info-circle', 'label'=>'Tentang'],
            ];
            @endphp
            @foreach($menus as $m)
            <li>
                <a href="{{ route($m['route']) }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all
                {{ request()->routeIs($m['route']) ? 'bg-white/20 text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="{{ $m['icon'] }} w-5 text-base {{ request()->routeIs($m['route']) ? 'text-white' : 'text-white/70' }}"></i>
                    <span>{{ $m['label'] }}</span>
                </a>
            </li>
            @endforeach
        </ul>

        <div class="px-3 mt-8">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 justify-center bg-[rgba(239,68,68,0.2)] text-white rounded-lg text-sm hover:bg-[rgba(239,68,68,0.3)] transition-all">
                    <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="flex-1 p-4 sm:p-5 md:p-6 lg:p-8 overflow-y-auto">

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

        @yield('content')

        {{-- Footer --}}
        <footer class="mt-8 md:mt-10 lg:mt-12 bg-gradient-to-r from-[#2b477a] to-[#2a5298] text-white rounded-xl overflow-hidden">
            <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8 py-6 md:py-8 lg:py-10">
                <div class="flex flex-col md:grid md:grid-cols-3 gap-6 md:gap-8">
                    <div class="text-center md:text-left">
                        <h3 class="text-lg md:text-xl lg:text-2xl font-bold mb-2">Smart Asset Booking</h3>
                        <p class="opacity-80 text-xs md:text-sm">Peminjaman dan Manajemen aset sekolah</p>
                    </div>
                    <div class="flex flex-row gap-4 md:gap-8 justify-between md:justify-start">
                        <div class="text-left">
                            <h4 class="text-sm md:text-base font-semibold mb-2 md:mb-3">Menu</h4>
                            <div class="flex flex-col gap-1 md:gap-2">
                                <a href="{{ route('peminjam.dashboard') }}"   class="text-white/85 hover:text-white hover:underline text-xs md:text-sm">Dashboard</a>
                                <a href="{{ route('peminjam.aset') }}"         class="text-white/85 hover:text-white hover:underline text-xs md:text-sm">Data Aset</a>
                                <a href="{{ route('peminjam.peminjaman') }}"   class="text-white/85 hover:text-white hover:underline text-xs md:text-sm">Transaksi Peminjaman</a>
                                <a href="{{ route('peminjam.pengembalian') }}" class="text-white/85 hover:text-white hover:underline text-xs md:text-sm">Transaksi Pengembalian</a>
                                <a href="{{ route('peminjam.riwayat') }}"      class="text-white/85 hover:text-white hover:underline text-xs md:text-sm">Riwayat</a>
                                <a href="{{ route('peminjam.tentang') }}"      class="text-white/85 hover:text-white hover:underline text-xs md:text-sm">Tentang</a>
                            </div>
                        </div>
                        <div class="text-left">
                            <h4 class="text-sm md:text-base font-semibold mb-2 md:mb-3">Contact</h4>
                            <div class="flex flex-col gap-1 md:gap-2 text-xs md:text-sm">
                                <p class="opacity-85">📧 admin@nexora.sch.id</p>
                                <p class="opacity-85">📞 0812-3456-7890</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center py-2 md:py-3 bg-black/20 text-xs md:text-sm">
                © {{ date('Y') }} Smart Asset Booking | Developed by Nexora Web
            </div>
        </footer>
    </main>

    {{-- Right Sidebar (xl only) --}}
    @yield('right-sidebar')
</div>

{{-- Toast --}}
<div class="toast" id="toast"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('hidden');
            overlay.classList.toggle('block');
        }
    }
    function toggleNotification() {
        document.getElementById('notificationDropdown').classList.toggle('show');
        document.getElementById('userDropdown')?.classList.remove('show');
    }
    function toggleUserDropdown() {
        document.getElementById('userDropdown').classList.toggle('show');
        document.getElementById('notificationDropdown').classList.remove('show');
    }
    function markAsRead(el, id) {
        el.classList.remove('unread');
        const badge = document.getElementById('notifBadge');
        if (badge) {
            let c = parseInt(badge.textContent);
            if (c > 0) { c--; badge.textContent = c; }
            if (c === 0) badge.style.display = 'none';
        }
        // Tandai ke server via fetch
        fetch('{{ route("peminjam.notif.read") }}', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ id })
        });
    }
    function markAllAsRead() {
        document.querySelectorAll('.notification-item.unread').forEach(i => i.classList.remove('unread'));
        const badge = document.getElementById('notifBadge');
        if (badge) { badge.textContent = '0'; badge.style.display = 'none'; }
        fetch('{{ route("peminjam.notif.readAll") }}', {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
    }
    document.addEventListener('click', function(e) {
        if (!e.target.closest('[onclick="toggleNotification()"]') && !e.target.closest('#notificationDropdown'))
            document.getElementById('notificationDropdown')?.classList.remove('show');
        if (!e.target.closest('[onclick="toggleUserDropdown()"]') && !e.target.closest('#userDropdown'))
            document.getElementById('userDropdown')?.classList.remove('show');
        if (window.innerWidth <= 768) {
            const s = document.getElementById('sidebar');
            if (!s?.contains(e.target) && !e.target.closest('[onclick="toggleSidebar()"]')) {
                s?.classList.remove('show');
                document.getElementById('overlay')?.classList.add('hidden');
                document.getElementById('overlay')?.classList.remove('block');
            }
        }
    });
    function showToast(msg, type='') {
        const t = document.getElementById('toast');
        t.innerHTML = type === 'success'
            ? `<i class="fas fa-check-circle"></i> ${msg}`
            : `<i class="fas fa-exclamation-circle"></i> ${msg}`;
        t.className = `toast ${type} show`;
        setTimeout(() => t.classList.remove('show'), 2800);
    }
    function openModal(id)  { document.getElementById(id).classList.add('show'); }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }
    function goToKeranjang() { location.href = '{{ route("peminjam.peminjaman") }}'; }
</script>
@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2500,
                background: 'linear-gradient(135deg, #3b82f6 0%, #1e40af 100%)', // Gradasi Biru Nexora
                color: '#ffffff',
                iconColor: '#facc15',
                backdrop: `rgba(30, 64, 175, 0.4)` 
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                background: '#dc2626', 
                color: '#ffffff',
                iconColor: '#ffffff'
            });
        @endif
    </script>
</body>
</html>
