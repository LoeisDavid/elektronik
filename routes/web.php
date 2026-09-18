<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Redirect root to Kasir for preview
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Standalone Login Page
Route::get('/login', function () {
    return view('login');
})->name('login');

// Point of Sales / Kasir View
Route::get('/kasir', function () {
    return view('kasir');
})->name('kasir');

// Riwayat Transaksi View
Route::get('/transaksi', function () {
    return view('transaksi');
})->name('transaksi');

// User Management View
Route::get('/user', function () {
    return view('user');
})->name('user');

// Sales Management View
Route::get('/sales', function () {
    return view('sales');
})->name('sales');

// Customer Management View
Route::get('/customer', function () {
    return view('customer');
})->name('customer');

// Laporan Kasir / Transaksi View
Route::get('/laporan', function () {
    return view('laporan');
})->name('laporan');





