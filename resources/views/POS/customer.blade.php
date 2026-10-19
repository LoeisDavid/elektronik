@extends('layouts.app')

@section('title', 'Customer - Elektronik')
@section('header_title', 'Customer')

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
@endphp

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6">

    {{-- ========================================================= --}}
    {{-- TOOLBAR --}}
    {{-- ========================================================= --}}

    <div class="flex items-center justify-between gap-3 mb-4">

        {{-- SEARCH --}}
        <div class="flex items-center gap-2">

            <div class="relative w-64 md:w-80">
                <form action="{{ route('POS.customer') }}" method="GET">
                    {{-- pertahankan sort saat search --}}
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
                        placeholder="Cari customer"
                        class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:border-primary-db focus:bg-primary-db/5 focus:ring-1 focus:ring-primary-db"
                        value="{{ request('keyword') }}"
                        name="keyword"
                    >
                </form>
            </div>

            {{-- DROPDOWN SORT --}}
                <div class="relative inline-block text-left" id="containerFilterCustomer">

                    {{-- Tombol Trigger Dropdown --}}
                    <button
                        type="button"
                        onclick="toggleFilterCustomer(event)"
                        class="h-9.5 px-3 bg-white border border-gray-800 rounded-xl flex items-center justify-between gap-2.5 text-sm font-medium text-black hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 cursor-pointer shadow-xs min-w-42.5"
                    >
                        <span id="labelSelectedCustomerSort">Urutkan: {{ $sortLabel }}</span>
                        <svg id="arrowCustomerSort" class="w-4 h-4 text-gray-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    {{-- Panel Pop-up Timbul --}}
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


        {{-- TAMBAH CUSTOMER --}}
        <button
            type="button"
            onclick="openTambahModal()"
            class="h-9.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
        >
            <span class="text-lg leading-none font-bold">+</span>
            <span>Tambah customer</span>
        </button>

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
                            Kode Customer
                        </th>

                        <th class="py-2.5 px-4 ">
                            Nama Customer
                        </th>

                        <th class="py-2.5 px-4 ">
                            Nomor Telepon
                        </th>

                        <th class="py-2.5 px-4 ">
                            Alamat
                        </th>

                        <th class="py-2.5 px-4 ">
                            Desa
                        </th>

                        <th class="py-2.5 px-4 text-center w-20 rounded-tr-lg">
                            Aksi
                        </th>

                    </tr>

                </thead>


                {{-- BODY --}}
                <tbody class="divide-y divide-gray-200 text-sm text-black">

                    @forelse ($data as $customer)

                        <tr class="hover:bg-gray-50/80 transition-colors">

                            {{-- CST --}}
                            <td class="py-3.5 px-4 font-medium">
                                {{ $customer->CST }}
                            </td>


                            {{-- DES --}}
                            <td class="py-3.5 px-4">
                                {{ $customer->DES }}
                            </td>


                            {{-- TLP --}}
                            <td class="py-3.5 px-4">
                                {{ $customer->TLP }}
                            </td>

                            <td class="py-3.5 px-4">
                                {{ $customer->AL }}
                            </td>

                            <td class="py-3.5 px-4">
                                {{ $customer->ds()->first()->DES }}
                            </td>


                            {{-- AKSI --}}
                            <td class="py-3.5 px-4 text-center">

                                <div class="relative inline-block">

                                    {{-- BUTTON --}}
                                    <button
                                        type="button"
                                        onclick="toggleActionMenu(this)"
                                        class="h-8 w-8 border border-gray-400 bg-white hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 rounded-md inline-flex items-center justify-center transition-all duration-200 cursor-pointer"
                                        title="Aksi Customer"
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


                                    {{-- ACTION MENU --}}
                                    <div
                                        class="action-menu absolute right-0 top-full mt-1 w-28 bg-white border border-gray-300 rounded-lg shadow-xl py-1 z-50 text-left hidden"
                                    >

                                        {{-- EDIT --}}
                                        <button
                                            type="button"
                                            data-cst="{{ $customer->CST }}"
                                            data-des="{{ $customer->DES }}"
                                            data-tlp="{{ $customer->TLP }}"
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
                                                onclick="return confirm('Hapus customer ini?')"
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
                                colspan="4"
                                class="py-8 text-center text-gray-500 text-sm"
                            >
                                Tidak ada customer.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ========================================================= --}}
        {{-- FOOTER --}}
        {{-- ========================================================= --}}

        <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-[#142854] rounded-b-lg">

            {{-- INFO --}}
            <div class="whitespace-nowrap">

                Menampilkan
                {{ $data->firstItem() ?? 0 }}
                -
                {{ $data->lastItem() ?? 0 }}
                dari
                {{ $data->total() ?? 0 }}
                customer

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
                            class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200"
                        >
                            ‹
                        </a>

                    @endif


                    @php
                        $current = $data->currentPage();
                        $last = $data->lastPage();
                    @endphp


                    {{-- ========================================= --}}
                    {{-- 1 --}}
                    {{-- ========================================= --}}

                    <a
                        href="{{ $data->url(1) }}"
                        class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                        {{ $current == 1
                            ? 'bg-[#142854] text-white border-[#142854]'
                            : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200' }}"
                    >
                        1
                    </a>


                    @if ($last <= 5)

                        {{-- PAGE 2 --}}
                        @for ($page = 2; $page <= $last; $page++)

                            <a
                                href="{{ $data->url($page) }}"
                                class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                                {{ $current == $page
                                    ? 'bg-[#142854] text-white border-[#142854]'
                                    : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200' }}"
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
                                : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200' }}"
                        >
                            2
                        </a>

                        <a
                            href="{{ $data->url(3) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                            {{ $current == 3
                                ? 'bg-[#142854] text-white border-[#142854]'
                                : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200' }}"
                        >
                            3
                        </a>

                        <span class="px-1">
                            ...
                        </span>

                        <a
                            href="{{ $data->url($last) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                            bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200"
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
                                : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200' }}"
                        >
                            {{ $last - 2 }}
                        </a>

                        <a
                            href="{{ $data->url($last - 1) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                            {{ $current == $last - 1
                                ? 'bg-[#142854] text-white border-[#142854]'
                                : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200' }}"
                        >
                            {{ $last - 1 }}
                        </a>

                        <a
                            href="{{ $data->url($last) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                            {{ $current == $last
                                ? 'bg-[#142854] text-white border-[#142854]'
                                : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200' }}"
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
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200"
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
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200"
                        >
                            {{ $current + 1 }}
                        </a>

                        <span class="px-1">
                            ...
                        </span>

                        <a
                            href="{{ $data->url($last) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200"
                        >
                            {{ $last }}
                        </a>

                    @endif


                    {{-- NEXT --}}
                    @if ($data->hasMorePages())

                        <a
                            href="{{ $data->nextPageUrl() }}"
                            class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200"
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
    {{-- MODAL TAMBAH --}}
    {{-- ========================================================= --}}

    <div
        id="tambahCustomerModal"
        class="fixed inset-0 z-50 {{ $errors->any() ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
    >

        <div
            class="absolute inset-0"
            onclick="closeTambahModal()"
        ></div>


        <div
            class="relative w-full max-w-sm bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black"
        >

            <h2 class="text-2xl font-bold mb-5">
                Tambah Customer
            </h2>


            <form
                id="formTambah"
                method="POST"
                action="{{ route('POS.customer.store') }}"
                class="space-y-4"
            >

                @csrf
                {{-- DES --}}
                <div>

                    <label class="block text-sm font-semibold mb-1">
                        Nama Customer
                    </label>

                    <input
                        type="text"
                        name="DES"
                        required
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        value="{{ old('DES') }}"
                    >
                    @error('DES')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror

                </div>


                {{-- TLP --}}
                <div>

                    <label class="block text-sm font-semibold mb-1">
                        Nomor Telepon
                    </label>

                    <input
                        type="text"
                        name="TLP"
                        required
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        value="{{ old('TLP') }}"
                    >
                    @error('TLP')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror

                </div>


                <div class="pt-4 flex items-center gap-3">

                    <button
                        type="button"
                        onclick="closeTambahModal()"
                        class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 transition-all duration-200 cursor-pointer"
                    >
                        Batal
                    </button>

                    <button
                        id="btnTambah"
                        type="submit"
                        class="flex-1 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
                    >
                        Simpan
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- MODAL EDIT --}}
    {{-- ========================================================= --}}

    <div
        id="editCustomerModal"
        class="fixed inset-0 z-50 {{ $errors->editCustomer->any() ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
    >

        <div
            class="absolute inset-0"
            onclick="closeEditModal()"
        ></div>


        <div
            class="relative w-full max-w-sm bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black"
        >

            <h2 class="text-2xl font-bold mb-5">
                Edit Customer
            </h2>


            <form
            id="editCustomerForm"
            method="POST"
            class="space-y-4"
            action="{{ $errors->editCustomer->any() && old('CST') ? route('POS.customer.update', ['id' => old('CST')]) : '' }}"
        >
            @csrf
            @method('PUT')

            {{-- CST (Kode Customer) --}}
            <div>
                <label class="block text-sm font-semibold mb-1 text-black">
                    Kode Customer
                </label>
                <input
                    id="editCST"
                    type="text"
                    name="CST"
                    readonly
                    required
                    class="w-full bg-gray-100 border border-black/80 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 cursor-not-allowed focus:outline-none"
                    value="{{ $errors->editCustomer->any() ? old('CST') : '' }}"
                >
            </div>

            {{-- DES (Nama Customer) --}}
            <div>
                <label class="block text-sm font-semibold mb-1 text-black">
                    Nama Customer
                </label>
                <input
                    id="editDES"
                    type="text"
                    name="DES"
                    required
                    placeholder="Masukkan nama customer"
                    class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    value="{{ $errors->editCustomer->any() ? old('DES') : '' }}"
                >
                @error('DES', 'editCustomer')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- TLP (Nomor Telepon) --}}
            <div>
                <label class="block text-sm font-semibold mb-1 text-black">
                    Nomor Telepon
                </label>
                <input
                    id="editTLP"
                    type="text"
                    name="TLP"
                    required
                    placeholder="Masukkan nomor telepon"
                    class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    value="{{ $errors->editCustomer->any() ? old('TLP') : '' }}"
                >
                @error('TLP', 'editCustomer')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="pt-4 flex items-center gap-3">
                <button
                    type="button"
                    onclick="closeEditModal()"
                    class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 transition-all duration-200 cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="flex-1 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
                >
                    Simpan
                </button>
            </div>

        </form>

        </div>

    </div>

</div>


{{-- ============================================================= --}}
{{-- JAVASCRIPT (hanya tampilan: menu aksi, dropdown, modal)        --}}
{{-- ============================================================= --}}

<script>

    // ---------- Menu aksi ----------
    function toggleActionMenu(button) {
        const menu = button.parentElement.querySelector('.action-menu');

        document.querySelectorAll('.action-menu').forEach(function (item) {
            if (item !== menu) item.classList.add('hidden');
        });

        menu.classList.toggle('hidden');
    }

    // ---------- Dropdown sort (buka/tutup saja) ----------
    function toggleFilterCustomer(event) {
        event.stopPropagation();
        document.getElementById('panelFilterCustomer').classList.toggle('hidden');
        document.getElementById('arrowCustomerSort').classList.toggle('rotate-180');
    }

    // ---------- Tutup menu/dropdown saat klik di luar ----------
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.action-menu') && !event.target.closest('[onclick="toggleActionMenu(this)"]')) {
            document.querySelectorAll('.action-menu').forEach(function (menu) {
                menu.classList.add('hidden');
            });
        }

        if (!event.target.closest('#containerFilterCustomer')) {
            document.getElementById('panelFilterCustomer').classList.add('hidden');
            document.getElementById('arrowCustomerSort').classList.remove('rotate-180');
        }
    });

    // ---------- Modal tambah ----------
    function openTambahModal() {
        const modal = document.getElementById('tambahCustomerModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeTambahModal() {
        const modal = document.getElementById('tambahCustomerModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Mencegah double submit (ditaruh di luar fungsi agar aktif otomatis)
    document.getElementById('formTambah').addEventListener('submit', function (event) {
        const btn = document.getElementById('btnTambah');
        
        // 1. Cek apakah tombol sudah dalam kondisi disabled
        if (btn.disabled) {
            event.preventDefault();
            return;
        }

        // 2. Matikan tombol dan ganti teksnya agar user tahu sedang memproses
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed'); // Opsional: Tambah efek visual redup Tailwind
        btn.innerText = 'Menyimpan...';
    });

    // ---------- Modal edit ----------
    function openEditModal(button) {
        document.getElementById('editCST').value = button.dataset.cst;
        document.getElementById('editDES').value = button.dataset.des;
        document.getElementById('editTLP').value = button.dataset.tlp;
        document.getElementById('editCustomerForm').action = button.dataset.action;

        document.querySelectorAll('.action-menu').forEach(function (menu) {
            menu.classList.add('hidden');
        });

        const modal = document.getElementById('editCustomerModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditModal() {
        const modal = document.getElementById('editCustomerModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

</script>

@endsection