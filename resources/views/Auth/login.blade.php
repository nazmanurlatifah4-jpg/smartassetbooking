<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Nexora Smart Asset Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes floatUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes shimmer {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }
        .animate-float { animation: floatUp 0.6s ease forwards; }
        .animate-float-delay { animation: floatUp 0.6s ease 0.2s forwards; opacity: 0; }
        .dot-pulse { animation: shimmer 2s infinite; }
        .input-line:focus { border-color: #3b82f6; }

        /* Toggle password eye */
        .eye-toggle { cursor: pointer; user-select: none; }
    </style>
</head>
<body class="font-['Segoe_UI',Tahoma,Geneva,Verdana,sans-serif] bg-[#b6bdd3] flex items-center justify-center min-h-screen p-4 sm:p-5">

    @if ($errors->any())
        <div class="fixed top-4 right-4 z-50 bg-white border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3 shadow-lg flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white rounded-3xl overflow-hidden shadow-[0_20px_60px_rgba(95,131,228,0.3)] flex flex-col md:flex-row w-full max-w-[900px]">

        <!-- Left Panel -->
        <div class="md:flex md:w-1/2 bg-gradient-to-br from-[#547baf] via-[#3b82f6] to-[#2563eb] flex-col items-center justify-center p-8 md:p-10 min-h-[220px] md:min-h-[520px] relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute top-[-30px] left-[-30px] w-24 h-24 rounded-full bg-white/10"></div>
            <div class="absolute bottom-[-20px] right-[-20px] w-36 h-36 rounded-full bg-white/10"></div>
            <div class="absolute top-1/2 left-[-15px] w-12 h-12 rounded-full bg-white/5"></div>

            <img src="{{ asset('assets/icon-login1.png') }}" alt="Login Illustration"
                class="w-1/2 md:w-[45%] h-auto object-contain mb-6 md:mb-8 mx-auto drop-shadow-xl animate-float z-10">

            <div class="text-center text-white z-10 animate-float-delay">
                <h1 class="text-2xl md:text-4xl font-bold mb-2 drop-shadow-md">Ayo Login!</h1>
                <p class="text-sm md:text-base opacity-90 leading-relaxed px-2 md:px-0">
                    Kelola aset sekolah dengan <span class="font-semibold">mudah</span> dan <span class="font-semibold">efisien</span>
                </p>
                <div class="flex gap-2 justify-center mt-4">
                    <span class="dot-pulse w-2 h-2 bg-white/70 rounded-full inline-block"></span>
                    <span class="dot-pulse w-2 h-2 bg-white/50 rounded-full inline-block" style="animation-delay:0.3s"></span>
                    <span class="dot-pulse w-2 h-2 bg-white/30 rounded-full inline-block" style="animation-delay:0.6s"></span>
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="w-full md:w-1/2 p-8 sm:p-10 md:p-12 lg:p-14 flex flex-col justify-center">
            <!-- Logo -->
            <div class="text-center mb-8 md:mb-10">
                <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-3 sm:mb-4 rounded-2xl bg-[#eff6ff] flex items-center justify-center shadow-inner">
                    <img src="{{ asset('assets/logo-removebg-preview.png') }}" alt="Nexora Logo" class="w-full h-full object-contain p-2">
                </div>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#2563eb] tracking-tight">LOGIN</h2>
                <p class="text-xs sm:text-sm text-[#94a3b8] mt-1">Masuk ke panel administrasi Nexora</p>
            </div>

            @if (session('status'))
                <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg px-3 py-2 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5 md:space-y-6" autocomplete="off">
    @csrf

    <input type="text" style="display:none" name="prevent_autofill_email">
    <input type="password" style="display:none" name="prevent_autofill_password">

    <div>
        <label class="block text-xs font-semibold text-[#64748b] mb-1.5 uppercase tracking-wider">Email Address</label>
        <div class="relative">
            <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-[#94a3b8] text-sm"></i>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="admin@nexora.sch.id"
                required autofocus
                autocomplete="off" {{-- Tambahkan ini --}}
                class="input-line w-full pl-10 pr-4 py-3 border-b-2 border-[#e2e8f0] text-sm text-[#1e293b] focus:outline-none transition-colors bg-transparent placeholder-[#cbd5e1] @error('email') border-red-400 @enderror"
            >
        </div>
        @error('email')
            <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold text-[#64748b] mb-1.5 uppercase tracking-wider">Password</label>
        <div class="relative">
            <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-[#94a3b8] text-sm"></i>
            <input
                type="password"
                name="password"
                id="passwordInput"
                placeholder="••••••••"
                required
                autocomplete="new-password" {{-- Gunakan new-password agar browser tidak menarik data lama --}}
                class="input-line w-full pl-10 pr-10 py-3 border-b-2 border-[#e2e8f0] text-sm text-[#1e293b] focus:outline-none transition-colors bg-transparent placeholder-[#cbd5e1] @error('password') border-red-400 @enderror"
            >
            <span onclick="togglePassword()" class="eye-toggle absolute right-3 top-1/2 -translate-y-1/2 text-[#94a3b8] hover:text-[#3b82f6] text-sm transition-colors cursor-pointer">
                <i id="eyeIcon" class="fas fa-eye"></i>
            </span>
        </div>
        @error('password')
            <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center justify-between text-xs">
        <label class="flex items-center gap-2 text-[#64748b] cursor-pointer">
            <input type="checkbox" name="remember" class="rounded border-[#e2e8f0] text-[#3b82f6] focus:ring-[#3b82f6]">
            Ingat saya
        </label>
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-[#3b82f6] hover:underline">Lupa password?</a>
        @endif
    </div>

    <button
        type="submit"
        class="w-full py-3 sm:py-3.5 bg-gradient-to-r from-[#547baf] via-[#3b82f6] to-[#2563eb] text-white rounded-full text-sm sm:text-base font-semibold shadow-[0_4px_15px_rgba(59,130,246,0.35)] hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(59,130,246,0.4)] active:scale-[0.98] transition-all duration-200"
    >
        <i class="fas fa-sign-in-alt mr-2"></i> MASUK SEKARANG
    </button>
</form>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('passwordInput');
    const eyeIcon = document.getElementById('eyeIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
            <!-- Footer Note -->
            <p class="text-center text-[10px] text-[#94a3b8] mt-6">
                Sistem ini hanya untuk personel yang berwenang. <br>
                &copy; {{ date('Y') }} Nexora Smart Asset Booking
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>
