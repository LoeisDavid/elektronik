@extends('layouts.app')

@section('title', 'Edit Transaksi #' . $acr->REF_NO . ' - Elektronik')
@section('header_title', 'Edit Transaksi')

@section('content')
<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6">

    {{-- ========================================================= --}}
    {{-- TOP NAVIGATION & BREADCRUMB                               --}}
    {{-- ========================================================= --}}
    <div class="mb-3">
        <a
            href="{{ route('POS.transaksi') }}"
            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-black transition-colors cursor-pointer"
        >
            <svg class="w-4 h-4 mr-1 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            Kembali ke Riwayat
        </a>
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold text-heading-dark flex items-center gap-2">
                Edit Transaksi
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-primary-db/10 text-primary-db font-mono">
                    {{ $acr->REF_NO }}
                </span>
            </h1>
            <p class="text-xs text-body-text">
                Ubah harga jual, diskon item, diskon transaksi global, serta PPN secara global untuk transaksi ini.
            </p>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- ERROR VALIDATION ALERT                                    --}}
    {{-- ========================================================= --}}
    @if (isset($errors) && $errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
            <div class="font-bold flex items-center gap-2 mb-1">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Terdapat kesalahan dalam pengisian formulir:
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-xs text-red-600 pl-2">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- MAIN FORM                                                 --}}
    {{-- ========================================================= --}}
    <form
        action="{{ route('POS.transaksi.update', $acr->REF_NO) }}"
        method="POST"
        id="formEditTransaksi"
    >
        @csrf
        @method('PUT')

        <div class="space-y-6">

            {{-- FORM CONTENT: VERTICAL FLOW --}}
            <div class="space-y-6">

                {{-- CARD INFORMASI TRANSAKSI --}}
                <div class="bg-white rounded-2xl border border-gray-300 p-5 shadow-xs">
                    <h2 class="text-sm font-bold text-heading-dark mb-4 flex items-center gap-2">
                        Informasi Transaksi
                    </h2>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 text-xs">
                        <div class="p-3 bg-secondary-bw/60 rounded-xl border border-gray-200">
                            <div class="text-body-text mb-0.5">No. Transaksi</div>
                            <div class="font-bold text-gray-800 font-mono text-sm truncate">{{ $acr->REF_NO }}</div>
                        </div>

                        <div class="p-3 bg-secondary-bw/60 rounded-xl border border-gray-200">
                            <div class="text-body-text mb-0.5">Tanggal Pembelian</div>
                            <div class="font-bold text-gray-800">
                                {{ $acr->DT ? \Carbon\Carbon::parse($acr->DT)->translatedFormat('d F Y') : '-' }}
                            </div>
                        </div>

                        <div class="p-3 bg-secondary-bw/60 rounded-xl border border-gray-200">
                            <div class="text-body-text mb-0.5">Pelanggan</div>
                            <div class="font-bold text-gray-800 truncate">
                                {{ $customer ? $customer->DES : ($acr->CST ?: 'Umum / Non-Member') }}
                            </div>
                        </div>

                        <div class="p-3 bg-secondary-bw/60 rounded-xl border border-gray-200">
                            <div class="text-body-text mb-0.5">Sales / Kasir</div>
                            <div class="font-bold text-gray-800 truncate">
                                {{ $sales ? $sales->DES : ($acr->USER ?: '-') }}
                            </div>
                        </div>

                        <div class="p-3 bg-secondary-bw/60 rounded-xl border border-gray-200">
                            <div class="text-body-text mb-0.5">Metode Pembayaran</div>
                            <div class="font-bold text-gray-800 flex items-center gap-1.5">
                              
                                {{ $acr->JNS == 2 ? 'Non-Tunai / QRIS' : 'Tunai' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD DAFTAR ITEM --}}
                

                    {{-- ITEM TABLE --}}
                    <div class="border border-gray-300 rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-primary-db text-white text-xs font-semibold">
                                    <th class="py-3 px-4 font-semibold">Barang</th>
                                    <th class="py-3 px-3 w-44 font-semibold">Harga Jual (Rp)</th>
                                    <th class="py-3 px-3 w-28 font-semibold">Diskon (%)</th>
                                    <th class="py-3 px-3 w-40 font-semibold">Voucher Item (Rp)</th>
                                    <th class="py-3 px-4 text-right w-40 font-semibold">Subtotal Item</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-xs text-black">
                                @foreach ($items as $idx => $item)
                                    @php
                                        $itemModel = $item->itm();
                                        $namaBarang = $itemModel?->DES ?? $item->ITM ?? 'Barang Tanpa Nama';
                                        $barcode = $item->BC_OUT ?? $item->ITM;
                                        $hgjVal = (float) ($item->HGJ ?? 0);
                                        $pot1Val = $item->POT_1 !== null ? (float) $item->POT_1 : '';
                                        $potRVal = (float) ($item->POT_R ?? 0);
                                    @endphp
                                    <tr class="item-row hover:bg-gray-50/70 transition-colors" data-index="{{ $idx }}">
                                        {{-- Hidden REC --}}
                                        <input type="hidden" name="items[{{ $idx }}][rec]" value="{{ $item->REC }}">

                                        {{-- NAMA & BARCODE --}}
                                        <td class="py-3.5 px-4 align-top">
                                            <div class="font-bold text-gray-900 text-sm leading-tight">
                                                {{ $namaBarang }}
                                            </div>
                                            <div class="flex items-center gap-2 mt-1 font-mono text-[11px] text-body-text">
                                                <span class="px-1.5 py-0.5 bg-gray-100 rounded border border-gray-200">
                                                    {{ $barcode }}
                                                </span>
                                                <span>REC: #{{ $item->REC }}</span>
                                            </div>
                                        </td>

                                        {{-- HARGA JUAL --}}
                                        <td class="py-3.5 px-3 align-top">
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400 font-semibold">
                                                    Rp
                                                </span>
                                                <input
                                                    type="text"
                                                    inputmode="numeric"
                                                    name="items[{{ $idx }}][hgj]"
                                                    value="{{ number_format($hgjVal, 0, ',', '.') }}"
                                                    required
                                                    class="input-hgj input-currency w-full pl-8 pr-2.5 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-900 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db transition-all"
                                                >
                                            </div>
                                        </td>

                                        {{-- DISKON % --}}
                                        <td class="py-3.5 px-3 align-top">
                                            <div class="relative">
                                                <input
                                                    type="number"
                                                    step="any"
                                                    min="0"
                                                    max="100"
                                                    placeholder="0"
                                                    name="items[{{ $idx }}][pot_1]"
                                                    value="{{ $pot1Val }}"
                                                    class="input-pot1 w-full pl-2.5 pr-6 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-900 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db transition-all"
                                                >
                                                <span class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-gray-400 font-semibold">
                                                    %
                                                </span>
                                            </div>
                                            <div class="item-discount-nominal mt-1 text-[13px] font-semibold text-gray-500 whitespace-nowrap"></div>
                                        </td>

                                        {{-- DISKON RP (VOUCHER ITEM) --}}
                                        <td class="py-3.5 px-3 align-top">
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-gray-400 font-semibold text-[11px]">
                                                    Rp
                                                </span>
                                                <input
                                                    type="text"
                                                    inputmode="numeric"
                                                    placeholder="0"
                                                    name="items[{{ $idx }}][pot_r]"
                                                    value="{{ $potRVal > 0 ? number_format($potRVal, 0, ',', '.') : '' }}"
                                                    class="input-potr input-currency w-full pl-7 pr-2 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-900 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db transition-all"
                                                >
                                            </div>
                                        </td>

                                        {{-- SUBTOTAL ITEM --}}
                                        <td class="py-3.5 px-4 text-right align-top">
                                            <div class="item-subtotal-display font-bold text-gray-900 text-sm">
                                                Rp 0
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                
                </div>

                {{-- CARD POTONGAN TRANSAKSI GLOBAL, VOUCHER & PPN GLOBAL --}}
                <div class="bg-white rounded-2xl border border-gray-300 p-5 shadow-xs">
                    <h2 class="text-sm font-bold text-heading-dark mb-1.5 flex items-center gap-2">
                        Diskon Transaksi Global, Voucher & PPN Global
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- DISKON GLOBAL % --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Diskon Global Transaksi (%)
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    step="any"
                                    min="0"
                                    max="100"
                                    id="inputDiscountPercent"
                                    name="discount_percent"
                                    value="{{ $globalPct > 0 ? $globalPct : '' }}"
                                    placeholder="Contoh: 10"
                                    class="w-full pl-3 pr-8 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-semibold text-gray-900 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db transition-all"
                                >
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 font-bold">
                                    %
                                </span>
                            </div>
                        </div>

                        {{-- VOUCHER TRANSAKSI RP --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Potongan Voucher Transaksi (Rp)
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 font-bold text-sm">
                                    Rp
                                </span>
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    id="inputVoucher"
                                    name="voucher"
                                    value="{{ $voucherCalc > 0 ? number_format($voucherCalc, 0, ',', '.') : '' }}"
                                    placeholder="0"
                                    class="input-currency w-full pl-10 pr-3 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-semibold text-gray-900 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db transition-all"
                                >
                            </div>
                        </div>

                        {{-- PPN GLOBAL % --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                                PPN Transaksi Global (%)
                            </label>
                            <div class="relative">
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    id="inputPpn"
                                    name="ppn"
                                    value="{{ $ppnPercent !== null && $ppnPercent > 0 ? $ppnPercent : '' }}"
                                    placeholder="Kosong / Bebas PPN"
                                    class="w-full pl-3 pr-8 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-semibold text-gray-900 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db transition-all"
                                >
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 font-bold">
                                    %
                                </span>
                            </div>
                            <p id="ppnWarningEdit" class="hidden text-[11px] text-red-500 font-semibold mt-1">
                                PPN tidak boleh menggunakan koma (,). Gunakan angka bulat.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- BOTTOM BAR (KASIR STYLE): SUBTOTAL + TOTAL + ACTIONS --}}
            <div class="flex items-center justify-between gap-6 bg-white border-t border-black px-6 py-3 -mx-4 md:-mx-6 -mb-4 md:-mb-6 mt-6">

                {{-- Kiri: Subtotal + TOTAL --}}
                <div class="min-w-0 leading-tight select-none self-center">
                    <span class="block text-[13px] text-gray-500 font-normal">Subtotal</span>
                    <span id="labelSubtotal" class="block text-[15px] text-black font-medium">Rp 0</span>
                    <span class="block text-[11px] text-gray-500 font-medium tracking-wide mt-1.5">TOTAL</span>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span id="labelGrandTotal" class="text-[26px] leading-none font-extrabold text-black tracking-tight">Rp 0</span>
                        <span id="labelPromoGlobal" class="hidden self-start mt-0.75 text-[15px] font-medium text-gray-500 whitespace-nowrap"></span>
                        <span id="labelPPNGlobal" class="hidden self-start mt-0.75 text-[15px] font-medium text-gray-500 whitespace-nowrap"></span>
                    </div>
                </div>

                {{-- Kanan: Batal (= Metode Pembayaran) + Simpan Perubahan (= Konfirmasi & Bayar) --}}
                <div class="flex items-end gap-2.5 shrink-0">
                    <a
                        href="{{ route('POS.transaksi') }}"
                        class="h-11 px-5 flex items-center bg-white border border-black rounded-[10px] text-[13px] font-bold text-black transition-all cursor-pointer whitespace-nowrap hover:border-primary-db hover:text-primary-db hover:bg-[#EEF2FF] hover:shadow-sm active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-db/40"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        id="btnSimpanTransaksi"
                        class="h-11 px-5 bg-primary-db hover:bg-primary-sdb text-white text-[13px] font-bold rounded-[10px] transition-colors cursor-pointer whitespace-nowrap"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

@endsection

@section('js')
    @vite(['resources/js/pages/pos/transaksi-edit.js'])
@endsection
