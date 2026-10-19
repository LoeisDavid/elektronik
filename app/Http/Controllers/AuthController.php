<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('POS.kasir');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        if (!$request->has('USERNAME') && $request->has('username')) {
            $request->merge(['USERNAME' => $request->input('username')]);
        }

        $credentials = $request->validate([
            'USERNAME' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('POS.kasir'));
        }

        return back()->withErrors([
            'USERNAME' => 'Username atau password salah!',
        ])->onlyInput('USERNAME');
    }

    public function logout(Request $request)
    {
        // Cek apakah ada shift kasir yang masih aktif / belum ditutup
        if (\App\Models\KAS::whereNull('TGLK')->exists()) {
            return redirect()->route('POS.kasir', ['action' => 'tutup_kasir'])->with('error', 'Kasir belum ditutup! Silakan lakukan tutup kasir terlebih dahulu sebelum logout.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
