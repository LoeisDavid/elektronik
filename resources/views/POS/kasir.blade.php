@extends('layouts.app')

@section('title', 'Point Of Sales - Kasir')
@section('header_title', 'Point Of Sales')

@section('content')
{{-- ========================================================================= --}}
{{-- KONTEN UTAMA HALAMAN KASIR (POINT OF SALES)                              --}}
{{-- ========================================================================= --}}
<div class="flex h-full w-full overflow-hidden relative" id="posContainer">

    {{-- ===================================================================== --}}
    {{-- BAGIAN TENGAH: KATALOG PRODUK & PENCARIAN                             --}}
    {{-- ===================================================================== --}}
    <div class="flex-1 flex flex-col h-full overflow-hidden bg-[#CECECE]/60">

        {{-- ----------------------------------------------------------------- --}}
        {{-- 1. TOOLBAR ATAS (PENCARIAN, FILTER, DAN TOMBOL SCAN BARCODE)      --}}
        {{-- ----------------------------------------------------------------- --}}
        <div class="p-4 pb-2 flex items-center justify-between gap-3 flex-shrink-0 z-10">
            
            {{-- Kiri: Input Pencarian & Tombol Filter --}}
            <div class="flex items-center gap-2 flex-1 max-w-md">
                
                {{-- Input Pencarian Produk --}}
                <div class="relative flex-1 group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                        {{-- Icon Search / Kaca Pembesar --}}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        id="searchInput"
                        onkeyup="filterProdukKatalog()"
                        placeholder="Cari produk"
                        class="w-full bg-white border border-[#E2E8F0] rounded-lg pl-10 pr-4 py-2 text-sm text-[#2D3748] placeholder-gray-400 focus:outline-none focus:border-[#2D8CFF] focus:ring-1 focus:ring-[#2D8CFF] shadow-xs transition-all" />
                </div>

                {{-- Tombol Filter / Urutkan --}}
                <button
                    type="button"
                    class="h-9 w-9 bg-white border border-[#E2E8F0] hover:bg-gray-50 rounded-lg flex items-center justify-center text-gray-700 shadow-xs cursor-pointer transition-colors"
                    title="Filter Produk">
                    {{-- Icon Filter / Corong --}}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.539.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.378-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                    </svg>
                </button>
            </div>

            {{-- Kanan: Tombol Buka Modal Scan Barcode --}}
            <button
                type="button"
                onclick="bukaModalBarcode()"
                class="h-9 px-3 bg-white border border-[#E2E8F0] hover:bg-gray-50 text-[#2D3748] rounded-lg flex items-center justify-center shadow-xs cursor-pointer transition-colors"
                title="Scan Barcode">
                {{-- Icon Barcode --}}
                <svg class="w-5 h-5 text-current" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M2 4h2v16H2V4zm3 0h1v16H5V4zm2 0h2v16H7V4zm3 0h1v16h-1V4zm2 0h3v16h-3V4zm4 0h1v16h-1V4zm2 0h2v16h-2V4zm3 0h1v16h-1V4z" />
                </svg>
            </button>
        </div>

        {{-- ----------------------------------------------------------------- --}}
        {{-- 2. GRID DAFTAR PRODUK (KATALOG - 2 ITEM DUMMY)                   --}}
        {{-- ----------------------------------------------------------------- --}}
        <div class="flex-1 overflow-y-auto p-4 pt-1">
            <div id="catalogGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5">
                
                {{-- ================= PRODUK ITEM 1 ================= --}}
                <div 
                    onclick="tambahProdukKatalogKeCart('item_airpods', 'AIRPODS01', 'Airpods pro 3', 'Deskripsi', 3400000)"
                    class="katalog-item bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md cursor-pointer border border-transparent hover:border-[#2D8CFF] transition-all flex flex-col group active:scale-[0.99]"
                    data-name="airpods pro 3 deskripsi">
                    {{-- Gambar Produk (Kotak Abu-Abu) --}}
                    <div class="h-44 bg-[#C5C8CC] relative flex items-center justify-center overflow-hidden p-3">
                        <img
                            src="https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?w=400&auto=format&fit=crop&q=80"
                            alt="Airpods pro 3"
                            class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-200"
                            onerror="this.src='https://via.placeholder.com/200x200?text=Airpods';" />
                    </div>

                    {{-- Informasi Produk --}}
                    <div class="p-3.5 bg-white flex flex-col flex-1 justify-between min-h-[90px]">
                        <div>
                            {{-- Nama Produk --}}
                            <h3 class="font-semibold text-sm text-[#1A1F36] leading-tight truncate">Airpods pro 3</h3>
                            {{-- Deskripsi Produk --}}
                            <p class="text-[11px] text-[#6B7280] truncate mt-0.5">Deskripsi</p>
                        </div>
                        {{-- Harga Produk --}}
                        <div class="mt-3 pt-1">
                            <span class="font-bold text-sm text-[#1A1F36] font-number">Rp 3.400.000</span>
                        </div>
                    </div>
                </div>

                {{-- ================= PRODUK ITEM 2 ================= --}}
                <div 
                    onclick="tambahProdukKatalogKeCart('item_asus_tuf', 'ASUSTUF01', 'Asus TUF A16', 'RTX 5070 Ryzen 7 260', 3400000)"
                    class="katalog-item bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md cursor-pointer border border-transparent hover:border-[#2D8CFF] transition-all flex flex-col group active:scale-[0.99]"
                    data-name="asus tuf a16 rtx 5070 ryzen 7 260">
                    {{-- Gambar Produk (Kotak Abu-Abu) --}}
                    <div class="h-44 bg-[#C5C8CC] relative flex items-center justify-center overflow-hidden p-3">
                        <img
                            src="https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400&auto=format&fit=crop&q=80"
                            alt="Asus TUF A16"
                            class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-200"
                            onerror="this.src='https://via.placeholder.com/200x200?text=Asus+TUF';" />
                    </div>

                    {{-- Informasi Produk --}}
                    <div class="p-3.5 bg-white flex flex-col flex-1 justify-between min-h-[90px]">
                        <div>
                            {{-- Nama Produk --}}
                            <h3 class="font-semibold text-sm text-[#1A1F36] leading-tight truncate">Asus TUF A16</h3>
                            {{-- Deskripsi Produk --}}
                            <p class="text-[11px] text-[#6B7280] truncate mt-0.5">RTX 5070 Ryzen 7 260</p>
                        </div>
                        {{-- Harga Produk --}}
                        <div class="mt-3 pt-1">
                            <span class="font-bold text-sm text-[#1A1F36] font-number">Rp 3.400.000</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- ===================================================================== --}}
    {{-- BAGIAN KANAN: PANEL ORDER / KERANJANG BELANJA (DARI SESSION & JS)     --}}
    {{-- ===================================================================== --}}
    <div class="w-80 lg:w-96 flex-shrink-0 bg-white border-l border-[#E2E8F0] flex flex-col h-full shadow-lg z-10">

        {{-- ----------------------------------------------------------------- --}}
        {{-- 1. HEADER ORDER & TOMBOL RESET KERANJANG                         --}}
        {{-- ----------------------------------------------------------------- --}}
        <div class="outline outline-1 outline-black border-y border-black p-4 pb-2 flex items-center justify-between">
            <h2 class="text-xl font-bold text-[#1A1F36] tracking-tight">Order</h2>
            
            {{-- Tombol Kosongkan Keranjang --}}
            <button 
                type="button" 
                onclick="resetKeranjang()" 
                class="text-xs font-semibold text-gray-500 hover:text-red-600 transition-colors cursor-pointer"
                title="Kosongkan Semua Item">
                Reset
            </button>
        </div>

        {{-- ----------------------------------------------------------------- --}}
        {{-- 2. DAFTAR ITEM KERANJANG (DIRENDER OTOMATIS OLEH JAVASCRIPT)      --}}
        {{-- ----------------------------------------------------------------- --}}
        <div id="cartItemsList" class="flex-1 overflow-y-auto outline outline-1 outline-black border-y border-black px-0 py-0 divide-y divide-black">
            {{-- Daftar item di-generate secara dinamis oleh fungsi renderCart() --}}
        </div>

        {{-- ----------------------------------------------------------------- --}}
        {{-- 3. RINGKASAN PEMBAYARAN (SUBTOTAL, VOUCHER, DISKON, TOTAL)         --}}
        {{-- ----------------------------------------------------------------- --}}
        <div class="border border-black outline outline-1 outline-black p-4 bg-white space-y-2">

            {{-- Subtotal --}}
            <div class="flex items-center justify-between text-sm">
                <span class="text-[#2D3748] font-medium">Subtotal</span>
                <span id="labelSubtotal" class="font-bold text-[#1A1F36] font-number">Rp 0</span>
            </div>

            {{-- Voucher (Dapat Diedit dengan Mengklik) --}}
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-1.5">
                    <span class="text-[#2D3748] font-medium">Voucher</span>
                    <button
                        type="button"
                        onclick="bukaModalVoucher()"
                        class="text-[11px] text-[#2D8CFF] hover:underline cursor-pointer"
                        title="Ubah Nominal Voucher">
                        (edit)
                    </button>
                </div>
                <span 
                    id="labelVoucher" 
                    onclick="bukaModalVoucher()"
                    class="font-bold text-[#1A1F36] font-number cursor-pointer hover:text-[#2D8CFF] transition-colors">
                    Rp 0
                </span>
            </div>

            {{-- Diskon Transaksi (Dapat Diedit dengan Mengklik) --}}
            <div class="flex flex-col text-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[#2D3748] font-medium">Diskon Transaksi</span>
                        <button
                            type="button"
                            onclick="bukaModalDiskonTransaksi()"
                            class="text-[11px] text-[#2D8CFF] hover:underline cursor-pointer"
                            title="Ubah Persen Diskon">
                            (edit)
                        </button>
                    </div>
                    <span 
                        id="labelDiskonPersen" 
                        onclick="bukaModalDiskonTransaksi()"
                        class="font-bold text-[#1A1F36] font-number cursor-pointer hover:text-[#2D8CFF] transition-colors">
                        0%
                    </span>
                </div>
                <div class="text-right">
                    <span id="labelDiskonNominal" class="text-xs text-[#6B7280] font-number">-Rp 0</span>
                </div>
            </div>

            {{-- Garis Pemisah --}}
            <hr class="border-[#E2E8F0] my-2" />

            {{-- Total Akhir --}}
            <div class="flex items-center justify-between text-base pt-0.5">
                <span class="text-xl font-bold text-[#1A1F36]">Total</span>
                <span id="labelGrandTotal" class="text-xl font-bold text-[#1A1F36] font-number">Rp 0</span>
            </div>

            {{-- Tombol Pembayaran (Full Width Hijau) --}}
            <div class="pt-3">
                <button
                    type="button"
                    onclick="prosesPembayaran()"
                    class="w-full bg-[#34D399] hover:bg-[#2ecc71] active:bg-[#27ae60] text-white text-lg font-bold py-3 rounded-none shadow-sm transition-all cursor-pointer flex items-center justify-center tracking-wide">
                    Pembayaran
                </button>
            </div>

        </div>

    </div>

    {{-- ===================================================================== --}}
    {{-- MODAL 1: POPUP SCAN BARCODE (SUBMIT DENGAN REFRESH & SESSION)         --}}
    {{-- ===================================================================== --}}
    <div
        id="modalBarcode"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
        
        {{-- Card Modal --}}
        <div class="w-full max-w-sm bg-[#F7F8FC] rounded-2xl p-5 shadow-2xl space-y-4 border border-[#E2E8F0]">
            
            {{-- Judul Modal --}}
            <h3 class="text-xl font-bold text-[#1A1F36] tracking-tight">Scan Barcode</h3>

            {{-- Area Kamera Scanner (Kotak Abu-Abu Gelap) --}}
            <div class="w-full h-52 bg-[#596172] rounded-xl relative overflow-hidden flex flex-col items-center justify-center text-white shadow-inner">
                {{-- Garis Animasi Laser Merah --}}
                <div class="w-full h-0.5 bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.9)] animate-pulse"></div>
                <span class="text-xs text-white/70 mt-2 font-medium">Arahkan barcode ke kamera</span>
            </div>

            {{-- Form Input Manual Kode Barcode (Mengirim POST ke Backend, Refresh & Simpan ke Session) --}}
            <form action="{{ route('POS.scan') }}" method="POST">
                @csrf
                <div class="flex items-center gap-2">
                    <input
                        type="text"
                        name="BC_OUT"
                        id="inputManualBarcode"
                        placeholder="Atau ketik kode manual..."
                        required
                        class="flex-1 bg-white border border-[#2D3748] rounded-lg px-3.5 py-2.5 text-sm text-[#1A1F36] placeholder-gray-500 outline-none focus:ring-1 focus:ring-[#2D8CFF]" />
                    
                    <button
                        type="submit"
                        class="bg-[#242F40] hover:bg-[#1A1F36] active:bg-black text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors cursor-pointer">
                        Cari
                    </button>
                </div>
            </form>

            {{-- Tombol Batal --}}
            <div>
                <button
                    type="button"
                    onclick="tutupModalBarcode()"
                    class="w-full bg-white hover:bg-gray-50 active:bg-gray-100 border border-[#2D3748] rounded-lg py-2.5 text-center text-sm font-semibold text-[#1A1F36] transition-colors cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- MODAL 2: UBAH DISKON ITEM (%)                                         --}}
    {{-- ===================================================================== --}}
    <div
        id="modalDiskonItem"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
        <div class="w-full max-w-sm bg-[#F7F8FC] rounded-2xl p-6 shadow-2xl space-y-4 border border-[#E2E8F0]">
            <h3 class="text-xl font-bold text-[#1A1F36] tracking-tight">Masukkan Diskon Item (%)</h3>
            <input
                type="number"
                id="inputDiskonItem"
                min="0"
                max="100"
                placeholder="Contoh: 10"
                class="w-full bg-white border border-[#2D3748] rounded-lg px-4 py-2.5 text-sm text-[#1A1F36] outline-none focus:ring-1 focus:ring-[#2D8CFF]" />
            <div class="space-y-2 pt-2">
                <button
                    type="button"
                    onclick="simpanDiskonItem()"
                    class="w-full bg-[#2D8CFF] hover:bg-blue-600 text-white rounded-lg py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer">
                    Terapkan Diskon
                </button>
                <button
                    type="button"
                    onclick="tutupModalDiskonItem()"
                    class="w-full bg-white hover:bg-gray-50 border border-[#2D3748] rounded-lg py-2.5 text-center text-sm font-semibold text-[#1A1F36] transition-colors cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- MODAL 3: UBAH VOUCHER (NOMINAL RP)                                    --}}
    {{-- ===================================================================== --}}
    <div
        id="modalVoucher"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
        <div class="w-full max-w-sm bg-[#F7F8FC] rounded-2xl p-6 shadow-2xl space-y-4 border border-[#E2E8F0]">
            <h3 class="text-xl font-bold text-[#1A1F36] tracking-tight">Masukkan Nominal Voucher</h3>
            <input
                type="number"
                id="inputVoucher"
                min="0"
                step="1000"
                placeholder="Contoh: 10000000"
                class="w-full bg-white border border-[#2D3748] rounded-lg px-4 py-2.5 text-sm text-[#1A1F36] outline-none focus:ring-1 focus:ring-[#2D8CFF]" />
            <div class="space-y-2 pt-2">
                <button
                    type="button"
                    onclick="simpanVoucher()"
                    class="w-full bg-[#2D8CFF] hover:bg-blue-600 text-white rounded-lg py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer">
                    Terapkan Voucher
                </button>
                <button
                    type="button"
                    onclick="tutupModalVoucher()"
                    class="w-full bg-white hover:bg-gray-50 border border-[#2D3748] rounded-lg py-2.5 text-center text-sm font-semibold text-[#1A1F36] transition-colors cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- MODAL 4: UBAH DISKON TRANSAKSI (%)                                    --}}
    {{-- ===================================================================== --}}
    <div
        id="modalDiskonTransaksi"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
        <div class="w-full max-w-sm bg-[#F7F8FC] rounded-2xl p-6 shadow-2xl space-y-4 border border-[#E2E8F0]">
            <h3 class="text-xl font-bold text-[#1A1F36] tracking-tight">Diskon Transaksi (%)</h3>
            <input
                type="number"
                id="inputDiskonTransaksi"
                min="0"
                max="100"
                placeholder="Contoh: 20"
                class="w-full bg-white border border-[#2D3748] rounded-lg px-4 py-2.5 text-sm text-[#1A1F36] outline-none focus:ring-1 focus:ring-[#2D8CFF]" />
            <div class="space-y-2 pt-2">
                <button
                    type="button"
                    onclick="simpanDiskonTransaksi()"
                    class="w-full bg-[#2D8CFF] hover:bg-blue-600 text-white rounded-lg py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer">
                    Terapkan Diskon Transaksi
                </button>
                <button
                    type="button"
                    onclick="tutupModalDiskonTransaksi()"
                    class="w-full bg-white hover:bg-gray-50 border border-[#2D3748] rounded-lg py-2.5 text-center text-sm font-semibold text-[#1A1F36] transition-colors cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>

