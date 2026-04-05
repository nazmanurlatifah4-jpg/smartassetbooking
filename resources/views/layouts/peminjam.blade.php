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
    
    {{-- NOTIFICATION DROPDOWN --}}
    <div id="notificationDropdown" class="notification-dropdown">
        <div class="notification-header">
            <h3>Notifikasi</h3>
            <span onclick="markAllAsRead()">Tandai sudah dibaca</span>
        </div>
        <div class="notification-list">
            @forelse(auth()->user()->notifikasi()->latest('tanggal_kirim')->limit(5)->get() as $notif)
            <div class="notification-item {{ $notif->tanda_baca === 'Belum Dibaca' ? 'unread' : '' }}" 
                 data-id="{{ $notif->id }}"
                 onclick="markAsRead(this, {{ $notif->id }})">
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
</div>        {{-- User Icon --}}
        <div class="relative">
            <div onclick="toggleUserDropdown()" class="cursor-pointer flex items-center gap-2 text-[#64748b] hover:text-[#3b82f6] transition-colors">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#3b82f6] to-[#2563eb] flex items-center justify-center text-white text-sm font-bold">{{ substr(auth()->user()->nama, 0, 1) }}</div>
                    <span class="text-sm font-medium hidden sm:block">{{ auth()->user()->nama }}</span>
                    <i class="fas fa-chevron-down text-xs hidden sm:block"></i>
                </div>

            <div id="userDropdown" class="user-dropdown">
                <div class="px-4 py-3 border-b border-[#e2e8f0]">
                    {{-- kolom 'nama' sesuai migration --}}
                    <p class="text-sm font-semibold text-[#1e293b]">{{ auth()->user()->nama }}</p>
                    <p class="text-xs text-[#64748b]">{{ auth()->user()->kelas ?? auth()->user()->jurusan }}</p>
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
<div class="flex min-h-[calc(100vh-64px)]">

    <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-mobile fixed md:sticky top-0 md:top-[64px] left-[-260px] md:left-0 w-[260px] h-screen md:h-[calc(100vh-64px)] bg-gradient-to-b from-[#3b82f6] to-[#2563eb] text-white py-6 overflow-y-auto z-50 transition-all duration-300 flex-shrink-0">
            <div class="md:hidden flex justify-end px-4 mb-4">
                <button onclick="toggleSidebar()" class="text-white/80 hover:text-white text-xl"><i class="fas fa-times"></i></button>
            </div>

     <!-- User Info -->
            <div class="px-4 mb-6">
                <div class="flex items-center gap-3 bg-white/10 rounded-xl p-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-lg font-bold">{{ substr(auth()->user()->nama, 0, 1) }}</div>
                    <div>
                        <p class="text-sm font-semibold">{{ auth()->user()->nama }}</p>
                        <p class="text-xs text-white/70">Peminjam</p>
                    </div>
                </div>
            </div>

            <p class="px-4 text-xs font-semibold text-white/50 uppercase tracking-wider mb-2">Menu Utama</p>
            <ul class="px-3 space-y-1">

        <ul class="px-3 space-y-1">
            @php
            $menus = [
                ['route'=>'peminjam.dashboard',   'icon'=>'fas fa-home',        'label'=>'Dashboard'],
                ['route'=>'peminjam.aset',         'icon'=>'fas fa-box',         'label'=>'Data Aset'],
                ['route'=>'peminjam.peminjaman',   'icon'=>'fas fa-exchange-alt','label'=>'Transaksi Peminjaman'],
                ['route'=>'peminjam.pengembalian', 'icon'=>'fas fa-undo',        'label'=>'Transaksi Pengembalian'],
                ['route'=>'peminjam.riwayat',      'icon'=>'fas fa-history',     'label'=>'Riwayat'],
                ['route'=>'peminjam.tentang',      'icon'=>'fas fa-info-circle', 'label'=>'Tentang'],
            ];
            @endphp
            @foreach($menus as $m)
            <li>
                <a href="{{ route($m['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all
                          {{ request()->routeIs($m['route'].'*') ? 'bg-white/15 text-white' : 'text-white/90 hover:bg-white/15 hover:text-white' }}">
                    <i class="{{ $m['icon'] }} w-5 text-base"></i>
                    <span>{{ $m['label'] }}</span>
                </a>
            </li>
            @endforeach
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
    <!-- Right Sidebar (xl only) - Aktivitas Peminjam -->
<aside class="right-sidebar-hide hidden xl:block w-[220px] p-5 bg-white border-l border-[#e2e8f0] sticky top-[64px] h-[calc(100vh-64px)] overflow-y-auto flex-shrink-0">
    {{-- Section Aktivitas Terbaru --}}
    <div class="mb-6">
        <p class="text-sm font-semibold text-[#1e293b] mb-3 flex items-center gap-2">
            <i class="fas fa-history text-[#3b82f6]"></i> Aktivitas Saya
        </p>
        
        @php
            // Mengambil aktivitas peminjaman khusus user ini
            $recentActivities = \App\Models\Peminjaman::with('aset')
                                ->where('user_id', auth()->id())
                                ->latest()
                                ->take(5)
                                ->get();
        @endphp

        @forelse($recentActivities as $activity)
            @php
                $bgColor = 'bg-blue-50';
                $borderColor = 'border-blue-400';
                $label = 'Dipinjam';
                $icon = 'fa-arrow-up';

                // Logika Status untuk Peminjam
                if($activity->status == 'Terlambat') {
                    $bgColor = 'bg-rose-50';
                    $borderColor = 'border-rose-500';
                    $label = 'Terlambat!';
                    $icon = 'fa-exclamation-triangle';
                } elseif($activity->status == 'Selesai') {
                    $bgColor = 'bg-emerald-50';
                    $borderColor = 'border-emerald-500';
                    $label = 'Dikembalikan';
                    $icon = 'fa-check-circle';
                } elseif($activity->status == 'Proses') {
                    $bgColor = 'bg-amber-50';
                    $borderColor = 'border-amber-400';
                    $label = 'Menunggu';
                    $icon = 'fa-clock';
                }
            @endphp
            
            <div class="p-3 {{ $bgColor }} rounded-lg mb-3 border-l-4 {{ $borderColor }} shadow-sm transition-transform hover:scale-[1.02]">
                <div class="flex justify-between items-start mb-1">
                    <p class="text-[9px] font-bold uppercase tracking-wider text-slate-500">{{ $label }}</p>
                    <i class="fas {{ $icon }} text-[10px] {{ str_replace('border-', 'text-', $borderColor) }}"></i>
                </div>
                <p class="text-xs font-bold text-[#1e293b] leading-tight">{{ $activity->aset->nama_aset ?? 'Aset' }}</p>
                <p class="text-[10px] text-[#64748b] mt-0.5 italic">Kode: {{ $activity->aset->kode_aset ?? '-' }}</p>
                <span class="text-[9px] text-[#94a3b8] block mt-1">
                    <i class="far fa-clock"></i> {{ $activity->updated_at->diffForHumans() }}
                </span>
            </div>
        @empty
            <div class="text-center py-6">
                <i class="fas fa-box-open text-slate-200 text-3xl mb-2"></i>
                <p class="text-xs text-slate-400 italic">Belum ada aktivitas</p>
            </div>
        @endforelse
    </div>

    {{-- Section Insight Peminjam --}}
    <div>
        <p class="text-sm font-semibold text-[#1e293b] mb-3 flex items-center gap-2">
            <i class="fas fa-chart-pie text-[#3b82f6]"></i> Statistik Saya
        </p>
        
        @php
            $totalPinjam = \App\Models\Peminjaman::where('user_id', auth()->id())->count();
            $totalSelesai = \App\Models\Peminjaman::where('user_id', auth()->id())->where('status', 'Selesai')->count();
            $totalTerlambat = \App\Models\Peminjaman::where('user_id', auth()->id())->where('status', 'Terlambat')->count();
            
            $rate = $totalPinjam > 0 ? round(($totalSelesai / $totalPinjam) * 100) : 0;
            
            $favorit = \App\Models\Peminjaman::where('user_id', auth()->id())
                        ->select('aset_id', \DB::raw('count(*) as total'))
                        ->groupBy('aset_id')
                        ->orderBy('total', 'desc')
                        ->with('aset')
                        ->first();
        @endphp

        <div class="p-3 bg-white border border-[#e2e8f0] rounded-lg mb-3 shadow-sm">
            <p class="text-[10px] text-[#64748b] uppercase font-bold mb-1">Skor Kepatuhan</p>
            <div class="flex items-end gap-1">
                <div class="text-2xl font-black text-[#3b82f6]">{{ $rate }}%</div>
                <p class="text-[9px] text-[#94a3b8] mb-1">Tepat Waktu</p>
            </div>
            <div class="w-full bg-[#f1f5f9] rounded-full h-1.5 mt-2">
                <div class="bg-gradient-to-r from-[#3b82f6] to-[#60a5fa] h-1.5 rounded-full" style="width: {{ $rate }}%"></div>
            </div>
        </div>

        <div class="p-3 bg-white border border-[#e2e8f0] rounded-lg shadow-sm">
            <p class="text-[10px] text-[#64748b] uppercase font-bold">Paling Sering Kamu Pinjam</p>
            @if($favorit)
                <p class="text-xs font-bold text-[#1e293b] mt-1">{{ $favorit->aset->nama_aset }}</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-[10px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full font-bold">
                        {{ $favorit->total }}x Pinjam
                    </span>
                </div>
            @else
                <p class="text-[10px] text-slate-400 italic mt-1">Belum ada data favorit</p>
            @endif
        </div>

        @if($totalTerlambat > 0)
        <div class="mt-3 p-2 bg-rose-100 border border-rose-200 rounded-md flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-rose-500 text-xs"></i>
            <p class="text-[10px] text-rose-700 font-medium">Ada {{ $totalTerlambat }} riwayat terlambat</p>
        </div>
        @endif
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
    const dropdown = document.getElementById('notificationDropdown');
    const userDropdown = document.getElementById('userDropdown');
    
    dropdown.classList.toggle('show');
    if (userDropdown) userDropdown.classList.remove('show');
}

function toggleUserDropdown() {
    const userDropdown = document.getElementById('userDropdown');
    const notifDropdown = document.getElementById('notificationDropdown');
    
    userDropdown.classList.toggle('show');
    if (notifDropdown) notifDropdown.classList.remove('show');
}

function markAsRead(el, id) {
    // Hapus class unread
    el.classList.remove('unread');
    
    // Update badge
    const badge = document.getElementById('notifBadge');
    if (badge && badge.style.display !== 'none') {
        let currentCount = parseInt(badge.textContent);
        if (!isNaN(currentCount) && currentCount > 0) {
            currentCount--;
            if (currentCount === 0) {
                badge.style.display = 'none';
            } else {
                badge.textContent = currentCount;
            }
        }
    }
    
    // Kirim ke server
    fetch('/peminjam/notifikasi/' + id + '/read', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).catch(err => console.error('Error:', err));
}

function markAllAsRead() {
    // Hapus semua class unread
    document.querySelectorAll('.notification-item.unread').forEach(item => {
        item.classList.remove('unread');
    });
    
    // Sembunyikan badge
    const badge = document.getElementById('notifBadge');
    if (badge) {
        badge.style.display = 'none';
        badge.textContent = '0';
    }
    
    // Kirim ke server
    fetch('{{ route("peminjam.notif.readAll") }}', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).catch(err => console.error('Error:', err));
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    const notifDropdown = document.getElementById('notificationDropdown');
    const userDropdown = document.getElementById('userDropdown');
    const notifBtn = e.target.closest('[onclick="toggleNotification()"]');
    const userBtn = e.target.closest('[onclick="toggleUserDropdown()"]');
    
    if (!notifBtn && notifDropdown && !notifDropdown.contains(e.target)) {
        notifDropdown.classList.remove('show');
    }
    
    if (!userBtn && userDropdown && !userDropdown.contains(e.target)) {
        userDropdown.classList.remove('show');
    }
    
    // Mobile sidebar close
    if (window.innerWidth <= 768) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        if (!e.target.closest('[onclick="toggleSidebar()"]') && 
            !sidebar?.contains(e.target) && 
            sidebar?.classList.contains('show')) {
            sidebar.classList.remove('show');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('block');
            }
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
</body>
</html>
