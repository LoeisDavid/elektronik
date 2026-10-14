<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckLogin
{
    /**
     * Handle an incoming request.
     * Logika sederhana untuk mengecek status login pengguna di seluruh halaman.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Izinkan akses rute login, proses login, logout, dan health check tanpa perlu autentikasi
        if ($request->is('login', 'login/*', 'logout', 'up')) {
            return $next($request);
        }

        // Cek jika pengguna belum login, arahkan ke halaman login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
