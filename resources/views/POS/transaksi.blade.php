@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Elektronik')
@section('header_title', 'Riwayat Transaksi')

@section('content')

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6">

    {{-- ========================================================= --}}
    {{-- TOOLBAR --}}
    {{-- ========================================================= --}}

    <div class="flex items-center justify-between gap-3 mb-4">

    {{-- SEARCH + SORT --}}
    <div class="flex items-center gap-2">

        {{-- SEARCH --}}
        <div class="relative w-64 md:w-80">

            <form action="{{ url()->current() }}" method="GET">

                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-black">
                    <svg
                        class="w-5 h-5 stroke-[2.5]"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                        />
                    </svg>
                </span>

                <input
                    type="text"
                    placeholder="Cari kode"
                    class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db"
                    value="{{ request('keyword') }}"
                    name="keyword"
                >

            </form>

        </div>


        {{-- DROPDOWN SORT --}}
        <div
            class="relative inline-block text-left"
            id="containerSortTransaksi"
        >

            {{-- BUTTON --}}
            <button
                type="button"
                onclick="toggleSortTransaksi(event)"
                class="h-9.5 px-3 bg-white border border-gray-800 rounded-xl flex items-center justify-between gap-2.5 text-sm font-medium text-black hover:bg-gray-50 transition-colors cursor-pointer shadow-xs min-w-42.5"
            >

                <span>
                    Urutkan:
                    @switch(request('sort', 'terbaru'))

                        @case('terbaru')
                            Terbaru
                            @break

                        @case('terlama')
                            Terlama
                            @break

                        @case('termahal')
                            Termahal
                            @break

                        @case('termurah')
                            Termurah
                            @break

                        @default
                            Terbaru

                    @endswitch
                </span>

                <svg
                    id="arrowSortTransaksi"
                    class="w-4 h-4 text-gray-600 transition-transform duration-200"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                    />
                </svg>

            </button>


            {{-- PANEL --}}
            <div
                id="panelSortTransaksi"
                class="hidden absolute left-0 mt-2 w-52 bg-secondary-bw rounded-2xl p-2 shadow-2xl border border-gray-300 z-50 space-y-1.5"
            >

                {{-- TERBARU --}}
                <a
                    href="{{ request()->fullUrlWithQuery(['sort' => 'terbaru', 'page' => 1]) }}"
                    class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer shadow-xs"
                >

                    <span>Terbaru</span>

                    @if(request('sort', 'terbaru') === 'terbaru')
                        <svg
                            class="w-4 h-4 text-gray-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4.5 12.75l6 6 9-13.5"
                            />
                        </svg>
                    @endif

                </a>


                {{-- TERLAMA --}}
                <a
                    href="{{ request()->fullUrlWithQuery(['sort' => 'terlama', 'page' => 1]) }}"
                    class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer shadow-xs"
                >

                    <span>Terlama</span>

                    @if(request('sort') === 'terlama')
                        <svg
                            class="w-4 h-4 text-gray-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4.5 12.75l6 6 9-13.5"
                            />
                        </svg>
                    @endif

                </a>


                {{-- TERMAHAL --}}
                <a
                    href="{{ request()->fullUrlWithQuery(['sort' => 'termahal', 'page' => 1]) }}"
                    class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer shadow-xs"
                >

                    <span>Termahal</span>

                    @if(request('sort') === 'termahal')
                        <svg
                            class="w-4 h-4 text-gray-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4.5 12.75l6 6 9-13.5"
                            />
                        </svg>
                    @endif

                </a>


                {{-- TERMURAH --}}
                <a
                    href="{{ request()->fullUrlWithQuery(['sort' => 'termurah', 'page' => 1]) }}"
                    class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer shadow-xs"
                >

                    <span>Termurah</span>

                    @if(request('sort') === 'termurah')
                        <svg
                            class="w-4 h-4 text-gray-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4.5 12.75l6 6 9-13.5"
                            />
                        </svg>
                    @endif

                </a>

            </div>

        </div>

    </div>

