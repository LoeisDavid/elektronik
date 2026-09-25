@extends('layouts.app')

@section('title', 'Sales - Elektronik')
@section('header_title', 'Sales')

@section('content')

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-[#CECECE]/60 p-4 md:p-6 select-none">

    {{-- ========================================================= --}}
    {{-- TOOLBAR --}}
    {{-- ========================================================= --}}

    <div class="flex items-center justify-between gap-3 mb-4">

        {{-- SEARCH & FILTER --}}
        <div class="flex items-center gap-2">

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
                    placeholder="Cari sales"
                    class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-[#1A1F36] placeholder-gray-500 focus:outline-none focus:border-[#172554] focus:ring-1 focus:ring-[#172554] shadow-xs"
                >

            </div>


            {{-- FILTER --}}
            <button
                type="button"
                class="h-9.5 w-9.5 border border-gray-400 bg-white rounded-lg flex items-center justify-center shadow-xs cursor-pointer hover:bg-gray-100 transition-colors"
                title="Filter"
            >
                <svg
                    class="w-5 h-5 text-black fill-current"
                    viewBox="0 0 24 24"
                >
                    <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/>
                </svg>
            </button>

        </div>


        {{-- TAMBAH SALES --}}
        <button
            type="button"
            onclick="openTambahModal()"
            class="h-9.5 px-4 bg-[#172554] hover:bg-[#1e3a8a] active:bg-[#0f172a] text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-all shadow-xs cursor-pointer"
        >
            <span class="text-lg leading-none font-bold">+</span>
            <span>Tambah sales</span>
        </button>

    </div>


    {{-- ========================================================= --}}
    {{-- TABLE --}}
    {{-- ========================================================= --}}

    <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden flex flex-col">

        <div class="overflow-x-auto">

            <table class="w-full text-left border-collapse">

                {{-- HEADER --}}
                <thead>
                    <tr class="bg-[#142854] text-white text-xs font-semibold">

                        <th class="py-2.5 px-4 font-semibold">
                            SLS Sales
                        </th>

                        <th class="py-2.5 px-4 font-semibold">
                            DES Sales
                        </th>

                        <th class="py-2.5 px-4 font-semibold">
                            Nomor HP
                        </th>

                        <th class="py-2.5 px-4 font-semibold">
                            Alamat
                        </th>

                        <th class="py-2.5 px-4 font-semibold text-center w-20">
                            Aksi
                        </th>

                    </tr>
                </thead>


                {{-- BODY --}}
                <tbody class="divide-y divide-gray-200 text-sm text-[#1A1F36]">

                    @forelse ($sales as $sale)

                        <tr class="hover:bg-gray-50/80 transition-colors">

                            {{-- SLS --}}
                            <td class="py-3.5 px-4 font-medium">
                                {{ $sale->SLS }}
                            </td>


                            {{-- DES --}}
                            <td class="py-3.5 px-4">
                                {{ $sale->DES }}
                            </td>


                            {{-- HP --}}
                            <td class="py-3.5 px-4">
                                {{ $sale->HP }}
                            </td>


                            {{-- AL --}}
                            <td class="py-3.5 px-4">
                                {{ $sale->AL }}
                            </td>


                            {{-- AKSI --}}
                            <td class="py-3.5 px-4 text-center">

                                <div class="relative inline-block">

                                    {{-- BUTTON AKSI --}}
                                    <button
                                        type="button"
                                        onclick="toggleActionMenu(this)"
                                        class="h-8 w-8 border border-gray-400 bg-white hover:bg-gray-100 hover:border-[#172554] rounded-md inline-flex items-center justify-center transition-colors shadow-2xs cursor-pointer"
                                        title="Aksi Sales"
                                    >
                                        <svg
                                            class="w-4 h-4 text-[#172554]"
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
                                            onclick="openEditModal(
                                                this.dataset.SLS,
                                                this.dataset.des,
                                                this.dataset.hp,
                                                this.dataset.al
                                            )"
                                            data-SLS="{{ $sale->SLS }}"
                                            data-des="{{ $sale->DES }}"
                                            data-hp="{{ $sale->HP }}"
                                            data-al="{{ $sale->AL }}"
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


                                        {{-- HAPUS --}}
                                        <form
                                            method="POST"
                                            action="{{ url('/sales/' . $sale->SLS) }}"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                onclick="return confirm('Hapus sales ini?')"
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
                                Tidak ada sales.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ========================================================= --}}
        {{-- FOOTER + PAGINATION --}}
        {{-- ========================================================= --}}

        <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-[#142854]">

            {{-- INFO DATA --}}
            <div class="whitespace-nowrap">

                Menampilkan
                {{ $sales->firstItem() ?? 0 }}
                -
                {{ $sales->lastItem() ?? 0 }}
                dari
                {{ $sales->total() ?? 0 }}
                sales

            </div>


            @php
                $current = $sales->currentPage();
                $last = $sales->lastPage();
            @endphp


            {{-- PAGINATION --}}
            <div class="flex items-center gap-1">

                {{-- PREVIOUS --}}
                @if ($sales->onFirstPage())

                    <span class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#A8B1C1]">
                        ‹
                    </span>

                @else

                    <a
                        href="{{ $sales->previousPageUrl() }}"
                        class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100"
                    >
                        ‹
                    </a>

                @endif


                {{-- PAGE 1 --}}
                <a
                    href="{{ $sales->url(1) }}"
                    class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                    {{ $current == 1
                        ? 'bg-[#142854] text-white border-[#142854]'
                        : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                >
                    1
                </a>


                @if ($last > 3)

                    {{-- CURRENT DI AWAL --}}
                    @if ($current <= 2)

                        <a
                            href="{{ $sales->url(2) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                            {{ $current == 2
                                ? 'bg-[#142854] text-white border-[#142854]'
                                : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                        >
                            2
                        </a>

                        <a
                            href="{{ $sales->url(3) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                            {{ $current == 3
                                ? 'bg-[#142854] text-white border-[#142854]'
                                : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                        >
                            3
                        </a>


                    {{-- CURRENT DI AKHIR --}}
                    @elseif ($current >= $last - 1)

                        <span class="px-1 text-[#142854]">
                            ...
                        </span>

                        <a
                            href="{{ $sales->url($last - 2) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                            {{ $current == $last - 2
                                ? 'bg-[#142854] text-white border-[#142854]'
                                : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                        >
                            {{ $last - 2 }}
                        </a>

                        <a
                            href="{{ $sales->url($last - 1) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                            {{ $current == $last - 1
                                ? 'bg-[#142854] text-white border-[#142854]'
                                : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                        >
                            {{ $last - 1 }}
                        </a>


                    {{-- CURRENT DI TENGAH --}}
                    @else

                        <span class="px-1 text-[#142854]">
                            ...
                        </span>

                        <a
                            href="{{ $sales->url($current - 1) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100"
                        >
                            {{ $current - 1 }}
                        </a>

                        <a
                            href="{{ $sales->url($current) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-[#142854] text-white border-[#142854]"
                        >
                            {{ $current }}
                        </a>

                        <a
                            href="{{ $sales->url($current + 1) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100"
                        >
                            {{ $current + 1 }}
                        </a>

                    @endif


                    {{-- DOTS --}}
                    @if ($current <= $last - 2)

                        <span class="px-1 text-[#142854]">
                            ...
                        </span>

                    @endif


                    {{-- LAST --}}
                    <a
                        href="{{ $sales->url($last) }}"
                        class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                        {{ $current == $last
                            ? 'bg-[#142854] text-white border-[#142854]'
                            : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                    >
                        {{ $last }}
                    </a>


                @elseif ($last == 3)

                    <a
                        href="{{ $sales->url(2) }}"
                        class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                        {{ $current == 2
                            ? 'bg-[#142854] text-white border-[#142854]'
                            : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                    >
                        2
                    </a>

                    <a
                        href="{{ $sales->url(3) }}"
                        class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                        {{ $current == 3
                            ? 'bg-[#142854] text-white border-[#142854]'
                            : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                    >
                        3
                    </a>


                @elseif ($last == 2)

                    <a
                        href="{{ $sales->url(2) }}"
                        class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                        {{ $current == 2
                            ? 'bg-[#142854] text-white border-[#142854]'
                            : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100' }}"
                    >
                        2
                    </a>

                @endif


                {{-- NEXT --}}
                @if ($sales->hasMorePages())

                    <a
                        href="{{ $sales->nextPageUrl() }}"
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

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- MODAL TAMBAH SALES --}}
    {{-- ========================================================= --}}

    <div
        id="tambahSalesModal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
    >

        <div
            class="absolute inset-0"
            onclick="closeTambahModal()"
        ></div>


        <div
            class="relative w-full max-w-sm bg-[#F8FAFC] border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-[#1A1F36]"
        >

            <h2 class="text-2xl font-bold text-[#1A1F36] tracking-tight mb-5">
                Tambah Sales
            </h2>


            <form
                method="POST"
                action="{{ url('/sales') }}"
                class="space-y-4"
            >

                @csrf


                {{-- SLS --}}
                <div>

                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">
                        SLS Sales
                    </label>

                    <input
                        type="text"
                        name="SLS"
                        placeholder="Contoh: SLS004"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#172554] focus:border-[#172554] transition-all"
                    >

                </div>


                {{-- DES --}}
                <div>

                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">
                        DES Sales
                    </label>

                    <input
                        type="text"
                        name="DES"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#172554] focus:border-[#172554] transition-all"
                    >

                </div>


                {{-- HP --}}
                <div>

                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">
                        Nomor HP
                    </label>

                    <input
                        type="text"
                        name="HP"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#172554] focus:border-[#172554] transition-all"
                    >

                </div>


                {{-- AL --}}
                <div>

                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">
                        Alamat
                    </label>

                    <input
                        type="text"
                        name="AL"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#172554] focus:border-[#172554] transition-all"
                    >

                </div>


                {{-- BUTTON --}}
                <div class="pt-4 flex items-center gap-3">

                    <button
                        type="button"
                        onclick="closeTambahModal()"
                        class="flex-1 py-2.5 px-4 bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl text-sm font-semibold text-[#1A1F36] hover:bg-gray-100 transition-colors cursor-pointer"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="flex-1 py-2.5 px-4 bg-[#172554] hover:bg-[#1e3a8a] active:bg-[#0f172a] text-white rounded-xl text-sm font-semibold transition-colors cursor-pointer shadow-xs"
                    >
                        Simpan
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- MODAL EDIT SALES --}}
    {{-- ========================================================= --}}

    <div
        id="editSalesModal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
    >

        <div
            class="absolute inset-0"
            onclick="closeEditModal()"
        ></div>


        <div
            class="relative w-full max-w-sm bg-[#F8FAFC] border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-[#1A1F36]"
        >

            <h2 class="text-2xl font-bold text-[#1A1F36] tracking-tight mb-5">
                Edit Sales
            </h2>


            <form
                id="editSalesForm"
                method="POST"
                class="space-y-4"
            >

                @csrf
                @method('PUT')


                {{-- SLS --}}
                <div>

                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">
                        SLS Sales
                    </label>

                    <input
                        id="editSLS"
                        type="text"
                        name="SLS"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#172554] focus:border-[#172554] transition-all"
                    >

                </div>


                {{-- DES --}}
                <div>

                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">
                        DES Sales
                    </label>

                    <input
                        id="editDES"
                        type="text"
                        name="DES"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#172554] focus:border-[#172554] transition-all"
                    >

                </div>


                {{-- HP --}}
                <div>

                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">
                        Nomor HP
                    </label>

                    <input
                        id="editHP"
                        type="text"
                        name="HP"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#172554] focus:border-[#172554] transition-all"
                    >

                </div>


                {{-- AL --}}
                <div>

                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">
                        Alamat
                    </label>

                    <input
                        id="editAL"
                        type="text"
                        name="AL"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#172554] focus:border-[#172554] transition-all"
                    >

                </div>


                {{-- BUTTON --}}
                <div class="pt-4 flex items-center gap-3">

                    <button
                        type="button"
                        onclick="closeEditModal()"
                        class="flex-1 py-2.5 px-4 bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl text-sm font-semibold text-[#1A1F36] hover:bg-gray-100 transition-colors cursor-pointer"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="flex-1 py-2.5 px-4 bg-[#172554] hover:bg-[#1e3a8a] active:bg-[#0f172a] text-white rounded-xl text-sm font-semibold transition-colors cursor-pointer shadow-xs"
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

    /*
    |--------------------------------------------------------------------------
    | ACTION MENU
    |--------------------------------------------------------------------------
    */

    function toggleActionMenu(button) {

        const currentMenu = button.parentElement.querySelector('.action-menu');

        document.querySelectorAll('.action-menu').forEach(function(menu) {

            if (menu !== currentMenu) {
                menu.classList.add('hidden');
            }

        });

        currentMenu.classList.toggle('hidden');
    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE ACTION MENU WHEN CLICK OUTSIDE
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function(event) {

        if (
            !event.target.closest('.action-menu') &&
            !event.target.closest('button[onclick="toggleActionMenu(this)"]')
        ) {

            document.querySelectorAll('.action-menu').forEach(function(menu) {
                menu.classList.add('hidden');
            });

        }

    });


    /*
    |--------------------------------------------------------------------------
    | TAMBAH MODAL
    |--------------------------------------------------------------------------
    */

    function openTambahModal() {

        const modal = document.getElementById('tambahSalesModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

    }


    function closeTambahModal() {

        const modal = document.getElementById('tambahSalesModal');

        modal.classList.remove('flex');
        modal.classList.add('hidden');

    }


    /*
    |--------------------------------------------------------------------------
    | EDIT MODAL
    |--------------------------------------------------------------------------
    */

    function openEditModal(SLS, DES, HP, AL) {

        document.getElementById('editSLS').value = SLS;
        document.getElementById('editDES').value = DES;
        document.getElementById('editHP').value = HP;
        document.getElementById('editAL').value = AL;


        document.getElementById('editSalesForm').action =
            "{{ url('/sales') }}/" + encodeURIComponent(SLS);


        document.querySelectorAll('.action-menu').forEach(function(menu) {
            menu.classList.add('hidden');
        });


        const modal = document.getElementById('editSalesModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

    }


    function closeEditModal() {

        const modal = document.getElementById('editSalesModal');

        modal.classList.remove('flex');
        modal.classList.add('hidden');

    }

</script>

@endsection