</div>

{{-- ========================================================================= --}}
{{-- JAVASCRIPT: SEMUA FUNGSI LOGIKA KERANJANG, TOTAL, DAN MODAL              --}}
{{-- ========================================================================= --}}
@push('scripts')
<script>
    // -------------------------------------------------------------------------
    // 1. DATA KERANJANG DARI SESSION LARAVEL (PERSISTEN SAAT DIREFRESH)
    // -------------------------------------------------------------------------
    let cartData = @json($cart ?? []);
    let voucherAmount = {{ (float) ($voucher ?? 10000000) }};
    let discountPercent = {{ (float) ($discountPercent ?? 20) }};
    let activeItemKeyForDiscount = null;

    // Pastikan jika cartData bukan object/array, inisialisasi sebagai object kosong
    if (!cartData || typeof cartData !== 'object') {
        cartData = {};
    }

    // CSRF Token untuk request AJAX
    const csrfToken = '{{ csrf_token() }}';

    // -------------------------------------------------------------------------
    // 2. FUNGSI RENDER TAMPILAN KERANJANG DI PANEL KANAN
    // -------------------------------------------------------------------------
    function renderCart() {
        const container = document.getElementById('cartItemsList');
        if (!container) return;

        const keys = Object.keys(cartData);

        // Jika keranjang kosong
        if (keys.length === 0) {
            container.innerHTML = `
                <div class="p-8 text-center text-gray-900">
                    <p class="text-sm font-medium">Keranjang masih kosong</p>
                    <p class="text-xs mt-1">Scan barcode atau pilih produk di sebelah kiri</p>
                </div>
            `;
            hitungRingkasan();
            return;
        }

        let html = '';

        // Looping setiap item di dalam cartData
        keys.forEach(key => {
            const item = cartData[key];
            const itemDiscount = item.discount || 0;
            const priceAfterDiscount = item.price - (item.price * (itemDiscount / 100));
            const itemTotal = priceAfterDiscount * item.qty;

            html += `
                <div class="px-4 py-3.5 bg-white w-full group transition-colors">
                    {{-- Baris Atas: Nama Produk, Deskripsi, Tombol Diskon (%) & Hapus (X) --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline gap-1.5 flex-wrap">
                                <span class="font-semibold text-sm text-[#1A1F36]">${escapeHtml(item.name)}</span>
                                <span class="text-xs text-[#6B7280] truncate">${escapeHtml(item.description || '')}</span>
                                ${itemDiscount > 0 ? `<span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded font-bold">Diskon ${itemDiscount}%</span>` : ''}
                            </div>
                        </div>
                        
                        {{-- Tombol Aksi: Diskon Item (%) dan Hapus Item (X) --}}
                        <div class="flex items-center gap-2 flex-shrink-0 text-xs font-semibold text-gray-600">
                            <button 
                                type="button" 
                                onclick="bukaModalDiskonItem('${key}')" 
                                class="hover:text-black transition-colors cursor-pointer px-1 py-0.5 rounded hover:bg-gray-100" 
                                title="Beri Diskon Item">
                                %
                            </button>
                            <button 
                                type="button" 
                                onclick="hapusItem('${key}')" 
                                class="hover:text-red-600 transition-colors cursor-pointer px-1 py-0.5 rounded hover:bg-red-50" 
                                title="Hapus Item">
                                X
                            </button>
                        </div>
                    </div>

                    {{-- Baris Bawah: Pengatur Jumlah (Quantity Stepper) & Total Harga Item --}}
                    <div class="mt-2.5 flex items-center justify-between">
                        {{-- Stepper Qty: [-] [qty] [+] --}}
                        <div class="flex items-center gap-1">
                            <button 
                                type="button" 
                                onclick="kurangQty('${key}')" 
                                class="w-6 h-6 border border-gray-400 rounded flex items-center justify-center text-xs font-bold text-gray-700 hover:bg-gray-200 transition-colors cursor-pointer">
                                -
                            </button>
                            <span class="w-6 text-center text-sm font-semibold text-[#1A1F36] font-number">${item.qty}</span>
                            <button 
                                type="button" 
                                onclick="tambahQty('${key}')" 
                                class="w-6 h-6 border border-gray-400 rounded flex items-center justify-center text-xs font-bold text-gray-700 hover:bg-gray-200 transition-colors cursor-pointer">
                                +
                            </button>
                        </div>

                        {{-- Harga Total Per Item --}}
                        <div class="text-right">
                            <span class="font-bold text-sm text-[#1A1F36] font-number">${formatRupiah(itemTotal)}</span>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        hitungRingkasan();
    }

    // -------------------------------------------------------------------------
    // 3. FUNGSI PERHITUNGAN TOTAL, VOUCHER, DAN DISKON TRANSAKSI
    // -------------------------------------------------------------------------
    function hitungRingkasan() {
        let subtotal = 0;

        // Hitung total seluruh item
        Object.keys(cartData).forEach(key => {
            const item = cartData[key];
            const itemDiscount = item.discount || 0;
            const priceAfterDiscount = item.price - (item.price * (itemDiscount / 100));
            subtotal += priceAfterDiscount * item.qty;
        });

        // Hitung diskon transaksi
        const diskonTransaksiNominal = subtotal * (discountPercent / 100);

        // Hitung Grand Total (Subtotal - Voucher - Diskon Transaksi)
        let grandTotal = subtotal - voucherAmount - diskonTransaksiNominal;
        if (grandTotal < 0) grandTotal = 0;

        // Perbarui teks di layar
        document.getElementById('labelSubtotal').innerText = formatRupiah(subtotal);
        document.getElementById('labelVoucher').innerText = formatRupiah(voucherAmount);
        document.getElementById('labelDiskonPersen').innerText = discountPercent + '%';
        document.getElementById('labelDiskonNominal').innerText = '-' + formatRupiah(diskonTransaksiNominal);
        document.getElementById('labelGrandTotal').innerText = formatRupiah(grandTotal);
    }

    // -------------------------------------------------------------------------
    // 4. FUNGSI TAMBAH DAN KURANG QUANTITY
    // -------------------------------------------------------------------------
    function tambahQty(key) {
        if (cartData[key]) {
            cartData[key].qty += 1;
            renderCart();
            simpanKeSession();
        }
    }

    function kurangQty(key) {
        if (cartData[key]) {
            if (cartData[key].qty > 1) {
                cartData[key].qty -= 1;
            } else {
                delete cartData[key];
            }
            renderCart();
            simpanKeSession();
        }
    }

    // -------------------------------------------------------------------------
    // 5. FUNGSI HAPUS ITEM DARI KERANJANG
    // -------------------------------------------------------------------------
    function hapusItem(key) {
        if (cartData[key]) {
            delete cartData[key];
            renderCart();
            simpanKeSession();
        }
    }

    // -------------------------------------------------------------------------
    // 6. FUNGSI KOSONGKAN KERANJANG (RESET)
    // -------------------------------------------------------------------------
    function resetKeranjang() {
        if (confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
            cartData = {};
            renderCart();
            simpanKeSession();
        }
    }

    // -------------------------------------------------------------------------
    // 7. FUNGSI TAMBAH PRODUK DARI KATALOG TENGAH KE KERANJANG
    // -------------------------------------------------------------------------
    function tambahProdukKatalogKeCart(id, code, name, description, price) {
        if (cartData[id]) {
            cartData[id].qty += 1;
        } else {
            cartData[id] = {
                id: id,
                code: code,
                name: name,
                description: description,
                price: parseFloat(price),
                qty: 1,
                discount: 0
            };
        }
        renderCart();
        simpanKeSession();
    }

    // -------------------------------------------------------------------------
    // 8. SINKRONISASI DATA KERANJANG KE LARAVEL SESSION (AGAR TIDAK HILANG)
    // -------------------------------------------------------------------------
    function simpanKeSession() {
        fetch('{{ route('POS.cart.sync') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                cart: cartData,
                voucher: voucherAmount,
                discount_percent: discountPercent
            })
        }).catch(err => console.error('Gagal sinkronisasi session cart:', err));
    }

    // -------------------------------------------------------------------------
    // 9. FUNGSI MODAL SCAN BARCODE
    // -------------------------------------------------------------------------
    function bukaModalBarcode() {
        const modal = document.getElementById('modalBarcode');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            const input = document.getElementById('inputManualBarcode');
            if (input) input.focus();
        }, 100);
    }

    function tutupModalBarcode() {
        const modal = document.getElementById('modalBarcode');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // -------------------------------------------------------------------------
    // 10. FUNGSI MODAL DISKON ITEM (%)
    // -------------------------------------------------------------------------
    function bukaModalDiskonItem(key) {
        activeItemKeyForDiscount = key;
        const currentDiscount = cartData[key] ? (cartData[key].discount || 0) : 0;
        document.getElementById('inputDiskonItem').value = currentDiscount;
        const modal = document.getElementById('modalDiskonItem');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('inputDiskonItem').focus();
    }

    function tutupModalDiskonItem() {
        const modal = document.getElementById('modalDiskonItem');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        activeItemKeyForDiscount = null;
    }

    function simpanDiskonItem() {
        const val = parseFloat(document.getElementById('inputDiskonItem').value) || 0;
        if (activeItemKeyForDiscount && cartData[activeItemKeyForDiscount]) {
            cartData[activeItemKeyForDiscount].discount = Math.min(100, Math.max(0, val));
            renderCart();
            simpanKeSession();
        }
        tutupModalDiskonItem();
    }

    // -------------------------------------------------------------------------
    // 11. FUNGSI MODAL VOUCHER (NOMINAL RP)
    // -------------------------------------------------------------------------
    function bukaModalVoucher() {
        document.getElementById('inputVoucher').value = voucherAmount;
        const modal = document.getElementById('modalVoucher');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('inputVoucher').focus();
    }

    function tutupModalVoucher() {
        const modal = document.getElementById('modalVoucher');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function simpanVoucher() {
        const val = parseFloat(document.getElementById('inputVoucher').value) || 0;
        voucherAmount = Math.max(0, val);
        renderCart();
        simpanKeSession();
        tutupModalVoucher();
    }

    // -------------------------------------------------------------------------
    // 12. FUNGSI MODAL DISKON TRANSAKSI (%)
    // -------------------------------------------------------------------------
    function bukaModalDiskonTransaksi() {
        document.getElementById('inputDiskonTransaksi').value = discountPercent;
        const modal = document.getElementById('modalDiskonTransaksi');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('inputDiskonTransaksi').focus();
    }

    function tutupModalDiskonTransaksi() {
        const modal = document.getElementById('modalDiskonTransaksi');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function simpanDiskonTransaksi() {
        const val = parseFloat(document.getElementById('inputDiskonTransaksi').value) || 0;
        discountPercent = Math.min(100, Math.max(0, val));
        renderCart();
        simpanKeSession();
        tutupModalDiskonTransaksi();
    }

    // -------------------------------------------------------------------------
    // 13. FILTER PENCARIAN KATALOG
    // -------------------------------------------------------------------------
    function filterProdukKatalog() {
        const q = document.getElementById('searchInput').value.toLowerCase().trim();
        const items = document.querySelectorAll('.katalog-item');
        items.forEach(item => {
            const name = item.getAttribute('data-name') || '';
            if (name.includes(q)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // -------------------------------------------------------------------------
    // 14. HELPER FORMAT RUPIAH
    // -------------------------------------------------------------------------
    function formatRupiah(number) {
        return 'Rp ' + Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Helper sanitasi teks HTML
    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // -------------------------------------------------------------------------
    // 15. PROSES PEMBAYARAN
    // -------------------------------------------------------------------------
    function prosesPembayaran() {
        const keys = Object.keys(cartData);
        if (keys.length === 0) {
            alert('Keranjang masih kosong!');
            return;
        }
        alert('Melanjutkan ke proses pembayaran...');
    }

    // Jalankan render awal saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        renderCart();
    });
</script>
@endpush
@endsection