</div>


    {{-- ========================================================= --}}
    {{-- TABLE --}}
    {{-- ========================================================= --}}

    <div class="bg-white rounded-lg shadow-sm border border-gray-300 flex flex-col">

        <div class="overflow-visible">

            <table class="w-full text-left border-collapse">

                {{-- HEADER --}}
                <thead>

                    <tr class="bg-primary-db text-white text-xs font-semibold">

                        <th class="py-2.5 px-4 rounded-tl-lg">
                            Kode Transaksi
                        </th>

                        <th class="py-2.5 px-4">
                            Tanggal pembelian
                        </th>

                        <th class="py-2.5 px-4">
                            Total
                        </th>

                        <th class="py-2.5 px-4 text-center rounded-tr-lg">
                            Aksi
                        </th>

                    </tr>

                </thead>


                {{-- BODY --}}
                <tbody class="divide-y divide-gray-200 text-sm text-black">

                    @forelse ($data as $item)

                        @php
                            $tglFmt = $item->DT
                                ? \Carbon\Carbon::parse($item->DT)->format('d/m/Y')
                                : '-';
                            $tglDisplay = $item->DT
                                ? \Carbon\Carbon::parse($item->DT)->translatedFormat('d M Y')
                                : '-';
                            $waktuDisplay = $item->created_at
                                ? \Carbon\Carbon::parse($item->created_at)->format('H.i')
                                : ($item->DT ? \Carbon\Carbon::parse($item->DT)->format('H.i') : '00.00');
                            $isTunai = ($item->JNS != 2);
                            $metodeDisplay = $isTunai ? 'Tunai' : 'Non-Tunai / QRIS';
                            $dibayar = (float) ($item->PAY ?? $item->BRT ?? 0);
                            $kembalian = max(0, $dibayar - (float) ($item->BRT ?? 0));

                            $detailArr = collect($item->detail ?? [])->values();
                        @endphp

                        <tr class="hover:bg-gray-50/80 transition-colors">

                            {{-- KODE --}}
                            <td class="py-3.5 px-4 font-medium">
                                {{ $item->REF_NO }}
                            </td>


                            {{-- TANGGAL --}}
                            <td class="py-3.5 px-4">
                                {{ $tglFmt }}
                            </td>


                            {{-- TOTAL --}}
                            <td class="py-3.5 px-4 font-semibold">
                                Rp {{ number_format($item->BRT ?? 0, 0, ',', '.') }}
                            </td>


                            {{-- AKSI --}}
                            <td class="py-3.5 px-4 text-center">

                                <button
                                    type="button"
                                    onclick="openStrukModal(this)"
                                    data-ref="{{ $item->REF_NO }}"
                                    data-tanggal="{{ $tglDisplay }}"
                                    data-waktu="{{ $waktuDisplay }}"
                                    data-kasir="{{ $item->USER ?? 'Kasir' }}"
                                    data-metode="{{ $metodeDisplay }}"
                                    data-subtotal="{{ $item->subtotal_calc ?? $item->BRT ?? 0 }}"
                                    data-diskon-item="{{ $item->item_discount_calc ?? 0 }}"
                                    data-diskon-global-pct="{{ (float) ($item->POT_1 ?? 0) }}"
                                    data-diskon-global="{{ $item->global_discount_calc ?? 0 }}"
                                    data-voucher="{{ $item->voucher_calc ?? 0 }}"
                                    data-total="{{ $item->BRT ?? 0 }}"
                                    data-dibayar="{{ $dibayar }}"
                                    data-kembalian="{{ $kembalian }}"
                                    data-detail="{{ json_encode($detailArr) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-gray-800 hover:bg-gray-100 rounded-lg text-xs font-semibold text-black cursor-pointer transition-colors"
                                >

                                    <svg
                                        class="w-3.5 h-3.5 text-current"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm0 5.25h.007v.008H3.75V12zm0 5.25h.007v.008H3.75V12.75z"
                                        />
                                    </svg>

                                    <span>Struk</span>

                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="py-8 text-center text-gray-500 text-sm"
                            >
                                Tidak ada transaksi.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ========================================================= --}}
        {{-- FOOTER --}}
        {{-- ========================================================= --}}

        <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-primary-db rounded-b-lg">

            {{-- INFO --}}
            <div class="whitespace-nowrap">

                Menampilkan
                {{ $data->firstItem() ?? 0 }}
                -
                {{ $data->lastItem() ?? 0 }}
                dari
                {{ $data->total() ?? 0 }}
                transaksi

            </div>


            {{-- PAGINATION --}}
            @if ($data->lastPage() > 1)

                <div class="flex items-center gap-1">

                    {{-- PREVIOUS --}}
                    @if ($data->onFirstPage())

                        <span class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#A8B1C1]">
                            ‹
                        </span>

                    @else

                        <a
                            href="{{ $data->previousPageUrl() }}"
                            class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100"
                        >
                            ‹
                        </a>

                    @endif


                    @php
                        $current = $data->currentPage();
                        $last = $data->lastPage();
                    @endphp


                    {{-- 1 --}}
                    <a
                        href="{{ $data->url(1) }}"
                        class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                        {{ $current == 1
                            ? 'bg-[#142854] text-white border-[#142854]'
                            : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                    >
                        1
                    </a>


                    @if ($last <= 5)

                        {{-- PAGE 2 - LAST --}}
                        @for ($page = 2; $page <= $last; $page++)

                            <a
                                href="{{ $data->url($page) }}"
                                class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                                {{ $current == $page
                                    ? 'bg-[#142854] text-white border-[#142854]'
                                    : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                            >
                                {{ $page }}
                            </a>

                        @endfor


                    @elseif ($current <= 3)

                        {{-- 1 2 3 ... LAST --}}

                        <a
                            href="{{ $data->url(2) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                            {{ $current == 2
                                ? 'bg-[#142854] text-white border-[#142854]'
                                : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                        >
                            2
                        </a>

                        <a
                            href="{{ $data->url(3) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                            {{ $current == 3
                                ? 'bg-[#142854] text-white border-[#142854]'
                                : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                        >
                            3
                        </a>

                        <span class="px-1">
                            ...
                        </span>

                        <a
                            href="{{ $data->url($last) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100"
                        >
                            {{ $last }}
                        </a>


                    @elseif ($current >= $last - 2)

                        {{-- 1 ... LAST-2 LAST-1 LAST --}}

                        <span class="px-1">
                            ...
                        </span>

                        <a
                            href="{{ $data->url($last - 2) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                            {{ $current == $last - 2
                                ? 'bg-[#142854] text-white border-[#142854]'
                                : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                        >
                            {{ $last - 2 }}
                        </a>

                        <a
                            href="{{ $data->url($last - 1) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                            {{ $current == $last - 1
                                ? 'bg-[#142854] text-white border-[#142854]'
                                : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                        >
                            {{ $last - 1 }}
                        </a>

                        <a
                            href="{{ $data->url($last) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                            {{ $current == $last
                                ? 'bg-[#142854] text-white border-[#142854]'
                                : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                        >
                            {{ $last }}
                        </a>


                    @else

                        {{-- 1 ... CURRENT-1 CURRENT CURRENT+1 ... LAST --}}

                        <span class="px-1">
                            ...
                        </span>

                        <a
                            href="{{ $data->url($current - 1) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100"
                        >
                            {{ $current - 1 }}
                        </a>

                        <a
                            href="{{ $data->url($current) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-[#142854] text-white border-[#142854]"
                        >
                            {{ $current }}
                        </a>

                        <a
                            href="{{ $data->url($current + 1) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100"
                        >
                            {{ $current + 1 }}
                        </a>

                        <span class="px-1">
                            ...
                        </span>

                        <a
                            href="{{ $data->url($last) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100"
                        >
                            {{ $last }}
                        </a>

                    @endif


                    {{-- NEXT --}}
                    @if ($data->hasMorePages())

                        <a
                            href="{{ $data->nextPageUrl() }}"
                            class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100"
                        >
                            ›
                        </a>

                    @else

                        <span class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#A8B1C1]">
                            ›
                        </span>

                    @endif

                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- MODAL STRUK (SESUAI DESAIN KASIR / POMELO)                --}}
    {{-- ========================================================= --}}

    <div
        id="modalStrukPembayaran"
        onclick="closeStrukKlikLuar(event)"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4"
    >

        <!-- Card Modal -->
        <div
            onclick="event.stopPropagation()"
            class="w-full max-w-sm bg-white rounded-3xl p-6 shadow-2xl space-y-4 border border-gray-100 animate-fade-in"
        >

            <!-- Judul Modal -->
            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight">Struk</h3>

            <!-- Tampilan Kertas Struk Sesuai Mockup Kasir -->
            <div id="areaStrukPrint" class="bg-white p-5 rounded-2xl border border-dashed border-gray-300 text-xs font-sans text-gray-800 space-y-3">

                <!-- Header Toko / Brand Name -->
                <div class="text-center space-y-1">
                    <h2 class="text-2xl font-serif font-extrabold text-black tracking-tight" id="strukNamaToko">Pomelo.</h2>
                    <p class="text-[11px] text-gray-400 font-medium mt-1">No. Transaksi</p>
                    <p id="strukNoNota" class="text-sm font-bold text-gray-900 font-mono tracking-wider">-</p>
                </div>

                <div class="border-b border-dashed border-gray-300 my-2"></div>

                <!-- Detail Transaksi (Kiri) -->
                <div class="space-y-0.5 text-xs text-gray-500 font-medium">
                    <p id="strukTanggal">-</p>
                    <p id="strukWaktu">-</p>
                    <p>Dilayani oleh: <span id="strukKasir" class="text-gray-800 font-semibold">Kasir</span></p>
                </div>

                <div class="border-b border-dashed border-gray-300 my-2"></div>

                <!-- Item / Produk -->
                <div id="strukListProduk" class="space-y-2.5">
                    <!-- Diisi secara dinamis via JavaScript -->
                </div>

                <div class="border-b border-dashed border-gray-300 my-2"></div>

                <!-- Rincian Subtotal, Diskon & Total -->
                <div class="space-y-1.5 pt-0.5 text-xs">
                    <!-- Subtotal -->
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span id="strukSubtotal" class="font-semibold text-gray-900">Rp 0</span>
                    </div>

                    <!-- Diskon Per Item (Tampil jika ada) -->
                    <div id="wrapperStrukDiskonItem" class="hidden justify-between text-gray-600">
                        <span>Diskon Per Item</span>
                        <span id="strukDiskonItem" class="font-semibold text-red-600">- Rp 0</span>
                    </div>

                    <!-- Diskon Global (Tampil jika ada) -->
                    <div id="wrapperStrukDiskonGlobal" class="hidden justify-between text-gray-600">
                        <span id="labelStrukDiskonGlobal">Diskon Global</span>
                        <span id="strukDiskonGlobal" class="font-semibold text-red-600">- Rp 0</span>
                    </div>

                    <!-- Voucher Global (Tampil jika ada) -->
                    <div id="wrapperStrukVoucher" class="hidden justify-between text-gray-600">
                        <span>Voucher</span>
                        <span id="strukVoucher" class="font-semibold text-red-600">- Rp 0</span>
                    </div>

                    <!-- Total -->
                    <div class="flex justify-between text-sm font-bold text-gray-900 pt-1 border-t border-gray-100">
                        <span>Total</span>
                        <span id="strukTotal" class="text-base font-extrabold text-black">Rp 0</span>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="flex justify-between text-gray-600 pt-0.5">
                        <span>Pembayaran</span>
                        <span id="strukMetode" class="font-semibold text-gray-900">Tunai</span>
                    </div>

                    <!-- Rincian Tunai (Dibayar & Kembali) -->
                    <div id="wrapperDetailTunai" class="hidden space-y-1 pt-0.5 text-gray-600">
                        <div class="flex justify-between">
                            <span>Dibayar</span>
                            <span id="strukDibayar" class="font-semibold text-gray-900">Rp 0</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kembali</span>
                            <span id="strukKembali" class="font-semibold text-gray-900">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi (Tutup & Print Struk) -->
            <div class="grid grid-cols-2 gap-3 pt-1">
                <button
                    type="button"
                    onclick="closeStrukModal()"
                    class="w-full bg-white border border-gray-400 hover:bg-gray-100 active:bg-gray-200 text-gray-900 rounded-xl py-2.5 text-center text-xs font-bold transition-colors cursor-pointer shadow-xs">
                    Tutup
                </button>
                <button
                    type="button"
                    onclick="cetakStruk()"
                    class="w-full bg-[#1A1F36] hover:bg-black active:bg-black text-white rounded-xl py-2.5 text-center text-xs font-bold transition-colors cursor-pointer shadow-xs flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Struk
                </button>
            </div>

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- [TAMBAHAN PRINT] FORM TERSEMBUNYI -> POST KE CONTROLLER    --}}
    {{-- ========================================================= --}}
    <form
        id="formPrintStruk"
        method="POST"
        action="{{ route('POS.struk.print') }}"
        target="_blank"
        class="hidden"
    >
        @csrf
        <input type="hidden" name="ref_no"          id="printRefNo">
        <input type="hidden" name="tanggal"         id="printTanggal">
        <input type="hidden" name="waktu"           id="printWaktu">
        <input type="hidden" name="kasir"           id="printKasir">
        <input type="hidden" name="metode"          id="printMetode">
        <input type="hidden" name="subtotal"        id="printSubtotal">
        <input type="hidden" name="discount_item"   id="printDiscountItem">
        <input type="hidden" name="discount_global" id="printDiscountGlobal">
        <input type="hidden" name="voucher"         id="printVoucher">
        <input type="hidden" name="total"           id="printTotal">
        <input type="hidden" name="dibayar"         id="printDibayar">
        <input type="hidden" name="kembalian"       id="printKembalian">
        <input type="hidden" name="items"           id="printItems">
    </form>

