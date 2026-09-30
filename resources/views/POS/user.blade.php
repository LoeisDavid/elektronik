@extends('layouts.app')

@section('title', 'User - Elektronik')
@section('header_title', 'User')

@section('content')

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 select-none">

    {{-- ========================================================= --}}
    {{-- TOOLBAR --}}
    {{-- ========================================================= --}}

    <div class="flex items-center justify-between gap-3 mb-4">

        {{-- SEARCH --}}
        <div class="flex items-center gap-2">

            <div class="relative w-64 md:w-80">
                <form action="{{ route('POS.user') }}" method="GET">
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
                        placeholder="Cari User"
                        class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db"
                        value="{{ request('keyword') }}"
                        name="keyword"
                    >
                </form>
            </div>


            {{-- DROPDOWN FILTER GUDANG TIMBUL --}}
                <div class="relative inline-block text-left" id="containerFilterUser">
                    
                    {{-- Tombol Trigger Dropdown --}}
                    <button
                        type="button"
                        onclick="toggleFilterUser(event)"
                        class="h-9.5 px-3 bg-white border border-gray-800 rounded-xl flex items-center justify-between gap-2.5 text-sm font-medium text-black hover:bg-gray-50 transition-colors cursor-pointer shadow-xs min-w-42.5"
                    >
                        <span id="labelSelectedUserSort">Urutkan: Nama (A-Z)</span>
                        <svg id="arrowUserSort" class="w-4 h-4 text-gray-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    {{-- Panel Pop-up Timbul --}}
                    <div
                        id="panelFilterUser"
                        class="hidden absolute left-0 mt-2 w-48 bg-secondary-bw rounded-2xl p-2 shadow-2xl border border-gray-300 z-50 space-y-1.5"
                    >
                        <!-- Opsi 1: Nama (A-Z) -->
                        <button
                            type="button"
                            onclick="pilihFilterUser('Nama (A-Z)')"
                            class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer shadow-xs"
                        >
                            <span>Nama (A-Z)</span>
                            <svg id="checkUserAsc" class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </button>

                        <!-- Opsi 2: Nama (Z-A) -->
                        <button
                            type="button"
                            onclick="pilihFilterUser('Nama (Z-A)')"
                            class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer shadow-xs"
                        >
                            <span>Nama (Z-A)</span>
                            <svg id="checkUserDesc" class="w-4 h-4 text-gray-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </button>
                    </div>

                </div>

        </div>


        {{-- TAMBAH User --}}
        <button
            type="button"
            onclick="openTambahModal()"
            class="h-9.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-[#0f172a] text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-colors cursor-pointer"
        >
            <span class="text-lg leading-none font-bold">+</span>
            <span>Tambah User</span>
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
                            Username
                        </th>

                        <th class="py-2.5 px-4 ">
                            Alamat
                        </th>

                        <th class="py-2.5 px-4 ">
                            Nomor Handphone
                        </th>

                        <th class="py-2.5 px-4 ">
                            Nama
                        </th>

                        <th class="py-2.5 px-4 ">
                            Role
                        </th>

                        <th class="py-2.5 px-4 text-center w-20 rounded-tr-lg">
                            Aksi
                        </th>

                    </tr>

                </thead>


                {{-- BODY --}}
                <tbody class="divide-y divide-gray-200 text-sm text-black">

                    @forelse ($data as $user)

                        <tr class="hover:bg-gray-50/80 transition-colors">

                            {{-- User --}}
                            <td class="py-3.5 px-4 font-medium">
                                {{ $user->USERNAME }}
                            </td>


                            {{-- Alamat --}}
                            <td class="py-3.5 px-4">
                                {{ $user->AL }}
                            </td>


                            {{-- Hp --}}
                            <td class="py-3.5 px-4">
                                {{ $user->HP }}
                            </td>

                            {{-- DES --}}
                            <td class="py-3.5 px-4">
                                {{ $user->DES }}
                            </td>

                            {{-- Role --}}
                            <td class="py-3.5 px-4">
                                {{ $user->ROLE }}
                            </td>


                            {{-- AKSI --}}
                            <td class="py-3.5 px-4 text-center">

                                <div class="relative inline-block">

                                    {{-- BUTTON --}}
                                    <button
                                        type="button"
                                        onclick="toggleActionMenu(this)"
                                        class="h-8 w-8 border border-gray-400 bg-white hover:bg-gray-100 hover:border-primary-db rounded-md inline-flex items-center justify-center transition-colors cursor-pointer"
                                        title="Aksi user"
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
                                            data-usr="{{ $user->USERNAME }}"
                                            data-al="{{ $user->AL }}"
                                            data-hp="{{ $user->HP }}"
                                            data-des="{{ $user->DES }}"
                                            data-role="{{ $user->ROLE }}"
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
                                            action="{{ route('POS.user.destroy', ['id' => $user->USERNAME]) }}"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                onclick="return confirm('Hapus user ini?')"
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
                                Tidak ada user.
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
                user

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
        id="tambahUserModal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
    >

        {{-- Backdrop Click Area --}}
        <div
            class="absolute inset-0"
            onclick="closeTambahModal()"
        ></div>

        {{-- Modal Card Container --}}
        <div
            class="relative w-full max-w-lg bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto"
        >

            <h2 class="text-2xl font-bold mb-5 tracking-tight">
                Tambah User
            </h2>

            <form
                method="POST"
                action="{{ route('POS.user.store') }}"
                class="space-y-5"
            >
                @csrf

                {{-- GROUP 1: Informasi Akun (Akses) --}}
                <div class="space-y-3 pt-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        {{-- USERNAME --}}
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Username
                            </label>
                            <input
                                type="text"
                                name="USERNAME"
                                placeholder="Contoh: CST004"
                                required
                                class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                            >
                        </div>

                        {{-- PASSWORD --}}
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Password
                            </label>
                            <input
                                type="password"
                                name="PASS"
                                placeholder="Masukkan password"
                                required
                                class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                            >
                        </div>
                    </div>
                </div>

                {{-- GROUP 2: Data Diri & Role --}}
                <div class="space-y-3 pt-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        {{-- NAMA LENGKAP (DES) --}}
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Nama Lengkap
                            </label>
                            <input
                                type="text"
                                name="DES"
                                placeholder="Masukkan nama"
                                required
                                class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                            >
                        </div>

                        {{-- NOMOR HANDPHONE (HP) --}}
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Nomor Handphone
                            </label>
                            <input
                                type="text"
                                name="HP"
                                placeholder="08123456789"
                                required
                                class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                            >
                        </div>
                    </div>

                    {{-- ROLE --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1">
                            Role
                        </label>
                        <input
                            type="text"
                            name="ROLE"
                            placeholder=""
                            required
                            class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                    </div>
                </div>

                {{-- GROUP 3: Alamat --}}
                <div class="space-y-3 pt-1">
                    {{-- ALAMAT --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1">
                            Alamat
                        </label>
                        <textarea
                            name="AL"
                            rows="2"
                            placeholder="Contoh: Jl. Jend. Sudirman No. 1, Jakarta Selatan"
                            class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db resize-none"
                        ></textarea>
                    </div>
                </div>

                {{-- TOMBOL AKSI --}}
                <div class="pt-4 flex items-center gap-3">
                    <button
                        type="button"
                        onclick="closeTambahModal()"
                        class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold hover:bg-gray-100 cursor-pointer transition-colors"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="flex-1 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb text-white rounded-xl text-sm font-semibold cursor-pointer transition-colors"
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
        id="editUserModal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
    >

        {{-- Backdrop Click Area --}}
        <div
            class="absolute inset-0"
            onclick="closeEditModal()"
        ></div>

        {{-- Modal Card Container --}}
        <div
            class="relative w-full max-w-lg bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto"
        >

            <h2 class="text-2xl font-bold mb-5 tracking-tight">
                Edit User
            </h2>

            <form
                id="editUserForm"
                method="POST"
                class="space-y-5"
            >
                @csrf
                @method('PUT')

                {{-- GROUP 1: Informasi Akun (Akses) --}}
                <div class="space-y-3 pt-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        {{-- USERNAME --}}
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Username
                            </label>
                            <input
                                id="editUSERNAME"
                                type="text"
                                name="USERNAME"
                                placeholder="Contoh: David Loes"
                                required
                                class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                            >
                        </div>

                        {{-- PASSWORD (Opsional saat Edit) --}}
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Password <span class="text-xs font-normal text-gray-500">(Kosongi jika tak diubah)</span>
                            </label>
                            <input
                                id="editPASS"
                                type="password"
                                name="PASS"
                                placeholder="Masukkan password"
                                class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                            >
                        </div>
                    </div>
                </div>

                {{-- GROUP 2: Data Diri & Role --}}
                <div class="space-y-3 pt-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        {{-- NAMA LENGKAP (DES) --}}
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Nama Lengkap
                            </label>
                            <input
                                id="editDES"
                                type="text"
                                name="DES"
                                placeholder="Masukkan nama"
                                required
                                class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                            >
                        </div>

                        {{-- NOMOR HANDPHONE (HP) --}}
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Nomor Handphone
                            </label>
                            <input
                                id="editHP"
                                type="text"
                                name="HP"
                                placeholder="08123456789"
                                required
                                class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                            >
                        </div>
                    </div>

                    {{-- ROLE --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1">
                            Role
                        </label>
                        <input
                            id="editROLE"
                            type="text"
                            name="ROLE"
                            placeholder=""
                            required
                            class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                    </div>
                </div>

                {{-- GROUP 3: Alamat --}}
                <div class="space-y-3 pt-1">
                    {{-- ALAMAT (AL) --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1">
                            Alamat
                        </label>
                        <textarea
                            id="editAL"
                            name="AL"
                            rows="2"
                            placeholder="Masukkan alamat lengkap..."
                            class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db resize-none"
                        ></textarea>
                    </div>
                </div>

                {{-- TOMBOL AKSI --}}
                <div class="pt-4 flex items-center gap-3">
                    <button
                        type="button"
                        onclick="closeEditModal()"
                        class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold hover:bg-gray-100 cursor-pointer transition-colors"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="flex-1 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb text-white rounded-xl text-sm font-semibold cursor-pointer transition-colors"
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

        const modal = document.getElementById('tambahUserModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

    }


    function closeTambahModal() {

        const modal = document.getElementById('tambahUserModal');

        modal.classList.remove('flex');
        modal.classList.add('hidden');

    }


    // ============================================================
    // EDIT MODAL
    // ============================================================

    function openEditModal(button) {

        const USERNAME = button.dataset.usr || '';
        const DES = button.dataset.des || '';
        const HP = button.dataset.hp || button.dataset.tlp || '';
        const ROLE = button.dataset.role || '';
        const AL = button.dataset.al || '';


        document.getElementById('editUSERNAME').value = USERNAME;
        document.getElementById('editDES').value = DES;
        document.getElementById('editHP').value = HP;
        document.getElementById('editROLE').value = ROLE;
        document.getElementById('editAL').value = AL;

        // Set URL action secara dinamis menggantikan placeholder ':id'
        const updateUrl = "{{ route('POS.user.update', ':id') }}".replace(':id', encodeURIComponent(USERNAME));
        document.getElementById('editUserForm').action = updateUrl;

        // Tutup menu aksi
        document.querySelectorAll('.action-menu').forEach(function(menu) {
            menu.classList.add('hidden');
        });


        // Buka modal
        const modal = document.getElementById('editUserModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

    }


    function closeEditModal() {

        const modal = document.getElementById('editUserModal');

        modal.classList.remove('flex');
        modal.classList.add('hidden');

    }

    // Function Buka/Tutup Panel Filter
        function toggleFilterUser(event) {
        if (event) event.stopPropagation();
        const panel = document.getElementById('panelFilterUser');
        const arrow = document.getElementById('arrowGudangSort');
        if (panel) {
            panel.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

        function tutupFilterUser() {
            const panel = document.getElementById('panelFilterUser');
            const arrow = document.getElementById('arrowGudangSort');
            if (panel) panel.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }

        function pilihFilterUser(labelOption) {
            // Update label tombol
            document.getElementById('labelSelectedUserSort').innerText = 'Urutkan: ' + labelOption;

            // Toggle centang aktif
            const checkAsc = document.getElementById('checkUserAsc');
            const checkDesc = document.getElementById('checkUserDesc');

            if (labelOption === 'Nama (A-Z)') {
                if (checkAsc) checkAsc.classList.remove('hidden');
                if (checkDesc) checkDesc.classList.add('hidden');
            } else {
                if (checkAsc) checkAsc.classList.add('hidden');
                if (checkDesc) checkDesc.classList.remove('hidden');
            }

            // Panggil fungsi pengurutan data tabel gudang Anda di sini
            if (typeof sortDataUser === 'function') {
                sortDataUser(labelOption);
            }

            tutupFilterUser();
        }

    // Klik di luar area dropdown untuk menutup
    document.addEventListener('click', function(event) {
        const container = document.getElementById('containerFilterGudang');
        if (container && !container.contains(event.target)) {
            tutupFilterUser();
        }
    });

    // Menekan tombol Escape untuk menutup
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            tutupFilterUser();
        }
    });

</script>

@endsection