<?php

use Illuminate\Support\Facades\Route;

// Redirect root to Kasir for preview
Route::get('/', function () {
    return redirect()->route('kasir');
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





