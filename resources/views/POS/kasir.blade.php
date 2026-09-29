@extends('layouts.app')

@section('title', 'Toko - Elektronik')

@section('content')

{{-- ========================================================================= --}}
{{-- KONTEN UTAMA HALAMAN KASIR (POINT OF SALES)                              --}}
{{-- ========================================================================= --}}
<div class="flex h-full w-full overflow-hidden relative" id="posContainer">

    {{-- ===================================================================== --}}
    {{-- TAMPILAN 0: BUKA KASIR (TAMPIL PERTAMA KALI, SEBELUM KASIR DIBUKA)    --}}
    {{-- ===================================================================== --}}
    <div id="viewBukaKasir" class="{{ !empty($isKasirOpen) ? 'hidden' : 'flex' }} flex-1 flex-col items-center justify-center h-full bg-secondary-bw text-center px-6">
        {{-- Ikon Lingkaran --}}
        <div class="w-20 h-20 rounded-full border-2 border-[#E2E8F0] bg-secondary-bw flex items-center justify-center shadow-xs mb-5">
            <svg class="w-9 h-9 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
            </svg>
        </div>

        {{-- Judul --}}
        <h1 class="text-2xl font-bold text-black tracking-tight mb-2">
            Buka Kasir
        </h1>

        {{-- Sub Judul --}}
        <p class="text-sm text-[#6B7280] mb-6 max-w-xs">
            Silakan buka kasir terlebih dahulu untuk mulai menggunakan POS.
        </p>

        {{-- Tombol Buka Kasir --}}
        <button
            type="button"
            onclick="bukaModalBukaKasir()"
            class="bg-primary-db hover:bg-primary-sdb text-white text-sm font-bold px-6 py-2.5 rounded-lg shadow-sm transition-all cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H3" />
            </svg>
            Buka Kasir
        </button>
    </div>


    {{-- ===================================================================== --}}
    {{-- BAGIAN TENGAH: KATALOG PRODUK & TOOLBAR                               --}}
    {{-- ===================================================================== --}}
    <div id="mainCenterSection" class="{{ !empty($isKasirOpen) ? 'flex' : 'hidden' }} flex-1 flex-col h-full overflow-hidden bg-secondary-bw">
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
                            class="w-full bg-secondary-bw border border-[#E2E8F0] rounded-lg pl-10 pr-4 py-2 text-sm text-[#2D3748] placeholder-gray-400 focus:outline-none focus:border-[#2D8CFF] focus:ring-1 focus:ring-[#2D8CFF] shadow-xs transition-all" />
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
                    class="h-9 px-3 bg-secondary-bw border border-[#E2E8F0] hover:bg-gray-50 text-[#2D3748] rounded-lg flex items-center justify-center shadow-xs cursor-pointer transition-colors"
                    title="Scan Barcode">
                    <svg class="w-5 h-5 text-current" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M2 4h2v16H2V4zm3 0h1v16H5V4zm2 0h2v16H7V4zm3 0h1v16h-1V4zm2 0h3v16h-3V4zm4 0h1v16h-1V4zm2 0h2v16h-2V4zm3 0h1v16h-1V4z" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- BAGIAN KANAN: PANEL ORDER / KERANJANG BELANJA                         --}}
    {{-- ===================================================================== --}}
    <div id="rightOrderPanel" class="w-80 lg:w-96 bg-white border-l border-black {{ !empty($isKasirOpen) ? 'flex' : 'hidden' }} flex-col h-full z-10">

        {{-- 1. HEADER ORDER & TOMBOL DRAFT TRANSAKSI --}}
        <div class="p-4 py-3 flex items-center justify-between border-y border-black bg-secondary-bw">
            <h2 class="text-xl font-bold text-black tracking-tight">Order</h2>
            
            {{-- Tombol Draft Transaksi --}}
            <button 
                type="button" 
                onclick="modalDraftTransaksi()" 
                class="bg-[#1A233A] hover:bg-[#0F1523] text-white text-xs font-semibold px-3 py-1.5 rounded-md transition-colors cursor-pointer">
                Draft Transaksi
            </button>
        </div>

        {{-- 2. DAFTAR ITEM KERANJANG (DIRENDER OTOMATIS OLEH JAVASCRIPT) --}}
        <div id="cartItemsList" class="flex-1 overflow-y-auto divide-y divide-gray-200">
            {{-- Item keranjang di-render via JavaScript --}}
        </div>

        {{-- 3. RINGKASAN PEMBAYARAN & ACTION BUTTONS --}}
        <div class="border-t border-black bg-white">
            
            {{-- Rincian Biaya (Subtotal, Diskon, Metode Pembayaran) --}}
            <div class="p-4 space-y-2.5">
                {{-- Subtotal --}}
                <div class="flex items-center justify-between text-sm">
                    <span class="text-black font-medium">Subtotal</span>
                    <span id="labelSubtotal" class="font-bold text-black">Rp 0</span>
                </div>

                {{-- Diskon --}}
                <div class="flex items-center justify-between text-sm">
                    <span class="text-black font-medium">Diskon</span>
                    <div onclick="modalDiskonGlobal()" class="flex items-center gap-1 cursor-pointer group">
                        <span id="labelDiskonGlobal" class="font-bold text-black">Rp 0</span>
                        <svg class="w-4 h-4 text-gray-500 group-hover:text-black transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </div>

                {{-- Metode Pembayaran --}}
                <div class="flex items-center justify-between text-sm">
                    <span class="text-black font-medium">Metode Pembayaran</span>
                    <div onclick="modalMetodePembayaran()" class="flex items-center gap-1 cursor-pointer group">
                        <span id="metodePembayaran" class="font-bold text-black">Cash</span>
                        <svg class="w-4 h-4 text-gray-500 group-hover:text-black transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Total Akhir --}}
            <div class="flex items-center justify-between px-4 py-3 border-t border-black bg-white">
                <span class="text-2xl font-bold text-black">Total</span>
                <span id="labelGrandTotal" class="text-2xl font-bold text-black">Rp 0</span>
            </div>

            {{-- Footer Action Buttons (Reload, Reset, Konfirmasi) --}}
            <div class="flex items-stretch border-y border-black">
                {{-- Tombol Reload / Refresh --}}
                <button 
                    type="button" 
                    onclick="resetKeranjang()"
                    class="w-14 h-14 flex items-center justify-center border-r border-black hover:bg-gray-100 text-black transition-colors cursor-pointer"
                    title="Reload Keranjang">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M20.985 19.644v-4.992m0 0h-4.992m4.992 0L17.2 18.008a9 9 0 11.236-11.282" />
                    </svg>
                </button>

                {{-- Tombol Simpan Draft / Arsip --}}
                <button 
                    type="button" 
                    onclick="modalTambahArsip()"
                    class="w-14 h-14 flex items-center justify-center border-r border-gray-300 hover:bg-gray-100 text-black transition-colors cursor-pointer"
                    title="Simpan Draft">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125v4.125m3-4.125v4.125M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </button>

                {{-- Tombol Utama Konfirmasi --}}
                <button 
                    type="button" 
                    onclick="prosesPembayaran()"
                    class="flex-1 bg-primary-db hover:bg-primary-sdb text-white font-bold text-xl transition-colors cursor-pointer flex items-center justify-center">
                    Konfirmasi
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
            <h3 class="text-xl font-bold text-black tracking-tight">Scan Barcode</h3>

            {{-- Area Kamera Scanner (Kotak Abu-Abu Gelap) --}}
            <div class="w-full h-52 bg-[#596172] rounded-xl relative overflow-hidden flex flex-col items-center justify-center text-white shadow-inner">
                {{-- Garis Animasi Laser Merah --}}
                <div class="w-full h-0.5 bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.9)] animate-pulse"></div>
                <span class="text-xs text-white/70 mt-2 font-medium">Arahkan barcode ke kamera</span>
            </div>

            {{-- Form Input Manual Kode Barcode --}}
            <form action="{{ route('POS.scan') }}" method="POST">
                @csrf
                <div class="flex items-center gap-2">
                    <input
                        type="text"
                        name="BC_OUT"
                        id="inputManualBarcode"
                        placeholder="Atau ketik kode manual..."
                        required
                        class="flex-1 bg-secondary-bw border border-[#2D3748] rounded-lg px-3.5 py-2.5 text-sm text-black placeholder-gray-500 outline-none focus:ring-1 focus:ring-[#2D8CFF]" />
                    
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
                    class="w-full bg-secondary-bw hover:bg-gray-50 active:bg-gray-100 border border-[#2D3748] rounded-lg py-2.5 text-center text-sm font-semibold text-black transition-colors cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Draft Transaksi -->
    <div
        id="modalDraftTransaksi"
        onclick="tutupModalDraftKlikLuar(event)"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
        
        <!-- Card Modal -->
        <div 
            onclick="event.stopPropagation()"
            class="w-full max-w-md bg-[#F8FAFC] rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-200">
            
            <!-- Judul Modal -->
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-black tracking-tight">Draft Transaksi</h3>
                <button type="button" onclick="tutupModalDraftTransaksi()" class="text-gray-400 hover:text-black">✕</button>
            </div>

            <!-- Container List Draft -->
            <div id="listDraftContainer" class="space-y-3 max-h-[60vh] overflow-y-auto pr-1">
                <!-- Diisi via JavaScript dari localStorage -->
            </div>

        </div>
    </div>

    <!-- Modal Diskon Per Item -->
    <div
        id="modalDiskonItem"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
        <div class="w-full max-w-sm bg-white rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-200">
            
            <!-- Judul Modal -->
            <h3 class="text-xl font-bold text-black tracking-tight">Diskon Per Item</h3>

            <!-- Form Input -->
            <div class="space-y-3">
                <!-- Input Diskon (%) -->
                <div>
                    <label for="inputDiskonItem" class="block text-sm font-semibold text-black mb-1">
                        Diskon (%)
                    </label>
                    <input
                        type="number"
                        id="inputDiskonItem"
                        min="0"
                        max="100"
                        placeholder="Contoh: 20"
                        class="w-full bg-white border border-gray-300 placeholder-gray-400 rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium focus:border-black" />
                </div>

                <!-- Input Voucher Nominal (Rp) -->
                <div>
                    <label for="inputVoucherItem" class="block text-sm font-semibold text-black mb-1">
                        Voucher (Rp)
                    </label>
                    <input
                        type="number"
                        id="inputVoucherItem"
                        min="0"
                        step="1000"
                        placeholder="Contoh: 20000"
                        class="w-full bg-white border border-gray-300 placeholder-gray-400 rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium focus:border-black" />
                </div>
            </div>

            <!-- Tombol Aksi (Batal & Terapkan) -->
            <div class="grid grid-cols-2 gap-3 pt-2">
                <button
                    type="button"
                    onclick="tutupModalDiskonItem()"
                    class="w-full bg-white border border-gray-300 hover:bg-gray-100 text-black rounded-xl py-2.5 text-center text-sm font-bold transition-colors cursor-pointer">
                    Batal
                </button>
                <button
                    type="button"
                    onclick="simpanDiskonItem()"
                    class="w-full bg-primary-db hover:bg-primary-sdb text-white rounded-xl py-2.5 text-center text-sm font-bold transition-colors cursor-pointer">
                    Terapkan
                </button>
            </div>

        </div>
    </div>
    <!-- end: Modal Diskon Per Item -->

    <!-- Modal Buka Kasir -->
    <div
        id="modalBukaKasir"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
        <div class="w-full max-w-md bg-secondary-bw rounded-2xl p-6 shadow-2xl space-y-4 border border-black">
            
            <!-- Header: Judul & Tanggal -->
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-black tracking-tight">Pembukaan kasir</h3>
                <span class="text-gray-500 font-medium text-sm">{{ now()->format('d F Y') }}</span>
            </div>

            <!-- Form Input -->
            <div class="space-y-4 pt-2">
                <!-- Kas Opening -->
                <div>
                    <label for="kasOpening" class="block text-sm font-semibold text-black mb-1">
                        Kas opening:
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            id="kasOpening"
                            placeholder="Masukkan nominal"
                            class="w-full bg-white border rounded-lg px-3 py-2.5 text-sm text-black outline-none pr-8 font-medium" />
                        <!-- Tombol Clear / X -->
                        <button 
                            type="button" 
                            onclick="document.getElementById('kasOpening').value = ''"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold hover:text-black">
                            ✕
                        </button>
                    </div>
                </div>

                <!-- Catatan Opening -->
                <div>
                    <label for="catatanOpening" class="block text-sm font-semibold text-black mb-1">
                        Catatan opening:
                    </label>
                    <textarea
                        id="catatanOpening"
                        rows="3"
                        placeholder="Tambahkan catatan opening jika ada..."
                        class="w-full bg-white border rounded-lg p-3 text-sm text-black outline-none resize-none font-medium"></textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 pt-4">
                <button
                    type="button"
                    id="btnEksekusiBukaKasir"
                    onclick="simpanBukaKasir()"
                    class="bg-primary-db hover:bg-primary-sdb text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition-colors cursor-pointer">
                    Buka kasir
                </button>
                <button
                    type="button"
                    onclick="tutupModalBukaKasir()"
                    class="bg-white border font-semibold px-5 py-2.5 rounded-lg text-sm transition-colors cursor-pointer">
                    Batal
                </button>
            </div>

        </div>
    </div>
    <!-- end: Modal Buka Kasir -->

    <!-- Modal Diskon Global -->
    <div
        id="modalDiskonGlobal"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
        <div class="w-full max-w-sm bg-secondary-bw rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-200">
            
            <!-- Judul Modal -->
            <h3 class="text-xl font-bold text-black tracking-tight">Diskon Global</h3>

            <!-- Form Input -->
            <div class="space-y-3">
                <!-- Input Diskon (%) -->
                <div>
                    <label for="inputDiskon" class="block text-sm font-semibold text-black mb-1">
                        Diskon (%)
                    </label>
                    <input
                        type="number"
                        id="inputDiskon"
                        min="0"
                        max="100"
                        placeholder="0%"
                        class="w-full bg-white placeholder-gray-400 rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium border" />
                </div>

                <!-- Input Voucher (Rp) -->
                <div>
                    <label for="inputVoucher" class="block text-sm font-semibold text-black mb-1">
                        Voucher (Rp)
                    </label>
                    <input
                        type="number"
                        id="inputVoucher"
                        min="0"
                        step="1000"
                        placeholder="Rp 100.000"
                        class="w-full bg-white placeholder-gray-400 rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium border" />
                </div>
            </div>

            <!-- Tombol Aksi (Batal & Terapkan) -->
            <div class="grid grid-cols-2 gap-3 pt-2">
                <button
                    type="button"
                    onclick="tutupModalDiskonGlobal()"
                    class="w-full border bg-white text-black rounded-xl py-2.5 text-center text-sm font-bold transition-colors cursor-pointer">
                    Batal
                </button>
                <button
                    type="button"
                    onclick="simpanDiskonGlobal()"
                    class="w-full bg-primary-db hover:bg-primary-sdb text-white rounded-xl py-2.5 text-center text-sm font-bold transition-colors cursor-pointer">
                    Terapkan
                </button>
            </div>

        </div>
    </div>
    <!-- end: Modal Diskon Global -->

    <!-- Modal Metode Pembayaran -->
    <div
        id="modalMetodePembayaran"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
        
        <!-- Card Modal -->
        <div class="w-full max-w-sm bg-[#F8FAFC] rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-200">
            
            <!-- Judul Modal -->
            <h3 class="text-lg font-bold text-black tracking-tight">Metode Pembayaran</h3>

            <!-- Opsi Pembayaran Non Tunai -->
            <div class="space-y-2">
                <span class="block text-sm font-semibold text-black">Non Tunai</span>
                <div class="space-y-2">
                    <button
                        type="button"
                        onclick="pilihMetodePembayaran('BNI')"
                        class="w-full bg-primary-db border border-gray-800 hover:bg-primary-sdb text-white font-medium py-2 rounded-xl text-sm transition-colors cursor-pointer">
                        BNI
                    </button>
                    <button
                        type="button"
                        onclick="pilihMetodePembayaran('BCA')"
                        class="w-full bg-primary-db border border-gray-800 hover:bg-primary-sdb text-white font-medium py-2 rounded-xl text-sm transition-colors cursor-pointer">
                        BCA
                    </button>
                    <button
                        type="button"
                        onclick="pilihMetodePembayaran('BRI')"
                        class="w-full bg-primary-db border border-gray-800 hover:bg-primary-sdb text-white font-medium py-2 rounded-xl text-sm transition-colors cursor-pointer">
                        BRI
                    </button>
                    <button
                        type="button"
                        onclick="pilihMetodePembayaran('Seabank')"
                        class="w-full bg-primary-db border border-gray-800 hover:bg-primary-sdb text-white font-medium py-2 rounded-xl text-sm transition-colors cursor-pointer">
                        Seabank
                    </button>
                    <button
                        type="button"
                        onclick="pilihMetodePembayaran('JAGO')"
                        class="w-full bg-primary-db border border-gray-800 hover:bg-primary-sdb text-white font-medium py-2 rounded-xl text-sm transition-colors cursor-pointer">
                        JAGO
                    </button>
                </div>
            </div>

            <!-- Opsi Pembayaran Tunai -->
            <div class="space-y-2 pt-1">
                <span class="block text-sm font-semibold text-black">Tunai</span>
                <button
                    type="button"
                    onclick="pilihMetodePembayaran('Cash')"
                    class="w-full bg-primary-db border border-gray-800 hover:bg-primary-sdb text-white font-medium py-2 rounded-xl text-sm transition-colors cursor-pointer">
                    Cash
                </button>
            </div>

            <!-- Tombol Batal -->
            <div class="pt-1">
                <button
                    type="button"
                    onclick="modalTutupMetodePembayaran()"
                    class="w-full bg-white border border-gray-800 hover:bg-gray-100 active:bg-gray-200 text-black font-semibold py-2 rounded-xl text-sm transition-colors cursor-pointer">
                    Batal
                </button>
            </div>

        </div>
    </div>
    <!-- end: Modal Metode Pembayaran -->

    <!-- Modal Tambah Arsip / Simpan Draft -->
    <div
        id="modalTambahArsip"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
        
        <!-- Card Modal -->
        <div class="w-full max-w-sm bg-[#F8FAFC] rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-200">
            
            <!-- Judul Modal -->
            <h3 class="text-lg font-bold text-black tracking-tight">Simpan sebagai draft?</h3>

            <!-- Form Input -->
            <div class="space-y-4">
                <!-- Input Pelanggan -->
                <div>
                    <label for="inputPelangganDraft" class="block text-sm font-semibold text-black mb-1">
                        Pelanggan
                    </label>
                    <input
                        type="text"
                        id="inputPelangganDraft"
                        list="listPelangganDraft"
                        placeholder="Cari Pelanggan"
                        class="w-full bg-white border border-gray-800 rounded-xl px-4 py-2.5 text-sm text-black placeholder-gray-400 outline-none focus:ring-1 focus:ring-[#1A233A] font-medium" />
                    <datalist id="listPelangganDraft">
                        @foreach($customers ?? [] as $cst)
                            <option value="{{ $cst->DES }}">{{ $cst->CST }}</option>
                        @endforeach
                    </datalist>
                </div>

                <!-- Input Sales -->
                <div>
                    <label for="inputSalesDraft" class="block text-sm font-semibold text-black mb-1">
                        Sales
                    </label>
                    <input
                        type="text"
                        id="inputSalesDraft"
                        list="listSalesDraft"
                        placeholder="Cari Sales"
                        class="w-full bg-white border border-gray-800 rounded-xl px-4 py-2.5 text-sm text-black placeholder-gray-400 outline-none focus:ring-1 focus:ring-[#1A233A] font-medium" />
                    <datalist id="listSalesDraft">
                        @foreach($salesList ?? [] as $sls)
                            <option value="{{ $sls->DES }}">{{ $sls->SLS }}</option>
                        @endforeach
                    </datalist>
                </div>

                <!-- Tombol Aksi (Batal & Konfirmasi Simpan Draft) -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button
                        type="button"
                        onclick="tutupModalTambahArsip()"
                        class="w-full bg-white border border-gray-800 hover:bg-gray-100 active:bg-gray-200 text-black rounded-xl py-2.5 text-center text-sm font-bold transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button
                        type="button"
                        onclick="simpanDraftTransaksi()"
                        class="w-full bg-primary-db hover:bg-primary-sdb active:bg-black text-white rounded-xl py-2.5 text-center text-sm font-bold transition-colors cursor-pointer">
                        Konfirmasi
                    </button>
                </div>
            </div>

        </div>
    </div>
    <!-- end: Modal Tambah Arsip -->

    <!-- Modal Tutup Kasir / Transaksi -->
    <div
        id="modalTutupKasir"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
        
        <!-- Card Modal -->
        <div class="w-full max-w-sm bg-[#F8FAFC] rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-200">
            
            <!-- Judul Modal -->
            <h3 class="text-lg font-bold text-black tracking-tight">Menutup Kasir</h3>

            <!-- Ringkasan Rincian Kasir -->
            <div class="space-y-2.5 text-sm text-black">
                <!-- Saldo Awal -->
                <div class="flex justify-between items-center">
                    <span class="font-medium">Saldo Awal</span>
                    <span id="labelTutupSaldoAwal" class="font-semibold">Rp 0</span>
                </div>

                <!-- Penjualan Tunai -->
                <div class="flex justify-between items-center">
                    <span class="font-medium">Penjualan Tunai</span>
                    <span id="labelTutupPenjualanTunai" class="font-semibold">Rp 0</span>
                </div>

                <!-- Penjualan Non-Tunai -->
                <div class="flex justify-between items-center">
                    <span class="font-medium">Penjualan Non-Tunai</span>
                    <span id="labelTutupPenjualanNonTunai" class="font-semibold">Rp 0</span>
                </div>

                <!-- Mutasi Keluar -->
                <div class="flex justify-between items-center">
                    <span class="font-medium">Mutasi Keluar</span>
                    <span id="labelTutupMutasiKeluar" class="font-semibold">Rp 0</span>
                </div>

                <!-- Saldo Akhir -->
                <div class="flex justify-between items-center pt-1 border-t border-gray-200">
                    <span class="font-medium">Saldo Akhir</span>
                    <span id="labelTutupSaldoAkhir" class="font-bold">Rp 0</span>
                </div>
            </div>

            <!-- Tombol Aksi (Batal & Tutup) -->
            <div class="grid grid-cols-2 gap-3 pt-2">
                <button
                    type="button"
                    onclick="tutupModalTutupKasir()"
                    class="w-full bg-white border border-gray-800 hover:bg-gray-100 active:bg-gray-200 text-black rounded-xl py-2.5 text-center text-sm font-bold transition-colors cursor-pointer">
                    Batal
                </button>
                <button
                    type="button"
                    id="btnEksekusiTutupKasir"
                    onclick="prosesTutupKasir()"
                    class="w-full bg-[#0F1D3A] hover:bg-[#0A1428] active:bg-black text-white rounded-xl py-2.5 text-center text-sm font-bold transition-colors cursor-pointer">
                    Tutup
                </button>
            </div>

        </div>
    </div>
    <!-- end: Modal Tutup Kasir / Transaksi -->

    <!-- ===================================================================== -->
    <!-- MODAL KONFIRMASI PEMBAYARAN (NT & T) SESUAI DESAIN MOCKUP             -->
    <!-- ===================================================================== -->
    <div
        id="modalKonfirmasiPembayaran"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
        
        <!-- Card Modal -->
        <div class="w-full max-w-sm bg-secondary-bw rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-100">
            
            <!-- Judul & Subtitle -->
            <div>
                <h3 class="text-xl font-bold text-black tracking-tight">Konfirmasi Pembayaran</h3>
                <p id="konfirmasiSubtitle" class="text-sm text-gray-700 mt-1 font-normal leading-relaxed">
                    Selesaikan transaksi sebesar Rp 0, untuk 0 item?
                </p>
            </div>

            <!-- Form Konfirmasi -->
            <div class="space-y-3.5 pt-1">
                <!-- Field 1: Uang Diterima (HANYA TAMPIL JIKA METODE TUNAI) -->
                <div id="wrapperUangDiterima" class="space-y-1">
                    <label for="inputUangDiterima" class="block text-sm font-semibold text-black">
                        Uang Diterima
                    </label>
                    <input
                        type="text"
                        id="inputUangDiterima"
                        oninput="formatDanHitungUangDiterima(this)"
                        placeholder="Rp 0"
                        class="w-full bg-white border  rounded-xl px-4 py-2.5 text-sm text-black placeholder-gray-400 outline-none focus:border-[#101D36] focus:ring-1 focus:ring-[#101D36] font-medium" />
                </div>

                <!-- Field 2: Pelanggan -->
                <div class="space-y-1">
                    <label for="inputPelangganKonfirmasi" class="block text-sm font-semibold text-black">
                        Pelanggan
                    </label>
                    <input
                        type="text"
                        id="inputPelangganKonfirmasi"
                        list="listPelanggan"
                        placeholder="Cari Pelanggan"
                        class="w-full bg-white border  rounded-xl px-4 py-2.5 text-sm text-black placeholder-gray-400 outline-none focus:border-[#101D36] focus:ring-1 focus:ring-[#101D36] font-medium" />
                    <datalist id="listPelanggan">
                        @foreach($customers ?? [] as $cst)
                            <option value="{{ $cst->DES }}">{{ $cst->CST }}</option>
                        @endforeach
                    </datalist>
                </div>

                <!-- Field 3: Sales -->
                <div class="space-y-1">
                    <label for="inputSalesKonfirmasi" class="block text-sm font-semibold text-black">
                        Sales
                    </label>
                    <input
                        type="text"
                        id="inputSalesKonfirmasi"
                        list="listSales"
                        placeholder="Cari Sales"
                        class="w-full bg-white border  rounded-xl px-4 py-2.5 text-sm text-black placeholder-gray-400 outline-none focus:border-[#101D36] focus:ring-1 focus:ring-[#101D36] font-medium" />
                    <datalist id="listSales">
                        @foreach($salesList ?? [] as $sls)
                            <option value="{{ $sls->DES }}">{{ $sls->SLS }}</option>
                        @endforeach
                    </datalist>
                </div>
            </div>

            <!-- Tombol Aksi (Batal & Konfirmasi) -->
            <div class="grid grid-cols-2 gap-3 pt-2">
                <button
                    type="button"
                    onclick="tutupModalKonfirmasiPembayaran()"
                    class="w-full bg-white border  hover:bg-gray-50 active:bg-gray-100 text-black rounded-xl py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer">
                    Batal
                </button>
                <button
                    type="button"
                    id="btnEksekusiBayar"
                    onclick="eksekusiPembayaranFinal()"
                    class="w-full bg-primary-db hover:bg-primary-sdb active:bg-black text-white rounded-xl py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer shadow-sm">
                    Konfirmasi
                </button>
            </div>

        </div>
    </div>
    <!-- end: Modal Konfirmasi Pembayaran -->

    <!-- Modal Struk Pembayaran -->
     @include('POS.struk')
    <!-- end: Modal Struk Pembayaran -->

