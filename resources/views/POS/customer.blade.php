@extends('layouts.app')

@section('title', 'Customer & Desa - Elektronik')
@section('header_title', 'Customer & Desa')

@section('content')

@php
    // Sort ditentukan dari query string (?sort=a-z|z-a|new-old|old-new), default a-z
    $sortOptions = [
        'a-z'     => 'Nama (A-Z)',
        'z-a'     => 'Nama (Z-A)',
        'new-old' => 'Terbaru',
        'old-new' => 'Terlama',
    ];
    $sort = array_key_exists((string) request('sort'), $sortOptions) ? request('sort') : 'a-z';
    $sortLabel = $sortOptions[$sort];

    $tipeOptions = [
        'all'      => 'Semua',
        'customer' => 'Customer',
        'desa'     => 'Desa',
    ];
    $currentTipe = request('tipe', 'all');
    if (!array_key_exists($currentTipe, $tipeOptions)) {
        $currentTipe = 'all';
    }
@endphp

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6">

    {{-- ========================================================= --}}
    {{-- TOOLBAR --}}
    {{-- ========================================================= --}}

    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">

        {{-- SEARCH & FILTER --}}
        <div class="flex flex-wrap items-center gap-2">

            {{-- SEARCH --}}
            <div class="relative w-64 md:w-72">
                <form action="{{ route('POS.customer') }}" method="GET">
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input type="hidden" name="tipe" value="{{ $currentTipe }}">

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
                        placeholder="Cari customer / desa..."
                        class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:border-primary-db focus:bg-primary-db/5 focus:ring-1 focus:ring-primary-db"
                        value="{{ request('keyword') }}"
                        name="keyword"
                    >
                </form>
            </div>

            {{-- FILTER TIPE (Pills) --}}
            <div class="inline-flex p-1 bg-gray-200/70 border border-gray-300 rounded-xl text-xs font-semibold">
                @foreach ($tipeOptions as $k => $label)
                    <a
                        href="{{ request()->fullUrlWithQuery(['tipe' => $k, 'page' => null]) }}"
                        class="px-3 py-1.5 rounded-lg transition-all duration-150 {{ $currentTipe === $k ? 'bg-primary-db text-white shadow-xs' : 'text-gray-700 hover:text-black hover:bg-white/60' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- DROPDOWN SORT --}}
            <div class="relative inline-block text-left" id="containerFilterCustomer">
                <button
                    type="button"
                    onclick="toggleFilterCustomer(event)"
                    class="h-9.5 px-3 bg-white border border-gray-800 rounded-xl flex items-center justify-between gap-2.5 text-sm font-medium text-black hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 cursor-pointer shadow-xs min-w-40"
                >
                    <span id="labelSelectedCustomerSort">Urutkan: {{ $sortLabel }}</span>
                    <svg id="arrowCustomerSort" class="w-4 h-4 text-gray-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <div
                    id="panelFilterCustomer"
                    class="hidden absolute left-0 mt-2 w-48 bg-secondary-bw rounded-2xl p-2 shadow-2xl border border-gray-300 z-50 space-y-1.5"
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

        {{-- BUTTONS: CETAK LAPORAN DESA & TAMBAH DATA --}}
        <div class="flex items-center gap-2">
            @if(auth()->user()?->isManagerOrAdmin())
            {{-- CETAK LAPORAN DESA (Hanya Manager dan Admin) --}}
            <a
                href="{{ route('POS.customer.desa.cetak') }}"
                target="_blank"
                class="h-9.5 px-3.5 bg-white border border-primary-db hover:bg-primary-db/10 text-primary-db rounded-lg flex items-center gap-2 text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs"
                title="Cetak Laporan Penjualan Desa"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.641-2.115-1.18-3.088a9.043 9.043 0 01-.54-1.241m0 0a9.014 9.014 0 0117.84 0m-17.84 0a9.043 9.043 0 00-.54 1.24m18.38 0c-.24 1.077-.641 2.116-1.18 3.089m0 0a9.043 9.043 0 01-.54 1.24m0 0a9.014 9.014 0 01-16.66 0" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Laporan Desa</span>
            </a>
            @endif

            {{-- TAMBAH CUSTOMER / DESA --}}
            <button
                type="button"
                onclick="openTambahModal()"
                class="h-9.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
            >
                <span class="text-lg leading-none font-bold">+</span>
                <span>Tambah Data</span>
            </button>
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
                        <th class="py-2.5 px-4 rounded-tl-lg">Kode</th>
                        <th class="py-2.5 px-4">Nama</th>
                        <th class="py-2.5 px-4 text-center">Tipe</th>
                        <th class="py-2.5 px-4">Nomor Telepon</th>
                        <th class="py-2.5 px-4">Alamat</th>
                        <th class="py-2.5 px-4">Desa</th>
                        <th class="py-2.5 px-4 text-center w-20 rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody class="divide-y divide-gray-200 text-sm text-black">
                    @forelse ($data as $customer)
                        @php
                            $isDesa = empty($customer->DS);
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            {{-- CST --}}
                            <td class="py-3.5 px-4 font-medium text-gray-900">
                                {{ $customer->CST }}
                            </td>

                            {{-- DES --}}
                            <td class="py-3.5 px-4 font-semibold text-gray-900">
                                {{ $customer->DES }}
                            </td>

                            {{-- TIPE --}}
                            <td class="py-3.5 px-4 text-center">
                                @if ($isDesa)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Desa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-300">
                                        Customer
                                    </span>
                                @endif
                            </td>

                            {{-- TLP --}}
                            <td class="py-3.5 px-4 text-gray-700">
                                {{ $customer->TLP ?? '-' }}
                            </td>

                            {{-- AL --}}
                            <td class="py-3.5 px-4 text-gray-700">
                                {{ $customer->AL ?? '-' }}
                            </td>

                            {{-- DESA --}}
                            <td class="py-3.5 px-4 text-gray-700">
                                @if ($isDesa)
                                    <span class="text-xs text-gray-400 italic">(Desa Induk)</span>
                                @else
                                    <span class="font-medium text-gray-900">{{ $customer->ds()->first()?->DES ?? '-' }}</span>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td class="py-3.5 px-4 text-center">
                                <div class="relative inline-block">
                                    <button
                                        type="button"
                                        onclick="toggleActionMenu(this)"
                                        class="h-8 w-8 border border-gray-400 bg-white hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 rounded-md inline-flex items-center justify-center transition-all duration-200 cursor-pointer"
                                        title="Aksi"
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

                                    <div
                                        class="action-menu absolute right-0 top-full mt-1 w-28 bg-white border border-gray-300 rounded-lg shadow-xl py-1 z-50 text-left hidden"
                                    >
                                        {{-- EDIT --}}
                                        <button
                                            type="button"
                                            data-cst="{{ $customer->CST }}"
                                            data-des="{{ $customer->DES }}"
                                            data-tlp="{{ $customer->TLP ?? '' }}"
                                            data-al="{{ $customer->AL ?? '' }}"
                                            data-ds="{{ $customer->DS ?? '' }}"
                                            data-is-desa="{{ $isDesa ? '1' : '0' }}"
                                            data-action="{{ route('POS.customer.update', ['id' => $customer->CST]) }}"
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

                                        {{-- DELETE --}}
                                        <form
                                            method="POST"
                                            action="{{ route('POS.customer.destroy', ['id' => $customer->CST]) }}"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                onclick="return confirm('Hapus {{ $isDesa ? "desa" : "customer" }} ini?')"
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
                                colspan="7"
                                class="py-8 text-center text-gray-500 text-sm"
                            >
                                Tidak ada data customer atau desa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>

        {{-- FOOTER / PAGINATION --}}
        <x-pagination :paginator="$data" label="data" />

    </div>

    {{-- MODAL TAMBAH & EDIT CUSTOMER / DESA --}}
    @include('POS.modals.customer-modal')

</div>

@endsection

@section('js')
    @vite(['resources/js/pages/pos/customer.js'])
@endsection
