<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Jika role user TIDAK ada dalam daftar role yang diizinkan rute
        if (!in_array($user->role, $roles)) {
            // Arahkan ke dashboard masing-masing agar tidak terjadi looping
            return match($user->role) {
                'admin'    => redirect()->route('admin.dashboard'),
                'peminjam' => redirect()->route('peminjam.dashboard'),
                default    => redirect()->route('home')->with('error', 'Akses ditolak.'),
            };
        }

        return $next($request);
    }
}