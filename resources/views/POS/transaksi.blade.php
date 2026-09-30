@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Elektronik')
@section('header_title', 'Riwayat Transaksi')

@section('content')

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 select-none">

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
                                    data-tanggal="{{ $tglFmt }}"
                                    data-total="{{ $item->BRT ?? 0 }}"
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
    {{-- MODAL STRUK --}}
    {{-- ========================================================= --}}

    <div
        id="modalStrukPembayaran"
        onclick="closeStrukKlikLuar(event)"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4"
    >

        <div
            onclick="event.stopPropagation()"
            class="w-full max-w-sm bg-white rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-100 animate-fade-in"
        >

            {{-- AREA STRUK --}}
            <div
                id="areaStrukPrint"
                class="bg-white p-4 rounded-xl border border-dashed border-gray-300 text-xs font-sans text-gray-800 space-y-3"
            >

                <div class="text-center space-y-1">

                    <h2
                        class="text-xl font-extrabold text-black tracking-wider uppercase"
                        id="strukNamaToko"
                    >
                        NamaToko
                    </h2>

                    <p
                        class="text-[11px] text-gray-500 leading-tight"
                        id="strukAlamatToko"
                    >
                        Medokan Ayu, Kec. Rungkut, Surabaya<br>
                        No Telp: 0881-0268-77983
                    </p>

                </div>


                <div class="border-b border-dashed border-gray-300 my-2"></div>


                <div class="space-y-1 text-gray-600">

                    <div class="flex justify-between">
                        <span>No. Transaksi</span>
                        <span
                            id="strukNoNota"
                            class="font-semibold text-gray-900"
                        >
                            -
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span>Tanggal</span>
                        <span id="strukTanggal">
                            -
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span>Dilayani oleh</span>
                        <span id="strukKasir">
                            User 2
                        </span>
                    </div>

                </div>


                <div class="border-b border-dashed border-gray-300 my-2"></div>


                <div
                    id="strukListProduk"
                    class="space-y-2"
                ></div>


                <div class="border-b border-dashed border-gray-300 my-2"></div>


                <div class="space-y-1.5 pt-1">

                    <div class="flex justify-between text-sm font-bold text-black">

                        <span>
                            Total
                        </span>

                        <span
                            id="strukTotal"
                            class="text-base"
                        >
                            Rp 0
                        </span>

                    </div>

                </div>

            </div>


            {{-- TOMBOL --}}
            <div class="grid grid-cols-2 gap-3 pt-2">

                <button
                    type="button"
                    onclick="downloadStruk()"
                    class="w-full bg-white border active:bg-black text-black rounded-xl py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer shadow-sm flex items-center justify-center gap-2"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="1em"
                        height="1em"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M0 0h24v24H0z"
                            fill="none"
                        />

                        <path
                            fill="none"
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 15V3m0 12l-4-4m4 4l4-4M2 17l.621 2.485A2 2 0 0 0 4.561 21h14.877a2 2 0 0 0 1.94-1.515L22 17"
                        />
                    </svg>

                    Download

                </button>


                <button
                    type="button"
                    onclick="cetakStruk()"
                    class="w-full bg-primary-db hover:bg-primary-sdb active:bg-black text-white rounded-xl py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer shadow-sm flex items-center justify-center gap-2"
                >

                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4h2m0 0h10m-6 4h6a2 2 0 002-2v-4H9v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                        />
                    </svg>

                    Print Struk

                </button>

            </div>

        </div>

    </div>

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

            items =
                JSON.parse(btn.dataset.detail || '[]');

        } catch (e) {

            items = [];

        }


        document.getElementById('strukNoNota').textContent =
            btn.dataset.ref;

        document.getElementById('strukTanggal').textContent =
            btn.dataset.tanggal;

        document.getElementById('strukTotal').textContent =
            formatRupiah(btn.dataset.total);


        const list =
            document.getElementById('strukListProduk');

        list.innerHTML = '';


        if (!Array.isArray(items) || items.length === 0) {

            list.innerHTML =
                '<p class="text-gray-400 text-center">' +
                'Tidak ada detail produk' +
                '</p>';

        } else {

            items.forEach(function(d) {

                const qty =
                    Number(d.qty ?? d.QTY ?? 1);

                const nama =
                    d.nama ?? d.NAMA ?? '-';

                const harga =
                    d.harga ?? d.HARGA ?? null;

                const subtotal =
                    d.subtotal ??
                    d.SUBTOTAL ??
                    (
                        harga !== null
                            ? harga * qty
                            : null
                    );


                const hargaHtml =
                    subtotal !== null
                        ? '<span class="font-semibold text-gray-900">' +
                          formatRupiah(subtotal) +
                          '</span>'
                        : '';


                list.innerHTML +=
                    '<div class="flex justify-between items-start">' +

                        '<p class="font-semibold text-gray-900">' +
                            escapeHtml(qty) +
                            'x ' +
                            escapeHtml(nama) +
                        '</p>' +

                        hargaHtml +

                    '</div>';

            });

        }


        const modal =
            document.getElementById('modalStrukPembayaran');

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
    // PRINT
    // ============================================================

    function cetakStruk() {

        window.print();

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