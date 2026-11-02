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
use App\Http\Controllers\RPController;

// Redirect root to role-based dashboard
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isStorage()
            ? redirect()->route('inventory.item')
            : redirect()->route('POS.kasir');
    }
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/user', function () {
        return redirect()->route('POS.user');
    })->name('user');

    // POS Routes: Dapat diakses Manager, Admin, dan Kasir (Storage TIDAK BISA akses POS)
    Route::prefix('POS')->middleware('role:manager,admin,kasir')->name('POS.')->group(function () {
        Route::get('kasir', [kasirController::class, 'index'])->name('kasir');
        Route::get('search-items', [kasirController::class, 'searchItems'])->name('search-items');
        Route::match(['get', 'post'], 'scan', [kasirController::class, 'scan'])->name('scan');
        Route::post('cart/sync', [kasirController::class, 'syncCart'])->name('cart.sync');
        Route::post('cart/clear', [kasirController::class, 'clearCart'])->name('cart.clear');
        Route::post('bayar', [kasirController::class, 'bayar'])->name('bayar');
        Route::post('drafts/check-sts', [kasirController::class, 'checkDraftSts'])->name('drafts.check-sts');
        Route::post('buka-kasir', [kasirController::class, 'bukaKasir'])->name('buka-kasir');
        Route::post('tutup-kasir', [kasirController::class, 'tutupKasir'])->name('tutup-kasir');
        Route::get('kas-status', [kasirController::class, 'kasStatus'])->name('kas-status');

        Route::post('/struk/print', [ACRController::class, 'print'])->name('struk.print');

        // Customer
        Route::get('customer', [CSTController::class, 'index'])->name('customer');
        // Cetak Laporan Desa: HANYA Manager dan Admin
        Route::get('customer/desa/cetak', [CSTController::class, 'cetakLaporanDesa'])
            ->middleware('role:manager,admin')
            ->name('customer.desa.cetak');
        Route::post('customer', [CSTController::class, 'store'])->name('customer.store');
        Route::put('customer/{id}', [CSTController::class, 'update'])->name('customer.update');
        Route::delete('customer/{id}', [CSTController::class, 'destroy'])->name('customer.destroy');

        // Sales
        Route::get('sales', [SLSController::class, 'index'])->name('sales');
        Route::post('sales', [SLSController::class, 'store'])->name('sales.store');
        Route::put('sales/{id}', [SLSController::class, 'update'])->name('sales.update');
        Route::delete('sales/{id}', [SLSController::class, 'destroy'])->name('sales.destroy');

        // User Management: HANYA Manager dan Admin
        Route::middleware('role:manager,admin')->group(function () {
            Route::get('user', [UserController::class, 'index'])->name('user');
            Route::post('user', [UserController::class, 'store'])->name('user.store');
            Route::put('user/{id}', [UserController::class, 'update'])->name('user.update');
            Route::delete('user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
        });

        // Riwayat Transaksi View
        Route::get('transaksi', [ACRController::class, 'index'])->name('transaksi');
        // Cetak Laporan Transaksi/Penjualan (Kasir, Admin, dan Manager)
        Route::get('transaksi/cetak', [ACRController::class, 'cetakLaporan'])->name('transaksi.cetak');
        // Edit Transaksi: HANYA Manager dan Admin
        Route::middleware('role:manager,admin')->group(function () {
            Route::get('transaksi/{id}/edit', [ACRController::class, 'edit'])->name('transaksi.edit');
            Route::put('transaksi/{id}', [ACRController::class, 'update'])->name('transaksi.update');
        });

        // Laporan View (Kasir, Admin, Manager bisa melihat & cetak laporan kas)
        Route::get('laporan', [KASController::class, 'index'])->name('laporan');
    });

    // Inventory Routes: Dapat diakses Manager, Admin, dan Storage (Kasir TIDAK BISA akses Inventory)
    Route::prefix('Inventory')->middleware('role:manager,admin,storage')->name('inventory.')->group(function () {
        Route::get('/item', [ITMController::class, 'index'])->name('item');
        Route::post('/item', [ITMController::class, 'store'])->name('item.store');
        Route::post('/item/stok', [ITMController::class, 'tambahStok'])->name('item.stok');
        Route::get('item/create', [ITMController::class, 'create'])->name('item.create');
        Route::post('item', [ITMController::class, 'store'])->name('item.store');

        // Edit Produk: HANYA Admin dan Manager (Storage TIDAK BISA edit)
        Route::middleware('role:manager,admin')->group(function () {
            Route::get('item/{id}/edit', [ITMController::class, 'edit'])->name('item.edit');
            Route::put('item/{id}', [ITMController::class, 'update'])->name('item.update');
            Route::put('/item/{id}', [ITMController::class, 'update'])->name('item.update');
        });

        Route::post('item/generate', [ITMController::class, 'generate'])->name('item.generate');
        Route::get('item/cetak',     [ITMController::class, 'cetak'])->name('item.cetak');
        Route::match(['get', 'post'], 'item/cetak-satuan', [ITMController::class, 'cetakSatuan'])->name('item.cetak.satuan');
        Route::get('item/{id}/show', [ITMController::class, 'show'])->name('item.show');

        // Hapus Produk: HANYA Admin
        Route::delete('item/{id}', [ITMController::class, 'destroy'])
            ->middleware('role:admin')
            ->name('item.destroy');

        Route::post('/item/stok-batch/selesai', [ITMController::class, 'selesaiStok'])
            ->name('item.stok.selesai');

        // Merk
        Route::get('merk', [MRKController::class, 'index'])->name('merk');
        Route::post('merk', [MRKController::class, 'store'])->name('merk.store');
        Route::put('merk/{mrk}', [MRKController::class, 'update'])->name('merk.update');
        Route::delete('merk/{mrk}', [MRKController::class, 'destroy'])->middleware('role:manager,admin')->name('merk.destroy');

        // Kategori
        Route::get('kategori', [KTGController::class, 'index'])->name('kategori');
        Route::post('kategori', [KTGController::class, 'store'])->name('kategori.store');
        Route::put('kategori/{ktg}', [KTGController::class, 'update'])->name('kategori.update');
        Route::delete('kategori/{ktg}', [KTGController::class, 'destroy'])->middleware('role:manager,admin')->name('kategori.destroy');

        // Gudang
        Route::get('gudang', [App\Http\Controllers\GDController::class, 'index'])->name('gudang');
        Route::get('gudang/create', [App\Http\Controllers\GDController::class, 'create'])->name('gudang.create');
        Route::post('gudang', [App\Http\Controllers\GDController::class, 'store'])->name('gudang.store');
        Route::put('gudang/{gd}', [App\Http\Controllers\GDController::class, 'update'])->name('gudang.update');
        Route::get('gudang/{gd}/show', [App\Http\Controllers\GDController::class, 'show'])->name('gudang.show');
        Route::get('gudang/{gd}/barang', [App\Http\Controllers\GDController::class, 'daftarBarang'])->name('gudang.daftarBarang');
        Route::delete('gudang/{gd}', [App\Http\Controllers\GDController::class, 'destroy'])->middleware('role:manager,admin')->name('gudang.destroy');

        // Perpindahan Gudang
        Route::get('perpindahan', [RPController::class, 'index'])->name('perpindahan');
        Route::post('perpindahan', [RPController::class, 'store'])->name('perpindahan.store');
        Route::get('perpindahan/check-barcode', [RPController::class, 'checkBarcode'])->name('perpindahan.check-barcode');
        Route::get('perpindahan/available-items', [RPController::class, 'getAvailableItems'])->name('perpindahan.available-items');
        Route::get('perpindahan/{id}', [RPController::class, 'show'])->name('perpindahan.show');
        Route::get('perpindahan/{id}/cetak', [RPController::class, 'cetak'])->name('perpindahan.cetak');
        Route::post('perpindahan/{id}/konfirmasi1', [RPController::class, 'konfirmasi1'])->name('perpindahan.konfirmasi1');
        Route::post('perpindahan/{id}/konfirmasi2', [RPController::class, 'konfirmasi2'])->name('perpindahan.konfirmasi2');
        Route::post('perpindahan/{id}/tolak', [RPController::class, 'tolak'])->name('perpindahan.tolak');
        Route::delete('perpindahan/{id}', [RPController::class, 'destroy'])->middleware('role:manager,admin')->name('perpindahan.destroy');

        // SPP
        Route::get('supplier', [SPPController::class, 'index'])->name('supplier');
        Route::post('supplier', [SPPController::class, 'store'])->name('supplier.store');
        Route::put('supplier/{spp}', [SPPController::class, 'update'])->name('supplier.update');
        Route::delete('supplier/{spp}', [SPPController::class, 'destroy'])->middleware('role:manager,admin')->name('supplier.destroy');

        // Cek Stok
        Route::get('/cek-stok', [OpnameStokController::class, 'index'])->name('opname');
        Route::get('/cek-stok/mulai', [OpnameStokController::class, 'pull'])->name('opname.pull');
        Route::match(['get', 'post'], '/cek-stok/cetak', [OpnameStokController::class, 'cetak'])->name('opname.cetak');
    });
});
