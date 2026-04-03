<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Smart Asset Booking') - Nexora</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media (max-width: 768px) {
            .sidebar-mobile { left: -260px; transition: left 0.3s; }
            .sidebar-mobile.show { left: 0; }
        }
        .stats-scroll { scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; }
        .stats-scroll > div { scroll-snap-align: start; }
        .stats-scroll::-webkit-scrollbar { display: none; }

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
            to { opacity: 1; transform: translateY(0); }
        }
        .notification-item { padding: 15px 20px; border-bottom: 1px solid #f1f5f9; transition: background 0.3s; cursor: pointer; }
        .notification-item:hover { background: #f8fafc; }
        .notification-item.unread { background: #eff6ff; }
        .notification-item-title { font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 4px; }
        .notification-item-desc { font-size: 13px; color: #64748b; margin-bottom: 4px; }
        .notification-item-time { font-size: 11px; color: #94a3b8; }

        /* Profile Dropdown */
        .profile-dropdown {
            position: absolute; top: 40px; right: 0; width: 200px;
            background: white; border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            z-index: 1000; display: none; overflow: hidden;
            animation: slideDown 0.3s ease;
        }
        .profile-dropdown.show { display: block; }

        /* Modal */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 9999;
            align-items: center; justify-content: center;
        }
        .modal-overlay.show { display: flex; }
        @media (max-width: 1024px) { .right-sidebar-hide { display: none; } }
    </style>
    @stack('styles')
</head>
<body class="font-['Segoe_UI',Tahoma,Geneva,Verdana,sans-serif] bg-[#f8fafc] text-[#1e293b]">

    <!-- Navbar -->
    <nav class="bg-white px-4 sm:px-6 md:px-8 py-3 md:py-4 shadow-[0_2px_10px_rgba(0,0,0,0.05)] flex justify-between items-center sticky top-0 z-50">
        <!-- Logo + Hamburger -->
        <div onclick="toggleSidebar()" class="flex items-center gap-2 sm:gap-3 cursor-pointer select-none">
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-gradient-to-br from-[#3b82f6] to-[#2563eb] flex items-center justify-center text-white text-sm font-bold shadow">
                <i class="fas fa-bars md:hidden"></i>
                <img src="{{ asset('assets/logo-removebg-preview.png') }}" alt="logo" class="w-6 h-6 object-contain hidden md:block">
            </div>
            <div>
                <h1 class="text-sm sm:text-base md:text-lg font-semibold text-[#1e293b]">Smart Asset Booking</h1>
                <p class="text-xs text-[#64748b]">@yield('page-subtitle', 'Dashboard Admin')</p>
            </div>
        </div>

        <!-- Navbar Right -->
        <div class="flex gap-3 sm:gap-4 items-center">
            <!-- Notification Bell -->
            <div class="relative">
                <div onclick="toggleNotification()" class="cursor-pointer text-[#64748b] hover:text-[#3b82f6] text-lg relative transition-colors">
                    <i class="fas fa-bell"></i>
                    <span id="notifBadge" class="absolute -top-1 -right-1 bg-[#ef4444] text-white text-[10px] px-1.5 py-0.5 rounded-full leading-none">5</span>
                </div>
                <div id="notificationDropdown" class="notification-dropdown">
                    <div class="px-5 py-3.5 border-b border-[#e2e8f0] flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-[#1e293b]">Notifikasi</h3>
                        <span onclick="markAllAsRead()" class="text-xs text-[#3b82f6] cursor-pointer hover:underline">Tandai semua dibaca</span>
                    </div>
                    <div class="max-h-[350px] overflow-y-auto">
                        <div class="notification-item unread" onclick="markAsRead(this)">
                            <div class="notification-item-title">Pengajuan Baru</div>
                            <div class="notification-item-desc">Budi Santoso mengajukan peminjaman laptop</div>
                            <div class="notification-item-time">5 menit lalu</div>
                        </div>
                        <div class="notification-item unread" onclick="markAsRead(this)">
                            <div class="notification-item-title">Pengembalian</div>
                            <div class="notification-item-desc">Siti Nurhaliza mengembalikan proyektor</div>
                            <div class="notification-item-time">1 jam lalu</div>
                        </div>
                        <div class="notification-item unread" onclick="markAsRead(this)">
                            <div class="notification-item-title">Terlambat!</div>
                            <div class="notification-item-desc">Ahmad Ridwan terlambat mengembalikan kamera</div>
                            <div class="notification-item-time">2 jam lalu</div>
                        </div>
                        <div class="notification-item" onclick="markAsRead(this)">
                            <div class="notification-item-title">Aset Tersedia</div>
                            <div class="notification-item-desc">Laptop Lenovo ThinkPad sudah tersedia</div>
                            <div class="notification-item-time">1 hari lalu</div>
                        </div>
                        <div class="notification-item" onclick="markAsRead(this)">
                            <div class="notification-item-title">Maintenance</div>
                            <div class="notification-item-desc">Proyektor Epson akan maintenance besok</div>
                            <div class="notification-item-time">2 hari lalu</div>
                        </div>
                    </div>
                    <div class="px-5 py-3 text-center border-t border-[#e2e8f0]">
                        <a href="#" class="text-xs text-[#3b82f6] hover:underline">Lihat semua notifikasi</a>
                    </div>
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative">
                <div onclick="toggleProfile()" class="cursor-pointer flex items-center gap-2 text-[#64748b] hover:text-[#3b82f6] transition-colors">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#3b82f6] to-[#2563eb] flex items-center justify-center text-white text-sm font-bold">A</div>
                    <span class="text-sm font-medium hidden sm:block">Admin</span>
                    <i class="fas fa-chevron-down text-xs hidden sm:block"></i>
                </div>
                <div id="profileDropdown" class="profile-dropdown">
                    <div class="px-4 py-3 border-b border-[#e2e8f0]">
                        <p class="text-sm font-semibold text-[#1e293b]">Administrator</p>
                        <p class="text-xs text-[#64748b]">admin@nexora.sch.id</p>
                    </div>
                    {{-- <a href="{{ route('admin.profile') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#475569] hover:bg-[#f8fafc] transition-colors">
                        <i class="fas fa-user w-4"></i> Profil Saya
                    </a>
                    <a href="{{ route('admin.settings') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-[#475569] hover:bg-[#f8fafc] transition-colors">
                        <i class="fas fa-cog w-4"></i> Pengaturan
                    </a> --}}
                    <div class="border-t border-[#e2e8f0]">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-[#ef4444] hover:bg-[#fef2f2] transition-colors">
                                <i class="fas fa-sign-out-alt w-4"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Overlay mobile -->
    <div id="overlay" class="hidden fixed inset-0 bg-black/50 z-40" onclick="toggleSidebar()"></div>

    <!-- Layout Wrapper -->
    <div class="flex min-h-[calc(100vh-64px)]">

        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-mobile fixed md:sticky top-0 md:top-[64px] left-[-260px] md:left-0 w-[260px] h-screen md:h-[calc(100vh-64px)] bg-gradient-to-b from-[#3b82f6] to-[#2563eb] text-white py-6 overflow-y-auto z-50 transition-all duration-300 flex-shrink-0">
            <div class="md:hidden flex justify-end px-4 mb-4">
                <button onclick="toggleSidebar()" class="text-white/80 hover:text-white text-xl"><i class="fas fa-times"></i></button>
            </div>

            <!-- User Info -->
            <div class="px-4 mb-6">
                <div class="flex items-center gap-3 bg-white/10 rounded-xl p-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-lg font-bold">A</div>
                    <div>
                        <p class="text-sm font-semibold">Administrator</p>
                        <p class="text-xs text-white/70">Super Admin</p>
                    </div>
                </div>
            </div>

            <p class="px-4 text-xs font-semibold text-white/50 uppercase tracking-wider mb-2">Menu Utama</p>
            <ul class="px-3 space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white font-semibold' : 'text-white/85 hover:bg-white/10 hover:text-white' }} transition-all">
                        <i class="fas fa-home w-5"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.masterdata') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.masterdata*') ? 'bg-white/20 text-white font-semibold' : 'text-white/85 hover:bg-white/10 hover:text-white' }} transition-all">
                        <i class="fas fa-database w-5"></i> <span>Master Data</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.transaksi') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.transaksi*') ? 'bg-white/20 text-white font-semibold' : 'text-white/85 hover:bg-white/10 hover:text-white' }} transition-all">
                        <i class="fas fa-exchange-alt w-5"></i> <span>Transaksi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.denda') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.denda*') ? 'bg-white/20 text-white font-semibold' : 'text-white/85 hover:bg-white/10 hover:text-white' }} transition-all">
                        <i class="fas fa-money-bill-wave w-5"></i> <span>Denda</span>
                        <span class="ml-auto bg-[#ef4444] text-white text-[10px] px-1.5 py-0.5 rounded-full">3</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.laporan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.laporan*') ? 'bg-white/20 text-white font-semibold' : 'text-white/85 hover:bg-white/10 hover:text-white' }} transition-all">
                        <i class="fas fa-file-alt w-5"></i> <span>Laporan</span>
                    </a>
                </li>
            </ul>

            <div class="px-3 mt-6 pt-4 border-t border-white/20">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 justify-center bg-white/10 text-white rounded-lg text-sm hover:bg-red-500/30 transition-all">
                        <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-5 md:p-6 lg:p-8 overflow-y-auto min-w-0">
            @yield('content')

            <!-- Footer -->
            <footer class="mt-8 bg-gradient-to-r from-[#2b477a] to-[#2a5298] text-white rounded-xl overflow-hidden">
                <div class="px-6 py-6">
                    <div class="flex flex-col md:grid md:grid-cols-3 gap-6">
                        <div class="text-center md:text-left">
                            <h3 class="text-lg font-bold mb-1">Smart Asset Booking</h3>
                            <p class="opacity-75 text-xs">Sistem peminjaman & manajemen aset sekolah</p>
                        </div>
                        <div class="flex gap-8 justify-center md:justify-start">
                            <div>
                                <h4 class="text-sm font-semibold mb-2">Menu</h4>
                                <div class="flex flex-col gap-1 text-xs text-white/80">
                                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white hover:underline">Dashboard</a>
                                    <a href="{{ route('admin.masterdata') }}" class="hover:text-white hover:underline">Master Data</a>
                                    <a href="{{ route('admin.transaksi') }}" class="hover:text-white hover:underline">Transaksi</a>
                                    <a href="{{ route('admin.denda') }}" class="hover:text-white hover:underline">Denda</a>
                                    <a href="{{ route('admin.laporan') }}" class="hover:text-white hover:underline">Laporan</a>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold mb-2">Kontak</h4>
                                <div class="flex flex-col gap-1 text-xs text-white/80">
                                    <p>📧 admin@nexora.sch.id</p>
                                    <p>📞 0812-3456-7890</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center py-2 bg-black/20 text-xs text-white/70">
                    © {{ date('Y') }} Smart Asset Booking — Nexora Web
                </div>
            </footer>
        </main>

        <!-- Right Sidebar (xl only) -->
        <aside class="right-sidebar-hide hidden xl:block w-[200px] p-5 bg-white border-l border-[#e2e8f0] sticky top-[64px] h-[calc(100vh-64px)] overflow-y-auto flex-shrink-0">
            <div class="mb-6">
                <p class="text-sm font-semibold text-[#1e293b] mb-3 flex items-center gap-2"><i class="fas fa-bell text-[#3b82f6]"></i> Notifikasi</p>
                <div class="p-3 bg-[#f8fafc] rounded-lg mb-2 border-l-4 border-[#3b82f6]">
                    <p class="text-xs font-semibold text-[#1e293b]">Pengajuan Baru</p>
                    <p class="text-xs text-[#64748b] mt-0.5">Budi Santoso mengajukan laptop</p>
                    <span class="text-[10px] text-[#94a3b8]">5 menit lalu</span>
                </div>
                <div class="p-3 bg-[#fef3c7] rounded-lg mb-2 border-l-4 border-[#f59e0b]">
                    <p class="text-xs font-semibold text-[#1e293b]">Terlambat!</p>
                    <p class="text-xs text-[#64748b] mt-0.5">Ahmad Ridwan belum mengembalikan kamera</p>
                    <span class="text-[10px] text-[#94a3b8]">2 jam lalu</span>
                </div>
                <div class="p-3 bg-[#f8fafc] rounded-lg mb-2 border-l-4 border-[#10b981]">
                    <p class="text-xs font-semibold text-[#1e293b]">Pengembalian</p>
                    <p class="text-xs text-[#64748b] mt-0.5">Siti Nurhaliza mengembalikan proyektor</p>
                    <span class="text-[10px] text-[#94a3b8]">1 jam lalu</span>
                </div>
            </div>
            <div>
                <p class="text-sm font-semibold text-[#1e293b] mb-3 flex items-center gap-2"><i class="fas fa-chart-line text-[#3b82f6]"></i> Insight</p>
                <div class="p-3 bg-[#f8fafc] rounded-lg mb-2 border-l-4 border-[#3b82f6]">
                    <p class="text-xs text-[#64748b]">Tingkat Pengembalian</p>
                    <div class="text-xl font-bold text-[#3b82f6]">94%</div>
                    <div class="w-full bg-[#e2e8f0] rounded-full h-1.5 mt-1"><div class="bg-[#3b82f6] h-1.5 rounded-full" style="width:94%"></div></div>
                </div>
                <div class="p-3 bg-[#f8fafc] rounded-lg border-l-4 border-[#3b82f6]">
                    <p class="text-xs text-[#64748b]">Aset Paling Populer</p>
                    <p class="text-sm font-semibold text-[#3b82f6] mt-0.5">Laptop Lenovo</p>
                </div>
            </div>
        </aside>
    </div>

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
            document.getElementById('profileDropdown').classList.remove('show');
        }
        function toggleProfile() {
            document.getElementById('profileDropdown').classList.toggle('show');
            document.getElementById('notificationDropdown').classList.remove('show');
        }
        function markAsRead(el) {
            el.classList.remove('unread');
            const badge = document.getElementById('notifBadge');
            let count = parseInt(badge.textContent);
            if (count > 0) count--;
            badge.textContent = count;
            if (count === 0) badge.style.display = 'none';
        }
        function markAllAsRead() {
            document.querySelectorAll('.notification-item.unread').forEach(i => i.classList.remove('unread'));
            const badge = document.getElementById('notifBadge');
            badge.textContent = '0';
            badge.style.display = 'none';
        }
        document.addEventListener('click', function(e) {
            const notifDropdown = document.getElementById('notificationDropdown');
            const profileDropdown = document.getElementById('profileDropdown');
            if (!e.target.closest('[onclick="toggleNotification()"]') && !e.target.closest('#notificationDropdown')) {
                notifDropdown?.classList.remove('show');
            }
            if (!e.target.closest('[onclick="toggleProfile()"]') && !e.target.closest('#profileDropdown')) {
                profileDropdown?.classList.remove('show');
            }
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                if (!sidebar?.contains(e.target) && !e.target.closest('[onclick="toggleSidebar()"]')) {
                    sidebar?.classList.remove('show');
                    document.getElementById('overlay')?.classList.add('hidden');
                }
            }
        });

        // Generic modal helpers
        function openModal(id) { document.getElementById(id).classList.add('show'); }
        function closeModal(id) { document.getElementById(id).classList.remove('show'); }
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