</div>

{{-- ========================================================================= --}}
{{-- JAVASCRIPT: SEMUA FUNGSI LOGIKA KERANJANG, TOTAL, DAN MODAL              --}}
{{-- ========================================================================= --}}
@push('scripts')
<script>
    // -------------------------------------------------------------------------
    // 1. DATA KERANJANG & STATE DARI SESSION LARAVEL
    // -------------------------------------------------------------------------
    let cartData = @json($cart ?? []);
    let voucherAmount = {{ (float) ($voucher ?? 0) }};
    let discountPercent = {{ (float) ($discountPercent ?? 0) }};
    let isKasirOpen = {{ !empty($isKasirOpen) ? 'true' : 'false' }};
    let activeItemKeyForDiscount = null;
    let currentGrandTotal = 0;
    let activePaymentMethod = 'Cash';
    let selectedBankName = '';

    // Pastikan jika cartData bukan object/array, inisialisasi sebagai object kosong
    if (!cartData || typeof cartData !== 'object') {
        cartData = {};
    }

    // CSRF Token untuk request AJAX
    const csrfToken = '{{ csrf_token() }}';

    // -------------------------------------------------------------------------
    // 2. FUNGSI BUKA & TUTUP KASIR (SHIFT MANAGEMENT)
    // -------------------------------------------------------------------------

    // Buka Modal Buka Kasir
    function bukaModalBukaKasir() {
        const modal = document.getElementById('modalBukaKasir');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                const inp = document.getElementById('kasOpening');
                if (inp) inp.focus();
            }, 100);
        }
    }

    // Tutup Modal Buka Kasir
    function tutupModalBukaKasir() {
        const modal = document.getElementById('modalBukaKasir');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    // Simpan Buka Kasir via Backend
    function simpanBukaKasir() {
        const kasOpening = document.getElementById('kasOpening').value;
        const catatanOpening = document.getElementById('catatanOpening').value;

        if (kasOpening === '' || parseFloat(kasOpening) < 0) {
            alert('Silakan masukkan nominal Kas Opening yang valid!');
            return;
        }

        const btn = document.getElementById('btnEksekusiBukaKasir');
        if (btn) {
            btn.disabled = true;
            btn.innerText = 'Membuka...';
        }

        fetch('{{ route('POS.buka-kasir') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                kas_opening: kasOpening,
                catatan_opening: catatanOpening
            })
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerText = 'Buka kasir';
            }

            if (data.success) {
                isKasirOpen = true;

                // Sembunyikan tampilan awal "Buka Kasir"
                const viewAwal = document.getElementById('viewBukaKasir');
                if (viewAwal) {
                    viewAwal.classList.add('hidden');
                    viewAwal.classList.remove('flex');
                }

                // Tampilkan panel tengah (katalog) & panel kanan (order)
                const mainCenter = document.getElementById('mainCenterSection');
                if (mainCenter) {
                    mainCenter.classList.remove('hidden');
                    mainCenter.classList.add('flex');
                }

                const rightPanel = document.getElementById('rightOrderPanel');
                if (rightPanel) {
                    rightPanel.classList.remove('hidden');
                    rightPanel.classList.add('flex');
                }

                // Tampilkan tombol Tutup Kasir di Header
                const btnHeader = document.getElementById('btnHeaderTutupKasir');
                if (btnHeader) {
                    btnHeader.classList.remove('hidden');
                }

                tutupModalBukaKasir();
                renderCart();
            } else {
                alert(data.message || 'Gagal membuka kasir!');
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerText = 'Buka kasir';
            }
            console.error('Error buka kasir:', err);
            alert('Terjadi kesalahan saat membuka kasir.');
        });
    }

    // Buka Modal Tutup Kasir & Muat Data Kas
    function modalTutupKasir() {
        fetch('{{ route('POS.kas-status') }}')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.kas) {
                    document.getElementById('labelTutupSaldoAwal').innerText = formatRupiah(data.kas.saldo_awal);
                    document.getElementById('labelTutupPenjualanTunai').innerText = formatRupiah(data.kas.penjualan_tunai);
                    document.getElementById('labelTutupPenjualanNonTunai').innerText = formatRupiah(data.kas.penjualan_non_tunai);
                    document.getElementById('labelTutupMutasiKeluar').innerText = formatRupiah(data.kas.mutasi_keluar);
                    document.getElementById('labelTutupSaldoAkhir').innerText = formatRupiah(data.kas.saldo_akhir);
                }
            })
            .catch(err => console.error('Error load kas status:', err));

        const modal = document.getElementById('modalTutupKasir');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    // Tutup Modal Tutup Kasir
    function tutupModalTutupKasir() {
        const modal = document.getElementById('modalTutupKasir');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    // Proses Tutup Kasir via Backend
    function prosesTutupKasir() {
        const btn = document.getElementById('btnEksekusiTutupKasir');
        if (btn) {
            btn.disabled = true;
            btn.innerText = 'Menutup...';
        }

        fetch('{{ route('POS.tutup-kasir') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerText = 'Tutup';
            }

            if (data.success) {
                isKasirOpen = false;
                cartData = {};
                voucherAmount = 0;
                discountPercent = 0;
                currentGrandTotal = 0;

                // Tampilkan tampilan awal "Buka Kasir"
                const viewAwal = document.getElementById('viewBukaKasir');
                if (viewAwal) {
                    viewAwal.classList.remove('hidden');
                    viewAwal.classList.add('flex');
                }

                // Sembunyikan panel kasir aktif
                const mainCenter = document.getElementById('mainCenterSection');
                if (mainCenter) {
                    mainCenter.classList.add('hidden');
                    mainCenter.classList.remove('flex');
                }

                const rightPanel = document.getElementById('rightOrderPanel');
                if (rightPanel) {
                    rightPanel.classList.add('hidden');
                    rightPanel.classList.remove('flex');
                }

                // Sembunyikan tombol Tutup Kasir di Header
                const btnHeader = document.getElementById('btnHeaderTutupKasir');
                if (btnHeader) {
                    btnHeader.classList.add('hidden');
                }

                tutupModalTutupKasir();
                alert('Kasir berhasil ditutup.');
            } else {
                alert(data.message || 'Gagal menutup kasir!');
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerText = 'Tutup';
            }
            console.error('Error tutup kasir:', err);
            alert('Terjadi kesalahan saat menutup kasir.');
        });
    }

    // -------------------------------------------------------------------------
    // 3. FUNGSI RENDER TAMPILAN KERANJANG DI PANEL KANAN
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
                    <p class="text-xs mt-1 text-gray-500">Scan barcode unit produk untuk memulai</p>
                </div>
            `;
            hitungRingkasan();
            return;
        }

        let html = '';

        // Looping setiap item di dalam cartData
        keys.forEach(key => {
            const item = cartData[key];
            const itemDiscountPct = parseFloat(item.discount) || 0;
            const itemDiscountVoucher = parseFloat(item.discount_voucher) || 0;
            const itemPrice = parseFloat(item.price) || 0;
            const qty = parseInt(item.qty) || 1;

            const discFromPct = itemPrice * (itemDiscountPct / 100);
            const priceAfterDiscount = Math.max(0, itemPrice - discFromPct - itemDiscountVoucher);
            const itemTotal = priceAfterDiscount * qty;

            html += `
                <div class="px-4 py-3 bg-secondary-bw w-full group transition-colors border-b border-black">
                    {{-- Baris Atas: Nama Produk, Deskripsi, Tombol Diskon (%) & Hapus (X) --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline gap-1.5 flex-wrap">
                                <span class="font-bold text-sm text-black">${escapeHtml(item.name)}</span>
                                <span class="text-xs text-[#6B7280] truncate">${escapeHtml(item.description || '')}</span>
                            </div>
                            <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                <span class="text-[11px] text-gray-500">Barcode: ${escapeHtml(item.code || key)}</span>
                                ${itemDiscountPct > 0 ? `<span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded font-bold">Diskon ${itemDiscountPct}%</span>` : ''}
                                ${itemDiscountVoucher > 0 ? `<span class="text-[10px] bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded font-bold">Pot. ${formatRupiah(itemDiscountVoucher)}</span>` : ''}
                            </div>
                        </div>
                        
                        {{-- Tombol Aksi: Diskon Item (% dan Rp) dan Hapus Item --}}
                        <div class="flex items-center gap-2 flex-shrink-0 text-xs font-semibold text-gray-600">
                            <button 
                                type="button" 
                                onclick="modalDiskonItem('${key}')" 
                                class="hover:text-black transition-colors cursor-pointer px-1 py-0.5 rounded hover:bg-gray-100" 
                                title="Beri Diskon Item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1.1em" height="1.1em" viewBox="0 0 24 24">
                                    <path d="M0 0h24v24H0z" fill="none" />
                                    <g fill="none">
                                        <path d="m11.878 22.02l-9.9-9.899l9.9-9.9l9.192.708l.707 9.192z" />
                                        <path d="M14 7.17A2 2 0 1 1 16.828 10A2 2 0 0 1 14 7.17" />
                                        <path stroke="currentColor" stroke-width="2" d="m11.878 22.02l-9.9-9.899l9.9-9.9l9.192.708l.707 9.192z" />
                                        <path stroke="currentColor" stroke-width="2" d="M14 7.17A2 2 0 1 1 16.828 10A2 2 0 0 1 14 7.17Z" />
                                    </g>
                                </svg>
                            </button>
                            <button 
                                type="button" 
                                onclick="hapusItem('${key}')" 
                                class="hover:text-red-600 transition-colors cursor-pointer px-1 py-0.5 rounded hover:bg-red-50 font-bold" 
                                title="Hapus Item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1.1em" height="1.1em" viewBox="0 0 24 24">
                                    <path d="M0 0h24v24H0z" fill="none" />
                                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16l-1.58 14.22A2 2 0 0 1 16.432 22H7.568a2 2 0 0 1-1.988-1.78zm3.345-2.853A2 2 0 0 1 9.154 2h5.692a2 2 0 0 1 1.81 1.147L18 6H6zM2 6h20m-12 5v5m4-5v5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Baris Bawah: Detail Harga --}}
                    <div class="mt-2 flex items-center justify-between text-xs">
                        <div class="text-gray-500">
                            ${(itemDiscountPct > 0 || itemDiscountVoucher > 0) ? `<span class="line-through text-gray-400 mr-1">${formatRupiah(itemPrice)}</span>` : ''}
                            <span>${formatRupiah(priceAfterDiscount)}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-sm text-black font-number">${formatRupiah(itemTotal)}</span>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        hitungRingkasan();
    }

    // -------------------------------------------------------------------------
    // 4. FUNGSI PERHITUNGAN TOTAL, VOUCHER, DAN DISKON TRANSAKSI
    // -------------------------------------------------------------------------
    function hitungRingkasan() {
        let subtotal = 0;

        // Hitung total seluruh item
        Object.keys(cartData).forEach(key => {
            const item = cartData[key];
            const itemDiscountPct = parseFloat(item.discount) || 0;
            const itemDiscountVoucher = parseFloat(item.discount_voucher) || 0;
            const itemPrice = parseFloat(item.price) || 0;
            const qty = parseInt(item.qty) || 1;

            const discFromPct = itemPrice * (itemDiscountPct / 100);
            const priceAfterDiscount = Math.max(0, itemPrice - discFromPct - itemDiscountVoucher);
            subtotal += (priceAfterDiscount * qty);
        });

        // Hitung diskon transaksi global
        const diskonTransaksiNominal = subtotal * (discountPercent / 100);

        // Hitung Grand Total (Subtotal - Voucher Global - Diskon Transaksi Global)
        let grandTotal = Math.max(0, subtotal - voucherAmount - diskonTransaksiNominal);
        currentGrandTotal = grandTotal;

        // Perbarui teks di layar Order
        const elSubtotal = document.getElementById('labelSubtotal');
        if (elSubtotal) elSubtotal.innerText = formatRupiah(subtotal);

        const elDiskonGlobal = document.getElementById('labelDiskonGlobal');
        if (elDiskonGlobal) {
            const totalPotonganGlobal = diskonTransaksiNominal + voucherAmount;
            if (totalPotonganGlobal > 0) {
                elDiskonGlobal.innerText = '-' + formatRupiah(totalPotonganGlobal);
            } else {
                elDiskonGlobal.innerText = 'Rp 0';
            }
        }

        const elGrandTotal = document.getElementById('labelGrandTotal');
        if (elGrandTotal) elGrandTotal.innerText = formatRupiah(grandTotal);

        const elMetode = document.getElementById('metodePembayaran');
        if (elMetode) elMetode.innerText = activePaymentMethod;
    }

    // -------------------------------------------------------------------------
    // 5. FUNGSI HAPUS DAN RESET KERANJANG
    // -------------------------------------------------------------------------
    function hapusItem(key) {
        if (cartData[key]) {
            delete cartData[key];
            renderCart();
            simpanKeSession();
        }
    }

    function resetKeranjang(skipConfirm = false) {
        if (Object.keys(cartData).length === 0) return;
        if (skipConfirm || confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
            cartData = {};
            voucherAmount = 0;
            discountPercent = 0;
            isLoadedFromDraft = false;
            currentDraftPelanggan = '';
            currentDraftSales = '';
            renderCart();
            simpanKeSession();
        }
    }

    // -------------------------------------------------------------------------
    // 6. SINKRONISASI DATA KERANJANG KE LARAVEL SESSION
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
    // 7. FUNGSI MODAL SCAN BARCODE
    // -------------------------------------------------------------------------
    function bukaModalBarcode() {
        const modal = document.getElementById('modalBarcode');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                const input = document.getElementById('inputManualBarcode');
                if (input) input.focus();
            }, 100);
        }
    }

    function tutupModalBarcode() {
        const modal = document.getElementById('modalBarcode');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    // -------------------------------------------------------------------------
    // 8. FUNGSI DISKON PER ITEM
    // -------------------------------------------------------------------------
    function modalDiskonItem(key) {
        activeItemKeyForDiscount = key;
        const currentDiscountPct = cartData[key] ? (cartData[key].discount || 0) : 0;
        const currentDiscountVch = cartData[key] ? (cartData[key].discount_voucher || 0) : 0;

        document.getElementById('inputDiskonItem').value = currentDiscountPct || '';
        document.getElementById('inputVoucherItem').value = currentDiscountVch || '';

        const modal = document.getElementById('modalDiskonItem');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('inputDiskonItem').focus();
        }
    }

    function tutupModalDiskonItem() {
        const modal = document.getElementById('modalDiskonItem');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
        activeItemKeyForDiscount = null;
    }

    function simpanDiskonItem() {
        const pct = parseFloat(document.getElementById('inputDiskonItem').value) || 0;
        const vch = parseFloat(document.getElementById('inputVoucherItem').value) || 0;

        if (activeItemKeyForDiscount && cartData[activeItemKeyForDiscount]) {
            cartData[activeItemKeyForDiscount].discount = Math.min(100, Math.max(0, pct));
            cartData[activeItemKeyForDiscount].discount_voucher = Math.max(0, vch);
            renderCart();
            simpanKeSession();
        }
        tutupModalDiskonItem();
    }

    // -------------------------------------------------------------------------
    // 9. FUNGSI DISKON GLOBAL
    // -------------------------------------------------------------------------
    function modalDiskonGlobal() {
        document.getElementById('inputDiskon').value = discountPercent || '';
        document.getElementById('inputVoucher').value = voucherAmount || '';

        const modal = document.getElementById('modalDiskonGlobal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('inputDiskon').focus();
        }
    }

    function tutupModalDiskonGlobal() {
        const modal = document.getElementById('modalDiskonGlobal');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function simpanDiskonGlobal() {
        const rawDiskon = parseFloat(document.getElementById('inputDiskon').value) || 0;
        discountPercent = Math.min(100, Math.max(0, rawDiskon));

        const rawVoucher = parseFloat(document.getElementById('inputVoucher').value) || 0;
        voucherAmount = Math.max(0, rawVoucher);

        renderCart();
        simpanKeSession();
        tutupModalDiskonGlobal();
    }

    // -------------------------------------------------------------------------
    // 10. FUNGSI METODE PEMBAYARAN
    // -------------------------------------------------------------------------
    function modalMetodePembayaran() {
        const modal = document.getElementById('modalMetodePembayaran');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function modalTutupMetodePembayaran() {
        const modal = document.getElementById('modalMetodePembayaran');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function pilihMetodePembayaran(method) {
        activePaymentMethod = method;
        if (['BNI', 'BCA', 'BRI', 'Seabank', 'JAGO'].includes(method)) {
            selectedBankName = method;
        } else {
            selectedBankName = '';
        }

        const elMetode = document.getElementById('metodePembayaran');
        if (elMetode) elMetode.innerText = method;

        modalTutupMetodePembayaran();
    }

    // -------------------------------------------------------------------------
    // 11. FUNGSI MODAL KONFIRMASI PEMBAYARAN (NT & T) SESUAI DESAIN
    // -------------------------------------------------------------------------

    // Buka Modal Konfirmasi Pembayaran saat tombol Konfirmasi di Order diklik
    function prosesPembayaran() {
        const keys = Object.keys(cartData);
        if (keys.length === 0) {
            alert('Keranjang masih kosong!');
            return;
        }

        // Hitung total item qty
        let totalItems = 0;
        keys.forEach(k => {
            totalItems += (parseInt(cartData[k].qty) || 1);
        });

        // Update Subtitle: "Selesaikan transaksi sebesar Rp [Total], untuk [X] item?"
        const subtitleEl = document.getElementById('konfirmasiSubtitle');
        if (subtitleEl) {
            subtitleEl.innerText = `Selesaikan transaksi sebesar ${formatRupiah(currentGrandTotal)}, untuk ${totalItems} item?`;
        }

        // Cek Metode Pembayaran (Tunai vs Non-Tunai)
        const wrapperUangDiterima = document.getElementById('wrapperUangDiterima');
        const inputUangDiterima = document.getElementById('inputUangDiterima');

        const isTunai = (activePaymentMethod.toLowerCase() === 'cash' || activePaymentMethod.toLowerCase() === 'tunai');

        if (isTunai) {
            // Tampilkan field Uang Diterima (Konfirmasi Pembayaran T)
            if (wrapperUangDiterima) wrapperUangDiterima.classList.remove('hidden');
            if (inputUangDiterima) {
                inputUangDiterima.value = formatRupiah(currentGrandTotal);
            }
        } else {
            // Sembunyikan field Uang Diterima (Konfirmasi Pembayaran NT)
            if (wrapperUangDiterima) wrapperUangDiterima.classList.add('hidden');
        }

        // --- AUTO-FILL DRAFT PELANGGAN & SALES ---
        const inputPelanggan = document.getElementById('inputPelangganKonfirmasi');
        const inputSales = document.getElementById('inputSalesKonfirmasi');

        if (isLoadedFromDraft) {
            // Jika dari draft, otomatis terisi
            if (inputPelanggan) inputPelanggan.value = currentDraftPelanggan;
            if (inputSales) inputSales.value = currentDraftSales;
        } else {
            // Jika transaksi baru, dikosongkan
            if (inputPelanggan) inputPelanggan.value = '';
            if (inputSales) inputSales.value = '';
        }

        // Buka Modal Konfirmasi
        const modal = document.getElementById('modalKonfirmasiPembayaran');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    // Tutup Modal Konfirmasi Pembayaran
    function tutupModalKonfirmasiPembayaran() {
        const modal = document.getElementById('modalKonfirmasiPembayaran');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    // Format input uang diterima pada modal konfirmasi tunai
    function formatDanHitungUangDiterima(input) {
        let rawVal = input.value.replace(/\D/g, '');
        if (!rawVal) {
            input.value = '';
            return;
        }
        let numVal = parseFloat(rawVal) || 0;
        input.value = formatRupiah(numVal);
    }

    // Eksekusi Simpan Transaksi Pembayaran ke Database
    function eksekusiPembayaranFinal() {
        const isTunai = (activePaymentMethod.toLowerCase() === 'cash' || activePaymentMethod.toLowerCase() === 'tunai');
        let nominalBayarClean = currentGrandTotal;

        if (isTunai) {
            const inputUang = document.getElementById('inputUangDiterima');
            if (inputUang && inputUang.value) {
                nominalBayarClean = parseFloat(inputUang.value.replace(/\D/g, '')) || 0;
            }
            if (nominalBayarClean < currentGrandTotal) {
                alert('Uang yang diterima kurang dari total transaksi!');
                return;
            }
        }

        const pelangganVal = document.getElementById('inputPelangganKonfirmasi')?.value || '';
        const salesVal = document.getElementById('inputSalesKonfirmasi')?.value || '';

        if (!isLoadedFromDraft) {
            if (!pelangganVal) {
                alert('Silakan pilih atau isi nama Pelanggan terlebih dahulu!');
                document.getElementById('inputPelangganKonfirmasi')?.focus();
                return;
            }
            if (!salesVal) {
                alert('Silakan pilih atau isi nama Sales terlebih dahulu!');
                document.getElementById('inputSalesKonfirmasi')?.focus();
                return;
            }
        }

        const btn = document.getElementById('btnEksekusiBayar');
        if (btn) {
            btn.disabled = true;
            btn.innerText = 'Memproses...';
        }

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
                nominal_bayar: nominalBayarClean,
                pelanggan: pelangganVal,
                sales: salesVal
            })
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerText = 'Konfirmasi';
            }

            if (data.success) {
                // 1. Hapus draft dari localStorage jika transaksi berasal dari muat draft
                if (isLoadedFromDraft && activeDraftIndex !== null) {
                    hapusDraftSetelahBayar(activeDraftIndex);
                }
                // 2. Tutup Modal Konfirmasi Pembayaran
                tutupModalKonfirmasiPembayaran();

                // 3. Tampilkan Pop-up Struk Pembayaran
                tampilModalStruk({
                    ref_no: data.ref_no,
                    tanggal: data.tanggal || new Date().toLocaleString('id-ID'),
                    kasir: data.kasir || 'Kasir',
                    metode: activePaymentMethod,
                    items: Object.values(cartData),
                    total: data.grand_total || currentGrandTotal,
                    dibayar: nominalBayarClean,
                    kembalian: data.kembalian || 0
                });

                // 4. Bersihkan Keranjang
                resetKeranjang(true);
                renderCart();

            } else {
                alert(data.message || 'Gagal memproses pembayaran!');
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerText = 'Konfirmasi';
            }
            console.error('Error proses pembayaran:', err);
            alert('Terjadi kesalahan saat memproses pembayaran.');
        });
    }

    // 2. Tampilkan dan Populasikan Data Struk
    function tampilModalStruk(transaksiData) {
        if (!transaksiData) return;

        // 1. Fill data header & info transaksi
        const elNoNota = document.getElementById('strukNoNota');
        const elTanggal = document.getElementById('strukTanggal');
        const elKasir = document.getElementById('strukKasir');
        const elTotal = document.getElementById('strukTotal');
        const elMetode = document.getElementById('strukMetode');

        if (elNoNota) elNoNota.innerText = transaksiData.ref_no || '-';
        if (elTanggal) elTanggal.innerText = transaksiData.tanggal || new Date().toLocaleString('id-ID');
        if (elKasir) elKasir.innerText = transaksiData.kasir || 'Kasir';
        if (elTotal) elTotal.innerText = formatRupiah(transaksiData.total || 0);
        if (elMetode) elMetode.innerText = (transaksiData.metode || 'Tunai').toUpperCase();

        // 2. Render daftar produk yang dibeli
        const listContainer = document.getElementById('strukListProduk');
        if (listContainer) {
            listContainer.innerHTML = ''; // Clear item dummy
            
            const items = Array.isArray(transaksiData.items) 
                ? transaksiData.items 
                : Object.values(transaksiData.items || {});

            items.forEach(item => {
                const qty = item.qty || 1;
                const nama = item.nama_produk || item.name || 'Produk';
                const subtotal = item.subtotal || (item.price * qty) || 0;

                listContainer.innerHTML += `
                    <div class="flex justify-between items-start text-xs">
                        <div>
                            <p class="font-semibold text-gray-900">${qty}x ${nama}</p>
                        </div>
                        <span class="font-semibold text-gray-900">${formatRupiah(subtotal)}</span>
                    </div>
                `;
            });
        }

        // 3. Handle Rincian Pembayaran Tunai (Uang Diterima & Kembalian)
        const wrapperTunai = document.getElementById('wrapperDetailTunai');
        const elDibayar = document.getElementById('strukDibayar');
        const elKembali = document.getElementById('strukKembali');

        const isTunai = (transaksiData.metode && (
            transaksiData.metode.toLowerCase() === 'cash' || 
            transaksiData.metode.toLowerCase() === 'tunai'
        ));

        if (isTunai && wrapperTunai) {
            wrapperTunai.classList.remove('hidden');
            if (elDibayar) elDibayar.innerText = formatRupiah(transaksiData.dibayar || 0);
            if (elKembali) elKembali.innerText = formatRupiah(transaksiData.kembalian || 0);
        } else if (wrapperTunai) {
            wrapperTunai.classList.add('hidden');
        }

        // 4. Tampilkan Modal
        const modal = document.getElementById('modalStrukPembayaran');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    // 3. Fungsi Tutup Modal Struk
    function tutupModalStruk() {
        const modal = document.getElementById('modalStrukPembayaran');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function tutupModalStrukKlikLuar(e) {
        if (e.target.id === 'modalStrukPembayaran') {
            tutupModalStruk();
        }
    }

    // -------------------------------------------------------------------------
    // 12. FUNGSI DRAFT TRANSAKSI (LOCAL STORAGE)
    // -------------------------------------------------------------------------
    function modalTambahArsip() {
        if (Object.keys(cartData).length === 0) {
            alert('Keranjang masih kosong, tidak ada yang dapat disimpan ke draft!');
            return;
        }
        const modal = document.getElementById('modalTambahArsip');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function tutupModalTambahArsip() {
        const modal = document.getElementById('modalTambahArsip');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function simpanDraftTransaksi() {
        const pelanggan = document.getElementById('inputPelangganDraft')?.value || '';
        const sales = document.getElementById('inputSalesDraft')?.value || '';

        let drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
        drafts.push({
            id: Date.now(),
            pelanggan: pelanggan,
            sales: sales,
            cart: JSON.parse(JSON.stringify(cartData)),
            voucher: voucherAmount,
            discount_percent: discountPercent,
            time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
        });

        localStorage.setItem('pos_drafts', JSON.stringify(drafts));
        tutupModalTambahArsip();
        resetKeranjang(true);
        alert('Draft transaksi berhasil disimpan!');
    }

    function modalDraftTransaksi() {
        renderDraftList();
        const modal = document.getElementById('modalDraftTransaksi');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function tutupModalDraftTransaksi() {
        const modal = document.getElementById('modalDraftTransaksi');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function tutupModalDraftKlikLuar(event) {
        if (event.target.id === 'modalDraftTransaksi') {
            tutupModalDraftTransaksi();
        }
    }

    function renderDraftList() {
        const container = document.getElementById('listDraftContainer');
        if (!container) return;

        const drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
        if (drafts.length === 0) {
            container.innerHTML = `
                <div class="p-6 text-center text-gray-500 text-sm">
                    Belum ada draft transaksi yang disimpan.
                </div>
            `;
            return;
        }

        let html = '';
        drafts.forEach((d, idx) => {
            html += `
                <div class="flex items-center justify-between p-3.5 bg-white border border-gray-800 rounded-xl">
                    <div>
                        <h4 class="font-bold text-black text-sm">${escapeHtml(d.pelanggan)}</h4>
                        <span class="text-xs text-gray-400 font-medium">${escapeHtml(d.time)}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            onclick="hapusDraft(${idx})"
                            class="px-3 py-1.5 bg-white border border-gray-800 hover:bg-gray-100 text-black rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                            Hapus
                        </button>
                        <button
                            type="button"
                            onclick="muatDraft(${idx})"
                            class="px-4 py-1.5 bg-[#0F1D3A] hover:bg-[#0A1428] text-white rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                            Muat
                        </button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    // Variabel global untuk menampung data draft aktif
    let isLoadedFromDraft = false;
    let activeDraftIndex = null; // Menampung indeks draft yang sedang diisi
    let currentDraftPelanggan = '';
    let currentDraftSales = '';

    function muatDraft(index) {
        let drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
        if (drafts[index]) {
            cartData = drafts[index].cart || {};
            voucherAmount = drafts[index].voucher || 0;
            discountPercent = drafts[index].discount_percent || 0;

            // Simpan pelanggan dan sales dari draft ke variabel global
            isLoadedFromDraft = true;
            activeDraftIndex = index; // Simpan indeks draft
            currentDraftPelanggan = drafts[index].pelanggan || '';
            currentDraftSales = drafts[index].sales || '';

            renderCart();
            simpanKeSession();
            tutupModalDraftTransaksi();
        }
    }

    function hapusDraft(index) {
        let drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
        drafts.splice(index, 1);
        localStorage.setItem('pos_drafts', JSON.stringify(drafts));
        renderDraftList();
        resetKeranjang(true);
        tutupModalDraftTransaksi();
    }

    // Helper Function: Menghapus draft dari localStorage setelah pembayaran sukses
    function hapusDraftSetelahBayar(index) {
        let drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
        if (drafts[index] !== undefined) {
            drafts.splice(index, 1);
            localStorage.setItem('pos_drafts', JSON.stringify(drafts));
        }
        // Reset index penanda
        activeDraftIndex = null;
        isLoadedFromDraft = false;
    }

    // ESC Keyboard Shortcut
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            tutupModalDraftTransaksi();
            tutupModalBarcode();
            tutupModalDiskonItem();
            tutupModalDiskonGlobal();
            modalTutupMetodePembayaran();
            tutupModalTambahArsip();
            tutupModalTutupKasir();
            tutupModalKonfirmasiPembayaran();
        }
    });

    // -------------------------------------------------------------------------
    // 13. FILTER PENCARIAN & HELPER
    // -------------------------------------------------------------------------
    function filterProdukKatalog() {
        // Implementasi pencarian lokal jika katalog produk dirender
    }

    function formatRupiah(number) {
        return 'Rp ' + Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Inisialisasi awal saat DOM siap
    document.addEventListener('DOMContentLoaded', function() {
        if (isKasirOpen) {
            renderCart();
        }
    });
</script>
@endpush
@endsection