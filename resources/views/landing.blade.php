<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora Smart Asset Booking — Sistem Peminjaman Aset Sekolah</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html { scroll-behavior: smooth; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-fadeInUp { animation: fadeInUp 0.7s ease forwards; }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .animate-d1 { animation-delay: 0.1s; opacity: 0; }
        .animate-d2 { animation-delay: 0.25s; opacity: 0; }
        .animate-d3 { animation-delay: 0.4s; opacity: 0; }
        .animate-d4 { animation-delay: 0.55s; opacity: 0; }

        /* Scroll-reveal via Intersection Observer */
        .reveal { opacity: 0; transform: translateY(24px); transition: opacity 0.6s ease, transform 0.6s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        .gradient-hero { background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 40%, #547baf 100%); }
        .card-hover { transition: transform 0.25s, box-shadow 0.25s; }
        .card-hover:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(59,130,246,0.15); }

        /* Mobile menu */
        #mobileMenu { display: none; }
        #mobileMenu.open { display: block; }

        .blob {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation: blobAnim 8s ease-in-out infinite;
        }
        @keyframes blobAnim {
            0%, 100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
            50% { border-radius: 70% 30% 30% 70% / 70% 70% 30% 30%; }
        }
    </style>
</head>
<body class="font-['Segoe_UI',Tahoma,Geneva,Verdana,sans-serif] text-[#1e293b] bg-white overflow-x-hidden">

    <!-- ===== NAVBAR ===== -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md shadow-sm border-b border-[#e2e8f0]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="#" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#3b82f6] to-[#2563eb] flex items-center justify-center shadow">
                    <img src="{{ asset('assets/logo-removebg-preview.png') }}" alt="Logo" class="w-6 h-6 object-contain" onerror="this.style.display='none';this.parentElement.innerHTML='<span class=\'text-white font-bold text-sm\'>N</span>'">
                </div>
                <div>
                    <span class="font-bold text-[#1e293b] text-base">Nexora</span>
                    <span class="text-[#3b82f6] font-bold text-base"> SAB</span>
                </div>
            </a>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-[#475569]">
                <a href="#fitur" class="hover:text-[#3b82f6] transition-colors">Fitur</a>
                <a href="#cara-kerja" class="hover:text-[#3b82f6] transition-colors">Cara Kerja</a>
                <a href="#testimoni" class="hover:text-[#3b82f6] transition-colors">Testimoni</a>
                <a href="#faq" class="hover:text-[#3b82f6] transition-colors">FAQ</a>
                <a href="{{ route('login') }}" class="px-5 py-2 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white rounded-full font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all shadow-[0_4px_12px_rgba(59,130,246,0.3)]">
                    Masuk
                </a>
            </div>

            <!-- Mobile Menu Toggle -->
            <button onclick="document.getElementById('mobileMenu').classList.toggle('open')" class="md:hidden text-[#475569] text-xl p-1">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="md:hidden bg-white border-t border-[#e2e8f0] px-4 py-4 space-y-3">
            <a href="#fitur" class="block text-sm text-[#475569] hover:text-[#3b82f6] py-1">Fitur</a>
            <a href="#cara-kerja" class="block text-sm text-[#475569] hover:text-[#3b82f6] py-1">Cara Kerja</a>
            <a href="#testimoni" class="block text-sm text-[#475569] hover:text-[#3b82f6] py-1">Testimoni</a>
            <a href="#faq" class="block text-sm text-[#475569] hover:text-[#3b82f6] py-1">FAQ</a>
            <a href="{{ route('login') }}" class="px-6 py-2 bg-[#3b82f6] text-white rounded-full font-semibold">
             Mulai Sekarang
            </a>
        </div>
    </nav>

    <!-- ===== HERO ===== -->
    <section class="gradient-hero min-h-screen flex items-center pt-16 pb-12 relative overflow-hidden">
        <!-- Background shapes -->
        <div class="absolute top-20 right-[-80px] w-80 h-80 bg-white/5 blob"></div>
        <div class="absolute bottom-10 left-[-60px] w-60 h-60 bg-white/5 blob" style="animation-delay:3s"></div>
        <div class="absolute top-1/2 right-1/4 w-4 h-4 bg-white/20 rounded-full"></div>
        <div class="absolute top-1/3 left-1/4 w-3 h-3 bg-white/15 rounded-full"></div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 w-full">
            <div class="grid md:grid-cols-2 gap-10 items-center">
                <!-- Left Text -->
                <div>
                    <span class="inline-block bg-white/15 text-white text-xs font-semibold px-3 py-1.5 rounded-full mb-5 animate-fadeInUp animate-d1 border border-white/20">
                        <i class="fas fa-star text-yellow-300 mr-1"></i> Sistem Peminjaman Aset Sekolah #1
                    </span>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight mb-5 animate-fadeInUp animate-d2">
                        Kelola Aset Sekolah<br>
                        <span class="text-yellow-300">Lebih Cerdas,</span><br>
                        Lebih Efisien
                    </h1>
                    <p class="text-white/85 text-sm sm:text-base leading-relaxed mb-8 max-w-lg animate-fadeInUp animate-d3">
                        Nexora Smart Asset Booking hadir untuk memudahkan proses peminjaman, pemantauan, dan pengembalian aset sekolah secara digital — transparan, terstruktur, dan real-time.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 animate-fadeInUp animate-d4">
                        <a href="{{ route('login') }}"
                            class="px-7 py-3.5 bg-white text-[#2563eb] rounded-full font-bold text-sm hover:-translate-y-1 hover:shadow-[0_8px_25px_rgba(255,255,255,0.3)] transition-all shadow-lg text-center">
                            <i class="fas fa-rocket mr-2"></i> Mulai Sekarang
                        </a>
                        <a href="#cara-kerja"
                            class="px-7 py-3.5 bg-white/10 border border-white/30 text-white rounded-full font-semibold text-sm hover:bg-white/20 transition-all text-center">
                            <i class="fas fa-play-circle mr-2"></i> Lihat Cara Kerja
                        </a>
                    </div>

                    <!-- Trust Badges -->
                    <div class="flex flex-wrap gap-4 mt-8 animate-fadeInUp animate-d4">
                        <div class="flex items-center gap-2 text-white/80 text-xs">
                            <i class="fas fa-shield-alt text-green-300"></i> Aman & Terenkripsi
                        </div>
                        <div class="flex items-center gap-2 text-white/80 text-xs">
                            <i class="fas fa-mobile-alt text-blue-200"></i> Responsif di semua device
                        </div>
                        <div class="flex items-center gap-2 text-white/80 text-xs">
                            <i class="fas fa-bolt text-yellow-300"></i> Real-time Update
                        </div>
                    </div>
                </div>

                <!-- Right Illustration -->
                <div class="hidden md:flex items-center justify-center">
                    <div class="relative">
                        <!-- Main card mockup -->
                        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-3xl p-6 w-80 animate-float">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-[#dbeafe] flex items-center justify-center text-[#3b82f6] text-base">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div>
                                    <p class="text-white font-semibold text-sm">Laptop Lenovo ThinkPad</p>
                                    <p class="text-white/60 text-xs">Elektronik · Stok: 5</p>
                                </div>
                                <span class="ml-auto bg-[#d1fae5] text-[#065f46] text-xs font-semibold px-2 py-0.5 rounded-full">Tersedia</span>
                            </div>
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-xs text-white/70">
                                    <span>Dipinjam bulan ini</span><span class="text-white font-semibold">18x</span>
                                </div>
                                <div class="w-full bg-white/10 rounded-full h-1.5">
                                    <div class="bg-gradient-to-r from-yellow-300 to-yellow-400 h-1.5 rounded-full" style="width:90%"></div>
                                </div>
                            </div>
                            <button class="w-full py-2.5 bg-white text-[#2563eb] rounded-xl text-sm font-bold hover:bg-white/90 transition-colors">
                                <i class="fas fa-hand-holding mr-2"></i> Pinjam Sekarang
                            </button>
                        </div>

                        <!-- Floating badge -->
                        <div class="absolute -top-4 -right-4 bg-white rounded-2xl px-3 py-2 shadow-lg border border-[#e2e8f0]">
                            <div class="flex items-center gap-2 text-xs">
                                <div class="w-6 h-6 rounded-full bg-[#d1fae5] flex items-center justify-center">
                                    <i class="fas fa-check text-[#10b981] text-xs"></i>
                                </div>
                                <span class="font-semibold text-[#1e293b]">Disetujui!</span>
                            </div>
                        </div>
                        <!-- Floating badge 2 -->
                        <div class="absolute -bottom-4 -left-4 bg-white rounded-2xl px-3 py-2 shadow-lg border border-[#e2e8f0]">
                            <div class="flex items-center gap-2 text-xs">
                                <div class="w-6 h-6 rounded-full bg-[#dbeafe] flex items-center justify-center">
                                    <i class="fas fa-users text-[#3b82f6] text-xs"></i>
                                </div>
                                <span class="font-semibold text-[#1e293b]">156 User Aktif</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== STATS STRIP ===== -->
    <section class="bg-white border-b border-[#e2e8f0]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                @php
                $stats = [
                    ['val'=>'156+','label'=>'User Aktif','icon'=>'fas fa-users'],
                    ['val'=>'89','label'=>'Total Aset','icon'=>'fas fa-box'],
                    ['val'=>'1.200+','label'=>'Transaksi','icon'=>'fas fa-exchange-alt'],
                    ['val'=>'94%','label'=>'Tingkat Kepuasan','icon'=>'fas fa-star'],
                ];
                @endphp
                @foreach($stats as $s)
                <div class="reveal">
                    <div class="w-10 h-10 rounded-xl bg-[#eff6ff] flex items-center justify-center text-[#3b82f6] mx-auto mb-2 text-base">
                        <i class="{{ $s['icon'] }}"></i>
                    </div>
                    <div class="text-2xl md:text-3xl font-bold text-[#1e293b]">{{ $s['val'] }}</div>
                    <div class="text-xs text-[#64748b] mt-0.5">{{ $s['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===== FITUR ===== -->
    <section id="fitur" class="py-16 md:py-20 bg-[#f8fafc]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-12 reveal">
                <span class="text-xs font-semibold text-[#3b82f6] uppercase tracking-widest">Kenapa Nexora?</span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#1e293b] mt-2 mb-3">Fitur Unggulan Kami</h2>
                <p class="text-[#64748b] text-sm sm:text-base max-w-xl mx-auto">Dirancang khusus untuk kebutuhan sekolah — dari peminjaman hingga pelaporan, semua dalam satu platform.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @php
                $features = [
                    ['icon'=>'fas fa-mobile-alt','color'=>'text-[#3b82f6]','bg'=>'bg-[#dbeafe]','title'=>'Akses Multi-Device','desc'=>'Gunakan di HP, tablet, atau laptop. Interface responsif yang nyaman di semua ukuran layar.'],
                    ['icon'=>'fas fa-bell','color'=>'text-[#f59e0b]','bg'=>'bg-[#fef3c7]','title'=>'Notifikasi Real-Time','desc'=>'Peminjam dan admin menerima notifikasi otomatis saat pengajuan, persetujuan, atau jatuh tempo.'],
                    ['icon'=>'fas fa-shield-alt','color'=>'text-[#10b981]','bg'=>'bg-[#d1fae5]','title'=>'Multi-Role Aman','desc'=>'Tiga level akses: Admin, Peminjam, dan Manajemen. Setiap role punya kontrol yang tepat.'],
                    ['icon'=>'fas fa-money-bill-wave','color'=>'text-[#ef4444]','bg'=>'bg-[#fecaca]','title'=>'Manajemen Denda','desc'=>'Sistem denda otomatis untuk keterlambatan. Transparent dan mudah dilacak oleh semua pihak.'],
                    ['icon'=>'fas fa-chart-bar','color'=>'text-[#7c3aed]','bg'=>'bg-[#e9d5ff]','title'=>'Laporan & Analitik','desc'=>'Rekap transaksi lengkap dengan export PDF. Pantau penggunaan aset secara akurat.'],
                    ['icon'=>'fas fa-clock','color'=>'text-[#0ea5e9]','bg'=>'bg-[#e0f2fe]','title'=>'Riwayat Lengkap','desc'=>'Semua aktivitas tercatat rapi. Mudah lacak siapa meminjam apa dan kapan dikembalikan.'],
                ];
                @endphp
                @foreach($features as $f)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e2e8f0] card-hover reveal">
                    <div class="w-12 h-12 rounded-2xl {{ $f['bg'] }} flex items-center justify-center {{ $f['color'] }} text-xl mb-4">
                        <i class="{{ $f['icon'] }}"></i>
                    </div>
                    <h3 class="text-base font-bold text-[#1e293b] mb-2">{{ $f['title'] }}</h3>
                    <p class="text-sm text-[#64748b] leading-relaxed">{{ $f['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===== CARA KERJA ===== -->
    <section id="cara-kerja" class="py-16 md:py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-12 reveal">
                <span class="text-xs font-semibold text-[#3b82f6] uppercase tracking-widest">Mudah & Cepat</span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#1e293b] mt-2 mb-3">Cara Kerja Nexora</h2>
                <p class="text-[#64748b] text-sm sm:text-base max-w-xl mx-auto">Hanya 3 langkah untuk meminjam aset sekolah dengan mudah.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 relative">
                <!-- connector line desktop -->
                <div class="hidden md:block absolute top-14 left-1/6 right-1/6 h-0.5 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] opacity-20 z-0" style="left:20%;right:20%"></div>
                @php
                $steps = [
                    ['num'=>'01','icon'=>'fas fa-sign-in-alt','title'=>'Login ke Sistem','desc'=>'Masuk menggunakan akun yang diberikan oleh admin sekolah. Sistem akan mengenali role kamu otomatis.'],
                    ['num'=>'02','icon'=>'fas fa-search','title'=>'Pilih & Ajukan Aset','desc'=>'Cari aset yang kamu butuhkan, periksa ketersediaannya, lalu ajukan peminjaman dengan mengisi formulir singkat.'],
                    ['num'=>'03','icon'=>'fas fa-check-circle','title'=>'Terima Persetujuan','desc'=>'Admin akan mereview dan menyetujui pengajuan. Kamu mendapat notifikasi langsung. Selesai!'],
                ];
                @endphp
                @foreach($steps as $s)
                <div class="text-center reveal relative z-10">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#3b82f6] to-[#2563eb] flex items-center justify-center text-white text-2xl mx-auto mb-4 shadow-lg shadow-[#3b82f6]/30">
                        <i class="{{ $s['icon'] }}"></i>
                    </div>
                    <span class="text-xs font-bold text-[#3b82f6] tracking-widest">LANGKAH {{ $s['num'] }}</span>
                    <h3 class="text-base font-bold text-[#1e293b] mt-1 mb-2">{{ $s['title'] }}</h3>
                    <p class="text-sm text-[#64748b] leading-relaxed">{{ $s['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===== ROLE SECTION ===== -->
    <section class="py-16 bg-gradient-to-br from-[#1d4ed8] to-[#3b82f6]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-12 reveal">
                <h2 class="text-2xl sm:text-3xl font-bold text-white mb-3">Tiga Role, Satu Platform</h2>
                <p class="text-white/80 text-sm max-w-lg mx-auto">Nexora mendukung tiga jenis pengguna dengan tampilan dan akses yang disesuaikan.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-5">
                @php
                $roles = [
                    ['icon'=>'fas fa-user-tie','title'=>'Admin','color'=>'border-yellow-300','badge'=>'bg-yellow-300 text-yellow-900','items'=>['Kelola data user & aset','Setujui / tolak peminjaman','Monitor semua transaksi','Atur tarif denda','Export laporan PDF']],
                    ['icon'=>'fas fa-user-graduate','title'=>'Peminjam','color'=>'border-green-300','badge'=>'bg-green-300 text-green-900','items'=>['Cari & ajukan peminjaman','Lihat status pengajuan','Riwayat peminjaman pribadi','Terima notifikasi real-time','Lihat tagihan denda']],
                    ['icon'=>'fas fa-user-cog','title'=>'Manajemen','color'=>'border-blue-200','badge'=>'bg-blue-200 text-blue-900','items'=>['Monitor semua aset','Lihat laporan rekap','Verifikasi pengembalian','Akses data statistik','Cetak laporan periode']],
                ];
                @endphp
                @foreach($roles as $r)
                <div class="bg-white/10 border border-white/20 rounded-2xl p-6 reveal hover:bg-white/15 transition-all">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center text-white text-xl">
                            <i class="{{ $r['icon'] }}"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-base">{{ $r['title'] }}</h3>
                            <span class="text-xs font-semibold {{ $r['badge'] }} px-2 py-0.5 rounded-full">Role</span>
                        </div>
                    </div>
                    <ul class="space-y-2">
                        @foreach($r['items'] as $item)
                        <li class="flex items-center gap-2 text-sm text-white/85">
                            <i class="fas fa-check-circle text-green-300 text-xs flex-shrink-0"></i>{{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===== TESTIMONI ===== -->
    <section id="testimoni" class="py-16 md:py-20 bg-[#f8fafc]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-12 reveal">
                <span class="text-xs font-semibold text-[#3b82f6] uppercase tracking-widest">Kata Mereka</span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#1e293b] mt-2">Testimoni Pengguna</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-5">
                @php
                $testimonials = [
                    ['name'=>'Pak Ahmad, S.Pd','role'=>'Kepala Sarana & Prasarana','quote'=>'Nexora benar-benar mempermudah pengelolaan aset sekolah. Tidak ada lagi catatan manual yang rawan hilang. Semua terpantau real-time!','stars'=>5],
                    ['name'=>'Reza Firmansyah','role'=>'Siswa Kelas XII RPL','quote'=>'Sekarang pinjam aset tinggal lewat HP. Cepat, mudah, dan langsung tau disetujui atau tidak. Sangat membantu buat praktik!','stars'=>5],
                    ['name'=>'Bu Dewi, Waka Humas','role'=>'Wakil Kepala Sekolah','quote'=>'Laporan peminjaman sekarang bisa langsung di-export PDF untuk keperluan rapat. Transparansi pengelolaan aset meningkat drastis.','stars'=>5],
                ];
                @endphp
                @foreach($testimonials as $t)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e2e8f0] card-hover reveal">
                    <div class="flex text-yellow-400 mb-3 text-sm">
                        @for($i=0;$i<$t['stars'];$i++) <i class="fas fa-star"></i> @endfor
                    </div>
                    <p class="text-sm text-[#475569] leading-relaxed mb-4 italic">"{{ $t['quote'] }}"</p>
                    <div class="flex items-center gap-3 border-t border-[#f1f5f9] pt-4">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#3b82f6] to-[#2563eb] flex items-center justify-center text-white font-bold text-sm">
                            {{ substr($t['name'],0,1) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[#1e293b]">{{ $t['name'] }}</p>
                            <p class="text-xs text-[#94a3b8]">{{ $t['role'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===== FAQ ===== -->
    <section id="faq" class="py-16 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-10 reveal">
                <span class="text-xs font-semibold text-[#3b82f6] uppercase tracking-widest">FAQ</span>
                <h2 class="text-2xl sm:text-3xl font-bold text-[#1e293b] mt-2">Pertanyaan Umum</h2>
            </div>
            @php
            $faqs = [
                ['q'=>'Siapa yang bisa menggunakan Nexora?','a'=>'Nexora dirancang untuk ekosistem sekolah. Admin sekolah, guru, siswa, dan staf manajemen dapat menggunakan sistem ini sesuai role masing-masing.'],
                ['q'=>'Apakah sistem ini gratis?','a'=>'Nexora dibuat sebagai proyek ujian kompetensi (ujikom) SMK. Hubungi pengembang untuk informasi lebih lanjut mengenai penggunaan di sekolah Anda.'],
                ['q'=>'Bagaimana sistem menghitung denda?','a'=>'Denda dihitung otomatis berdasarkan jumlah hari keterlambatan dikali tarif per hari yang ditetapkan admin. Admin juga bisa atur batas maksimal denda.'],
                ['q'=>'Apakah bisa diakses dari HP?','a'=>'Tentu! Interface Nexora sepenuhnya responsif dan dapat diakses dengan nyaman di smartphone, tablet, maupun komputer.'],
                ['q'=>'Bagaimana cara mendapatkan akun?','a'=>'Akun dibuat oleh Admin sekolah. Hubungi penanggung jawab lab atau sarana prasarana di sekolah Anda untuk mendapatkan akun.'],
            ];
            @endphp
            <div class="space-y-3">
                @foreach($faqs as $i => $faq)
                <div class="bg-[#f8fafc] rounded-xl border border-[#e2e8f0] overflow-hidden reveal">
                    <button onclick="toggleFaq({{ $i }})" class="w-full flex items-center justify-between px-5 py-4 text-left">
                        <span class="text-sm font-semibold text-[#1e293b]">{{ $faq['q'] }}</span>
                        <i id="faq-icon-{{ $i }}" class="fas fa-chevron-down text-[#94a3b8] text-xs transition-transform flex-shrink-0 ml-3"></i>
                    </button>
                    <div id="faq-body-{{ $i }}" class="hidden px-5 pb-4">
                        <p class="text-sm text-[#64748b] leading-relaxed">{{ $faq['a'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section class="gradient-hero py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 blob"></div>
        <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center relative z-10 reveal">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-4">Siap Digitalkan Aset Sekolahmu?</h2>
            <p class="text-white/85 text-sm sm:text-base mb-8 max-w-xl mx-auto">Bergabung dengan pengguna Nexora dan rasakan kemudahan manajemen aset sekolah yang modern.</p>
            <a href="{{ route('login') }}"
                class="inline-block px-10 py-4 bg-white text-[#2563eb] rounded-full font-bold text-base hover:-translate-y-1 hover:shadow-2xl transition-all shadow-xl">
                <i class="fas fa-rocket mr-2"></i> Mulai Gunakan Nexora
            </a>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-gradient-to-r from-[#1e3a6e] to-[#1d4ed8] text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center">
                            <i class="fas fa-cube text-white text-sm"></i>
                        </div>
                        <span class="font-bold text-lg">Nexora Smart Asset Booking</span>
                    </div>
                    <p class="text-white/70 text-sm leading-relaxed max-w-xs">
                        Platform digital peminjaman dan manajemen aset sekolah yang modern, transparan, dan efisien.
                    </p>
                    <div class="flex gap-3 mt-4">
                        <a href="#" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors"><i class="fab fa-instagram text-sm"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors"><i class="fab fa-github text-sm"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors"><i class="fas fa-envelope text-sm"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-3 text-sm">Navigasi</h4>
                    <div class="space-y-2 text-sm text-white/70">
                        <a href="#fitur" class="block hover:text-white transition-colors">Fitur</a>
                        <a href="#cara-kerja" class="block hover:text-white transition-colors">Cara Kerja</a>
                        <a href="#testimoni" class="block hover:text-white transition-colors">Testimoni</a>
                        <a href="#faq" class="block hover:text-white transition-colors">FAQ</a>
                        <a href="{{ route('login') }}" class="block hover:text-white transition-colors">Login</a>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-3 text-sm">Kontak</h4>
                    <div class="space-y-2 text-sm text-white/70">
                        <p class="flex items-center gap-2"><i class="fas fa-envelope w-4"></i> admin@nexora.sch.id</p>
                        <p class="flex items-center gap-2"><i class="fas fa-phone w-4"></i> 0812-3456-7890</p>
                        <p class="flex items-center gap-2"><i class="fas fa-map-marker-alt w-4"></i> Padalarang, Jawa Barat</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-white/10 text-center py-4 text-xs text-white/50">
            © {{ date('Y') }} Nexora Smart Asset Booking — Dikembangkan untuk Ujian Kompetensi SMK
        </div>
    </footer>

    <script>
        // FAQ toggle
        function toggleFaq(i) {
            const body = document.getElementById('faq-body-' + i);
            const icon = document.getElementById('faq-icon-' + i);
            body.classList.toggle('hidden');
            icon.style.transform = body.classList.contains('hidden') ? '' : 'rotate(180deg)';
        }

        // Mobile menu close on link click
        document.querySelectorAll('#mobileMenu a').forEach(a => {
            a.addEventListener('click', () => document.getElementById('mobileMenu').classList.remove('open'));
        });

        // Scroll reveal
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    observer.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    </script>
</body>
</html>
