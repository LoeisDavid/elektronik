@extends('layouts.app')

@section('title', 'Edit Transaksi #' . $acr->REF_NO . ' - Elektronik')
@section('header_title', 'Edit Transaksi')

@section('content')
<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6">

    {{-- ========================================================= --}}
    {{-- TOP NAVIGATION & BREADCRUMB                               --}}
    {{-- ========================================================= --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a
                href="{{ route('POS.transaksi') }}"
                class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-gray-100 border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:text-primary-db shadow-xs transition-all duration-200"
            >
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke Riwayat
            </a>
            <div class="h-5 w-px bg-gray-300"></div>
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

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold {{ $acr->JNS == 2 ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                <span class="w-2 h-2 rounded-full {{ $acr->JNS == 2 ? 'bg-purple-600' : 'bg-emerald-600' }}"></span>
                Metode: {{ $acr->JNS == 2 ? 'Non-Tunai / QRIS' : 'Tunai' }}
            </span>
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- LEFT COLUMN: DETAIL TRANSAKSI & ITEM TABLE (8 COLS) --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- CARD INFORMASI TRANSAKSI --}}
                <div class="bg-white rounded-2xl border border-gray-300 p-5 shadow-xs">
                    <h2 class="text-sm font-bold text-heading-dark mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary-db" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Informasi Transaksi
                    </h2>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
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
                    </div>
                </div>

                {{-- CARD DAFTAR ITEM --}}
                <div class="bg-white rounded-2xl border border-gray-300 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-gray-200 bg-white">
                        <h2 class="text-sm font-bold text-heading-dark flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary-db" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Rincian Item Barang ({{ count($items) }})
                        </h2>
                        <p class="text-xs text-body-text mt-0.5">
                            Sesuaikan harga jual dan diskon (persen / nominal) untuk setiap barang.
                        </p>
                    </div>

                    {{-- ITEM TABLE --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-primary-db text-white font-semibold">
                                    <th class="py-3 px-4">Barang</th>
                                    <th class="py-3 px-3 w-44">Harga Jual (Rp)</th>
                                    <th class="py-3 px-3 w-28">Diskon (%)</th>
                                    <th class="py-3 px-3 w-40">Voucher Item (Rp)</th>
                                    <th class="py-3 px-4 text-right w-40">Subtotal Item</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-gray-800">
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
                                                    type="number"
                                                    step="any"
                                                    min="0"
                                                    name="items[{{ $idx }}][hgj]"
                                                    value="{{ $hgjVal }}"
                                                    required
                                                    class="input-hgj w-full pl-8 pr-2.5 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-900 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db transition-all"
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
                                        </td>

                                        {{-- DISKON RP (VOUCHER ITEM) --}}
                                        <td class="py-3.5 px-3 align-top">
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-gray-400 font-semibold text-[11px]">
                                                    Rp
                                                </span>
                                                <input
                                                    type="number"
                                                    step="any"
                                                    min="0"
                                                    placeholder="0"
                                                    name="items[{{ $idx }}][pot_r]"
                                                    value="{{ $potRVal > 0 ? $potRVal : '' }}"
                                                    class="input-potr w-full pl-7 pr-2 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-900 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db transition-all"
                                                >
                                            </div>
                                        </td>

                                        {{-- SUBTOTAL ITEM --}}
                                        <td class="py-3.5 px-4 text-right align-top">
                                            <div class="item-subtotal-display font-bold text-gray-900 text-sm">
                                                Rp 0
                                            </div>
                                            <div class="item-discount-label text-[11px] text-critical-red mt-0.5">
                                                -
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
                        <svg class="w-4 h-4 text-primary-db" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Diskon Transaksi Global, Voucher & PPN Global
                    </h2>
                    <p class="text-xs text-body-text mb-4">
                        PPN, Diskon Global, dan Voucher diinput sekali di sini dan berlaku untuk seluruh transaksi ini.
                    </p>

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
                            <span class="text-[11px] text-body-text mt-1 block">
                                Diterapkan ke subtotal barang setelah diskon item.
                            </span>
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
                                    type="number"
                                    step="any"
                                    min="0"
                                    id="inputVoucher"
                                    name="voucher"
                                    value="{{ $voucherCalc > 0 ? $voucherCalc : '' }}"
                                    placeholder="0"
                                    class="w-full pl-10 pr-3 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-semibold text-gray-900 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db transition-all"
                                >
                            </div>
                            <span class="text-[11px] text-body-text mt-1 block">
                                Nilai nominal voucher global potongan transaksi.
                            </span>
                        </div>

                        {{-- PPN GLOBAL % (CUKUP 1 KOLOM UNTUK SEMUA) --}}
                        <div class="p-3 bg-blue-50/60 border border-blue-200 rounded-xl">
                            <label class="block text-xs font-bold text-blue-900 mb-1.5 flex items-center justify-between">
                                <span>PPN Transaksi Global (%)</span>
                                <span class="text-[10px] text-blue-600 font-normal">Global</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    step="1"
                                    min="0"
                                    max="100"
                                    id="inputPpn"
                                    name="ppn"
                                    value="{{ $ppnPercent !== null && $ppnPercent > 0 ? $ppnPercent : '' }}"
                                    placeholder="Kosong / Bebas PPN"
                                    class="w-full pl-3 pr-8 py-2 bg-white border border-blue-300 rounded-lg text-sm font-bold text-blue-950 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db transition-all"
                                >
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-blue-600 font-bold">
                                    %
                                </span>
                            </div>

                            {{-- PRESET SHORTCUTS --}}
                            <div class="flex items-center gap-1.5 mt-2">
                                <button
                                    type="button"
                                    onclick="setPpnPreset(11)"
                                    class="px-2 py-0.5 bg-white hover:bg-primary-db hover:text-white border border-blue-300 rounded text-[11px] font-semibold text-blue-800 transition-colors shadow-2xs cursor-pointer"
                                >
                                    11%
                                </button>
                                <button
                                    type="button"
                                    onclick="setPpnPreset(12)"
                                    class="px-2 py-0.5 bg-white hover:bg-primary-db hover:text-white border border-blue-300 rounded text-[11px] font-semibold text-blue-800 transition-colors shadow-2xs cursor-pointer"
                                >
                                    12%
                                </button>
                                <button
                                    type="button"
                                    onclick="setPpnPreset('')"
                                    class="px-2 py-0.5 bg-white hover:bg-red-600 hover:text-white border border-gray-300 rounded text-[11px] font-semibold text-gray-600 transition-colors shadow-2xs cursor-pointer"
                                >
                                    Bebas / 0%
                                </button>
                            </div>
                            <span class="text-[10.5px] text-blue-700 mt-1.5 block leading-tight">
                                Cukup isi di 1 kolom ini, semua item transaksi otomatis mengikuti PPN tersebut.
                            </span>
                        </div>
                    </div>

                    {{-- ATURAN PENGHITUNGAN PPN --}}
                    <div class="mt-4 p-3 bg-secondary-bw rounded-xl border border-gray-200 flex items-start gap-2.5 text-xs text-gray-700">
                        <svg class="w-4 h-4 text-primary-db shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <span class="font-bold text-gray-900">Ketentuan Perhitungan:</span>
                            PPN dihitung setelah menjumlahkan total seluruh item barang dan mengurangi seluruh potongan diskon item serta diskon transaksi global/voucher. Nilai PPN dapat dikosongkan apabila transaksi bebas pajak.
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: RINGKASAN KALKULASI & ACTION BUTTONS (4 COLS) --}}
            <div class="lg:col-span-4 lg:sticky lg:top-6 space-y-4">

                <div class="bg-white rounded-2xl border border-gray-300 p-5 shadow-sm">
                    <h2 class="text-base font-bold text-heading-dark pb-3 border-b border-gray-200 flex items-center justify-between">
                        <span>Ringkasan Transaksi</span>
                        <span class="text-xs font-normal text-body-text">Kalkulasi Otomatis</span>
                    </h2>

                    {{-- BREAKDOWN LIST --}}
                    <div class="py-4 space-y-2.5 text-xs text-gray-600">

                        <div class="flex justify-between items-center">
                            <span>Subtotal Kotor Barang</span>
                            <span class="font-bold text-gray-900 font-mono text-sm" id="calcRawSubtotal">Rp 0</span>
                        </div>

                        <div class="flex justify-between items-center text-critical-red">
                            <span>Diskon Item Barang</span>
                            <span class="font-bold font-mono" id="calcItemDiscount">- Rp 0</span>
                        </div>

                        <div class="pt-2 border-t border-dashed border-gray-200 flex justify-between items-center">
                            <span>Subtotal Setelah Diskon Item</span>
                            <span class="font-semibold text-gray-800 font-mono" id="calcSubtotalAfterItemDisc">Rp 0</span>
                        </div>

                        <div class="flex justify-between items-center text-critical-red">
                            <span id="labelGlobalDiscount">Diskon Transaksi (0%)</span>
                            <span class="font-bold font-mono" id="calcGlobalDiscount">- Rp 0</span>
                        </div>

                        <div class="flex justify-between items-center text-critical-red">
                            <span>Voucher Transaksi</span>
                            <span class="font-bold font-mono" id="calcVoucher">- Rp 0</span>
                        </div>

                        <div class="pt-2 border-t border-dashed border-gray-200 flex justify-between items-center">
                            <span>Total Setelah Seluruh Diskon</span>
                            <span class="font-semibold text-gray-800 font-mono" id="calcTotalAfterAllDisc">Rp 0</span>
                        </div>

                        <div class="flex justify-between items-center text-blue-700">
                            <span id="labelPpnRate">PPN (0%)</span>
                            <span class="font-bold font-mono" id="calcPpnNominal">+ Rp 0</span>
                        </div>
                    </div>

                    {{-- GRAND TOTAL HIGHLIGHT --}}
                    <div class="pt-4 border-t-2 border-gray-200">
                        <div class="p-3.5 bg-secondary-bw rounded-xl border border-gray-300 flex flex-col justify-between">
                            <span class="text-xs font-medium text-body-text">Grand Total Baru</span>
                            <span class="text-2xl font-black text-primary-db font-mono mt-1" id="calcGrandTotal">
                                Rp 0
                            </span>
                        </div>

                        <div class="flex items-center justify-between mt-2.5 text-[11px] text-body-text px-1">
                            <span>Total Sebelumnya:</span>
                            <span class="font-mono font-medium text-gray-700">Rp {{ number_format((float) $acr->BRT, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="mt-6 space-y-2">
                        <button
                            type="submit"
                            id="btnSimpanTransaksi"
                            class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-primary-db hover:bg-primary-sdb active:scale-[0.99] text-white rounded-xl font-bold text-sm shadow-md transition-all duration-200 cursor-pointer"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Perubahan
                        </button>

                        <a
                            href="{{ route('POS.transaksi') }}"
                            class="w-full flex items-center justify-center py-2.5 px-4 bg-white hover:bg-gray-100 text-gray-700 hover:text-gray-900 border border-gray-300 rounded-xl font-semibold text-sm transition-colors text-center"
                        >
                            Batal
                        </a>
                    </div>
                </div>

                {{-- TIP CARD --}}
                <div class="p-4 bg-amber-50/80 border border-amber-200 rounded-2xl text-xs text-amber-900">
                    <div class="font-bold flex items-center gap-1.5 mb-1 text-amber-800">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Perhatian
                    </div>
                    Menyimpan perubahan akan memperbarui harga jual & diskon masing-masing barang, mengupdate kolom PPN pada seluruh item transaksi (`STS`), serta mengupdate grand total (`BRT`), pembayaran (`PAY`), dan diskon (`TOT_POT`) pada tabel `ACR`.
                </div>

            </div>

        </div>
    </form>
</div>

{{-- ========================================================= --}}
{{-- JAVASCRIPT REAL-TIME CALCULATION                           --}}
{{-- ========================================================= --}}
<script>
    function setPpnPreset(val) {
        const ppnInput = document.getElementById('inputPpn');
        if (ppnInput) {
            ppnInput.value = val;
            recalculateTotals();
        }
    }

    function recalculateTotals() {
        const rows = document.querySelectorAll('.item-row');
        let rawSubtotal = 0;
        let totalItemDiscount = 0;

        rows.forEach(row => {
            const hgjInput = row.querySelector('.input-hgj');
            const pot1Input = row.querySelector('.input-pot1');
            const potrInput = row.querySelector('.input-potr');
            const subtotalDisplay = row.querySelector('.item-subtotal-display');
            const discountLabel = row.querySelector('.item-discount-label');

            const hgj = Math.max(0, parseFloat(hgjInput ? hgjInput.value : 0) || 0);
            const pot1 = Math.min(100, Math.max(0, parseFloat(pot1Input ? pot1Input.value : 0) || 0));
            const potr = Math.max(0, parseFloat(potrInput ? potrInput.value : 0) || 0);

            // Hitung diskon item
            const discPctNominal = hgj * (pot1 / 100);
            const itemDiscTotal = discPctNominal + potr;
            const itemSubtotal = Math.max(0, hgj - itemDiscTotal);

            // Update baris subtotal
            if (subtotalDisplay) {
                subtotalDisplay.innerText = formatRupiah(itemSubtotal);
            }

            if (discountLabel) {
                if (itemDiscTotal > 0) {
                    let desc = [];
                    if (pot1 > 0) desc.push(`-${pot1}%`);
                    if (potr > 0) desc.push(`-Rp ${Math.round(potr).toLocaleString('id-ID')}`);
                    discountLabel.innerText = `Hemat: ${desc.join(' + ')}`;
                    discountLabel.classList.remove('hidden', 'text-gray-400');
                    discountLabel.classList.add('text-critical-red');
                } else {
                    discountLabel.innerText = 'Tanpa Diskon';
                    discountLabel.classList.add('text-gray-400');
                    discountLabel.classList.remove('text-critical-red');
                }
            }

            rawSubtotal += hgj;
            totalItemDiscount += itemDiscTotal;
        });

        // Global Diskon, Voucher & PPN Global
        const discPercentInput = document.getElementById('inputDiscountPercent');
        const voucherInput = document.getElementById('inputVoucher');
        const ppnInput = document.getElementById('inputPpn');

        const globalPct = Math.min(100, Math.max(0, parseFloat(discPercentInput ? discPercentInput.value : 0) || 0));
        const voucher = Math.max(0, parseFloat(voucherInput ? voucherInput.value : 0) || 0);

        // PPN Global Rate
        let ppnRate = 0;
        if (ppnInput && ppnInput.value !== '' && !isNaN(ppnInput.value)) {
            ppnRate = Math.min(100, Math.max(0, parseFloat(ppnInput.value) || 0));
        }

        const subtotalAfterItemDisc = Math.max(0, rawSubtotal - totalItemDiscount);
        const globalDiscNominal = subtotalAfterItemDisc * (globalPct / 100);
        const totalAfterAllDisc = Math.max(0, subtotalAfterItemDisc - globalDiscNominal - voucher);

        // PPN Nominal: dihitung setelah menjumlahkan total semua barang dan setelah mengurangi total barang dari diskon
        const ppnNominal = ppnRate > 0 ? (totalAfterAllDisc * (ppnRate / 100)) : 0;
        const grandTotal = totalAfterAllDisc + ppnNominal;

        // Render Summary
        document.getElementById('calcRawSubtotal').innerText = formatRupiah(rawSubtotal);
        document.getElementById('calcItemDiscount').innerText = `- ${formatRupiah(totalItemDiscount)}`;
        document.getElementById('calcSubtotalAfterItemDisc').innerText = formatRupiah(subtotalAfterItemDisc);
        
        document.getElementById('labelGlobalDiscount').innerText = `Diskon Transaksi (${globalPct}%)`;
        document.getElementById('calcGlobalDiscount').innerText = `- ${formatRupiah(globalDiscNominal)}`;
        
        document.getElementById('calcVoucher').innerText = `- ${formatRupiah(voucher)}`;
        document.getElementById('calcTotalAfterAllDisc').innerText = formatRupiah(totalAfterAllDisc);
        
        document.getElementById('labelPpnRate').innerText = ppnRate > 0 ? `PPN (${ppnRate}%)` : 'PPN (Bebas / 0%)';
        document.getElementById('calcPpnNominal').innerText = ppnRate > 0 ? `+ ${formatRupiah(ppnNominal)}` : 'Rp 0';
        
        document.getElementById('calcGrandTotal').innerText = formatRupiah(grandTotal);
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Daftarkan event listener untuk seluruh input
        const editForm = document.getElementById('formEditTransaksi');
        if (editForm) {
            editForm.addEventListener('input', function (e) {
                if (e.target.matches('.input-hgj, .input-pot1, .input-potr, #inputDiscountPercent, #inputVoucher, #inputPpn')) {
                    recalculateTotals();
                }
            });

            editForm.addEventListener('change', function (e) {
                if (e.target.matches('.input-hgj, .input-pot1, .input-potr, #inputDiscountPercent, #inputVoucher, #inputPpn')) {
                    recalculateTotals();
                }
            });
        }

        // Kalkulasi awal
        recalculateTotals();
    });
</script>
@endsection
