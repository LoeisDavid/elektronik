@extends('layouts.app')

@section('title', 'Point Of Sales - Kasir')
@section('header_title', 'Point Of Sales')

@section('content')
{{--
    ===================================================================
    BACKEND DEVELOPER INTEGRATION NOTES:
    ===================================================================
    1. Products List:
       Replace `initialProducts` in the Alpine `posApp()` function below
       with your controller data: `initialProducts: @json($products ?? [])`.
    2. Cart persistence / checkout:
       The "Pembayaran" button triggers the payment flow.
       Submit `cartItems`, `voucherAmount`, `discountPercent`, and `grandTotal`
       to your payment/order endpoint.
    ===================================================================
--}}
<div
    x-data="posApp()"
    class="flex h-full w-full overflow-hidden relative">
    {{-- ========================================================= --}}
    {{-- CENTER: WORKSPACE (CATALOG & PAYMENT)                      --}}
    {{-- ========================================================= --}}
    <div class="flex-1 flex flex-col h-full overflow-hidden bg-[#CECECE]/60">

        {{-- 1. CATALOG VIEW --}}
        <div x-show="viewMode === 'catalog'" class="flex-1 flex flex-col h-full overflow-hidden">
            {{-- Top Action Toolbar (Search, Filter Dropdown, Barcode) --}}
            <div class="p-4 pb-2 flex items-center justify-between gap-3 flex-shrink-0 z-20">
                <div class="flex items-center gap-2 flex-1 max-w-md">
                    {{-- Search Input with Magnifying Glass (with hover effect) --}}
                    <div class="relative flex-1 group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500 group-hover:text-[#2D8CFF] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            x-model="searchQuery"
                            placeholder="Cari produk"
                            class="w-full bg-white border border-[#E2E8F0] hover:border-[#2D8CFF] focus:border-[#2D8CFF] rounded-lg pl-10 pr-4 py-2 text-sm text-[#2D3748] placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#2D8CFF] transition-all shadow-2xs" />
                    </div>

                    {{-- Filter Dropdown Trigger & Popover (Matching Image 3, with hover effect) --}}
                    <div class="relative" @click.outside="filterOpen = false">
                        <button
                            type="button"
                            @click="filterOpen = !filterOpen"
                            :class="filterOpen ? 'bg-blue-50 border-[#2D8CFF] text-[#2D8CFF] ring-1 ring-[#2D8CFF]' : 'bg-white hover:bg-blue-50 hover:border-[#2D8CFF] hover:text-[#2D8CFF] text-gray-600'"
                            class="h-9 w-9 border border-[#E2E8F0] rounded-lg flex items-center justify-center transition-all flex-shrink-0 shadow-2xs cursor-pointer"
                            title="Urutkan / Filter Produk">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.539.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.378-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                            </svg>
                        </button>

                        {{-- Dropdown Menu Matching Image 3 --}}
                        <div
                            x-show="filterOpen"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 mt-2 w-56 bg-[#cecece] rounded-xl p-2 shadow-2xl space-y-1.5 border border-gray-300 z-50 select-none"
                            style="display: none;">
                            <template x-for="option in sortOptions" :key="option">
                                <button
                                    type="button"
                                    @click="selectSort(option)"
                                    class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white hover:bg-gray-50 active:bg-gray-100 rounded-lg text-sm font-medium text-[#1A1F36] text-left transition-colors shadow-xs cursor-pointer">
                                    <span x-text="option"></span>
                                    {{-- Checkmark matching mockup --}}
                                    <svg
                                        x-show="selectedSort === option"
                                        class="w-5 h-5 text-gray-400 stroke-[2.5]"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Barcode Scanner Icon Button (Opens Barcode Modal, with hover effect) --}}
                <button
                    type="button"
                    @click="openBarcodeModal()"
                    class="h-9 px-3 bg-white border border-[#E2E8F0] hover:border-[#2D8CFF] hover:bg-blue-50 text-[#2D3748] hover:text-[#2D8CFF] rounded-lg flex items-center justify-center transition-all shadow-2xs gap-1.5 cursor-pointer group"
                    title="Scan Barcode">
                    <svg class="w-5 h-5 text-current transition-colors" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M2 4h2v16H2V4zm3 0h1v16H5V4zm2 0h2v16H7V4zm3 0h1v16h-1V4zm2 0h3v16h-3V4zm4 0h1v16h-1V4zm2 0h2v16h-2V4zm3 0h1v16h-1V4z" />
                    </svg>
                </button>
            </div>

            {{-- Product Grid (4 Columns) --}}
            <div class="flex-1 overflow-y-auto p-4 pt-1">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5">
                    <template x-for="product in sortedAndFilteredProducts" :key="product.id">
                        <div
                            @click="addToCart(product)"
                            class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md cursor-pointer border border-transparent hover:border-[#2D8CFF] transition-all flex flex-col group active:scale-[0.99]">
                            {{-- Top Image Placeholder (taller vertical card) --}}
                            <div class="h-44 bg-[#c5c8cc] relative flex items-center justify-center overflow-hidden p-3">
                                <img
                                    :src="product.image"
                                    :alt="product.name"
                                    class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-200"
                                    onerror="this.src='https://via.placeholder.com/150?text=Produk';" />
                            </div>

                            {{-- Bottom Card Details --}}
                            <div class="p-3.5 bg-white flex flex-col flex-1 justify-between min-h-[90px]">
                                <div>
                                    <h3 class="font-semibold text-sm text-[#1A1F36] leading-tight truncate" x-text="product.name"></h3>
                                    <p class="text-[11px] text-[#6B7280] truncate mt-0.5" x-text="product.description"></p>
                                </div>
                                <div class="mt-3 pt-1">
                                    <span class="font-bold text-sm text-[#1A1F36] font-number" x-text="formatRupiah(product.price)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- 2. PAYMENT VIEW (Matches Payment Mockups) --}}
        <div 
            x-show="viewMode === 'payment'" 
            class="flex-1 flex flex-col items-center justify-start h-full overflow-y-auto pt-16 pb-8 px-6"
            style="display: none;"
        >
            {{-- Big Total in Middle --}}
            <h1 class="text-5xl md:text-6xl font-bold text-[#1A1F36] font-number tracking-tight mb-10" x-text="formatRupiah(grandTotal)"></h1>

            {{-- Payment Method Buttons (Tunai vs Bank) --}}
            <div class="flex items-center justify-center gap-5 mb-10">
                {{-- Tunai Button --}}
                <button 
                    type="button"
                    @click="paymentMethod = 'Tunai'"
                    :class="paymentMethod === 'Tunai' 
                        ? 'bg-[#2D8CFF] text-white border-[#2D8CFF] shadow-sm' 
                        : 'bg-white text-black border-black hover:bg-gray-50'"
                    class="w-36 py-2 text-center text-xl font-bold border rounded-lg transition-all cursor-pointer"
                >
                    Tunai
                </button>

                {{-- Bank Button --}}
                <button 
                    type="button"
                    @click="paymentMethod = 'Bank'"
                    :class="paymentMethod === 'Bank' 
                        ? 'bg-[#2D8CFF] text-white border-[#2D8CFF] shadow-sm' 
                        : 'bg-white text-black border-black hover:bg-gray-50'"
                    class="w-36 py-2 text-center text-xl font-bold border rounded-lg transition-all cursor-pointer"
                >
                    Bank
                </button>
            </div>

            {{-- 1. Tunai Section (Auto-calculated Kembalian) --}}
            <div x-show="paymentMethod === 'Tunai'" class="w-full max-w-lg space-y-3">
                {{-- Masukkan nominal --}}
                <div class="bg-white border border-black rounded-lg p-3.5 flex items-center justify-between shadow-2xs">
                    <span class="text-lg font-bold text-[#1A1F36]">Masukkan nominal:</span>
                    <div class="flex items-center gap-1.5">
                        <span class="font-bold text-lg text-[#1A1F36] font-number">Rp</span>
                        <input 
                            type="number" 
                            x-model.number="cashTendered"
                            @input="calculateKembalian()"
                            placeholder="0"
                            class="w-48 text-right font-bold text-lg text-[#1A1F36] font-number outline-none bg-transparent"
                        />
                    </div>
                </div>

                {{-- Kembalian (Auto-calculated on nominal input) --}}
                <div class="bg-white border border-black rounded-lg p-3.5 flex items-center justify-between shadow-2xs">
                    <span class="text-lg font-bold text-[#1A1F36]">Kembalian:</span>
                    <span class="font-bold text-lg text-[#1A1F36] font-number" x-text="formatRupiah(kembalianValue)"></span>
                </div>
            </div>

            {{-- 2. Bank Section (Pilih Bank Dropdown) --}}
            <div x-show="paymentMethod === 'Bank'" class="w-full max-w-lg relative" @click.outside="bankDropdownOpen = false">
                {{-- Dropdown Trigger Button --}}
                <button 
                    type="button"
                    @click="bankDropdownOpen = !bankDropdownOpen"
                    class="w-full bg-white border border-black rounded-lg p-3.5 flex items-center justify-between shadow-2xs hover:bg-gray-50 transition-colors cursor-pointer text-left"
                >
                    <span class="text-xl font-bold text-[#1A1F36]" x-text="selectedBank"></span>
                    <span class="text-base font-bold text-[#1A1F36]">V</span>
                </button>

                {{-- Bank List Dropdown --}}
                <div 
                    x-show="bankDropdownOpen"
                    x-transition
                    class="absolute left-0 right-0 mt-2 bg-white border border-black rounded-lg shadow-xl overflow-hidden z-30 divide-y divide-gray-100"
                    style="display: none;"
                >
                    <template x-for="bank in ['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB Niaga']" :key="bank">
                        <button 
                            type="button"
                            @click="selectedBank = bank; bankDropdownOpen = false"
                            class="w-full px-5 py-3.5 text-left font-semibold text-base text-[#1A1F36] hover:bg-blue-50 hover:text-[#2D8CFF] transition-colors flex items-center justify-between cursor-pointer"
                        >
                            <span x-text="bank"></span>
                            <span x-show="selectedBank === bank" class="text-[#2D8CFF] font-bold">✓</span>
                        </button>
                    </template>
                </div>
            </div>

        </div>

        {{-- 3. PEMBAYARAN BERHASIL & INVOICE VIEW (Matches Image 1 & Image 2) --}}
        <div 
            x-show="viewMode === 'success'" 
            class="flex-1 flex flex-col h-full overflow-y-auto p-6"
            style="display: none;"
        >
            {{-- Top Link: < Kembali ke Kasir (Matching Image 1) --}}
            <div class="mb-2">
                <button 
                    type="button" 
                    @click="backToKasir()"
                    class="flex items-center gap-1.5 text-sm font-medium text-gray-700 hover:text-black italic cursor-pointer transition-colors"
                >
                    <span class="text-base leading-none">‹</span>
                    <span>Kembali ke Kasir</span>
                </button>
            </div>

            {{-- Main Title: Pembayaran berhasil (Matching Image 1) --}}
            <h1 class="text-4xl md:text-6xl font-bold text-[#1A1F36] text-center my-6 tracking-tight">
                Pembayaran berhasil
            </h1>

            {{-- Invoice Container (Matching Image 2) --}}
            <div class="max-w-xl mx-auto w-full bg-white p-6 md:p-8 shadow-sm mb-12 text-black font-sans">
                
                {{-- Store Header --}}
                <div class="flex items-start justify-between gap-4">
                    <h2 class="text-3xl md:text-4xl font-bold text-black tracking-tight font-sans">
                        NamaToko
                    </h2>
                    <div class="text-right text-xs text-gray-800 leading-tight">
                        <p>Medokan Ayu, Kec. Rungkut,</p>
                        <p>Surabaya</p>
                        <p>No Telp: 0881-0268-77983</p>
                    </div>
                </div>

                {{-- Dotted Separator --}}
                <div class="border-b-2 border-dotted border-black my-4"></div>

                {{-- Transaction Info --}}
                <div class="space-y-1 text-sm text-black">
                    <div class="flex items-center justify-between">
                        <span x-text="'Tanggal: ' + invoiceDate"></span>
                        <span class="font-medium font-number" x-text="'No: ' + invoiceNumber"></span>
                    </div>
                    <div>
                        <span>Dilayani oleh: User 2</span>
                    </div>
                </div>

                {{-- Dotted Separator --}}
                <div class="border-b-2 border-dotted border-black my-4"></div>

                {{-- Line Items --}}
                <div class="space-y-3 text-base">
                    <template x-for="item in cartItems" :key="item.id">
                        <div class="flex items-center justify-between font-bold text-black">
                            <span x-text="item.qty + 'x ' + item.name"></span>
                            <span class="font-number" x-text="formatRupiah(item.price * item.qty)"></span>
                        </div>
                    </template>
                </div>

                {{-- Dotted Separator --}}
                <div class="border-b-2 border-dotted border-black my-4"></div>

                {{-- Total & Payment Method --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-2xl font-bold text-black">
                        <span>Total:</span>
                        <span class="font-number" x-text="formatRupiah(grandTotal)"></span>
                    </div>
                    <div class="flex items-center justify-between text-base text-gray-700">
                        <span>Metode pembayaran:</span>
                        <span class="font-bold text-black uppercase" x-text="paymentMethod === 'Bank' ? selectedBank : 'Tunai'"></span>
                    </div>
                </div>

                {{-- Download & Print Buttons (Matching Image 2) --}}
                <div class="mt-8 pt-4 flex items-center justify-center gap-6">
                    <button 
                        type="button"
                        @click="downloadInvoice()"
                        class="px-8 py-2.5 bg-white border-2 border-black rounded-md text-base font-bold text-black hover:bg-gray-100 active:bg-gray-200 transition-colors shadow-2xs cursor-pointer min-w-[140px] text-center"
                    >
                        Download
                    </button>
                    <button 
                        type="button"
                        @click="window.print()"
                        class="px-8 py-2.5 bg-white border-2 border-black rounded-md text-base font-bold text-black hover:bg-gray-100 active:bg-gray-200 transition-colors shadow-2xs cursor-pointer min-w-[140px] text-center"
                    >
                        Print
                    </button>
                </div>

            </div>
        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- RIGHT: ORDER / CART PANEL (Hidden on Success page)        --}}
    {{-- ========================================================= --}}
    <div x-show="viewMode !== 'success'" class="w-80 lg:w-96 flex-shrink-0 bg-white border-l border-[#E2E8F0] flex flex-col h-full shadow-lg z-10">

        {{-- Order Header --}}
        <div class="outline outline-1 outline-black border-y border-black p-4 pb-2 border-b border-[#E2E8F0]">
            <h2 class="text-xl font-bold text-[#1A1F36] tracking-tight">Order</h2>
        </div>

        {{-- Cart Items List (Full width left-to-right, black outline section) --}}
        <div class="flex-1 overflow-y-auto outline outline-1 outline-black border-y border-black px-0 py-0 divide-y divide-black">
            <template x-for="(item, index) in cartItems" :key="item.id">
                {{-- Product item: fully rectangle, no margin, full width left to right --}}
                <div class="rounded-none px-4 py-3.5 bg-white shadow-none transition-colors group w-full">
                    {{-- Row 1: Title, Subtitle, and Item Actions (% and X) --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline gap-1.5 flex-wrap">
                                <span class="font-semibold text-sm text-[#1A1F36]" x-text="item.name"></span>
                                <span class="text-xs text-[#6B7280] truncate" x-text="item.description"></span>
                            </div>
                        </div>
                        {{-- Top Right Actions (% discount modal & X remove) --}}
                        <div class="flex items-center gap-1.5 flex-shrink-0 text-xs font-semibold text-gray-600">
                            <button
                                type="button"
                                @click="openItemDiscountModal(index)"
                                class="px-1.5 py-0.5 rounded hover:bg-gray-100 text-gray-600 hover:text-black transition-colors cursor-pointer"
                                title="Masukkan diskon per item">
                                %
                            </button>
                            <button
                                type="button"
                                @click="removeItem(index)"
                                class="px-1.5 py-0.5 rounded hover:bg-red-50 text-gray-600 hover:text-red-600 transition-colors cursor-pointer"
                                title="Hapus Item">
                                X
                            </button>
                        </div>
                    </div>

                    {{-- Row 2: Quantity Stepper & Price --}}
                    <div class="mt-2.5 flex items-center justify-between">
                        {{-- Quantity Stepper: [-] [qty] [+] with hover to primary blue --}}
                        <div class="flex items-center gap-1">
                            <button
                                type="button"
                                @click="decreaseQty(index)"
                                class="w-6 h-6 border border-gray-400 rounded flex items-center justify-center text-xs font-bold text-gray-700 hover:bg-[#2D8CFF] hover:text-white hover:border-[#2D8CFF] active:bg-blue-700 transition-colors cursor-pointer">
                                -
                            </button>
                            <span class="w-6 text-center text-sm font-semibold text-[#1A1F36] font-number" x-text="item.qty"></span>
                            <button
                                type="button"
                                @click="increaseQty(index)"
                                class="w-6 h-6 border border-gray-400 rounded flex items-center justify-center text-xs font-bold text-gray-700 hover:bg-[#2D8CFF] hover:text-white hover:border-[#2D8CFF] active:bg-blue-700 transition-colors cursor-pointer">
                                +
                            </button>
                        </div>

                        {{-- Line Item Total Price --}}
                        <div class="text-right">
                            <span class="font-bold text-sm text-[#1A1F36] font-number" x-text="formatRupiah(item.price * item.qty)"></span>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Empty Cart State --}}
            <div x-show="cartItems.length === 0" class="py-12 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <p class="text-sm">Keranjang kosong</p>
                <p class="text-xs mt-1">Pilih produk di sebelah kiri</p>
            </div>
        </div>

        {{-- Order Summary Calculations & Checkout Button (Black outline) --}}
        <div class="border border-black outline outline-1 outline-black p-4 bg-white space-y-2">

            {{-- Subtotal --}}
            <div class="flex items-center justify-between text-sm">
                <span class="text-[#2D3748] font-medium">Subtotal</span>
                <span class="font-bold text-[#1A1F36] font-number" x-text="formatRupiah(subtotal)"></span>
            </div>

            {{-- Voucher (Opens Voucher Modal, Same Design as Item Discount) --}}
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-1.5">
                    <span class="text-[#2D3748] font-medium">Voucher</span>
                    <button
                        type="button"
                        @click="openVoucherModal()"
                        class="text-[11px] text-[#2D8CFF] hover:underline cursor-pointer"
                        title="Ubah nominal voucher">
                        (edit)
                    </button>
                </div>
                <span
                    @click="openVoucherModal()"
                    class="font-bold text-[#1A1F36] font-number cursor-pointer hover:text-[#2D8CFF] transition-colors"
                    x-text="formatRupiah(voucherAmount)"></span>
            </div>

            {{-- Diskon Transaksi (Opens Global Discount Modal, Same Design as Item Discount) --}}
            <div class="flex flex-col text-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[#2D3748] font-medium">Diskon Transaksi</span>
                        <button
                            type="button"
                            @click="openGlobalDiscountModal()"
                            class="text-[11px] text-[#2D8CFF] hover:underline cursor-pointer"
                            title="Ubah persen diskon transaksi">
                            (edit)
                        </button>
                    </div>
                    <span
                        @click="openGlobalDiscountModal()"
                        class="font-bold text-[#1A1F36] font-number cursor-pointer hover:text-[#2D8CFF] transition-colors"
                        x-text="discountPercent + '%'"></span>
                </div>
                <div class="text-right">
                    <span class="text-xs text-[#6B7280] font-number" x-text="'-' + formatRupiah(discountAmount)"></span>
                </div>
            </div>

            {{-- Divider --}}
            <hr class="border-[#E2E8F0] my-2" />

            {{-- Grand Total --}}
            <div class="flex items-center justify-between text-base pt-0.5">
                <span class="text-xl font-bold text-[#1A1F36]">Total</span>
                <span class="text-xl font-bold text-[#1A1F36] font-number" x-text="formatRupiah(grandTotal)"></span>
            </div>

            {{-- Action Buttons: Pembayaran vs Batal & Konfirmasi (Matching Mockup) --}}
            <div class="pt-3">
                {{-- In Catalog Mode: Single Pembayaran Button --}}
                <div x-show="viewMode === 'catalog'">
                    <button
                        type="button"
                        @click="openPaymentView()"
                        class="w-full bg-[#34D399] hover:bg-[#2ecc71] active:bg-[#27ae60] text-white text-lg font-bold py-3 rounded-none shadow-sm hover:shadow transition-all cursor-pointer flex items-center justify-center tracking-wide">
                        Pembayaran
                    </button>
                </div>

                {{-- In Payment Mode: Batal and Konfirmasi Buttons Side-by-Side --}}
                <div x-show="viewMode === 'payment'" class="flex items-center w-full">
                    <button
                        type="button"
                        @click="viewMode = 'catalog'"
                        class="w-1/2 bg-[#EF4444] hover:bg-red-600 active:bg-red-700 text-white text-lg font-bold py-3 rounded-none shadow-sm hover:shadow transition-all cursor-pointer flex items-center justify-center tracking-wide">
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="confirmPayment()"
                        class="w-1/2 bg-[#34D399] hover:bg-[#2ecc71] active:bg-[#27ae60] text-white text-lg font-bold py-3 rounded-none shadow-sm hover:shadow transition-all cursor-pointer flex items-center justify-center tracking-wide">
                        Konfirmasi
                    </button>
                </div>
            </div>

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 1: SCAN BARCODE (Matching Image 1)                   --}}
    {{-- ========================================================= --}}
    <div
        x-show="activeModal === 'barcode'"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] flex items-center justify-center z-50 p-4"
        style="display: none;">
        <div
            @click.outside="closeModal()"
            class="w-full max-w-sm bg-[#F7F8FC] rounded-2xl p-5 shadow-2xl space-y-4 border border-[#E2E8F0]">
            {{-- Title --}}
            <h3 class="text-xl font-bold text-[#1A1F36] tracking-tight">Scan Barcode</h3>

            {{-- Viewport / Slate Camera Area (#596172) --}}
            <div class="w-full h-52 bg-[#5e6676] rounded-xl relative overflow-hidden flex flex-col items-center justify-center text-white shadow-inner">
                {{-- Simulated Red Scanning Laser Line --}}
                <div class="w-full h-0.5 bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.9)] animate-pulse"></div>
                <div class="mt-3 text-center px-4">
                    <svg class="w-10 h-10 mx-auto text-white/50 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                    </svg>
                    <span class="text-xs text-white/80 font-medium">Arahkan barcode ke kamera</span>
                </div>
            </div>

            {{-- Manual Input Row with "Cari" Button --}}
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    x-model="manualBarcodeCode"
                    @keydown.enter="submitManualBarcode()"
                    placeholder="Atau ketik kode manual..."
                    class="flex-1 bg-white border border-[#2D3748] rounded-lg px-3.5 py-2.5 text-sm text-[#1A1F36] placeholder-gray-500 outline-none focus:ring-1 focus:ring-[#2D8CFF]" />
                <button
                    type="button"
                    @click="submitManualBarcode()"
                    class="bg-[#242F40] hover:bg-[#1A1F36] active:bg-black text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors cursor-pointer">
                    Cari
                </button>
            </div>

            {{-- "Batal" Button --}}
            <div>
                <button
                    type="button"
                    @click="closeModal()"
                    class="w-full bg-white hover:bg-gray-50 active:bg-gray-100 border border-[#2D3748] rounded-lg py-2.5 text-center text-sm font-semibold text-[#1A1F36] transition-colors cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 2: MASUKKAN DISKON PER ITEM (Matching Image 2)      --}}
    {{-- ========================================================= --}}
    <div
        x-show="activeModal === 'itemDiscount'"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] flex items-center justify-center z-50 p-4"
        style="display: none;">
        <div
            @click.outside="closeModal()"
            class="w-full max-w-sm bg-[#F7F8FC] rounded-2xl p-6 shadow-2xl space-y-5 border border-[#E2E8F0]">
            {{-- Title --}}
            <h3 class="text-xl font-bold text-[#1A1F36] tracking-tight">Masukkan diskon per item</h3>

            {{-- Input Field --}}
            <div>
                <input
                    type="text"
                    x-model="modalInputValue"
                    @keydown.enter="confirmItemDiscount()"
                    placeholder="Masukkan diskon"
                    class="w-full bg-white border border-[#2D3748] rounded-lg px-4 py-2.5 text-sm text-[#1A1F36] placeholder-gray-500 outline-none focus:ring-1 focus:ring-[#2D8CFF]"
                    autofocus />
            </div>

            {{-- Action Buttons --}}
            <div class="space-y-2">
                <button
                    type="button"
                    @click="confirmItemDiscount()"
                    class="w-full bg-white hover:bg-gray-50 active:bg-gray-100 border border-[#2D3748] rounded-lg py-2.5 text-center text-sm font-semibold text-[#1A1F36] transition-colors cursor-pointer">
                    Konfirmasi
                </button>
                <button
                    type="button"
                    @click="closeModal()"
                    class="w-full text-center text-xs text-gray-500 hover:text-black py-1 cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 3: MASUKKAN VOUCHER (Same Design as Image 2)         --}}
    {{-- ========================================================= --}}
    <div
        x-show="activeModal === 'voucher'"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] flex items-center justify-center z-50 p-4"
        style="display: none;">
        <div
            @click.outside="closeModal()"
            class="w-full max-w-sm bg-[#F7F8FC] rounded-2xl p-6 shadow-2xl space-y-5 border border-[#E2E8F0]">
            {{-- Title --}}
            <h3 class="text-xl font-bold text-[#1A1F36] tracking-tight">Masukkan voucher</h3>

            {{-- Input Field (Number Only) --}}
            <div>
                <input
                    type="number"
                    x-model="modalInputValue"
                    @keydown.enter="confirmVoucher()"
                    placeholder="Masukkan voucher"
                    class="w-full bg-white border border-[#2D3748] rounded-lg px-4 py-2.5 text-sm text-[#1A1F36] placeholder-gray-500 outline-none focus:ring-1 focus:ring-[#2D8CFF]"
                    autofocus />
            </div>

            {{-- Action Buttons --}}
            <div class="space-y-2">
                <button
                    type="button"
                    @click="confirmVoucher()"
                    class="w-full bg-white hover:bg-gray-50 active:bg-gray-100 border border-[#2D3748] rounded-lg py-2.5 text-center text-sm font-semibold text-[#1A1F36] transition-colors cursor-pointer">
                    Konfirmasi
                </button>
                <button
                    type="button"
                    @click="closeModal()"
                    class="w-full text-center text-xs text-gray-500 hover:text-black py-1 cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 4: MASUKKAN DISKON TRANSAKSI (Same Design as Image 2)--}}
    {{-- ========================================================= --}}
    <div
        x-show="activeModal === 'globalDiscount'"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] flex items-center justify-center z-50 p-4"
        style="display: none;">
        <div
            @click.outside="closeModal()"
            class="w-full max-w-sm bg-[#F7F8FC] rounded-2xl p-6 shadow-2xl space-y-5 border border-[#E2E8F0]">
            {{-- Title --}}
            <h3 class="text-xl font-bold text-[#1A1F36] tracking-tight">Masukkan diskon transaksi</h3>

            {{-- Input Field (Percentage Only) --}}
            <div>
                <input
                    type="number"
                    x-model="modalInputValue"
                    @keydown.enter="confirmGlobalDiscount()"
                    placeholder="Masukkan diskon"
                    min="0"
                    max="100"
                    class="w-full bg-white border border-[#2D3748] rounded-lg px-4 py-2.5 text-sm text-[#1A1F36] placeholder-gray-500 outline-none focus:ring-1 focus:ring-[#2D8CFF]"
                    autofocus />
            </div>

            {{-- Action Buttons --}}
            <div class="space-y-2">
                <button
                    type="button"
                    @click="confirmGlobalDiscount()"
                    class="w-full bg-white hover:bg-gray-50 active:bg-gray-100 border border-[#2D3748] rounded-lg py-2.5 text-center text-sm font-semibold text-[#1A1F36] transition-colors cursor-pointer">
                    Konfirmasi
                </button>
                <button
                    type="button"
                    @click="closeModal()"
                    class="w-full text-center text-xs text-gray-500 hover:text-black py-1 cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function posApp() {
        return {
            searchQuery: '',

            // View mode: 'catalog' | 'payment' | 'success'
            viewMode: 'catalog',

            // Payment View state (Matches Mockups)
            paymentMethod: 'Bank', // 'Tunai' | 'Bank'
            cashTendered: 30000000,
            kembalianValue: 3200000,
            selectedBank: 'Pilih bank',
            bankDropdownOpen: false,

            // Invoice metadata
            invoiceDate: '13/09/2026 (23:01)',
            invoiceNumber: '2026-0001',

            // Filter dropdown state & options (Matching Image 3)
            filterOpen: false,
            selectedSort: 'Nama (A-Z)',
            sortOptions: [
                'Nama (A-Z)',
                'Nama (Z-A)',
                'Terlaris',
                'Harga Tertinggi',
                'Harga Terendah'
            ],

            // Modals state
            activeModal: null, // 'barcode' | 'itemDiscount' | 'voucher' | 'globalDiscount'
            modalInputValue: '',
            targetItemIndex: null,
            manualBarcodeCode: '',

            // Global Voucher (raw number value)
            voucherAmount: 10000000,

            // Global Transaction Discount (percentage value)
            discountPercent: 20,

            // Initial Dummy Products Catalog matching Kasir.png
            products: [{
                    id: 1,
                    name: 'Airpods pro 3',
                    description: 'Deskripsi',
                    price: 3400000,
                    salesCount: 120,
                    image: 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?w=240&auto=format&fit=crop&q=80'
                },
                {
                    id: 2,
                    name: 'Airpods pro 3',
                    description: 'Deskripsi',
                    price: 3400000,
                    salesCount: 85,
                    image: 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?w=240&auto=format&fit=crop&q=80'
                },
                {
                    id: 3,
                    name: 'Airpods pro 3',
                    description: 'Deskripsi',
                    price: 3400000,
                    salesCount: 95,
                    image: 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?w=240&auto=format&fit=crop&q=80'
                },
                {
                    id: 4,
                    name: 'Maskot Toko',
                    description: 'Koleksi Spesial',
                    price: 500000,
                    salesCount: 300,
                    image: 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=240&auto=format&fit=crop&q=80'
                },
                {
                    id: 5,
                    name: 'Asus TUF A16',
                    description: 'RTX 5070 Ryzen 7 260',
                    price: 18500000,
                    salesCount: 45,
                    image: 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=240&auto=format&fit=crop&q=80'
                },
                {
                    id: 6,
                    name: 'Asus TUF A14',
                    description: 'RTX 5060 Ryzen AI 7 350H',
                    price: 15500000,
                    salesCount: 60,
                    image: 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=240&auto=format&fit=crop&q=80'
                },
                {
                    id: 7,
                    name: 'Airpods Max',
                    description: 'Space Gray Wireless',
                    price: 8900000,
                    salesCount: 35,
                    image: 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=240&auto=format&fit=crop&q=80'
                },
                {
                    id: 8,
                    name: 'Maskot Hitam',
                    description: 'Edisi Terbatas',
                    price: 650000,
                    salesCount: 220,
                    image: 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=240&auto=format&fit=crop&q=80'
                }
            ],

            // Initial Cart items matching the exact mockup in Kasir.png:
            cartItems: [{
                    id: 101,
                    name: 'namaProduk',
                    description: 'Deskripsi',
                    price: 20000000,
                    originalPrice: 20000000,
                    itemDiscount: 0,
                    qty: 1
                },
                {
                    id: 102,
                    name: 'Asus TUF Gaming A14',
                    description: 'RTX 5060, Ryzen AI 7 350H',
                    price: 15000000,
                    originalPrice: 15000000,
                    itemDiscount: 0,
                    qty: 1
                },
                {
                    id: 103,
                    name: 'Asus TUF Gaming A16',
                    description: 'RTX 5070, Ryzen 7 260',
                    price: 11000000,
                    originalPrice: 11000000,
                    itemDiscount: 0,
                    qty: 1
                }
            ],

            // Computed Sorted & Filtered Products
            get sortedAndFilteredProducts() {
                let list = this.products;
                if (this.searchQuery.trim()) {
                    const q = this.searchQuery.toLowerCase();
                    list = list.filter(p =>
                        p.name.toLowerCase().includes(q) ||
                        p.description.toLowerCase().includes(q)
                    );
                }

                // Apply sorting option
                const sorted = [...list];
                if (this.selectedSort === 'Nama (A-Z)') {
                    sorted.sort((a, b) => a.name.localeCompare(b.name));
                } else if (this.selectedSort === 'Nama (Z-A)') {
                    sorted.sort((a, b) => b.name.localeCompare(a.name));
                } else if (this.selectedSort === 'Harga Tertinggi') {
                    sorted.sort((a, b) => b.price - a.price);
                } else if (this.selectedSort === 'Harga Terendah') {
                    sorted.sort((a, b) => a.price - b.price);
                } else if (this.selectedSort === 'Terlaris') {
                    sorted.sort((a, b) => (b.salesCount || 0) - (a.salesCount || 0));
                }

                return sorted;
            },

            selectSort(option) {
                this.selectedSort = option;
                this.filterOpen = false;
            },

            // Subtotal calculation
            get subtotal() {
                return this.cartItems.reduce((acc, item) => acc + (item.price * item.qty), 0);
            },

            // Diskon Transaksi calculation (Percent of subtotal)
            get discountAmount() {
                return Math.round((this.subtotal * (this.discountPercent || 0)) / 100);
            },

            // Grand Total calculation: Subtotal - Voucher - Diskon Transaksi
            get grandTotal() {
                const total = this.subtotal - (this.voucherAmount || 0) - this.discountAmount;
                return total > 0 ? total : 0;
            },

            // Auto-calculated Kembalian for Tunai payment
            get kembalian() {
                const paid = Number(this.cashTendered || 0);
                return paid >= this.grandTotal ? paid - this.grandTotal : 0;
            },

            // Cart Actions
            addToCart(product) {
                const existing = this.cartItems.find(item => item.id === product.id);
                if (existing) {
                    existing.qty++;
                } else {
                    this.cartItems.push({
                        id: product.id,
                        name: product.name,
                        description: product.description,
                        price: product.price,
                        originalPrice: product.price,
                        itemDiscount: 0,
                        qty: 1
                    });
                }
            },

            increaseQty(index) {
                this.cartItems[index].qty++;
            },

            decreaseQty(index) {
                if (this.cartItems[index].qty > 1) {
                    this.cartItems[index].qty--;
                } else {
                    this.removeItem(index);
                }
            },

            removeItem(index) {
                this.cartItems.splice(index, 1);
            },

            // Modal triggers & handlers
            openBarcodeModal() {
                this.manualBarcodeCode = '';
                this.activeModal = 'barcode';
            },

            submitManualBarcode() {
                if (this.manualBarcodeCode.trim()) {
                    const found = this.products.find(p =>
                        p.name.toLowerCase().includes(this.manualBarcodeCode.toLowerCase())
                    ) || this.products[0];
                    this.addToCart(found);
                    this.closeModal();
                }
            },

            openItemDiscountModal(index) {
                this.targetItemIndex = index;
                const currentDisc = this.cartItems[index].itemDiscount;
                this.modalInputValue = currentDisc > 0 ? currentDisc : '';
                this.activeModal = 'itemDiscount';
            },

            confirmItemDiscount() {
                if (this.targetItemIndex !== null) {
                    const item = this.cartItems[this.targetItemIndex];
                    const val = parseFloat(this.modalInputValue);
                    if (!isNaN(val) && val > 0) {
                        item.itemDiscount = val;
                        if (val <= 100) {
                            // Interpreted as percent discount from originalPrice
                            item.price = Math.round(item.originalPrice * (1 - val / 100));
                        } else {
                            // Interpreted as direct nominal deduction from originalPrice
                            item.price = Math.max(0, item.originalPrice - val);
                        }
                    } else if (this.modalInputValue === '' || val === 0) {
                        // Reset discount back to original base price
                        item.itemDiscount = 0;
                        item.price = item.originalPrice;
                    }
                }
                this.closeModal();
            },

            openVoucherModal() {
                this.modalInputValue = this.voucherAmount;
                this.activeModal = 'voucher';
            },

            confirmVoucher() {
                const val = parseInt(this.modalInputValue, 10);
                if (!isNaN(val)) {
                    this.voucherAmount = Math.max(0, val);
                }
                this.closeModal();
            },

            openGlobalDiscountModal() {
                this.modalInputValue = this.discountPercent;
                this.activeModal = 'globalDiscount';
            },

            confirmGlobalDiscount() {
                const val = parseFloat(this.modalInputValue);
                if (!isNaN(val)) {
                    this.discountPercent = Math.min(100, Math.max(0, val));
                }
                this.closeModal();
            },

            closeModal() {
                this.activeModal = null;
                this.modalInputValue = '';
                this.targetItemIndex = null;
            },

            calculateKembalian() {
                const paid = Number(this.cashTendered) || 0;
                this.kembalianValue = paid >= this.grandTotal ? paid - this.grandTotal : 0;
            },

            openPaymentView() {
                this.viewMode = 'payment';
                this.calculateKembalian();
            },

            backToKasir() {
                this.viewMode = 'catalog';
            },

            confirmPayment() {
                if (this.paymentMethod === 'Tunai' && (Number(this.cashTendered || 0) < this.grandTotal)) {
                    alert('Nominal tunai kurang dari total pembayaran!');
                    return;
                }
                if (this.paymentMethod === 'Bank' && this.selectedBank === 'Pilih bank') {
                    alert('Silakan pilih bank terlebih dahulu!');
                    return;
                }

                // Format current date and generate invoice number
                const now = new Date();
                const d = String(now.getDate()).padStart(2, '0');
                const m = String(now.getMonth() + 1).padStart(2, '0');
                const y = now.getFullYear();
                const h = String(now.getHours()).padStart(2, '0');
                const min = String(now.getMinutes()).padStart(2, '0');
                this.invoiceDate = `${d}/${m}/${y} (${h}:${min})`;
                this.invoiceNumber = `${y}-${String(Math.floor(Math.random() * 9000) + 1000)}`;

                // Transition into Pembayaran Berhasil & Invoice View (Image 1 & 2)
                this.viewMode = 'success';
            },

            downloadInvoice() {
                alert('Mengunduh file invoice ' + this.invoiceNumber + '...');
            },

            // Rupiah Currency Formatter
            formatRupiah(amount) {
                return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
            }
        };
    }
</script>
@endpush
@endsection