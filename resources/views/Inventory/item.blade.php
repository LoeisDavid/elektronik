@extends('layouts.app')

@section('title', 'Item - Elektronik')
@section('header_title', 'Item')

@section('content')

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 select-none">

    {{-- ========================================================= --}}
    {{-- TOOLBAR --}}
    {{-- ========================================================= --}}

    <div class="flex items-center justify-between gap-3 mb-4">

        {{-- SEARCH --}}
        <div class="flex items-center gap-2">

            <div class="relative w-64 md:w-80">
                <form action="{{ route('inventory.item') }}" method="GET">
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
                        placeholder="Cari item"
                        class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db"
                        value="{{ request('keyword') }}"
                        name="keyword"
                    >
                </form>
            </div>

            {{-- DROPDOWN FILTER Item TIMBUL --}}
                <div class="relative inline-block text-left" id="containerFilterItem">
                    
                    {{-- Tombol Trigger Dropdown --}}
                    <button
                        type="button"
                        onclick="toggleFilterItem(event)"
                        class="h-9.5 px-3 bg-white border border-gray-800 rounded-xl flex items-center justify-between gap-2.5 text-sm font-medium text-black hover:bg-gray-50 transition-colors cursor-pointer shadow-xs min-w-42.5"
                    >
                        <span id="labelSelectedItemSort">Urutkan: Nama (A-Z)</span>
                        <svg id="arrowItemSort" class="w-4 h-4 text-gray-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    {{-- Panel Pop-up Timbul --}}
                    <div
                        id="panelFilterItem"
                        class="hidden absolute left-0 mt-2 w-48 bg-secondary-bw rounded-2xl p-2 shadow-2xl border border-gray-300 z-50 space-y-1.5"
                    >
                        <!-- Opsi 1: Nama (A-Z) -->
                        <button
                            type="button"
                            onclick="pilihFilterItem('Nama (A-Z)')"
                            class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer shadow-xs"
                        >
                            <span>Nama (A-Z)</span>
                            <svg id="checkItemAsc" class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </button>

                        <!-- Opsi 2: Nama (Z-A) -->
                        <button
                            type="button"
                            onclick="pilihFilterItem('Nama (Z-A)')"
                            class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer shadow-xs"
                        >
                            <span>Nama (Z-A)</span>
                            <svg id="checkItemDesc" class="w-4 h-4 text-gray-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            onclick="pilihFilterItem('Tanggal Masuk(Terbaru)')"
                            class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer shadow-xs"
                        >
                            <span>Tanggal Masuk (Terbaru)</span>
                            <svg id="checkItemDesc" class="w-4 h-4 text-gray-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            onclick="pilihFilterItem('Tanggal Masuk(Terlama)')"
                            class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer shadow-xs"
                        >
                            <span>Tanggal Masuk (Terlama)</span>
                            <svg id="checkItemDesc" class="w-4 h-4 text-gray-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            onclick="pilihFilterItem('Stok Terbanyak')"
                            class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer shadow-xs"
                        >
                            <span>Stok Terbanyak</span>
                            <svg id="checkItemDesc" class="w-4 h-4 text-gray-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            onclick="pilihFilterItem('Stok Terkecil')"
                            class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer shadow-xs"
                        >
                            <span>Stok Terkecil</span>
                            <svg id="checkItemDesc" class="w-4 h-4 text-gray-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </button>
                    </div>

                </div>

        </div>


        {{-- TAMBAH ITEM --}}
        <button
            type="button"
            onclick="openTambahModal()"
            class="h-9.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-[#0f172a] text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-colors cursor-pointer"
        >
            <span class="text-lg leading-none font-bold">+</span>
            <span>Tambah item</span>
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
                            Kode Item
                        </th>

                        <th class="py-2.5 px-4 ">
                            Nama Item
                        </th>

                        <th class="py-2.5 px-4 ">
                            Kategori
                        </th>

                        <th class="py-2.5 px-4 ">
                            Merk
                        </th>

                        <th class="py-2.5 px-4 ">
                            Supplier
                        </th>

                        <th class="py-2.5 px-4  text-right">
                            Stok
                        </th>

                        <th class="py-2.5 px-4  text-right">
                            Harga
                        </th>

                        <th class="py-2.5 px-4  text-center w-20 rounded-tr-lg">
                            Aksi
                        </th>

                    </tr>

                </thead>


                {{-- BODY --}}
                <tbody class="divide-y divide-gray-200 text-sm text-black">

                    @forelse ($data as $item)

                        <tr class="hover:bg-gray-50/80 transition-colors">

                            {{-- ITM --}}
                            <td class="py-3.5 px-4 font-medium">
                                {{ $item->ITM }}
                            </td>


                            {{-- DES --}}
                            <td class="py-3.5 px-4">
                                {{ $item->DES }}
                            </td>


                            {{-- KTG --}}
                            <td class="py-3.5 px-4">
                                {{ $item->KTG->DES ?? '-' }}
                            </td>


                            {{-- MRK --}}
                            <td class="py-3.5 px-4">
                                {{ $item->MRK->DES ?? '-' }}
                            </td>


                            {{-- SPP --}}
                            <td class="py-3.5 px-4">
                                {{ $item->SPP->DES ?? '-' }}
                            </td>


                            {{-- STOK --}}
                            <td class="py-3.5 px-4 text-right">
                                {{ $item->STOK }}
                            </td>


                            {{-- HRG --}}
                            <td class="py-3.5 px-4 text-right">
                               Rp{{ number_format($item->HRG, 0, ',', '.') }}
                            </td>


                            {{-- AKSI --}}
                            <td class="py-3.5 px-4 text-center">

                                <div class="relative inline-block">

                                    {{-- BUTTON --}}
                                    <button
                                        type="button"
                                        onclick="toggleActionMenu(this)"
                                        class="h-8 w-8 border border-gray-400 bg-white hover:bg-gray-100 hover:border-primary-db rounded-md inline-flex items-center justify-center transition-colors cursor-pointer"
                                        title="Aksi Item"
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

                                        {{-- DETAIL --}}
                                            <a
                                                class="w-full px-3 py-1.5 text-xs font-semibold text-gray-800 hover:bg-gray-100 transition-colors flex items-center gap-2 cursor-pointer"
                                                href="{{ route('inventory.item.show', $item->ITM) }}"
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
                                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                                                    />
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                    />
                                                </svg>

                                                <span>Detail</span>
                                            </a>

                                        {{-- EDIT --}}
                                        <button
                                            type="button"
                                            data-itm="{{ $item->ITM }}"
                                            data-des="{{ $item->DES }}"
                                            data-stok="{{ $item->STOK }}"
                                            data-hrg="{{ $item->HRG }}"
                                            data-ktg="{{ $item->KTG->KTG ?? '' }}"
                                            data-mrk="{{ $item->MRK->MRK ?? '' }}"
                                            data-spp="{{ $item->SPP->SPP ?? '' }}"
                                            onclick="openEditModal(this)"
                                            class="w-full px-3 py-1.5 text-xs font-semibold text-gray-800 hover:bg-gray-100 transition-colors flex items-center gap-2 cursor-pointer"
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
                                            action="{{ route('inventory.item.destroy', ['id' => $item->ITM]) }}"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                onclick="return confirm('Hapus item ini?')"
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
                                colspan="8"
                                class="py-8 text-center text-gray-500 text-sm"
                            >
                                Tidak ada item.
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
                item

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


                    {{-- ========================================= --}}
                    {{-- 1 --}}
                    {{-- ========================================= --}}

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

                        {{-- PAGE 2 --}}
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
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                            bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100"
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
    {{-- MODAL TAMBAH --}}
    {{-- ========================================================= --}}

    <div
        id="tambahItemModal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
    >

        {{-- BACKDROP CLICK TO CLOSE --}}
        <div
            class="absolute inset-0"
            onclick="closeTambahModal()"
        ></div>

        {{-- MODAL CONTAINER --}}
        <div
            class="relative w-full max-w-2xl bg-secondary-bw border border-gray-200 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto"
        >

            {{-- JUDUL MODAL --}}
            <h2 class="text-xl font-bold mb-6 text-black">
                Tambah Barang
            </h2>

            {{-- FORM --}}
            <form
                method="POST"
                action="{{ route('inventory.item.store') }}"
                class="space-y-6"
            >

                @csrf

                {{-- GRID 2 KOLOM --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">

                    {{-- KODE ITEM --}}
                    <div>
                        <label class="block text-xs font-medium text-black mb-1.5">
                            Kode Item
                        </label>

                        <input
                            type="text"
                            name="ITM"
                            value="{{ old('ITM') }}"
                            placeholder="Contoh: ITM004"
                            required
                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-black focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                        @error('ITM')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- NAMA BARANG --}}
                    <div>
                        <label class="block text-xs font-medium text-black mb-1.5">
                            Nama Barang
                        </label>

                        <input
                            type="text"
                            name="DES"
                            value="{{ old('DES') }}"
                            required
                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-black focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                        @error('DES')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- MEREK --}}
                    <div>
                        <label class="block text-xs font-medium text-black mb-1.5">
                            Merek
                        </label>

                        <select
                            name="MRK"
                            required
                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-black focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                            <option value="" disabled selected>Pilih Merk</option>
                            @foreach ($MRK as $mrk)
                                <option value="{{ $mrk->MRK }}" {{ old('MRK') == $mrk->MRK ? 'selected' : '' }}>
                                    {{ $mrk->DES }}
                                </option>
                            @endforeach
                        </select>
                        @error('MRK')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- HARGA JUAL --}}
                    <div>
                        <label class="block text-xs font-medium text-black mb-1.5">
                            Harga Jual
                        </label>

                        <input
                            type="number"
                            name="HRG"
                            step="any"
                            min="0"
                            value="{{ old('HRG') }}"
                            required
                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-black focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                        @error('HRG')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- KATEGORI --}}
                    <div>
                        <label class="block text-xs font-medium text-black mb-1.5">
                            Kategori
                        </label>

                        <select
                            name="KTG"
                            required
                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-black focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                            <option value="" disabled selected>Pilih Kategori</option>
                            @foreach ($KTG as $ktg)
                                <option value="{{ $ktg->KTG }}" {{ old('KTG') == $ktg->KTG ? 'selected' : '' }}>
                                    {{ $ktg->DES }}
                                </option>
                            @endforeach
                        </select>
                        @error('KTG')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- STOK --}}
                    <div>
                        <label class="block text-xs font-medium text-black mb-1.5">
                            Stok
                        </label>

                        <input
                            type="number"
                            name="STOK"
                            min="0"
                            value="{{ old('STOK') }}"
                            required
                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-black focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                        @error('STOK')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- SUPPLIER --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-black mb-1.5">
                            Supplier
                        </label>

                        <select
                            name="SPP"
                            required
                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-black focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                            <option value="" disabled selected>Pilih Supplier</option>
                            @foreach ($SPP as $spp)
                                <option value="{{ $spp->SPP }}" {{ old('SPP') == $spp->SPP ? 'selected' : '' }}>
                                    {{ $spp->DES }}
                                </option>
                            @endforeach
                        </select>
                        @error('SPP')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                {{-- ACTION BUTTONS --}}
                <div class="pt-2 flex items-center gap-4 w-full">

                    <button
                        type="button"
                        onclick="closeTambahModal()"
                        class="w-1/2 py-2.5 px-4 bg-white border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold hover:bg-gray-100 transition-colors cursor-pointer text-center"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="w-1/2 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb text-white rounded-xl text-sm font-semibold transition-colors cursor-pointer text-center"
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
        id="editItemModal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
    >

        {{-- BACKDROP CLICK TO CLOSE --}}
        <div
            class="absolute inset-0"
            onclick="closeEditModal()"
        ></div>

        {{-- MODAL CONTAINER (Dibuat max-w-2xl agar muat layout 2 kolom) --}}
        <div
            class="relative w-full max-w-2xl bg-secondary-bw border border-gray-200 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto"
        >

            {{-- JUDUL MODAL --}}
            <h2 class="text-xl font-bold mb-6 text-black">
                Edit Barang
            </h2>

            {{-- FORM EDIT --}}
            <form
                id="editItemForm"
                method="POST"
                class="space-y-6"
            >
                @csrf
                @method('PUT')

                {{-- GRID 2 KOLOM UNTUK FORM INPUT --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">

                    {{-- ITM (Kode Item) --}}
                    <div>
                        <label class="block text-xs font-medium text-black mb-1.5">
                            Kode Item
                        </label>
                        <input
                            id="editITM"
                            type="text"
                            name="ITM"
                            readonly
                            required
                            class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-600 cursor-not-allowed focus:outline-none font-mono"
                        >
                    </div>

                    {{-- DES (Nama Item) --}}
                    <div>
                        <label class="block text-xs font-medium text-black mb-1.5">
                            Nama Item
                        </label>
                        <input
                            id="editDES"
                            type="text"
                            name="DES"
                            required
                            placeholder="Masukkan nama item"
                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-black focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                    </div>

                    {{-- MRK (Merek) --}}
                    <div>
                        <label class="block text-xs font-medium text-black mb-1.5">
                            Merk
                        </label>
                        <select
                            id="editMRK"
                            name="MRK"
                            required
                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-black focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                            @foreach ($MRK as $mrk)
                                <option value="{{ $mrk->MRK }}">{{ $mrk->DES }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- HRG (Harga) --}}
                    <div>
                        <label class="block text-xs font-medium text-black mb-1.5">
                            Harga
                        </label>
                        <input
                            id="editHRG"
                            type="number"
                            name="HRG"
                            min="0"
                            step="any"
                            required
                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-black focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                    </div>

                    {{-- KTG (Kategori) --}}
                    <div>
                        <label class="block text-xs font-medium text-black mb-1.5">
                            Kategori
                        </label>
                        <select
                            id="editKTG"
                            name="KTG"
                            required
                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-black focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                            @foreach ($KTG as $ktg)
                                <option value="{{ $ktg->KTG }}">{{ $ktg->DES }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- STOK (Stok) --}}
                    <div>
                        <label class="block text-xs font-medium text-black mb-1.5">
                            Stok
                        </label>
                        <input
                            id="editSTOK"
                            type="number"
                            name="STOK"
                            min="0"
                            required
                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-black focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                    </div>

                    {{-- SPP (Supplier) --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-black mb-1.5">
                            Supplier
                        </label>
                        <select
                            id="editSPP"
                            name="SPP"
                            required
                            class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-black focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                            @foreach ($SPP as $spp)
                                <option value="{{ $spp->SPP }}">{{ $spp->DES }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- ACTION BUTTONS (FULL WIDTH) --}}
                <div class="pt-2 flex items-center gap-4 w-full">

                    <button
                        type="button"
                        onclick="closeEditModal()"
                        class="w-1/2 py-2.5 px-4 bg-white border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold hover:bg-gray-100 transition-colors cursor-pointer text-center"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="w-1/2 py-2.5 px-4 bg-[#1E295D] hover:bg-[#161F48] text-white rounded-xl text-sm font-semibold transition-colors cursor-pointer text-center"
                    >
                        Simpan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- ============================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ============================================================= --}}

