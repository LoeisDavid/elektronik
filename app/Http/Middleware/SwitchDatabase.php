<?php

namespace App\Http\Middleware;

use App\Services\DatabaseSwitcher;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SwitchDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasSession() && $request->session()->has('selected_database')) {
            $database = $request->session()->get('selected_database');

            if (DatabaseSwitcher::isValid($database)) {
                try {
                    DatabaseSwitcher::switch($database);
                } catch (\Throwable $e) {
                    $request->session()->forget('selected_database');

                    if (!$request->is('login', 'login/*', 'logout')) {
                        return redirect()->route('login')->withErrors([
                            'database' => 'Koneksi ke database "' . $database . '" terputus atau tidak ditemukan: ' . $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        return $next($request);
    }
}
