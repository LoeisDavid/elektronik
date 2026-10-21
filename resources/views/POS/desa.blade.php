@extends('layouts.app')

@section('title', 'Desa - Elektronik')
@section('header_title', 'Desa')

@section('content')

@php
    // Sort ditentukan dari query string, default a-z
    $sortOptions = [
        'a-z'               => 'Nama (A - Z)',
        'z-a'               => 'Nama (Z - A)',
        'transaksi-banyak'  => 'Transaksi Terbanyak',
        'transaksi-sedikit' => 'Transaksi Tersedikit',
        'nominal-banyak'    => 'Nominal Terbanyak',
        'nominal-sedikit'   => 'Nominal Tersedikit',
    ];
    $requestedSort = (string) request('sort', 'a-z');
    if ($requestedSort === 'terbanyak') $requestedSort = 'transaksi-banyak';
    if ($requestedSort === 'terdikit') $requestedSort = 'transaksi-sedikit';
    $sort = array_key_exists($requestedSort, $sortOptions) ? $requestedSort : 'a-z';
    $sortLabel = $sortOptions[$sort];
@endphp

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6">

    {{-- ========================================================= --}}
    {{-- TOOLBAR                                                   --}}
    {{-- ========================================================= --}}
    <div class="flex items-center justify-between gap-3 mb-4">

        {{-- SEARCH & FILTER --}}
        <div class="flex items-center gap-2">

            {{-- SEARCH --}}
            <div class="relative w-64 md:w-80">
                <form action="{{ route('POS.desa') }}" method="GET">
                    {{-- Pertahankan sort saat search --}}
                    <input type="hidden" name="sort" value="{{ $sort }}">

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
                        placeholder="Cari desa, transaksi, atau nominal..."
                        class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:border-primary-db focus:bg-primary-db/5 focus:ring-1 focus:ring-primary-db shadow-xs"
                        value="{{ request('keyword') }}"
                        name="keyword"
                    >
                </form>
            </div>

            {{-- DROPDOWN SORT --}}
            <div class="relative inline-block text-left" id="containerFilterDesa">

                {{-- Tombol Trigger Dropdown --}}
                <button
                    type="button"
                    onclick="toggleFilterDesa(event)"
                    class="h-9.5 px-3 bg-white border border-gray-800 rounded-xl flex items-center justify-between gap-2.5 text-sm font-medium text-black hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 cursor-pointer shadow-xs min-w-42.5"
                >
                    <span id="labelSelectedDesaSort">Urutkan: {{ $sortLabel }}</span>
                    <svg id="arrowDesaSort" class="w-4 h-4 text-gray-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                {{-- Panel Pop-up Timbul --}}
                <div
                    id="panelFilterDesa"
                    class="hidden absolute left-0 mt-2 w-52 bg-secondary-bw rounded-2xl p-2 shadow-2xl border border-gray-300 z-50 space-y-1.5"
                >
                    @foreach ($sortOptions as $key => $label)
                        <a
                            href="{{ request()->fullUrlWithQuery(['sort' => $key, 'page' => null]) }}"
                            class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 cursor-pointer shadow-xs"
                        >
                            <span>{{ $label }}</span>
                            <svg class="w-4 h-4 text-gray-600 {{ $sort === $key ? '' : 'hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </a>
                    @endforeach
                </div>

            </div>

        </div>

        <div class="flex items-center gap-2">
            {{-- PRINT LAPORAN DESA --}}
            <a
                href="{{ route('POS.desa.cetak') }}"
                target="_blank"
                class="h-9.5 px-3.5 bg-white border border-gray-800 hover:bg-gray-100 active:bg-gray-200 text-gray-800 rounded-lg flex items-center gap-2 text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs"
                title="Cetak Laporan Penjualan Per Desa"
            >
                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.673-2.684-1.28-4.329m13.12 4.329c.24-1.076.673-2.684 1.28-4.329m-14.4 0a17.962 17.962 0 0114.4 0m-14.4 0a17.962 17.962 0 0014.4 0M6 18h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zM6 14h12M9 10V6a2 2 0 012-2h2a2 2 0 012 2v4" />
                </svg>
                <span>Print Laporan</span>
            </a>

            {{-- TAMBAH DESA --}}
            <button
                type="button"
                onclick="openTambahModal()"
                class="h-9.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
            >
                <span class="text-lg leading-none font-bold">+</span>
                <span>Tambah desa</span>
            </button>
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- TABLE                                                     --}}
    {{-- ========================================================= --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 flex flex-col">

        <div class="overflow-visible">

            <table class="w-full text-left border-collapse">

                {{-- HEADER --}}
                <thead>
                    <tr class="bg-primary-db text-white text-xs font-semibold">
                        <th class="py-2.5 px-4 rounded-tl-lg">
                            Kode Desa
                        </th>
                        <th class="py-2.5 px-4">
                            Nama Desa
                        </th>
                        <th class="py-2.5 px-4 text-right">
                            Total Transaksi
                        </th>
                        <th class="py-2.5 px-4 text-right">
                            Total Pembelian
                        </th>
                        <th class="py-2.5 px-4 text-center w-20 rounded-tr-lg">
                            Aksi
                        </th>
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody class="divide-y divide-gray-200 text-sm text-black">
                    @forelse ($data as $desa)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            {{-- Kode Desa --}}
                            <td class="py-3.5 px-4 font-medium">
                                {{ $desa->DS }}
                            </td>

                            {{-- Nama Desa --}}
                            <td class="py-3.5 px-4">
                                {{ $desa->DES }}
                            </td>

                            {{-- Total Transaksi --}}
                            <td class="py-3.5 px-4 text-gray-900 text-right font-medium">
                                {{ number_format($desa->total_transaksi ?? 0, 0, ',', '.') }}
                            </td>

                            {{-- Total Pembelian --}}
                            <td class="py-3.5 px-4 text-gray-900 text-right font-medium">
                                Rp {{ number_format($desa->total_nominal ?? 0, 0, ',', '.') }}
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3.5 px-4 text-center">
                                <div class="relative inline-block">
                                    {{-- Trigger Action Menu Button --}}
                                    <button
                                        type="button"
                                        onclick="toggleActionMenu(this)"
                                        class="h-8 w-8 border border-gray-400 bg-white hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 rounded-md inline-flex items-center justify-center transition-all duration-200 cursor-pointer"
                                        title="Aksi Desa"
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
                                    </button>

                                    {{-- Action Menu Dropdown --}}
                                    <div
                                        class="action-menu absolute right-0 top-full mt-1 w-28 bg-white border border-gray-300 rounded-lg shadow-xl py-1 z-50 text-left hidden"
                                    >
                                        {{-- Edit Button --}}
                                        <button
                                            type="button"
                                            data-ds="{{ $desa->DS }}"
                                            data-des="{{ $desa->DES }}"
                                            data-action="{{ route('POS.desa.update', ['id' => $desa->DS]) }}"
                                            onclick="openEditModal(this)"
                                            class="w-full px-3 py-1.5 text-xs font-semibold text-gray-800 hover:bg-primary-db/5 hover:text-primary-db transition-all duration-200 flex items-center gap-2 cursor-pointer"
                                        >
                                            <svg
                                                class="w-3.5 h-3.5 text-gray-600"
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
                                            <span>Edit</span>
                                        </button>

                                        {{-- Delete Button / Form --}}
                                        <form
                                            method="POST"
                                            action="{{ route('POS.desa.destroy', ['id' => $desa->DS]) }}"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                onclick="return confirm('Hapus desa ini?')"
                                                class="w-full px-3 py-1.5 text-xs font-semibold text-red-500 hover:bg-red-50 transition-colors flex items-center gap-2 cursor-pointer"
                                            >
                                                <svg
                                                    class="w-3.5 h-3.5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="2"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4v3M4 7h16"
                                                    />
                                                </svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="py-8 text-center text-gray-500 text-sm"
                            >
                                Tidak ada data desa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>

        {{-- FOOTER / PAGINATION --}}
        <x-pagination :paginator="$data" label="desa" />
    </div>

{{-- MODAL TAMBAH & EDIT DESA --}}
    @include('POS.modals.desa-modal')

</div>

@endsection

@section('js')
    @vite(['resources/js/pages/pos/desa.js'])
@endsection
