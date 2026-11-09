<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Memeriksa apakah role pengguna yang sedang login termasuk dalam daftar role yang diperbolehkan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $roleMap = [
            'admin'   => 1,
            'kasir'   => 2,
            'storage' => 3,
            'manager' => 4,
            'import'  => 5,
        ];

        $allowedRoleIds = [];
        foreach ($roles as $r) {
            $rLower = strtolower(trim($r));
            if (isset($roleMap[$rLower])) {
                $allowedRoleIds[] = $roleMap[$rLower];
            } elseif (is_numeric($r)) {
                $allowedRoleIds[] = (int) $r;
            }
        }

        if (
            in_array((int) $user->ROLE, $allowedRoleIds, true) ||
            ($user->isAdmin() && in_array(1, $allowedRoleIds, true)) ||
            ($user->isImport() && in_array(5, $allowedRoleIds, true))
        ) {
            return $next($request);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses untuk tindakan ini.',
            ], 403);
        }

        $redirectRoute = ((int) $user->ROLE === 3) ? 'inventory.item' : 'POS.kasir';

        return redirect()->route($redirectRoute)->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
    }
}

