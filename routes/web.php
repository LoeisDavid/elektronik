<?php

use App\Http\Controllers\ACRController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CSTController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\kasirController;
use App\Http\Controllers\SLSController;
use App\Http\Controllers\STSController;
use App\Http\Controllers\UserController;

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

    // User
    Route::get('user', [UserController::class, 'index'])->name('user');
    Route::post('user', [UserController::class, 'store'])->name('user.store');
    Route::put('user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('user/{id}', [UserController::class, 'destroy'])->name('user.destroy');

    // Riwayat Transaksi View
    Route::get('transaksi', [ACRController::class, 'index'])->name('transaksi');
});

Route::get('/opname-stok', [App\Http\Controllers\OpnameStokController::class, 'index'])->name('opname-stok');
Route::prefix('Inventory')->name('inventory.')->group(function () {
    Route::get('item', [App\Http\Controllers\ITMController::class, 'index'])->name('item');
    Route::get('item/create', [App\Http\Controllers\ITMController::class, 'create'])->name('item.create');
    Route::post('item', [App\Http\Controllers\ITMController::class, 'store'])->name('item.store');
    Route::get('item/{id}/edit', [App\Http\Controllers\ITMController::class, 'edit'])->name('item.edit');
    Route::put('item/{id}', [App\Http\Controllers\ITMController::class, 'update'])->name('item.update');
    Route::delete('item/{id}', [App\Http\Controllers\ITMController::class, 'destroy'])->name('item.destroy');

    Route::get('gudang', [App\Http\Controllers\GDController::class, 'index'])->name('gudang');
    Route::get('gudang/create', [App\Http\Controllers\GDController::class, 'create'])->name('gudang.create');
    Route::post('gudang', [App\Http\Controllers\GDController::class, 'store'])->name('gudang.store');
    Route::get('gudang/{gd}/edit', [App\Http\Controllers\GDController::class, 'edit'])->name('gudang.edit');
    Route::put('gudang/{gd}', [App\Http\Controllers\GDController::class, 'update'])->name('gudang.update');
    Route::delete('gudang/{gd}', [App\Http\Controllers\GDController::class, 'destroy'])->name('gudang.destroy');

    Route::get('kategori', [App\Http\Controllers\KTGController::class, 'index'])->name('kategori');
    Route::get('merk', [App\Http\Controllers\MRKController::class, 'index'])->name('merk');
    Route::get('supplier', [App\Http\Controllers\SPPController::class, 'index'])->name('supplier');
});



