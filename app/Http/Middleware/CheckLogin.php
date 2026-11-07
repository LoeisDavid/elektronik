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

        $user = Auth::user();

        // Proteksi rute import-xls:
        // Halaman Export/Import dapat dibuka oleh Admin (termasuk adminhunt), namun proses POST Import hanya oleh adminhunt
        if ($request->is('import-xls', 'import-xls/*')) {
            if (!$user->canExport() && !$user->isImport()) {
                $redirectRoute = $user->isStorage() ? 'inventory.item' : 'POS.kasir';
                return redirect()->route($redirectRoute)->with('error', 'Anda tidak memiliki hak akses ke halaman Import / Export.');
            }

            if ($request->isMethod('post') && $request->is('import-xls') && !$user->isImport()) {
                return redirect()->route('import.xls')->with('error', 'Fitur Import hanya dapat dilakukan oleh akun khusus adminhunt.');
            }
        }

        return $next($request);
    }
}
