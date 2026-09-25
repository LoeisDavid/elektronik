<?php

use App\Http\Controllers\ACRController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CSTController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\kasirController;
use App\Http\Controllers\SLSController;
use App\Http\Controllers\STSController;

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

// Riwayat Transaksi View
Route::get('/transaksi', [ACRController::class, 'index'])->name('transaksi');
Route::post('/transaksi', [ACRController::class, 'store'])->name('transaksi.store');
Route::put('/transaksi/{id}', [ACRController::class, 'update'])->name('transaksi.update');
Route::delete('/transaksi/{id}', [ACRController::class, 'destroy'])->name('transaksi.destroy');

// User Management View
Route::get('/user', function () {
    return view('user');
})->name('user');

// Laporan Kasir / Transaksi View
Route::get('/laporan', function () {
    return view('laporan');
})->name('laporan');

Route::prefix('POS')->name('POS.')->group(function () {
    Route::get('kasir', [kasirController::class, 'index'])->name('kasir');
    Route::match(['get', 'post'], 'scan', [kasirController::class, 'scan'])->name('scan');
    Route::post('cart/sync', [kasirController::class, 'syncCart'])->name('cart.sync');
    Route::post('cart/clear', [kasirController::class, 'clearCart'])->name('cart.clear');
    Route::post('bayar', [kasirController::class, 'bayar'])->name('bayar');
    Route::post('buka-kasir', [kasirController::class, 'bukaKasir'])->name('buka-kasir');
    Route::post('tutup-kasir', [kasirController::class, 'tutupKasir'])->name('tutup-kasir');
    Route::get('kas-status', [kasirController::class, 'kasStatus'])->name('kas-status');

    // customer
    Route::get('customer', [CSTController::class, 'index'])->name('customer');
    Route::post('customer', [CSTController::class, 'store'])->name('customer.store');
    Route::put('customer/{id}', [CSTController::class, 'update'])->name('customer.update');
    Route::delete('customer/{id}', [CSTController::class, 'destroy'])->name('customer.destroy');

    // sales
    Route::get('sales', [SLSController::class, 'index'])->name('sales');
    Route::post('sales', [SLSController::class, 'store'])->name('sales.store');
    Route::put('sales/{id}', [SLSController::class, 'update'])->name('sales.update');
    Route::delete('sales/{id}', [SLSController::class, 'destroy'])->name('sales.destroy');
});




