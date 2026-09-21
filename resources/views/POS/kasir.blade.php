@extends('layouts.app')

@section('title', 'Point Of Sales - Kasir')
@section('header_title', 'Point Of Sales')

@section('content')

<!-- Halaman awal sebelum membuka kasir -->

{{-- ========================================================================= --}}
{{-- KONTEN UTAMA HALAMAN KASIR (POINT OF SALES)                              --}}
{{-- ========================================================================= --}}
<div class="flex h-full w-full overflow-hidden relative" id="posContainer">

    {{-- ===================================================================== --}}
    {{-- TAMPILAN 0: BUKA KASIR (TAMPIL PERTAMA KALI, SEBELUM KASIR DIBUKA)    --}}
    {{-- ===================================================================== --}}
    <div id="viewBukaKasir" class="flex-1 flex flex-col items-center justify-center h-full bg-[#CECECE]/60 text-center px-6">
        {{-- Ikon Lingkaran --}}
        <div class="w-20 h-20 rounded-full border-2 border-[#E2E8F0] bg-white flex items-center justify-center shadow-xs mb-5">
            <svg class="w-9 h-9 text-[#1A1F36]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
            </svg>
        </div>

        {{-- Judul --}}
        <h1 class="text-2xl font-bold text-[#1A1F36] tracking-tight mb-2">
            Buka Kasir
        </h1>

        {{-- Sub Judul --}}
        <p class="text-sm text-[#6B7280] mb-6 max-w-xs">
            Silakan buka kasir terlebih dahulu untuk mulai menggunakan POS.
        </p>

        {{-- Tombol Buka Kasir --}}
        <button
            type="button"
            onclick="bukaKasir()"
            class="bg-[#327337] hover:bg-[#285e2c] text-white text-sm font-bold px-6 py-2.5 rounded-lg shadow-sm transition-all cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H3" />
            </svg>
            Buka Kasir
        </button>
    </div>


    {{-- ===================================================================== --}}
    {{-- BAGIAN TENGAH: KATALOG PRODUK & MODE PEMBAYARAN                       --}}
    {{-- ===================================================================== --}}
    <div id="mainCenterSection" class="hidden flex-1 flex-col h-full overflow-hidden bg-[#CECECE]/60">
        {{-- ----------------------------------------------------------------- --}}
        {{-- A. SUB-VIEW 1: KATALOG PRODUK NORMAL                              --}}
        {{-- ----------------------------------------------------------------- --}}
        <div id="viewKatalogNormal" class="flex-1 flex flex-col h-full overflow-hidden">
            {{-- TOOLBAR ATAS (PENCARIAN, FILTER, DAN TOMBOL SCAN BARCODE) --}}
            <div class="p-4 pb-2 flex items-center justify-between gap-3 flex-shrink-0 z-10">
                
                {{-- Kiri: Input Pencarian & Tombol Filter --}}
                <div class="flex items-center gap-2 flex-1 max-w-md">
                    <div class="relative flex-1 group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
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

                    <button
                        type="button"
                        class="h-9 w-9 bg-white border border-[#E2E8F0] hover:bg-gray-50 rounded-lg flex items-center justify-center text-gray-700 shadow-xs cursor-pointer transition-colors"
                        title="Filter Produk">
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
                    <svg class="w-5 h-5 text-current" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M2 4h2v16H2V4zm3 0h1v16H5V4zm2 0h2v16H7V4zm3 0h1v16h-1V4zm2 0h3v16h-3V4zm4 0h1v16h-1V4zm2 0h2v16h-2V4zm3 0h1v16h-1V4z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- BAGIAN KANAN: PANEL ORDER / KERANJANG BELANJA (DARI SESSION & JS)     --}}
    {{-- ===================================================================== --}}
    <div id="rightOrderPanel" class="hidden w-80 lg:w-96 flex-shrink-0 bg-white border-l border-[#E2E8F0] flex-col h-full shadow-lg z-10">

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

            {{-- Tombol Aksi Mode Normal (Tombol Pembayaran Hijau) --}}
            <div id="panelActionNormal" class="pt-3">
                <button
                    type="button"
                    onclick="prosesPembayaran()"
                    class="w-full bg-[#327337] hover:bg-[#285e2c] text-white text-lg font-bold py-3 rounded-none shadow-sm transition-all cursor-pointer flex items-center justify-center tracking-wide">
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
    let voucherAmount = {{ (float) ($voucher ?? 0) }};
    let discountPercent = {{ (float) ($discountPercent ?? 0) }};
    let activeItemKeyForDiscount = null;
    let currentGrandTotal = 0;
    let activePaymentMethod = 'tunai';
    let selectedBankName = '';

    // Pastikan jika cartData bukan object/array, inisialisasi sebagai object kosong
    if (!cartData || typeof cartData !== 'object') {
        cartData = {};
    }

    // CSRF Token untuk request AJAX
    const csrfToken = '{{ csrf_token() }}';

    // -------------------------------------------------------------------------
    // 0. FUNGSI BUKA KASIR (MENAMPILKAN KATALOG & PANEL ORDER PERTAMA KALI)
    // -------------------------------------------------------------------------
    function bukaKasir() {
        // Sembunyikan tampilan awal "Buka Kasir"
        const viewAwal = document.getElementById('viewBukaKasir');
        viewAwal.classList.add('hidden');
        viewAwal.classList.remove('flex');

        // Tampilkan panel tengah (katalog) & panel kanan (order)
        const mainCenter = document.getElementById('mainCenterSection');
        mainCenter.classList.remove('hidden');
        mainCenter.classList.add('flex');

        const rightPanel = document.getElementById('rightOrderPanel');
        rightPanel.classList.remove('hidden');
        rightPanel.classList.add('flex');
    }

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
                <div class="px-4 py-3 bg-white w-full group transition-colors border-b border-black">
                    {{-- Baris Atas: Nama Produk, Deskripsi, Tombol Diskon (%) & Hapus (X) --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline gap-1.5 flex-wrap">
                                <span class="font-bold text-sm text-[#1A1F36]">${escapeHtml(item.name)}</span>
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
                                class="hover:text-red-600 transition-colors cursor-pointer px-1 py-0.5 rounded hover:bg-red-50 font-bold" 
                                title="Hapus Item">
                                X
                            </button>
                        </div>
                    </div>

                    {{-- Baris Bawah: Pengatur Jumlah (Quantity Stepper) & Total Harga Item --}}
                    <div class="mt-2.5 flex items-center justify-end">
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
        currentGrandTotal = grandTotal;

        // Perbarui teks di layar Order
        document.getElementById('labelSubtotal').innerText = formatRupiah(subtotal);
        document.getElementById('labelVoucher').innerText = formatRupiah(voucherAmount);
        document.getElementById('labelDiskonPersen').innerText = discountPercent + '%';
        document.getElementById('labelDiskonNominal').innerText = '-' + formatRupiah(diskonTransaksiNominal);
        document.getElementById('labelGrandTotal').innerText = formatRupiah(grandTotal);

        // Perbarui teks di layar Pembayaran
        const paymentDisplay = document.getElementById('paymentDisplayTotal');
        if (paymentDisplay) {
            paymentDisplay.innerText = formatRupiah(grandTotal);
        }

        // Hitung ulang kembalian jika sedang di mode tunai
        const inputNominal = document.getElementById('inputNominalBayar');
        if (inputNominal && inputNominal.value) {
            formatDanHitungTunai(inputNominal);
        }
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

    // C. Pilih Metode Pembayaran (Tunai / Bank)
    function pilihMetodePembayaran(method) {
        activePaymentMethod = method;
        const btnTunai = document.getElementById('btnMethodTunai');
        const btnBank = document.getElementById('btnMethodBank');
        const formTunai = document.getElementById('formBayarTunai');
        const formBank = document.getElementById('formBayarBank');

        if (method === 'tunai') {
            // Style tombol Tunai aktif (Orange)
            btnTunai.className = "w-36 py-2 rounded-md font-bold text-base shadow-sm border transition-all cursor-pointer bg-[#F38300] text-white border-[#E07400]";
            // Style tombol Bank inaktif (Putih)
            btnBank.className = "w-36 py-2 rounded-md font-bold text-base shadow-sm border transition-all cursor-pointer bg-white text-[#1A1F36] border-gray-400 hover:bg-gray-50";

            formTunai.classList.remove('hidden');
            formBank.classList.add('hidden');

            setTimeout(() => {
                const inputNominal = document.getElementById('inputNominalBayar');
                if (inputNominal) inputNominal.focus();
            }, 100);
        } else {
            // Style tombol Bank aktif (Orange)
            btnBank.className = "w-36 py-2 rounded-md font-bold text-base shadow-sm border transition-all cursor-pointer bg-[#F38300] text-white border-[#E07400]";
            // Style tombol Tunai inaktif (Putih)
            btnTunai.className = "w-36 py-2 rounded-md font-bold text-base shadow-sm border transition-all cursor-pointer bg-white text-[#1A1F36] border-gray-400 hover:bg-gray-50";

            formBank.classList.remove('hidden');
            formTunai.classList.add('hidden');
        }
    }

    // D. Format Input Tunai & Hitung Kembalian Otomatis
    function formatDanHitungTunai(input) {
        // Ambil hanya angka
        let rawVal = input.value.replace(/\D/g, '');
        if (!rawVal) {
            input.value = '';
            document.getElementById('labelKembalian').innerText = 'Rp 0';
            return;
        }

        let numVal = parseFloat(rawVal) || 0;
        // Format angka dengan pemisah ribuan titik
        input.value = numVal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        // Hitung Kembalian
        let kembalian = numVal - currentGrandTotal;
        if (kembalian >= 0) {
            document.getElementById('labelKembalian').innerText = formatRupiah(kembalian);
        } else {
            document.getElementById('labelKembalian').innerText = 'Rp 0';
        }
    }

    // F. Konfirmasi Pembayaran (Kirim ke Backend -> Simpan ke Tabel ACR & STS)
    function konfirmasiPembayaran() {
        const keys = Object.keys(cartData);
        if (keys.length === 0) {
            alert('Keranjang masih kosong!');
            return;
        }

        let nominalBayarClean = 0;
        if (activePaymentMethod === 'tunai') {
            const inputNominal = document.getElementById('inputNominalBayar');
            if (inputNominal) {
                nominalBayarClean = parseFloat(inputNominal.value.replace(/\D/g, '')) || 0;
            }
            if (nominalBayarClean < currentGrandTotal) {
                alert('Nominal pembayaran kurang dari total tagihan!');
                return;
            }
        } else if (activePaymentMethod === 'bank') {
            if (!selectedBankName) {
                alert('Silakan pilih bank terlebih dahulu!');
                return;
            }
        }

        // Tampilkan loading cursor pada tombol
        const btnKonfirmasi = event ? event.currentTarget : null;
        if (btnKonfirmasi) {
            btnKonfirmasi.disabled = true;
            btnKonfirmasi.innerText = 'Memproses...';
        }

        // Kirim request transaksi ke backend (akan disimpan ke ACR & STS)
        fetch('{{ route('POS.bayar') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                cart: cartData,
                voucher: voucherAmount,
                discount_percent: discountPercent,
                payment_method: activePaymentMethod,
                bank_name: selectedBankName,
                nominal_bayar: nominalBayarClean
            })
        })
        .then(res => res.json())
        .then(data => {
            if (btnKonfirmasi) {
                btnKonfirmasi.disabled = false;
                btnKonfirmasi.innerText = 'Konfirmasi';
            }

            if (data.success) {
                // Reset state data keranjang di browser
                cartData = {};
                voucherAmount = 0;
                discountPercent = 0;
                currentGrandTotal = 0;

                // Sembunyikan panel tengah dan panel order kanan
                document.getElementById('mainCenterSection').classList.add('hidden');
                document.getElementById('rightOrderPanel').classList.add('hidden');

                // Tampilkan tampilan sukses pembayaran layar penuh
                const viewSuccess = document.getElementById('viewSuccessPayment');
                viewSuccess.classList.remove('hidden');
                viewSuccess.classList.add('flex');
            } else {
                alert(data.message || 'Gagal memproses pembayaran!');
            }
        })
        .catch(err => {
            if (btnKonfirmasi) {
                btnKonfirmasi.disabled = false;
                btnKonfirmasi.innerText = 'Konfirmasi';
            }
            console.error('Error proses pembayaran:', err);
            alert('Terjadi kesalahan saat memproses pembayaran.');
        });
    }

    // Jalankan render awal saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        renderCart();
    });
</script>
@endpush
@endsection