<script>

    // ============================================================
    // ACTION MENU
    // ============================================================

    function toggleActionMenu(button) {

        const menu = button.parentElement.querySelector('.action-menu');

        document.querySelectorAll('.action-menu').forEach(function(item) {

            if (item !== menu) {
                item.classList.add('hidden');
            }

        });

        menu.classList.toggle('hidden');
    }


    // ============================================================
    // CLOSE ACTION MENU WHEN CLICK OUTSIDE
    // ============================================================

    document.addEventListener('click', function(event) {

        if (
            !event.target.closest('.action-menu') &&
            !event.target.closest('[onclick="toggleActionMenu(this)"]')
        ) {

            document.querySelectorAll('.action-menu').forEach(function(menu) {
                menu.classList.add('hidden');
            });

        }

    });


    // ============================================================
    // TAMBAH MODAL
    // ============================================================

    function openTambahModal() {

        const modal = document.getElementById('tambahItemModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

    }


    function closeTambahModal() {

        const modal = document.getElementById('tambahItemModal');

        modal.classList.remove('flex');
        modal.classList.add('hidden');

    }


    // ============================================================
    // EDIT MODAL
    // ============================================================

    function openEditModal(button) {

        const ITM = button.dataset.itm;
        const DES = button.dataset.des;
        const STOK = button.dataset.stok;
        const HRG = button.dataset.hrg;
        const KTG = button.dataset.ktg;
        const MRK = button.dataset.mrk;
        const SPP = button.dataset.spp;


        document.getElementById('editITM').value = ITM;
        document.getElementById('editDES').value = DES;
        document.getElementById('editSTOK').value = STOK;
        document.getElementById('editHRG').value = HRG;
        document.getElementById('editKTG').value = KTG;
        document.getElementById('editMRK').value = MRK;
        document.getElementById('editSPP').value = SPP;

        // Set URL action secara dinamis menggantikan placeholder ':id'
        const updateUrl = "{{ route('inventory.item.update', ':id') }}".replace(':id', encodeURIComponent(ITM));
        document.getElementById('editItemForm').action = updateUrl;

        // Tutup menu aksi
        document.querySelectorAll('.action-menu').forEach(function(menu) {
            menu.classList.add('hidden');
        });


        // Buka modal
        const modal = document.getElementById('editItemModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

    }


    function closeEditModal() {

        const modal = document.getElementById('editItemModal');

        modal.classList.remove('flex');
        modal.classList.add('hidden');

    }

    // Function Buka/Tutup Panel Filter
        function toggleFilterItem(event) {
        if (event) event.stopPropagation();
        const panel = document.getElementById('panelFilterItem');
        const arrow = document.getElementById('arrowItemSort');
        if (panel) {
            panel.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

        function tutupFilterItem() {
            const panel = document.getElementById('panelFilterItem');
            const arrow = document.getElementById('arrowItemSort');
            if (panel) panel.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }

        function pilihFilterItem(labelOption) {
            // Update label tombol
            document.getElementById('labelSelectedItemSort').innerText = 'Urutkan: ' + labelOption;

            // Toggle centang aktif
            const checkAsc = document.getElementById('checkItemAsc');
            const checkDesc = document.getElementById('checkItemDesc');

            if (labelOption === 'Nama (A-Z)') {
                if (checkAsc) checkAsc.classList.remove('hidden');
                if (checkDesc) checkDesc.classList.add('hidden');
            } else {
                if (checkAsc) checkAsc.classList.add('hidden');
                if (checkDesc) checkDesc.classList.remove('hidden');
            }

            // Panggil fungsi pengurutan data tabel Item Anda di sini
            if (typeof sortDataItem === 'function') {
                sortDataItem(labelOption);
            }

            tutupFilterItem();
        }

    // Klik di luar area dropdown untuk menutup
    document.addEventListener('click', function(event) {
        const container = document.getElementById('containerFilterItem');
        if (container && !container.contains(event.target)) {
            tutupFilterItem();
        }
    });

    // Menekan tombol Escape untuk menutup
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            tutupFilterItem();
        }
    });


</script>

@endsection