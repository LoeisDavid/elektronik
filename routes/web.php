<?php

use App\Http\Controllers\ACRController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CSTController;
use App\Http\Controllers\ITMController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\kasirController;
use App\Http\Controllers\SLSController;
use App\Http\Controllers\STSController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KASController;
use App\Http\Controllers\SPPController;
use App\Http\Controllers\MRKController;
use App\Http\Controllers\KTGController;
use App\Http\Controllers\OpnameStokController;
use App\Http\Controllers\DSController;

// Redirect root to Kasir for preview
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/user', function () {
        return view('user');
    })->name('user');

    Route::prefix('POS')->name('POS.')->group(function () {
        Route::get('kasir', [kasirController::class, 'index'])->name('kasir');
        Route::get('search-items', [kasirController::class, 'searchItems'])->name('search-items');
        Route::match(['get', 'post'], 'scan', [kasirController::class, 'scan'])->name('scan');
        Route::post('cart/sync', [kasirController::class, 'syncCart'])->name('cart.sync');
        Route::post('cart/clear', [kasirController::class, 'clearCart'])->name('cart.clear');
        Route::post('bayar', [kasirController::class, 'bayar'])->name('bayar');
        Route::post('buka-kasir', [kasirController::class, 'bukaKasir'])->name('buka-kasir');
        Route::post('tutup-kasir', [kasirController::class, 'tutupKasir'])->name('tutup-kasir');
        Route::get('kas-status', [kasirController::class, 'kasStatus'])->name('kas-status');

        Route::post('/struk/print', [ACRController::class, 'print'])->name('struk.print');

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

        // Desa
        Route::get('desa', [DSController::class, 'index'])->name('desa');
        Route::post('desa', [DSController::class, 'store'])->name('desa.store');
        Route::put('desa/{id}', [DSController::class, 'update'])->name('desa.update');
        Route::delete('desa/{id}', [DSController::class, 'destroy'])->name('desa.destroy');

        // Riwayat Transaksi View
        Route::get('transaksi', [ACRController::class, 'index'])->name('transaksi');

        // Laporan View
        Route::get('laporan', [KASController::class, 'index'])->name('laporan');
    });

    Route::prefix('Inventory')->name('inventory.')->group(function () {
        Route::get('/item', [ITMController::class, 'index'])->name('item');
        Route::post('/item', [ITMController::class, 'store'])->name('item.store');
        Route::post('/item/stok', [ITMController::class, 'tambahStok'])->name('item.stok');
        Route::put('/item/{id}', [ITMController::class, 'update'])->name('item.update');
        Route::get('item/create', [App\Http\Controllers\ITMController::class, 'create'])->name('item.create');
        Route::post('item', [App\Http\Controllers\ITMController::class, 'store'])->name('item.store');
        Route::get('item/{id}/edit', [App\Http\Controllers\ITMController::class, 'edit'])->name('item.edit');
        Route::put('item/{id}', [App\Http\Controllers\ITMController::class, 'update'])->name('item.update');
        Route::post('item/generate', [ITMController::class, 'generate'])->name('item.generate');
        Route::get('item/cetak',     [ITMController::class, 'cetak'])->name('item.cetak');
        Route::match(['get', 'post'], 'item/cetak-satuan', [ITMController::class, 'cetakSatuan'])->name('item.cetak.satuan');
        Route::get('item/{id}/show', [App\Http\Controllers\ITMController::class, 'show'])->name('item.show');
        Route::delete('item/{id}', [App\Http\Controllers\ITMController::class, 'destroy'])->name('item.destroy');

        Route::post('/item/stok-batch/selesai', [ITMController::class, 'selesaiStok'])
            ->name('item.stok.selesai');
        // Merk
        Route::get('merk', [MRKController::class, 'index'])->name('merk');
        Route::post('merk', [MRKController::class, 'store'])->name('merk.store');
        Route::put('merk/{mrk}', [MRKController::class, 'update'])->name('merk.update');
        Route::delete('merk/{mrk}', [MRKController::class, 'destroy'])->name('merk.destroy');

        // Kategori
        Route::get('kategori', [KTGController::class, 'index'])->name('kategori');
        Route::post('kategori', [KTGController::class, 'store'])->name('kategori.store');
        Route::put('kategori/{ktg}', [KTGController::class, 'update'])->name('kategori.update');
        Route::delete('kategori/{ktg}', [KTGController::class, 'destroy'])->name('kategori.destroy');

        Route::get('gudang', [App\Http\Controllers\GDController::class, 'index'])->name('gudang');
        Route::get('gudang/create', [App\Http\Controllers\GDController::class, 'create'])->name('gudang.create');
        Route::post('gudang', [App\Http\Controllers\GDController::class, 'store'])->name('gudang.store');
        Route::put('gudang/{gd}', [App\Http\Controllers\GDController::class, 'update'])->name('gudang.update');
        Route::get('gudang/{gd}/show', [App\Http\Controllers\GDController::class, 'show'])->name('gudang.show');
        Route::get('gudang/{gd}/barang', [App\Http\Controllers\GDController::class, 'daftarBarang'])->name('gudang.daftarBarang');
        Route::delete('gudang/{gd}', [App\Http\Controllers\GDController::class, 'destroy'])->name('gudang.destroy');

        // Perpindahan Gudang — FRONTEND ONLY (backend to implement RPController)
        Route::get('perpindahan', function () {
            return view('Inventory.perpindahan');
        })->name('perpindahan');

        Route::get('kategori', [App\Http\Controllers\KTGController::class, 'index'])->name('kategori');
        Route::get('merk', [App\Http\Controllers\MRKController::class, 'index'])->name('merk');

        // SPP
        Route::get('supplier', [SPPController::class, 'index'])->name('supplier');
        Route::post('supplier', [SPPController::class, 'store'])->name('supplier.store');
        Route::put('supplier/{spp}', [SPPController::class, 'update'])->name('supplier.update');
        Route::delete('supplier/{spp}', [SPPController::class, 'destroy'])->name('supplier.destroy');

        // Cek Stok
        Route::get('/cek-stok', [OpnameStokController::class, 'index'])->name('opname');
        Route::get('/cek-stok/mulai', [OpnameStokController::class, 'pull'])->name('opname.pull');
    });
});
// User Management View
