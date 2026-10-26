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
            return redirect()->route('POS.kasir');
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

        $credentials = [
            'USERNAME' => $request->input('USERNAME'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $request->session()->put('selected_database', $selectedDb);

            return redirect()->intended(route('POS.kasir'));
        }

        return back()->withErrors([
            'USERNAME' => 'Username atau password salah!',
        ])->onlyInput('username', 'USERNAME', 'database');
    }

    public function logout(Request $request)
    {
        // Cek apakah ada shift kasir yang masih aktif / belum ditutup
        if (\App\Models\KAS::whereNull('TGLK')->exists()) {
            return redirect()->route('POS.kasir', ['action' => 'tutup_kasir'])->with('error', 'Kasir belum ditutup! Silakan lakukan tutup kasir terlebih dahulu sebelum logout.');
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
