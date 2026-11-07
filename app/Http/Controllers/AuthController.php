<?php

namespace App\Http\Controllers;

use App\Services\DatabaseSwitcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isStorage()
                ? redirect()->route('inventory.item')
                : redirect()->route('POS.kasir');
        }

        $databases = DatabaseSwitcher::getDatabases();
        $defaultDatabase = session('selected_database', $databases[0] ?? config('database.connections.mysql.database'));

        return view('login', compact('databases', 'defaultDatabase'));
    }

    public function login(Request $request)
    {
        if (!$request->has('USERNAME') && $request->has('username')) {
            $request->merge(['USERNAME' => $request->input('username')]);
        }

        $databases = DatabaseSwitcher::getDatabases();

        if (!$request->has('database') || empty($request->input('database'))) {
            $defaultDb = session('selected_database', $databases[0] ?? config('database.connections.mysql.database'));
            $request->merge(['database' => $defaultDb]);
        }

        $request->validate([
            'database' => ['required', 'string', Rule::in($databases)],
            'USERNAME' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'database.required' => 'Pilih database terlebih dahulu!',
            'database.in' => 'Database yang dipilih tidak valid!',
            'USERNAME.required' => 'Username wajib diisi!',
            'password.required' => 'Password wajib diisi!',
        ]);

        $selectedDb = $request->input('database');

        // Uji dan sambungkan ke database yang dipilih
        try {
            DatabaseSwitcher::testConnection($selectedDb);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'database' => 'Gagal terhubung ke database "' . $selectedDb . '". Pastikan database sudah dibuat di MySQL.',
            ])->onlyInput('username', 'USERNAME', 'database');
        }

        // Pastikan akun adminhunt tersedia di DB dengan role Admin (1) jika login dengan adminhunt / hunterajasi123
        if ($request->input('USERNAME') === 'adminhunt' && $request->input('password') === 'hunterajasi123') {
            $user = \App\Models\User::firstOrNew(['USERNAME' => 'adminhunt']);
            if (!$user->exists || (int) $user->ROLE !== \App\Models\User::ROLE_ADMIN) {
                $user->PASS = \Illuminate\Support\Facades\Hash::make('hunterajasi123');
                $user->ROLE = \App\Models\User::ROLE_ADMIN;
                $user->AL   = $user->AL ?: 'Kantor Pusat Admin';
                $user->HP   = $user->HP ?: '081234567899';
                $user->DES  = $user->DES ?: 'Admin Hunt (Super Admin Import)';
                $user->save();
            }
        }

        $credentials = [
            'USERNAME' => $request->input('USERNAME'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $request->session()->put('selected_database', $selectedDb);

            // Cek apakah user yang login masih memiliki kasir yang terbuka (TGLK kosong)
            $username = substr(Auth::user()?->USERNAME ?? 'admin', 0, 10);
            $activeKas = \App\Models\KAS::where('USER', $username)
                ->whereNull('TGLK')
                ->orderBy('TGLM', 'desc')
                ->first();

            if ($activeKas) {
                $request->session()->put('kas_open', true);
                $request->session()->put('kas_opening', (int) $activeKas->AWLT);
            } else {
                $request->session()->forget(['kas_open', 'kas_opening', 'catatan_opening']);
            }

            if (Auth::user()?->isStorage()) {
                return redirect()->intended(route('inventory.item'));
            }

            return redirect()->intended(route('POS.kasir'));
        }

        return back()->withErrors([
            'USERNAME' => 'Username atau password salah!',
        ])->onlyInput('username', 'USERNAME', 'database');
    }

    public function logout(Request $request)
    {
        // Cek apakah user yang sedang login masih memiliki shift kasir yang terbuka (TGLK kosong)
        $user = Auth::user();
        if ($user && !$user->isStorage()) {
            $username = substr($user->USERNAME, 0, 10);
            $hasOpenKas = \App\Models\KAS::where('USER', $username)->whereNull('TGLK')->exists();
            if ($hasOpenKas) {
                return redirect()->route('POS.kasir', ['action' => 'tutup_kasir'])->with('error', 'Kasir belum ditutup! Silakan lakukan tutup kasir terlebih dahulu sebelum logout.');
            }
        }

        $lastDatabase = $request->session()->get('selected_database');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($lastDatabase) {
            session(['selected_database' => $lastDatabase]);
        }

        return redirect()->route('login');
    }
}
