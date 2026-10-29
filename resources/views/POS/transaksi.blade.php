@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Elektronik')
@section('header_title', 'Riwayat Transaksi')

@section('content')

@php
    $sortOpsi = [
        'terbaru'  => 'Terbaru',
        'terlama'  => 'Terlama',
        'termahal' => 'Termahal',
        'termurah' => 'Termurah',
    ];
    $sortAktif = array_key_exists(request('sort'), $sortOpsi) ? request('sort') : 'terbaru';
@endphp

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6">

    {{-- ========================================================= --}}
    {{-- TOOLBAR                                                    --}}
    {{-- Urutan: Search -> Urutkan -> Tanggal dari/sampai -> Cari   --}}
    {{-- Semua filter HANYA diterapkan saat tombol Cari ditekan     --}}
    {{-- (atau Enter di kolom pencarian).                           --}}
    {{-- ========================================================= --}}

    <form
        action="{{ url()->current() }}"
        method="GET"
        class="flex flex-wrap items-center gap-2 mb-4"
    >

        {{-- NILAI SORT (diisi dropdown di bawah, tidak auto-submit) --}}
        <input type="hidden" name="sort" id="inputSortTransaksi" value="{{ $sortAktif }}">

        {{-- SEARCH --}}
        <div class="relative w-64 md:w-80">
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
                name="keyword"
                value="{{ request('keyword') }}"
                placeholder="Cari kode"
                class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:border-primary-db focus:bg-primary-db/5 focus:ring-1 focus:ring-primary-db"
            >
        </div>


        {{-- DROPDOWN SORT (di sebelah search) --}}
        <div
            class="relative inline-block text-left"
            id="containerSortTransaksi"
        >

            {{-- BUTTON --}}
            <button
                type="button"
                onclick="toggleSortTransaksi(event)"
                class="h-9.5 px-3 bg-white border border-gray-800 rounded-xl flex items-center justify-between gap-2.5 text-sm font-medium text-black hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 cursor-pointer shadow-xs min-w-42.5"
            >

                <span>
                    Urutkan:
                    <span id="labelSortTransaksi">{{ $sortOpsi[$sortAktif] }}</span>
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


            {{-- PANEL (hanya memilih nilai, TIDAK submit) --}}
            <div
                id="panelSortTransaksi"
                class="hidden absolute left-0 mt-2 w-52 bg-secondary-bw rounded-2xl p-2 shadow-2xl border border-gray-300 z-50 space-y-1.5"
            >

                @foreach ($sortOpsi as $nilai => $label)

                    <button
                        type="button"
                        data-sort="{{ $nilai }}"
                        data-label="{{ $label }}"
                        onclick="pilihSortTransaksi(this)"
                        class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 cursor-pointer shadow-xs"
                    >

                        <span>{{ $label }}</span>

                        <svg
                            class="icon-check-sort w-4 h-4 text-gray-600 {{ $sortAktif === $nilai ? '' : 'hidden' }}"
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

                    </button>

                @endforeach

            </div>

        </div>


        {{-- TANGGAL DARI - SAMPAI --}}
        <div class="flex items-center gap-2">
            <input
                type="date"
                name="tanggal_dari"
                value="{{ request('tanggal_dari') }}"
                max="{{ request('tanggal_sampai') }}"
                title="Dari tanggal"
                class="h-9.5 px-3 bg-white border border-gray-400 rounded-lg text-sm text-black transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 cursor-pointer focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db"
            >

            <span class="text-sm text-gray-600">s/d</span>

            <input
                type="date"
                name="tanggal_sampai"
                value="{{ request('tanggal_sampai') }}"
                min="{{ request('tanggal_dari') }}"
                title="Sampai tanggal"
                class="h-9.5 px-3 bg-white border border-gray-400 rounded-lg text-sm text-black transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 cursor-pointer focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db"
            >
        </div>


        {{-- TOMBOL CARI --}}
        <button
            type="submit"
            class="h-9.5 px-4 bg-primary-db text-white rounded-lg text-sm font-semibold cursor-pointer transition-colors duration-200 hover:bg-primary-sdb active:bg-dark-navy shadow-xs hover:shadow"
        >
            Cari
        </button>


        {{-- RESET --}}
        @if(request('keyword') || request('tanggal_dari') || request('tanggal_sampai') || $sortAktif !== 'terbaru')
            <a
                href="{{ url()->current() }}"
                class="h-9.5 px-4 inline-flex items-center bg-white border border-gray-800 text-black hover:bg-gray-100 rounded-lg text-sm font-semibold transition-colors cursor-pointer"
            >
                Reset
            </a>
        @endif

    </form>


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

                                <div class="inline-flex items-center justify-center gap-2">
                                    {{-- TOMBOL EDIT TRANSAKSI --}}
                                    <a
                                        href="{{ route('POS.transaksi.edit', ['id' => $item->REF_NO]) }}"
                                        title="Edit Transaksi {{ $item->REF_NO }}"
                                        class="h-8 w-8 border border-gray-400 bg-white hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 rounded-md inline-flex items-center justify-center transition-all duration-200 shadow-2xs cursor-pointer text-primary-db"
                                    >
                                        <svg
                                            class="w-4 h-4 text-primary-db"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"
                                            />
                                        </svg>
                                    </a>

                                    @php
                                        $cstModel = $item->cst();
                                        $dsModel = ($cstModel && $cstModel->DS) ? \App\Models\CST::where('CST', $cstModel->DS)->first() : null;
                                        $cstNama = $cstModel?->DES ?? '';
                                        $cstAlamat = $cstModel?->AL ?? '';
                                        $cstDesa = $dsModel?->DES ?? '';
                                    @endphp
                                    {{-- TOMBOL LIHAT STRUK --}}
                                    <button
                                        type="button"
                                        onclick="openStrukModal(this)"
                                        data-ref="{{ $item->REF_NO }}"
                                        data-tanggal="{{ $tglDisplay }}"
                                        data-waktu="{{ $waktuDisplay }}"
                                        data-kasir="{{ $item->USER ?? 'Kasir' }}"
                                        data-customer-nama="{{ $cstNama }}"
                                        data-customer-desa="{{ $cstDesa }}"
                                        data-customer-alamat="{{ $cstAlamat }}"
                                        data-metode="{{ $metodeDisplay }}"
                                        data-subtotal="{{ $item->subtotal_calc ?? $item->BRT ?? 0 }}"
                                        data-diskon-item="{{ $item->item_discount_calc ?? 0 }}"
                                        data-diskon-global-pct="{{ (float) ($item->POT_1 ?? 0) }}"
                                        data-diskon-global="{{ $item->global_discount_calc ?? 0 }}"
                                        data-voucher="{{ $item->voucher_calc ?? 0 }}"
                                        data-ppn="{{ $item->ppn ?? '' }}"
                                        data-ppn-nominal="{{ $item->ppn_nominal ?? 0 }}"
                                        data-total="{{ $item->BRT ?? 0 }}"
                                        data-dibayar="{{ $dibayar }}"
                                        data-kembalian="{{ $kembalian }}"
                                        data-detail="{{ json_encode($detailArr) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-gray-800 hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 rounded-lg text-xs font-semibold text-black cursor-pointer transition-all duration-200"
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
                                </div>

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


        <x-pagination :paginator="$data" label="transaksi" />
</div>


    {{-- MODALS TRANSAKSI (STRUK & PRINT LAPORAN) --}}
@include('POS.modals.transaksi-modal')

</div>

@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    @vite(['resources/js/pages/pos/transaksi.js'])
@endsection