</div>


{{-- ============================================================= --}}
{{-- CSS CETAK --}}
{{-- ============================================================= --}}

<style>

    @media print {

        body * {
            visibility: hidden !important;
        }

        #areaStrukPrint,
        #areaStrukPrint * {
            visibility: visible !important;
        }

        #areaStrukPrint {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            max-width: 350px;
            border: none !important;
        }

    }

</style>


{{-- ============================================================= --}}
{{-- HTML2CANVAS --}}
{{-- ============================================================= --}}

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>


<script>

    // ============================================================
    // [TAMBAHAN PRINT] DATA STRUK YANG SEDANG DIBUKA DI MODAL
    // ============================================================

    let strukAktif = null;


    // ============================================================
    // DROPDOWN SORT
    // ============================================================

    function toggleSortTransaksi(event) {

        if (event) {
            event.stopPropagation();
        }

        const panel =
            document.getElementById('panelSortTransaksi');

        const arrow =
            document.getElementById('arrowSortTransaksi');

        panel.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');

    }


    function tutupSortTransaksi() {

        const panel =
            document.getElementById('panelSortTransaksi');

        const arrow =
            document.getElementById('arrowSortTransaksi');

        panel.classList.add('hidden');
        arrow.classList.remove('rotate-180');

    }


    // ============================================================
    // MODAL STRUK
    // ============================================================

    function escapeHtml(str) {

        return String(str ?? '').replace(/[&<>"']/g, function(c) {

            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[c];

        });

    }


    function formatRupiah(value) {

        return 'Rp ' +
            Number(value || 0).toLocaleString('id-ID');

    }


    function openStrukModal(btn) {

        let items = [];

        try {
            items = JSON.parse(btn.dataset.detail || '[]');
        } catch (e) {
            items = [];
        }

        const refNo = btn.dataset.ref || '-';
        const tanggal = btn.dataset.tanggal || '-';
        const waktu = btn.dataset.waktu || '00.00';
        const kasir = btn.dataset.kasir || 'Kasir';
        const metode = btn.dataset.metode || 'Tunai';
        const subtotal = parseFloat(btn.dataset.subtotal) || 0;
        const diskonItem = parseFloat(btn.dataset.diskonItem) || 0;
        const diskonGlobalPct = parseFloat(btn.dataset.diskonGlobalPct) || 0;
        const diskonGlobal = parseFloat(btn.dataset.diskonGlobal) || 0;
        const voucher = parseFloat(btn.dataset.voucher) || 0;
        const total = parseFloat(btn.dataset.total) || 0;
        const dibayar = parseFloat(btn.dataset.dibayar) || 0;
        const kembalian = parseFloat(btn.dataset.kembalian) || 0;

        strukAktif = {
            ref: refNo,
            tanggal: tanggal,
            waktu: waktu,
            kasir: kasir,
            metode: metode,
            subtotal: subtotal,
            diskonItem: diskonItem,
            diskonGlobalPct: diskonGlobalPct,
            diskonGlobal: diskonGlobal,
            voucher: voucher,
            total: total,
            dibayar: dibayar,
            kembalian: kembalian,
            items: items
        };

        document.getElementById('strukNoNota').textContent = refNo;
        document.getElementById('strukTanggal').textContent = tanggal;
        document.getElementById('strukWaktu').textContent = waktu;
        document.getElementById('strukKasir').textContent = kasir;
        document.getElementById('strukMetode').textContent = metode;

        // Render List Produk
        const list = document.getElementById('strukListProduk');
        list.innerHTML = '';

        if (!Array.isArray(items) || items.length === 0) {
            list.innerHTML = '<p class="text-gray-400 text-center py-2">Tidak ada detail produk</p>';
        } else {
            items.forEach(function(d) {
                const qty = Number(d.qty ?? d.QTY ?? 1);
                const nama = d.nama ?? d.NAMA ?? '-';
                const harga = d.price ?? d.harga ?? d.HARGA ?? 0;
                const discInfo = d.discount_info || '';
                const subtotalItem = d.subtotal ?? (harga * qty);

                list.innerHTML +=
                    '<div class="flex justify-between items-start text-xs pt-1">' +
                        '<div class="min-w-0 pr-2">' +
                            '<p class="font-bold text-gray-900 truncate">' + escapeHtml(nama) + '</p>' +
                            '<p class="text-[11px] text-gray-500 font-medium">' + qty + 'x ' + formatRupiah(harga) + '</p>' +
                            (discInfo ? '<p class="text-[10.5px] text-red-600 font-medium mt-0.5">' + escapeHtml(discInfo) + '</p>' : '') +
                        '</div>' +
                        '<span class="font-bold text-gray-900 shrink-0 text-right">' + formatRupiah(subtotalItem) + '</span>' +
                    '</div>';
            });
        }

        // Subtotal
        document.getElementById('strukSubtotal').textContent = formatRupiah(subtotal);

        // Diskon Per Item
        const elWrapDiscItem = document.getElementById('wrapperStrukDiskonItem');
        const elDiscItem = document.getElementById('strukDiskonItem');
        if (diskonItem > 0) {
            elDiscItem.textContent = '- ' + formatRupiah(diskonItem);
            elWrapDiscItem.classList.remove('hidden');
            elWrapDiscItem.classList.add('flex');
        } else {
            elWrapDiscItem.classList.remove('flex');
            elWrapDiscItem.classList.add('hidden');
        }

        // Diskon Global
        const elWrapDiscGlobal = document.getElementById('wrapperStrukDiskonGlobal');
        const elDiscGlobal = document.getElementById('strukDiskonGlobal');
        const elLabelDiscGlobal = document.getElementById('labelStrukDiskonGlobal');
        if (diskonGlobal > 0) {
            if (diskonGlobalPct > 0) {
                elLabelDiscGlobal.textContent = 'Diskon Global (' + diskonGlobalPct + '%)';
            } else {
                elLabelDiscGlobal.textContent = 'Diskon Global';
            }
            elDiscGlobal.textContent = '- ' + formatRupiah(diskonGlobal);
            elWrapDiscGlobal.classList.remove('hidden');
            elWrapDiscGlobal.classList.add('flex');
        } else {
            elWrapDiscGlobal.classList.remove('flex');
            elWrapDiscGlobal.classList.add('hidden');
        }

        // Voucher
        const elWrapVoucher = document.getElementById('wrapperStrukVoucher');
        const elVoucher = document.getElementById('strukVoucher');
        if (voucher > 0) {
            elVoucher.textContent = '- ' + formatRupiah(voucher);
            elWrapVoucher.classList.remove('hidden');
            elWrapVoucher.classList.add('flex');
        } else {
            elWrapVoucher.classList.remove('flex');
            elWrapVoucher.classList.add('hidden');
        }

        // Total
        document.getElementById('strukTotal').textContent = formatRupiah(total);

        // Detail Tunai (Dibayar & Kembali)
        const elWrapTunai = document.getElementById('wrapperDetailTunai');
        const isTunai = (metode || '').toLowerCase().includes('tunai') || (metode || '').toLowerCase().includes('cash');
        if (isTunai) {
            document.getElementById('strukDibayar').textContent = formatRupiah(dibayar);
            document.getElementById('strukKembali').textContent = formatRupiah(kembalian);
            elWrapTunai.classList.remove('hidden');
        } else {
            elWrapTunai.classList.add('hidden');
        }

        const modal = document.getElementById('modalStrukPembayaran');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }


    function closeStrukModal() {

        const modal =
            document.getElementById('modalStrukPembayaran');

        modal.classList.remove('flex');
        modal.classList.add('hidden');

    }


    function closeStrukKlikLuar(event) {

        if (event.target === event.currentTarget) {
            closeStrukModal();
        }

    }


    // ============================================================
    // DOWNLOAD STRUK
    // ============================================================

    function downloadStruk() {

        if (typeof html2canvas === 'undefined') {

            alert('Library html2canvas gagal dimuat.');

            return;
        }


        const area =
            document.getElementById('areaStrukPrint');


        html2canvas(area, {
            scale: 2,
            backgroundColor: '#ffffff'
        }).then(function(canvas) {

            const a =
                document.createElement('a');

            a.download =
                'struk-' +
                document.getElementById('strukNoNota').textContent +
                '.png';

            a.href =
                canvas.toDataURL('image/png');

            a.click();

        });

    }


    // ============================================================
    // PRINT (DomPDF) - kirim transaksi terpilih ke Controller
    // ============================================================

    function cetakStruk() {

        if (!strukAktif) {

            alert('Data struk tidak ditemukan.');

            return;
        }


        const items = (strukAktif.items || []).map(function(d) {

            const qty =
                Number(d.qty ?? d.QTY ?? 1);

            const nama =
                d.nama ?? d.NAMA ?? '-';

            const harga =
                d.price ?? d.harga ?? d.HARGA ?? 0;

            const discInfo =
                d.discount_info || '';

            const subtotal =
                d.subtotal ?? (harga * qty);

            return {
                nama: nama,
                qty: qty,
                price: harga,
                discount_info: discInfo,
                subtotal: subtotal
            };

        });

        const isTunai = (strukAktif.metode || '').toLowerCase().includes('tunai') || (strukAktif.metode || '').toLowerCase().includes('cash');

        document.getElementById('printRefNo').value =
            strukAktif.ref;

        document.getElementById('printTanggal').value =
            strukAktif.tanggal;

        document.getElementById('printWaktu').value =
            strukAktif.waktu;

        document.getElementById('printKasir').value =
            strukAktif.kasir;

        document.getElementById('printMetode').value =
            strukAktif.metode;

        document.getElementById('printSubtotal').value =
            strukAktif.subtotal;

        document.getElementById('printDiscountItem').value =
            strukAktif.diskonItem;

        document.getElementById('printDiscountGlobal').value =
            strukAktif.diskonGlobal;

        document.getElementById('printVoucher').value =
            strukAktif.voucher;

        document.getElementById('printTotal').value =
            strukAktif.total;

        document.getElementById('printDibayar').value =
            isTunai ? strukAktif.dibayar : '';

        document.getElementById('printKembalian').value =
            isTunai ? strukAktif.kembalian : '';

        document.getElementById('printItems').value =
            JSON.stringify(items);


        document.getElementById('formPrintStruk').submit();

    }


    // ============================================================
    // CLICK OUTSIDE
    // ============================================================

    document.addEventListener('click', function(event) {

        const container =
            document.getElementById('containerSortTransaksi');

        if (
            container &&
            !container.contains(event.target)
        ) {

            tutupSortTransaksi();

        }

    });


    // ============================================================
    // ESCAPE
    // ============================================================

    document.addEventListener('keydown', function(event) {

        if (event.key === 'Escape') {

            tutupSortTransaksi();
            closeStrukModal();

        }

    });

</script>

@endsection