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
    {{-- BAGIAN UTAMA: SCAN, KERANJANG, DAN BAR TOTAL (SESUAI MOCKUP)          --}}
    {{-- ===================================================================== --}}
    <div id="mainCenterSection" class="{{ !empty($isKasirOpen) ? 'flex' : 'hidden' }} flex-1 flex-col h-full overflow-hidden bg-secondary-bw">

        {{-- ----------------------------------------------------------------- --}}
        {{-- AREA ATAS: INPUT SCAN + DAFTAR KERANJANG (SCROLL DI DALAM LIST)   --}}
        {{-- ----------------------------------------------------------------- --}}
        <div id="viewKatalogNormal" class="flex-1 min-h-0 flex flex-col overflow-hidden px-6 pt-6">

            {{-- 1. INPUT SCAN BARCODE / PENCARIAN PRODUK ITM --}}
            <div class="relative flex-shrink-0" id="containerSearchUtama">
                <form
                    id="formScanUtama"
                    action="{{ route('POS.scan') }}"
                    method="POST"
                    onsubmit="return handleScanSubmit(event)"
                    class="flex items-center gap-3 bg-white border border-black rounded-2xl pl-5 pr-3 py-3 transition-all hover:border-primary-db hover:shadow-sm focus-within:border-primary-db focus-within:ring-2 focus-within:ring-primary-db/30 focus-within:shadow-sm">
                    @csrf
                    <svg class="w-5 h-5 text-black flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M2 4h2v16H2V4zm3 0h1v16H5V4zm2 0h2v16H7V4zm3 0h1v16h-1V4zm2 0h3v16h-3V4zm4 0h1v16h-1V4zm2 0h2v16h-2V4zm3 0h1v16h-1V4z" />
                    </svg>
                    <input
                        type="text"
                        name="BC_OUT"
                        id="inputScanUtama"
                        autocomplete="off"
                        oninput="handleSearchItmInput(this.value)"
                        onkeydown="handleSearchItmKeydown(event)"
                        onfocus="handleSearchItmFocus()"
                        placeholder="Cari nama, harga, kode produk (ITM) / scan barcode..."
                        class="flex-1 min-w-0 bg-transparent text-base text-black placeholder-gray-500 outline-none" />

                    {{-- Tombol Clear Search --}}
                    <button
                        type="button"
                        id="btnClearSearch"
                        onclick="clearSearchItm()"
                        class="hidden p-1.5 text-gray-400 hover:text-gray-700 rounded-full hover:bg-gray-100 transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    {{-- Tombol Scan -> membuka modal barcode --}}
                    <button
                        type="button"
                        onclick="bukaModalBarcode()"
                        class="h-12 px-7 bg-primary-db hover:bg-primary-sdb text-white text-sm font-bold rounded-xl flex items-center gap-2.5 cursor-pointer transition-colors flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
                        </svg>
                        Scan
                    </button>
                </form>

                {{-- PANEL HASIL PENCARIAN ITM (DI ATAS CONTAINER KERANJANG) --}}
                <div
                    id="panelHasilSearchItm"
                    class="hidden absolute left-0 right-0 top-full mt-2 bg-white rounded-2xl shadow-2xl border border-gray-300 z-50 max-h-88 overflow-y-auto divide-y divide-gray-100">
                    <div id="loadingSearchItm" class="hidden p-4 text-center text-sm text-gray-500 font-medium">
                        <div class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Mencari di tabel ITM...</span>
                        </div>
                    </div>
                    <div id="listHasilSearchItm" class="divide-y divide-gray-100"></div>
                </div>
            </div>

            {{-- 2. INFO ITEM TERAKHIR YANG DITAMBAHKAN (DIISI VIA JAVASCRIPT) --}}
            <p id="infoItemTerakhir" class="hidden flex-shrink-0 items-center gap-2 mt-3 text-sm text-black">
                <span class="w-2 h-2 rounded-full bg-black flex-shrink-0"></span>
                <span id="infoItemTerakhirText"></span>
            </p>

            {{-- 3. JUDUL KERANJANG --}}
            <div class="flex items-baseline justify-between flex-shrink-0 mt-6">
                <div class="flex items-baseline gap-4">
                    <h2 class="text-lg font-bold text-black tracking-tight">Keranjang</h2>
                    <span id="labelJumlahItem" class="text-sm text-gray-500">0 item</span>
                </div>
            </div>

            {{-- 4. HEADER KOLOM TABEL (grid harus sama dengan baris item di script) --}}
            <div class="flex-shrink-0 grid grid-cols-[minmax(0,3.2fr)_1.3fr_1fr_1fr_1.4fr_56px] gap-4 items-center px-6 mt-4 mb-2 text-xs font-medium text-black">
                <span class="pl-[72px]">BARANG</span>
                <span>HARGA</span>
                <span>DISKON</span>
                <span>VOUCHER</span>
                <span class="text-right">SUBTOTAL</span>
                <span class="text-center">AKSI</span>
            </div>

            {{-- 5. DAFTAR ITEM KERANJANG (DIRENDER OTOMATIS OLEH JAVASCRIPT) --}}
            <div id="cartItemsList" class="flex-1 min-h-0 overflow-y-auto space-y-3 pb-4">
                {{-- Item keranjang di-render via JavaScript --}}
            </div>
        </div>

        {{-- ----------------------------------------------------------------- --}}
        {{-- BAR BAWAH: TOTAL, RINCIAN, DAN TOMBOL AKSI (SESUAI REFERENSI)     --}}
        {{-- Kiri: Subtotal + TOTAL | Kanan: Kosongkan, Draft, %, Metode, Bayar --}}
        {{-- (id rightOrderPanel dipertahankan agar JS buka/tutup kasir aman)  --}}
        {{-- ----------------------------------------------------------------- --}}
        <div id="rightOrderPanel" class="flex-shrink-0 flex items-center justify-between gap-6 bg-white border-t border-black px-6 py-3">

            {{-- Kiri: Subtotal + TOTAL (sesuai referensi) --}}
            <div class="min-w-0 leading-tight select-none self-center">
                <span class="block text-[13px] text-gray-500 font-normal">Subtotal</span>
                <span id="labelSubtotal" class="block text-[15px] text-black font-medium">Rp 0</span>
                <span class="block text-[11px] text-gray-500 font-medium tracking-wide mt-1.5">TOTAL</span>
                <div class="flex items-baseline gap-2.5 flex-wrap">
                    <span id="labelGrandTotal" class="text-[26px] leading-none font-extrabold text-black tracking-tight">Rp 0</span>
                    <span id="labelPromoGlobal" class="hidden text-[13px] font-medium text-gray-500 whitespace-nowrap">Diskon: Rp 0&nbsp;&nbsp;Voucher: Rp 0</span>
                </div>
                {{-- Rincian diskon/voucher tetap di DOM (hidden) agar JS tidak error, tidak tampil sesuai referensi --}}
                <span id="labelDiskonGlobal" class="hidden">Rp 0</span>
                <span id="labelVoucherGlobal" class="hidden">Rp 0</span>
                {{-- Dipertahankan untuk JS (nilai metode pembayaran aktif) --}}
                <span id="metodePembayaran" class="hidden">Cash</span>
            </div>

            {{-- Kanan: Tombol aksi (vertikal center / floating sesuai referensi) --}}
            <div class="flex items-center gap-2.5 shrink-0 self-center">
                {{-- Kosongkan Keranjang (trash) --}}
                <button
                    type="button"
                    onclick="kosongkanKeranjang()"
                    title="Kosongkan keranjang"
                    class="w-11 h-11 flex items-center justify-center bg-white border border-black rounded-[10px] text-black transition-all cursor-pointer hover:border-primary-db hover:text-primary-db hover:bg-[#EEF2FF] hover:shadow-sm active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-db/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </button>

                {{-- Simpan Draft / Arsip --}}
                <button
                    type="button"
                    onclick="modalTambahArsip()"
                    title="Simpan sebagai draft"
                    class="w-11 h-11 flex items-center justify-center bg-white border border-black rounded-[10px] text-black transition-all cursor-pointer hover:border-primary-db hover:text-primary-db hover:bg-[#EEF2FF] hover:shadow-sm active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-db/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3M2.25 13.5v4.5A2.25 2.25 0 004.5 20.25h15a2.25 2.25 0 002.25-2.25v-4.5" />
                    </svg>
                </button>

                {{-- Diskon Global + Voucher (%) -> buka modal diskon & voucher --}}
                <button
                    type="button"
                    onclick="modalDiskonGlobal()"
                    title="Diskon global & voucher"
                    class="w-11 h-11 flex items-center justify-center bg-white border border-black rounded-[10px] text-black transition-all cursor-pointer hover:border-primary-db hover:text-primary-db hover:bg-[#EEF2FF] hover:shadow-sm active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-db/40">
                    <span class="text-lg font-extrabold leading-none tracking-tight select-none">%</span>
                </button>

                {{-- Metode Pembayaran (single button sesuai referensi) --}}
                <button
                    type="button"
                    id="btnMetodePembayaran"
                    onclick="modalMetodePembayaran()"
                    class="h-11 px-5 flex items-center bg-white border border-black rounded-[10px] text-[13px] font-bold text-black transition-all cursor-pointer whitespace-nowrap hover:border-primary-db hover:text-primary-db hover:bg-[#EEF2FF] hover:shadow-sm active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-db/40">
                    <span id="labelMetodeAktif">Metode Pembayaran</span>
                </button>

                {{-- Konfirmasi & Bayar --}}
                <button
                    type="button"
                    onclick="prosesPembayaran()"
                    class="h-11 px-5 bg-primary-db hover:bg-primary-sdb text-white text-[13px] font-bold rounded-[10px] transition-colors cursor-pointer whitespace-nowrap">
                    Konfirmasi &amp; Bayar
                </button>
            </div>
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- MODAL 1: POPUP SCAN BARCODE (SUBMIT DENGAN REFRESH & SESSION)         --}}
    {{-- ===================================================================== --}}
    <div
        id="modalBarcode"
        class="fixed inset-0 bg-black/50 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">

        {{-- Card Modal --}}
        <div class="w-full max-w-2xl bg-[#F7F8FC] rounded-2xl p-6 shadow-2xl space-y-4 border border-[#E2E8F0] max-h-[92vh] overflow-y-auto">

            {{-- Header Modal --}}
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-black tracking-tight">Scan Barcode Kamera</h3>
                <button
                    type="button"
                    onclick="tutupModalBarcode()"
                    class="text-gray-400 hover:text-gray-700 text-2xl font-bold leading-none cursor-pointer">
                    &times;
                </button>
            </div>

            {{-- KAMERA: live (HTTPS/localhost/HTTP) atau foto --}}
            <div class="w-full">
                @include('inventory.partials.barcode-camera', ['cameraId' => 'kasir'])
            </div>

            {{-- Form Input Manual Kode Barcode --}}
            <form action="{{ route('POS.scan') }}" method="POST" id="formScanKasir">
                @csrf
                <div class="flex items-center gap-2">
                    <input
                        type="text"
                        name="BC_OUT"
                        id="inputManualBarcode"
                        inputmode="text"
                        autocomplete="off"
                        autocapitalize="off"
                        spellcheck="false"
                        placeholder="Atau scan / ketik kode barcode manual..."
                        required
                        class="flex-1 bg-white border border-[#2D3748] rounded-xl px-4 py-2.5 text-sm text-black placeholder-gray-500 outline-none focus:ring-2 focus:ring-[#101D36]" />

                    <button
                        type="submit"
                        class="bg-[#101D36] hover:bg-[#1A2C50] active:bg-black text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors cursor-pointer">
                        Cari
                    </button>
                </div>
            </form>

            {{-- Tombol Batal --}}
            <div>
                <button
                    type="button"
                    onclick="tutupModalBarcode()"
                    class="w-full bg-white hover:bg-gray-100 active:bg-gray-200 border border-slate-300 rounded-xl py-2.5 text-center text-sm font-semibold text-black transition-colors cursor-pointer">
                    Tutup
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
            class="w-full max-w-lg bg-[#F8FAFC] rounded-3xl p-6 shadow-2xl space-y-4 border border-gray-200">

            <!-- Header Modal -->
            <div class="flex items-start justify-between pb-2 border-b border-gray-300">
                <div>
                    <h3 class="text-2xl font-extrabold text-black tracking-tight">Draft Transaksi</h3>
                    <p id="draftCountText" class="text-xs text-gray-500 font-medium mt-1">0 transaksi tersimpan</p>
                </div>
                <button
                    type="button"
                    onclick="tutupModalDraftTransaksi()"
                    class="w-8 h-8 flex items-center justify-center border border-gray-400 rounded-lg text-black hover:bg-gray-200 transition-colors font-bold text-sm cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- Container List Draft (Max Height setara 4 Item, Scrollbar Khusus List) -->
            <div id="listDraftContainer" class="space-y-3 max-h-[360px] overflow-y-auto pr-1">
                <!-- Diisi via JavaScript dari localStorage -->
            </div>

            <!-- Footer Modal -->
            <div class="pt-3 border-t border-gray-300 flex items-center justify-between">
                <span class="text-xs text-gray-400 font-medium">Klik Lanjutkan untuk memuat draft ke keranjang</span>
                <button
                    type="button"
                    onclick="tutupModalDraftTransaksi()"
                    class="px-6 py-2 bg-white border border-gray-800 hover:bg-gray-100 text-black rounded-xl text-sm font-bold transition-colors cursor-pointer">
                    Tutup
                </button>
            </div>

        </div>
    </div>



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
                            type="text"
                            inputmode="numeric"
                            id="kasOpening"
                            placeholder="Rp 0"
                            oninput="formatInputRupiah(this)"
                            class="input-rupiah w-full bg-white border rounded-lg px-3 py-2.5 text-sm text-black outline-none pr-8 font-medium" />
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

    <!-- Modal Diskon Global + Voucher (dibuka via tombol % sesuai referensi) -->
    <div
        id="modalDiskonGlobal"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
        <div class="w-full max-w-sm bg-secondary-bw rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-200">

            <!-- Judul Modal -->
            <h3 class="text-xl font-bold text-black tracking-tight">Diskon &amp; Voucher</h3>

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
                        oninput="if(parseFloat(this.value) > 100) this.value = 100; if(parseFloat(this.value) < 0) this.value = 0;"
                        placeholder="0%"
                        class="w-full bg-white placeholder-gray-400 rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium border" />
                </div>

                <!-- Input Voucher (Rp) -->
                <div>
                    <label for="inputVoucher" class="block text-sm font-semibold text-black mb-1">
                        Voucher (Rp)
                    </label>
                    <input
                        type="text"
                        inputmode="numeric"
                        id="inputVoucher"
                        placeholder="Rp 0"
                        oninput="formatInputRupiah(this)"
                        class="input-rupiah w-full bg-white placeholder-gray-400 rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium border" />
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
            <div class="space-y-2">
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
                    <select
                        id="inputPelangganDraft"
                        class="searchable-select w-full bg-white border border-gray-800 rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium">
                        <option value="">Cari Pelanggan</option>
                        @foreach($customers ?? [] as $cst)
                        <option value="{{ $cst->DES }}">{{ $cst->DES }} ({{ $cst->CST }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Input Sales -->
                <div>
                    <label for="inputSalesDraft" class="block text-sm font-semibold text-black mb-1">
                        Sales
                    </label>
                    <select
                        id="inputSalesDraft"
                        class="searchable-select w-full bg-white border border-gray-800 rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium">
                        <option value="">Cari Sales</option>
                        @foreach($salesList ?? [] as $sls)
                        <option value="{{ $sls->DES }}">{{ $sls->DES }} ({{ $sls->SLS }})</option>
                        @endforeach
                    </select>
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
                    <select
                        id="inputPelangganKonfirmasi"
                        class="searchable-select w-full bg-white border rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium">
                        <option value="">Cari / Pilih Pelanggan</option>
                        @foreach($customers ?? [] as $cst)
                        <option value="{{ $cst->DES }}">{{ $cst->DES }} ({{ $cst->CST }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Field 3: Sales -->
                <div class="space-y-1">
                    <label for="inputSalesKonfirmasi" class="block text-sm font-semibold text-black">
                        Sales
                    </label>
                    <select
                        id="inputSalesKonfirmasi"
                        class="searchable-select w-full bg-white border rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium">
                        <option value="">Cari / Pilih Sales</option>
                        @foreach($salesList ?? [] as $sls)
                        <option value="{{ $sls->DES }}">{{ $sls->DES }} ({{ $sls->SLS }})</option>
                        @endforeach
                    </select>
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

    {{-- ===================================================================== --}}
    {{-- [TAMBAHAN PRINT] FORM TERSEMBUNYI -> POST KE CONTROLLER PRINT (DomPDF) --}}
    {{-- ===================================================================== --}}
    <form
        id="formPrintStruk"
        method="POST"
        action="{{ route('POS.struk.print') }}"
        target="_blank"
        class="hidden">
        @csrf
        <input type="hidden" name="ref_no" id="printRefNo">
        <input type="hidden" name="tanggal" id="printTanggal">
        <input type="hidden" name="waktu" id="printWaktu">
        <input type="hidden" name="kasir" id="printKasir">
        <input type="hidden" name="metode" id="printMetode">
        <input type="hidden" name="subtotal" id="printSubtotal">
        <input type="hidden" name="discount_item" id="printDiscountItem">
        <input type="hidden" name="discount_global" id="printDiscountGlobal">
        <input type="hidden" name="voucher" id="printVoucher">
        <input type="hidden" name="total" id="printTotal">
        <input type="hidden" name="dibayar" id="printDibayar">
        <input type="hidden" name="kembalian" id="printKembalian">
        <input type="hidden" name="items" id="printItems">
    </form>

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
    let voucherAmount = {{ (float)($voucher ?? 0) }};
    let discountPercent = {{ (float)($discountPercent ?? 0) }};
    let isKasirOpen = {{ !empty($isKasirOpen) ? 'true' : 'false' }};
    let activeItemKeyForDiscount = null;
    let currentGrandTotal = 0;
    let activePaymentMethod = 'Cash';
    let selectedBankName = '';
    let hasPickedPaymentMethod = false;

    // State draft transaksi (dideklarasikan di awal agar aman dipakai semua fungsi)
    let isLoadedFromDraft = false;
    let activeDraftIndex = null; // Menampung indeks draft yang sedang diisi
    let currentDraftPelanggan = '';
    let currentDraftSales = '';

    // [TAMBAHAN PRINT] Menampung data struk transaksi terakhir (untuk print DomPDF)
    let strukTerakhir = null;

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
        const rawKasOpening = document.getElementById('kasOpening').value;
        const cleanOpening = parseFloat(rawKasOpening.replace(/\D/g, '')) || 0;
        const catatanOpening = document.getElementById('catatanOpening').value;

        if (rawKasOpening === '' || cleanOpening < 0) {
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
                        kas_opening: cleanOpening,
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

                    // Tampilkan area utama (scan & keranjang) & bar total
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

                    // Tampilkan tombol Draft Transaksi di Header
                    const btnHeaderDraft = document.getElementById('btnHeaderDraft');
                    if (btnHeaderDraft) {
                        btnHeaderDraft.classList.remove('hidden');
                        btnHeaderDraft.classList.add('flex');
                    }
                    updateDraftBadgeHeader();

                    tutupModalBukaKasir();
                    renderCart();

                    const inputScan = document.getElementById('inputScanUtama');
                    if (inputScan) inputScan.focus();
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

                    // Sembunyikan area kasir aktif
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

                    // Sembunyikan tombol Draft Transaksi di Header
                    const btnHeaderDraft = document.getElementById('btnHeaderDraft');
                    if (btnHeaderDraft) {
                        btnHeaderDraft.classList.add('hidden');
                        btnHeaderDraft.classList.remove('flex');
                    }

                    tutupModalTutupKasir();
                    hapusDraft();
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
    // 3. FUNGSI RENDER TAMPILAN KERANJANG (BARIS KARTU SESUAI MOCKUP)
    // -------------------------------------------------------------------------
    function renderCart() {
        const container = document.getElementById('cartItemsList');
        if (!container) return;

        const keys = Object.keys(cartData);

        // Jumlah item di judul "Keranjang"
        const elJumlah = document.getElementById('labelJumlahItem');
        if (elJumlah) elJumlah.innerText = hitungTotalQty() + ' item';

        // Jika keranjang kosong
        if (keys.length === 0) {
            container.innerHTML = `
                <div class="p-10 text-center text-gray-900 bg-white border border-dashed border-gray-400 rounded-2xl">
                    <p class="text-sm font-medium">Keranjang masih kosong</p>
                    <p class="text-xs mt-1 text-gray-500">Scan barcode unit produk untuk memulai</p>
                </div>
            `;
            const info = document.getElementById('infoItemTerakhir');
            if (info) {
                info.classList.add('hidden');
                info.classList.remove('flex');
            }
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
                <div class="grid grid-cols-[minmax(0,3.2fr)_1.3fr_1fr_1fr_1.4fr_56px] gap-4 items-center bg-white border border-black rounded-2xl px-5 py-4">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-14 h-14 flex-shrink-0 rounded-xl border border-black flex items-center justify-center text-black">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125v4.125m3-4.125v4.125M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-black truncate" title="${escapeHtml(item.description || '')}">${escapeHtml(item.name)}</p>
                            <p class="text-[11px] text-gray-500 font-mono truncate">${escapeHtml(item.code || key)}${qty > 1 ? ' · x' + qty : ''}</p>
                        </div>
                    </div>

                    <span class="text-sm text-gray-600">${formatRupiah(itemPrice)}</span>

                    {{-- Input Diskon Langsung (%) + nominal hasil (render saat blur/Enter, bukan saat mengetik) --}}
                    <div class="w-24 flex flex-col items-center">
                        <div class="relative w-full">
                            <input
                                type="number"
                                min="0"
                                max="100"
                                value="${itemDiscountPct > 0 ? itemDiscountPct : ''}"
                                placeholder="0"
                                title="Diskon (%)"
                                onfocus="tahanHasilDiskonItem(this)"
                                onkeydown="if(event.key==='Enter'){this.blur();}"
                                onblur="commitDiskonItemLangsung('${key}', this)"
                                class="w-full h-8 bg-white border border-black rounded-lg px-2.5 pr-6 text-xs font-semibold text-black placeholder-gray-400 outline-none text-right focus:ring-1 focus:ring-black [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-500 pointer-events-none">%</span>
                        </div>
                        ${discFromPct > 0 ? `<span class="mt-1 text-[13px] font-semibold text-gray-500 whitespace-nowrap">Rp ${Number(discFromPct * qty).toLocaleString('id-ID')}</span>` : ``}
                    </div>

                    {{-- Input Voucher Langsung (Rp) --}}
                    <div class="relative w-28">
                        <input
                            type="text"
                            inputmode="numeric"
                            value="${itemDiscountVoucher > 0 ? formatRupiah(itemDiscountVoucher) : ''}"
                            placeholder="Rp 0"
                            title="Voucher (Rp)"
                            oninput="formatInputRupiah(this); ubahVoucherItemLangsung('${key}', this.value)"
                            class="input-rupiah w-full h-8 bg-white border border-black rounded-lg px-2 text-xs font-semibold text-black placeholder-gray-400 outline-none text-right focus:ring-1 focus:ring-black" />
                    </div>

                    <span id="subtotalItem-${key}" class="text-right text-lg font-bold text-black font-number">${formatRupiah(itemTotal)}</span>

                    <button
                        type="button"
                        onclick="hapusItem('${key}')"
                        title="Hapus Item"
                        class="mx-auto w-8 h-8 bg-red-600 hover:bg-red-700 text-white rounded-md flex items-center justify-center cursor-pointer transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                </div>
            `;
        });

        container.innerHTML = html;
        hitungRingkasan();
    }

    // Total qty seluruh item di keranjang
    function hitungTotalQty() {
        let total = 0;
        Object.keys(cartData).forEach(k => {
            total += (parseInt(cartData[k].qty) || 1);
        });
        return total;
    }

    // Badge jumlah draft di header (pill "Draft Transaksi")
    function updateDraftBadgeHeader(count) {
        let n = count;
        if (typeof n === 'undefined' || n === null) {
            try {
                const drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
                n = Array.isArray(drafts) ? drafts.length : 0;
            } catch (e) {
                n = 0;
            }
        }
        const badgeHeader = document.getElementById('DraftCountBadgeHeader');
        if (!badgeHeader) return;
        if (n > 0) {
            badgeHeader.innerText = n;
            badgeHeader.classList.remove('hidden');
            badgeHeader.classList.add('inline-flex');
        } else {
            badgeHeader.classList.add('hidden');
            badgeHeader.classList.remove('inline-flex');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateDraftBadgeHeader();
    });

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

        // Grand Total = Subtotal - Voucher Global - Diskon Transaksi Global
        let grandTotal = Math.max(0, subtotal - voucherAmount - diskonTransaksiNominal);
        currentGrandTotal = grandTotal;

        // Perbarui teks di bar total
        const elSubtotal = document.getElementById('labelSubtotal');
        if (elSubtotal) elSubtotal.innerText = formatRupiah(subtotal);

        // Label di blade sudah diawali "Diskon −" / "Voucher −", jadi cukup nominalnya
        const elDiskonGlobal = document.getElementById('labelDiskonGlobal');
        if (elDiskonGlobal) elDiskonGlobal.innerText = formatRupiah(diskonTransaksiNominal);

        const elVoucherGlobal = document.getElementById('labelVoucherGlobal');
        if (elVoucherGlobal) elVoucherGlobal.innerText = formatRupiah(voucherAmount);

        // Ringkasan promo global di samping TOTAL (hanya tampil jika ada diskon/voucher)
        const elPromoGlobal = document.getElementById('labelPromoGlobal');
        if (elPromoGlobal) {
            if (diskonTransaksiNominal > 0 || voucherAmount > 0) {
                elPromoGlobal.innerHTML = `Diskon: ${formatRupiah(diskonTransaksiNominal)}&nbsp;&nbsp;Voucher: ${formatRupiah(voucherAmount)}`;
                elPromoGlobal.classList.remove('hidden');
            } else {
                elPromoGlobal.classList.add('hidden');
            }
        }

        const elGrandTotal = document.getElementById('labelGrandTotal');
        if (elGrandTotal) elGrandTotal.innerText = formatRupiah(grandTotal);

        const elMetode = document.getElementById('metodePembayaran');
        if (elMetode) elMetode.innerText = activePaymentMethod;

        updateTombolMetode();
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
            activePaymentMethod = 'Cash';
            selectedBankName = '';
            hasPickedPaymentMethod = false;
            isLoadedFromDraft = false;
            currentDraftPelanggan = '';
            currentDraftSales = '';
            const elMetode = document.getElementById('metodePembayaran');
            if (elMetode) elMetode.innerText = 'Cash';
            renderCart();
            updateTombolMetode();
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

    let html5QrcodeScanner = null;
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
        // Matikan kamera supaya lampu kamera padam
        if (window.BarcodeCamera) BarcodeCamera.stop('kasir');
    }

    // Sambungkan hasil scan kamera ke form kasir
    (function() {
        const camKasir = document.getElementById('bcam-kasir');
        if (!camKasir) return;

        camKasir.addEventListener('barcode:scan', function(e) {
            var kode = String(e.detail.code || '').replace(/[\u0000-\u001F\u007F]/g, '').trim();
            if (!kode) return;

            // Isi ke form manual di modal
            var inputModal = document.getElementById('inputManualBarcode');
            if (inputModal) inputModal.value = kode;

            // Juga isi ke input scan utama (karena form submit ke POS.scan)
            var inputUtama = document.getElementById('inputScanUtama');
            if (inputUtama) inputUtama.value = kode;

            // Submit form scan kasir
            var form = document.getElementById('formScanKasir');
            if (form) {
                // keepResume: setelah halaman reload, kamera live menyala lagi
                if (window.BarcodeCamera) BarcodeCamera.stop('kasir', {
                    keepResume: true
                });
                form.requestSubmit();
            }
        });
    })();

    // -------------------------------------------------------------------------
    // 8. FUNGSI EDIT DISKON & VOUCHER PER ITEM LANGSUNG DI CARD
    // -------------------------------------------------------------------------
    let syncCartDebounceTimer = null;

    function ubahDiskonItemLangsung(key, inputEl) {
        // Dipakai saat blur/Enter via commitDiskonItemLangsung agar tidak re-render saat mengetik.
        commitDiskonItemLangsung(key, inputEl);
    }

    // Tandai input sedang diketik: tahan hasil lama, jangan hitung ulang dulu
    function tahanHasilDiskonItem(inputEl) {
        if (inputEl) inputEl.dataset.mengetik = '1';
    }

    // Commit diskon item saat input tidak dipilih lagi (blur / Enter):
    // baru simpan + render ulang hasil nominal di bawah box.
    function commitDiskonItemLangsung(key, inputEl) {
        if (!cartData[key]) return;
        if (inputEl) delete inputEl.dataset.mengetik;

        let rawVal = (typeof inputEl === 'object' && inputEl !== null) ? inputEl.value : inputEl;
        let val = parseFloat(rawVal) || 0;

        if (val > 100) {
            val = 100;
            if (typeof inputEl === 'object' && inputEl !== null) {
                inputEl.value = 100;
            }
        } else if (val < 0) {
            val = 0;
            if (typeof inputEl === 'object' && inputEl !== null) {
                inputEl.value = 0;
            }
        }

        // Jika nilai sama, cukup pastikan ringkasan sinkron tanpa render ulang
        if ((parseFloat(cartData[key].discount) || 0) === val) {
            hitungRingkasan();
            return;
        }

        cartData[key].discount = val;

        renderCart();
        simpanKeSession();
    }

    function ubahVoucherItemLangsung(key, rawVal) {
        if (!cartData[key]) return;
        let cleaned = typeof rawVal === 'string' ? rawVal.replace(/\D/g, '') : rawVal;
        let val = parseFloat(cleaned) || 0;
        val = Math.max(0, val);
        cartData[key].discount_voucher = val;

        updateSubtotalBarisItem(key);
        hitungRingkasan();
        triggerSyncCartDebounced();
    }

    function updateSubtotalBarisItem(key) {
        const item = cartData[key];
        if (!item) return;

        const itemDiscountPct = parseFloat(item.discount) || 0;
        const itemDiscountVoucher = parseFloat(item.discount_voucher) || 0;
        const itemPrice = parseFloat(item.price) || 0;
        const qty = parseInt(item.qty) || 1;

        const discFromPct = itemPrice * (itemDiscountPct / 100);
        const priceAfterDiscount = Math.max(0, itemPrice - discFromPct - itemDiscountVoucher);
        const itemTotal = priceAfterDiscount * qty;

        const el = document.getElementById('subtotalItem-' + key);
        if (el) {
            el.innerText = formatRupiah(itemTotal);
        }
    }

    function triggerSyncCartDebounced() {
        clearTimeout(syncCartDebounceTimer);
        syncCartDebounceTimer = setTimeout(() => {
            simpanKeSession();
        }, 400);
    }

    // -------------------------------------------------------------------------
    // 9. FUNGSI DISKON GLOBAL
    // -------------------------------------------------------------------------
    function modalDiskonGlobal() {
        document.getElementById('inputDiskon').value = discountPercent || '';
        document.getElementById('inputVoucher').value = voucherAmount > 0 ? formatRupiah(voucherAmount) : '';

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

        const rawVoucherStr = document.getElementById('inputVoucher').value || '';
        const rawVoucher = parseFloat(rawVoucherStr.replace(/\D/g, '')) || 0;
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
        hasPickedPaymentMethod = true;
        if (['BNI', 'BCA', 'BRI', 'Seabank', 'JAGO'].includes(method)) {
            selectedBankName = method;
        } else {
            selectedBankName = '';
        }

        const elMetode = document.getElementById('metodePembayaran');
        if (elMetode) elMetode.innerText = method;

        updateTombolMetode();
        modalTutupMetodePembayaran();
    }

    // Sinkronkan tampilan toggle Tunai / Non-tunai di bar total
    // Footer button menampilkan pilihan user (cth: JAGO), default "Metode Pembayaran"
    function updateTombolMetode() {
        const btnTunai = document.getElementById('btnMetodeTunai');
        const btnNon = document.getElementById('btnMetodeNonTunai');
        const btnSingle = document.getElementById('btnMetodePembayaran');
        const labelSingle = document.getElementById('labelMetodeAktif');

        const isTunai = (String(activePaymentMethod || 'cash').toLowerCase() === 'cash' || String(activePaymentMethod || '').toLowerCase() === 'tunai');

        // Layout lama (jika masih ada): toggle Tunai / Non-tunai
        if (btnTunai && btnNon) {
            const aktif = ['bg-primary-db', 'text-white', 'border-black', 'font-bold'];
            const nonaktif = ['bg-white', 'text-gray-500', 'border-gray-400', 'font-medium'];

            btnTunai.classList.remove(...(isTunai ? nonaktif : aktif));
            btnTunai.classList.add(...(isTunai ? aktif : nonaktif));

            btnNon.classList.remove(...(isTunai ? aktif : nonaktif));
            btnNon.classList.add(...(isTunai ? nonaktif : aktif));

            // Jika non-tunai, tampilkan nama bank di tombol
            btnNon.innerText = isTunai ? 'Non-tunai' : activePaymentMethod;
        }

        // Layout referensi: satu tombol metode — tampilkan pilihan user
        if (labelSingle) {
            labelSingle.innerText = hasPickedPaymentMethod ? activePaymentMethod : 'Metode Pembayaran';
        }
        if (btnSingle) {
            if (hasPickedPaymentMethod) {
                btnSingle.classList.remove('bg-white', 'text-black');
                btnSingle.classList.add('bg-primary-db', 'text-white');
                // Override hover agar teks tetap putih saat terpilih
                btnSingle.classList.remove('hover:text-primary-db', 'hover:bg-[#EEF2FF]');
                btnSingle.classList.add('hover:bg-primary-sdb', 'hover:border-primary-sdb', 'hover:text-white');
            } else {
                btnSingle.classList.remove('bg-primary-db', 'text-white', 'hover:bg-primary-sdb', 'hover:border-primary-sdb', 'hover:text-white');
                btnSingle.classList.add('bg-white', 'text-black', 'hover:text-primary-db', 'hover:bg-[#EEF2FF]');
            }
        }
    }

    // Kosongkan keranjang (tombol trash sesuai referensi) — pakai logika resetKeranjang
    function kosongkanKeranjang() {
        resetKeranjang(false);
    }

    // -------------------------------------------------------------------------
    // 11. FUNGSI MODAL KONFIRMASI PEMBAYARAN (NT & T) SESUAI DESAIN
    // -------------------------------------------------------------------------

    // Buka Modal Konfirmasi Pembayaran saat tombol "Konfirmasi & Bayar" diklik
    function prosesPembayaran() {
        const keys = Object.keys(cartData);
        if (keys.length === 0) {
            alert('Keranjang masih kosong!');
            return;
        }

        // Hitung total item qty
        const totalItems = hitungTotalQty();

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
            if (inputPelanggan) {
                inputPelanggan.value = currentDraftPelanggan;
                if (typeof jQuery !== 'undefined') $(inputPelanggan).val(currentDraftPelanggan).trigger('change');
            }
            if (inputSales) {
                inputSales.value = currentDraftSales;
                if (typeof jQuery !== 'undefined') $(inputSales).val(currentDraftSales).trigger('change');
            }
        } else {
            // Jika transaksi baru, dikosongkan
            if (inputPelanggan) {
                inputPelanggan.value = '';
                if (typeof jQuery !== 'undefined') $(inputPelanggan).val('').trigger('change');
            }
            if (inputSales) {
                inputSales.value = '';
                if (typeof jQuery !== 'undefined') $(inputSales).val('').trigger('change');
            }
        }

        // Buka Modal Konfirmasi
        const modal = document.getElementById('modalKonfirmasiPembayaran');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
                $(modal).find('select.searchable-select').each(function() {
                    $(this).select2({
                        width: '100%',
                        dropdownParent: $(modal)
                    });
                });
            }
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
                        tanggal: data.tanggal || new Date().toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        }),
                        waktu: data.waktu || new Date().toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        }).replace(':', '.'),
                        kasir: data.kasir || 'Kasir',
                        metode: activePaymentMethod,
                        items: Object.values(cartData),
                        subtotal: data.subtotal,
                        item_discount: data.item_discount,
                        discount_percent: discountPercent,
                        discount_global: data.discount_global,
                        voucher: voucherAmount,
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

    // Tampilkan dan Populasikan Data Struk Sesuai Desain
    function tampilModalStruk(transaksiData) {
        if (!transaksiData) return;

        // [TAMBAHAN PRINT] simpan data struk (salinan) agar bisa dikirim ke Controller print
        strukTerakhir = JSON.parse(JSON.stringify(transaksiData));

        // 1. Fill data header & info transaksi
        const elNoNota = document.getElementById('strukNoNota');
        const elTanggal = document.getElementById('strukTanggal');
        const elWaktu = document.getElementById('strukWaktu');
        const elKasir = document.getElementById('strukKasir');
        const elSubtotal = document.getElementById('strukSubtotal');
        const elTotal = document.getElementById('strukTotal');
        const elMetode = document.getElementById('strukMetode');

        if (elNoNota) elNoNota.innerText = transaksiData.ref_no || '-';
        if (elTanggal) elTanggal.innerText = transaksiData.tanggal || new Date().toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
        if (elWaktu) elWaktu.innerText = transaksiData.waktu || new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        }).replace(':', '.');
        if (elKasir) elKasir.innerText = transaksiData.kasir || 'Kasir';
        if (elMetode) elMetode.innerText = (transaksiData.metode || 'Tunai').toUpperCase();

        // 2. Render daftar produk yang dibeli + hitung breakdown diskon
        const listContainer = document.getElementById('strukListProduk');
        let rawSubtotal = 0;
        let totalItemDisc = 0;

        const items = Array.isArray(transaksiData.items) ?
            transaksiData.items :
            Object.values(transaksiData.items || {});

        if (listContainer) {
            listContainer.innerHTML = '';

            items.forEach(item => {
                const qty = parseInt(item.qty) || 1;
                const nama = item.nama_produk || item.name || 'Produk';
                const price = parseFloat(item.price) || 0;
                const discPct = parseFloat(item.discount) || 0;
                const discVch = parseFloat(item.discount_voucher) || 0;

                const discPctNominal = price * (discPct / 100);
                const itemDiscUnit = discPctNominal + discVch;
                const itemSubtotal = Math.max(0, (price - itemDiscUnit) * qty);

                rawSubtotal += (price * qty);
                totalItemDisc += (itemDiscUnit * qty);

                let discInfoText = '';
                if (discPct > 0 && discVch > 0) {
                    discInfoText = `Diskon: -${discPct}% -${formatRupiah(discVch)}`;
                } else if (discPct > 0) {
                    discInfoText = `Diskon: -${discPct}% (-${formatRupiah(discPctNominal)})`;
                } else if (discVch > 0) {
                    discInfoText = `Voucher item: -${formatRupiah(discVch)}`;
                }

                listContainer.innerHTML += `
                    <div class="flex justify-between items-start text-xs pt-1">
                        <div class="min-w-0 pr-2">
                            <p class="font-bold text-gray-900 truncate">${escapeHtml(nama)}</p>
                            <p class="text-[11px] text-gray-500 font-medium">${qty}x ${formatRupiah(price)}</p>
                            ${discInfoText ? `<p class="text-[10.5px] text-red-600 font-medium mt-0.5">${escapeHtml(discInfoText)}</p>` : ''}
                        </div>
                        <span class="font-bold text-gray-900 shrink-0 text-right">${formatRupiah(itemSubtotal)}</span>
                    </div>
                `;
            });
        }

        // Subtotal kotor sebelum diskon apapun
        const finalSubtotal = transaksiData.subtotal !== undefined ? parseFloat(transaksiData.subtotal) : rawSubtotal;
        if (elSubtotal) elSubtotal.innerText = formatRupiah(finalSubtotal);

        // Diskon per item
        const finalItemDisc = transaksiData.item_discount !== undefined ? parseFloat(transaksiData.item_discount) : totalItemDisc;
        const wrapperDiscItem = document.getElementById('wrapperStrukDiskonItem');
        const elDiscItem = document.getElementById('strukDiskonItem');
        if (wrapperDiscItem && elDiscItem) {
            if (finalItemDisc > 0) {
                elDiscItem.innerText = `- ${formatRupiah(finalItemDisc)}`;
                wrapperDiscItem.classList.remove('hidden');
                wrapperDiscItem.classList.add('flex');
            } else {
                wrapperDiscItem.classList.add('hidden');
                wrapperDiscItem.classList.remove('flex');
            }
        }

        // Diskon global (%)
        const globalPct = parseFloat(transaksiData.discount_percent) || 0;
        const subtotalAfterItem = Math.max(0, finalSubtotal - finalItemDisc);
        const finalGlobalDisc = transaksiData.discount_global !== undefined ?
            parseFloat(transaksiData.discount_global) :
            (subtotalAfterItem * (globalPct / 100));

        const wrapperDiscGlobal = document.getElementById('wrapperStrukDiskonGlobal');
        const labelDiscGlobal = document.getElementById('labelStrukDiskonGlobal');
        const elDiscGlobal = document.getElementById('strukDiskonGlobal');
        if (wrapperDiscGlobal && elDiscGlobal) {
            if (finalGlobalDisc > 0) {
                if (labelDiscGlobal) labelDiscGlobal.innerText = globalPct > 0 ? `Diskon Global (${globalPct}%)` : 'Diskon Global';
                elDiscGlobal.innerText = `- ${formatRupiah(finalGlobalDisc)}`;
                wrapperDiscGlobal.classList.remove('hidden');
                wrapperDiscGlobal.classList.add('flex');
            } else {
                wrapperDiscGlobal.classList.add('hidden');
                wrapperDiscGlobal.classList.remove('flex');
            }
        }

        // Voucher global (Rp)
        const finalVoucher = parseFloat(transaksiData.voucher) || 0;
        const wrapperVoucher = document.getElementById('wrapperStrukVoucher');
        const elVoucher = document.getElementById('strukVoucher');
        if (wrapperVoucher && elVoucher) {
            if (finalVoucher > 0) {
                elVoucher.innerText = `- ${formatRupiah(finalVoucher)}`;
                wrapperVoucher.classList.remove('hidden');
                wrapperVoucher.classList.add('flex');
            } else {
                wrapperVoucher.classList.add('hidden');
                wrapperVoucher.classList.remove('flex');
            }
        }

        // Grand Total
        const finalGrandTotal = transaksiData.total !== undefined ?
            parseFloat(transaksiData.total) :
            Math.max(0, subtotalAfterItem - finalGlobalDisc - finalVoucher);
        if (elTotal) elTotal.innerText = formatRupiah(finalGrandTotal);

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
            if (elDibayar) elDibayar.innerText = formatRupiah(transaksiData.dibayar || finalGrandTotal);
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

    // Fungsi Tutup Modal Struk
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
    // [TAMBAHAN PRINT] CETAK STRUK POS VIA DomPDF
    // Panggil fungsi ini dari tombol Print di POS/struk.blade.php
    // -------------------------------------------------------------------------
    function cetakStrukPdf() {
        if (!strukTerakhir) {
            alert('Data struk tidak ditemukan.');
            return;
        }

        const rawItems = Array.isArray(strukTerakhir.items) ?
            strukTerakhir.items :
            Object.values(strukTerakhir.items || {});

        let rawSubtotal = 0;
        let totalItemDisc = 0;

        const items = rawItems.map(function(item) {
            const qty = parseInt(item.qty) || 1;
            const nama = item.nama_produk || item.name || 'Produk';
            const price = parseFloat(item.price) || 0;
            const pct = parseFloat(item.discount) || 0;
            const vch = parseFloat(item.discount_voucher) || 0;
            const itemDiscUnit = (price * (pct / 100)) + vch;
            const hargaAkhir = Math.max(0, price - itemDiscUnit);
            const subtotal = hargaAkhir * qty;

            rawSubtotal += (price * qty);
            totalItemDisc += (itemDiscUnit * qty);

            let discInfo = '';
            if (pct > 0 && vch > 0) {
                discInfo = `${pct}% + ${formatRupiah(vch)}`;
            } else if (pct > 0) {
                discInfo = `${pct}%`;
            } else if (vch > 0) {
                discInfo = `${formatRupiah(vch)}`;
            }

            return {
                nama: nama,
                qty: qty,
                price: price,
                discount_info: discInfo,
                subtotal: subtotal
            };
        });

        const metode = strukTerakhir.metode || 'Tunai';
        const isTunai = metode.toLowerCase() === 'cash' || metode.toLowerCase() === 'tunai';

        const finalSubtotal = strukTerakhir.subtotal !== undefined ? strukTerakhir.subtotal : rawSubtotal;
        const finalItemDisc = strukTerakhir.item_discount !== undefined ? strukTerakhir.item_discount : totalItemDisc;
        const globalPct = parseFloat(strukTerakhir.discount_percent) || 0;
        const finalGlobalDisc = strukTerakhir.discount_global !== undefined ?
            strukTerakhir.discount_global :
            Math.max(0, (finalSubtotal - finalItemDisc) * (globalPct / 100));
        const finalVoucher = parseFloat(strukTerakhir.voucher) || 0;

        document.getElementById('printRefNo').value = strukTerakhir.ref_no || '';
        document.getElementById('printTanggal').value = strukTerakhir.tanggal || '';
        document.getElementById('printWaktu').value = strukTerakhir.waktu || '';
        document.getElementById('printKasir').value = strukTerakhir.kasir || '';
        document.getElementById('printMetode').value = metode;
        document.getElementById('printSubtotal').value = finalSubtotal;
        document.getElementById('printDiscountItem').value = finalItemDisc;
        document.getElementById('printDiscountGlobal').value = finalGlobalDisc;
        document.getElementById('printVoucher').value = finalVoucher;
        document.getElementById('printTotal').value = strukTerakhir.total || 0;
        document.getElementById('printDibayar').value = isTunai ? (strukTerakhir.dibayar || 0) : '';
        document.getElementById('printKembalian').value = isTunai ? (strukTerakhir.kembalian || 0) : '';
        document.getElementById('printItems').value = JSON.stringify(items);

        document.getElementById('formPrintStruk').submit();
    }

    // Alias: jika tombol Print di POS/struk.blade.php memanggil cetakStruk(),
    // maka otomatis diarahkan ke print DomPDF. Hapus baris ini jika tombol
    // sudah diubah langsung ke cetakStrukPdf().
    window.cetakStruk = cetakStrukPdf;

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
            // Inisialisasi Select2 di dalam modal draft (dropdownParent = modal agar tidak terpotong)
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
                ['#inputPelangganDraft', '#inputSalesDraft'].forEach(function(sel) {
                    const $el = jQuery(sel);
                    if ($el.length && !$el.hasClass('select2-hidden-accessible')) {
                        $el.select2({
                            width: '100%',
                            placeholder: $el.find('option:first').text() || 'Cari...',
                            allowClear: true,
                            dropdownParent: jQuery(modal)
                        });
                    }
                });
            }
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
            time: new Date().toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            }) + ' WIB'
        });

        localStorage.setItem('pos_drafts', JSON.stringify(drafts));
        tutupModalTambahArsip();
        // Reset pilihan pelanggan/sales (termasuk tampilan Select2)
        ['inputPelangganDraft', 'inputSalesDraft'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) {
                el.value = '';
                if (typeof jQuery !== 'undefined' && jQuery(el).hasClass('select2-hidden-accessible')) {
                    jQuery(el).val('').trigger('change');
                }
            }
        });
        resetKeranjang(true);
        updateDraftBadgeHeader(drafts.length);
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
        const countText = document.getElementById('draftCountText');
        if (!container) return;

        const drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');

        // Update teks jumlah draft + badge di header
        if (countText) {
            countText.innerText = `${drafts.length} transaksi tersimpan`;
        }
        updateDraftBadgeHeader(drafts.length);

        if (drafts.length === 0) {
            container.innerHTML = `
            <div class="p-8 text-center text-gray-400 text-sm font-medium">
                Belum ada draft transaksi yang disimpan.
            </div>
        `;
            return;
        }

        let html = '';
        drafts.forEach((d, idx) => {
            // Hitung total item qty dari objek cart
            let totalItems = 0;
            let grandTotal = 0;
            if (d.cart && typeof d.cart === 'object') {
                Object.values(d.cart).forEach(item => {
                    const qty = parseInt(item.qty) || 1;
                    totalItems += qty;
                    const price = parseFloat(item.price) || 0;
                    grandTotal += (price * qty);
                });
            }

            // Tampilkan info ringkasan
            const itemInfo = `${totalItems} item`;
            const totalFormatted = formatRupiah ? formatRupiah(grandTotal) : `Rp ${grandTotal.toLocaleString('id-ID')}`;

            html += `
            <div class="flex items-center justify-between p-3.5 bg-white border border-gray-800 rounded-2xl shadow-xs hover:border-black transition-all">
                <!-- Info Kiri (Ikon + Detail) -->
                <div class="flex items-center gap-3.5">
                    <!-- Icon Box Tray -->
                    <div class="w-10 h-10 border border-gray-800 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-50">
                        <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>

                    <!-- Text Details -->
                    <div>
                        <h4 class="font-bold text-black text-sm leading-tight">${escapeHtml(d.pelanggan || 'Pelanggan Umum')}</h4>
                        <div class="flex items-center gap-1.5 text-xs text-gray-500 font-medium mt-0.5">
                            <span>${itemInfo}</span>
                            <span>•</span>
                            <span>${totalFormatted}</span>
                            <span>•</span>
                            <span>${escapeHtml(d.time || '')}</span>
                        </div>
                    </div>
                </div>

                <!-- Tombol Kanan (Lanjutkan & Hapus) -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button
                        type="button"
                        onclick="muatDraft(${idx})"
                        class="px-5 py-2 bg-[#0F1D3A] hover:bg-[#0A1428] active:bg-black text-white rounded-xl text-xs font-bold transition-colors cursor-pointer shadow-xs">
                        Lanjutkan
                    </button>
                    <button
                        type="button"
                        onclick="hapusDraft(${idx})"
                        class="w-9 h-9 bg-[#DC2626] hover:bg-[#B91C1C] text-white rounded-xl flex items-center justify-center transition-colors cursor-pointer shadow-xs"
                        title="Hapus Draft">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                </div>
            </div>
        `;
        });

        container.innerHTML = html;
    }

    function muatDraft(index) {
        let drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
        if (drafts[index]) {
            const draft = drafts[index];
            cartData = draft.cart || {};
            voucherAmount = draft.voucher || 0;
            discountPercent = draft.discount_percent || 0;

            // Simpan pelanggan dan sales dari draft ke variabel global
            isLoadedFromDraft = true;
            currentDraftPelanggan = draft.pelanggan || '';
            currentDraftSales = draft.sales || '';

            // Hapus draft dari daftar tersimpan (localStorage) karena sudah dimuat
            drafts.splice(index, 1);
            localStorage.setItem('pos_drafts', JSON.stringify(drafts));
            updateDraftBadgeHeader(drafts.length);

            renderCart();
            simpanKeSession();
            tutupModalDraftTransaksi();
        }
    }

    function hapusDraft(index) {
        let drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
        if (index !== undefined) {
            drafts.splice(index, 1);
        } else {
            drafts = [];
        }
        localStorage.setItem('pos_drafts', JSON.stringify(drafts));
        renderDraftList();
        updateDraftBadgeHeader(drafts.length);
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
            tutupModalDiskonGlobal();
            modalTutupMetodePembayaran();
            tutupModalTambahArsip();
            tutupModalTutupKasir();
            tutupModalKonfirmasiPembayaran();
            tutupPanelSearchItm();
        }
    });

    // -------------------------------------------------------------------------
    // 13. HELPER
    // -------------------------------------------------------------------------
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

    // -------------------------------------------------------------------------
    // 14. INFO ITEM TERAKHIR (SETELAH SCAN) + INISIALISASI
    // -------------------------------------------------------------------------

    // Scan memakai form submit (halaman reload), jadi simpan jumlah item sebelum submit
    document.addEventListener('submit', function(e) {
        if (e.target.querySelector && e.target.querySelector('[name="BC_OUT"]')) {
            sessionStorage.setItem('scanJumlahSebelum', String(hitungTotalQty()));
        }
    });

    function tampilInfoItemTerakhir() {
        const keys = Object.keys(cartData);
        const el = document.getElementById('infoItemTerakhir');
        const txt = document.getElementById('infoItemTerakhirText');
        if (!el || !txt || keys.length === 0) return;

        const key = keys[keys.length - 1];
        const item = cartData[key];
        txt.innerText = (item.code || key) + ' · ' + (item.name || '') + ' — ditambahkan ke keranjang';
        el.classList.remove('hidden');
        el.classList.add('flex');
    }

    // -------------------------------------------------------------------------
    // 15. LIVE SEARCH ITM (BERDASARKAN NAMA/DES, HARGA/HRG, DAN KODE/ITM)
    // -------------------------------------------------------------------------
    let searchItmDebounceTimer = null;
    let searchResultItems = [];

    function handleSearchItmInput(val) {
        val = val.trim();
        const btnClear = document.getElementById('btnClearSearch');
        if (btnClear) {
            if (val.length > 0) {
                btnClear.classList.remove('hidden');
            } else {
                btnClear.classList.add('hidden');
            }
        }

        clearTimeout(searchItmDebounceTimer);
        if (val.length < 1) {
            tutupPanelSearchItm();
            searchResultItems = [];
            return;
        }

        searchItmDebounceTimer = setTimeout(() => {
            fetchSearchItm(val);
        }, 200);
    }

    function handleSearchItmFocus() {
        const val = (document.getElementById('inputScanUtama')?.value || '').trim();
        if (val.length > 0 && searchResultItems.length > 0) {
            bukaPanelSearchItm();
        }
    }

    function handleSearchItmKeydown(e) {
        if (e.key === 'Escape') {
            tutupPanelSearchItm();
        }
    }

    function clearSearchItm() {
        const input = document.getElementById('inputScanUtama');
        if (input) {
            input.value = '';
            input.focus();
        }
        const btnClear = document.getElementById('btnClearSearch');
        if (btnClear) btnClear.classList.add('hidden');
        tutupPanelSearchItm();
        searchResultItems = [];
    }

    function bukaPanelSearchItm() {
        const panel = document.getElementById('panelHasilSearchItm');
        if (panel) panel.classList.remove('hidden');
    }

    function tutupPanelSearchItm() {
        const panel = document.getElementById('panelHasilSearchItm');
        if (panel) panel.classList.add('hidden');
    }

    function fetchSearchItm(q) {
        const panel = document.getElementById('panelHasilSearchItm');
        const list = document.getElementById('listHasilSearchItm');
        const loading = document.getElementById('loadingSearchItm');

        if (!panel || !list) return;

        bukaPanelSearchItm();
        if (loading) loading.classList.remove('hidden');
        list.innerHTML = '';

        fetch('{{ route('POS.search-items') }}?q=' + encodeURIComponent(q))
            .then(res => res.json())
            .then(data => {
                if (loading) loading.classList.add('hidden');
                if (data.success && Array.isArray(data.items)) {
                    searchResultItems = data.items;
                    renderSearchItmResults(data.items);
                } else {
                    searchResultItems = [];
                    renderSearchItmResults([]);
                }
            })
            .catch(err => {
                if (loading) loading.classList.add('hidden');
                console.error('Error search ITM:', err);
                list.innerHTML = '<div class="p-4 text-center text-sm text-red-500 font-medium">Gagal memuat hasil pencarian.</div>';
            });
    }

    function renderSearchItmResults(items) {
        const list = document.getElementById('listHasilSearchItm');
        if (!list) return;

        if (items.length === 0) {
            list.innerHTML = '<div class="p-5 text-center text-sm text-gray-500 font-medium">Tidak ada produk ditemukan di tabel ITM.</div>';
            return;
        }

        let html = `
            <div class="px-4 py-2 bg-gray-50 border-b border-gray-100 flex items-center justify-between text-[11px] text-gray-500 font-semibold tracking-wide uppercase">
                <span>Informasi Produk &amp; Harga (Tabel ITM)</span>
                <span>${items.length} produk ditemukan</span>
            </div>
        `;

        items.forEach(item => {
            const availableCount = item.available_count || 0;
            const inStock = availableCount > 0;

            let badgeStok = '';
            if (inStock) {
                badgeStok = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Stok: ${availableCount} unit</span>`;
            } else {
                badgeStok = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">Stok Habis</span>`;
            }

            html += `
                <div class="flex items-center justify-between p-3.5 hover:bg-gray-50/80 transition-colors">
                    <div class="min-w-0 flex-1 pr-4">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="text-sm font-bold text-gray-900">${escapeHtml(item.des)}</span>
                            <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded font-mono font-medium">${escapeHtml(item.itm)}</span>
                            ${badgeStok}
                        </div>
                        <p class="text-xs text-gray-500">Kode: <span class="font-mono text-gray-700">${escapeHtml(item.itm)}</span></p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-[11px] text-gray-400 font-medium block">Harga Satuan</span>
                        <span class="text-base font-extrabold text-black font-number">${formatRupiah(item.hrg)}</span>
                    </div>
                </div>
            `;
        });

        list.innerHTML = html;
    }

    function handleScanSubmit(event) {
        const input = document.getElementById('inputScanUtama');
        const val = input ? input.value.trim() : '';

        if (!val) {
            event.preventDefault();
            return false;
        }

        // Form submit langsung ke POS.scan untuk scan barcode BC_OUT / input manual
        return true;
    }

    // Klik di luar search panel untuk menutup
    document.addEventListener('click', function(e) {
        const container = document.getElementById('containerSearchUtama');
        if (container && !container.contains(e.target)) {
            tutupPanelSearchItm();
        }
    });

    // Inisialisasi awal saat DOM siap
    document.addEventListener('DOMContentLoaded', function() {
        if (isKasirOpen) {
            renderCart();

            // Tampilkan info hanya jika halaman dimuat setelah scan yang menambah item
            const sebelum = sessionStorage.getItem('scanJumlahSebelum');
            if (sebelum !== null) {
                sessionStorage.removeItem('scanJumlahSebelum');
                if (hitungTotalQty() > parseInt(sebelum)) {
                    tampilInfoItemTerakhir();
                }
            }

            // Fokus ke kolom scan agar scanner fisik langsung bisa dipakai
            const inputScan = document.getElementById('inputScanUtama');
            if (inputScan) inputScan.focus();
        }
    });
</script>
@endpush
@endsection