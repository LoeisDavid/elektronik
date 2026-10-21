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
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span id="labelGrandTotal" class="text-[26px] leading-none font-extrabold text-black tracking-tight">Rp 0</span>
                    <span id="labelPromoGlobal" class="hidden self-start mt-0.75 text-[15px] font-medium text-gray-500 whitespace-nowrap">Diskon: Rp 0&nbsp;&nbsp;Voucher: Rp 0</span>
                    <span id="labelPPNGlobal" class="hidden self-start mt-0.75 text-[15px] font-medium text-gray-500 whitespace-nowrap">PPN: Rp 0</span>
                </div>
                {{-- Rincian diskon/voucher tetap di DOM (hidden) agar JS tidak error, tidak tampil sesuai referensi --}}
                <span id="labelDiskonGlobal" class="hidden">Rp 0</span>
                <span id="labelVoucherGlobal" class="hidden">Rp 0</span>
                {{-- Dipertahankan untuk JS (nilai metode pembayaran aktif) --}}
                <span id="metodePembayaran" class="hidden">Cash</span>
            </div>

            {{-- Kanan: Tombol aksi (vertikal center / floating sesuai referensi) --}}
            <div class="flex items-end gap-2.5 shrink-0">
                <!-- PPN -->
                <div class="w-18 flex flex-col items-start">
                    <label for="ppnInput" class="text-xs font-bold text-black mb-1">PPN</label>
                    <div class="relative w-full">
                        <input
                            id="ppnInput"
                            type="number"
                            min="0"
                            max="100"
                            placeholder="0"
                            title="PPN (%)"
                            value="{{ $ppn ?? '' }}"
                            oninput="handlePpnInput(this)"
                            class="w-full h-11 bg-white border border-black rounded-lg pl-2 pr-6 text-sm font-semibold text-black placeholder-gray-400 outline-none text-right focus:ring-1 focus:ring-black [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-500 pointer-events-none">%</span>
                    </div>
                </div>
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

    {{-- MODALS HALAMAN KASIR (BARCODE, DRAFT, SHIFT, DISKON, PEMBAYARAN) --}}
    @include('POS.modals.kasir-modal')


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
        <input type="hidden" name="ppn" id="printPpn">
        <input type="hidden" name="ppn_nominal" id="printPpnNominal">
        <input type="hidden" name="total" id="printTotal">
        <input type="hidden" name="dibayar" id="printDibayar">
        <input type="hidden" name="kembalian" id="printKembalian">
        <input type="hidden" name="customer_nama" id="printCustomerNama">
        <input type="hidden" name="customer_desa" id="printCustomerDesa">
        <input type="hidden" name="customer_alamat" id="printCustomerAlamat">
        <input type="hidden" name="items" id="printItems">
    </form>

</div>

<script>
    window.__KASIR_CONFIG__ = {
        cart: @json($cart ?? []),
        voucher: {{ (float)($voucher ?? 0) }},
        discountPercent: {{ (float)($discountPercent ?? 0) }},
        ppn: @json($ppn !== null ? (int)$ppn : null),
        isKasirOpen: {{ !empty($isKasirOpen) ? 'true' : 'false' }},
        csrfToken: '{{ csrf_token() }}',
        routes: {
            bukaKasir: '{{ route('POS.buka-kasir') }}',
            kasStatus: '{{ route('POS.kas-status') }}',
            tutupKasir: '{{ route('POS.tutup-kasir', [], false) }}',
            cartSync: '{{ route('POS.cart.sync') }}',
            bayar: '{{ route('POS.bayar') }}',
            searchItems: '{{ route('POS.search-items') }}'
        }
    };
</script>

@endsection

@section('js')
    @vite(['resources/js/pages/pos/kasir/index.js'])
@endsection