@extends('layouts.app')

@section('title', 'User - Elektronik')
@section('header_title', 'User')

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
                <form action="{{ route('POS.user') }}" method="GET">
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
                        placeholder="Cari User"
                        class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:border-primary-db focus:bg-primary-db/5 focus:ring-1 focus:ring-primary-db"
                        value="{{ request('keyword') }}"
                        name="keyword"
                    >
                </form>
            </div>


            {{-- DROPDOWN SORT --}}
                <div class="relative inline-block text-left" id="containerFilterUser">

                    {{-- Tombol Trigger Dropdown --}}
                    <button
                        type="button"
                        onclick="toggleFilterUser(event)"
                        class="h-9.5 px-3 bg-white border border-gray-800 rounded-xl flex items-center justify-between gap-2.5 text-sm font-medium text-black hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 cursor-pointer shadow-xs min-w-42.5"
                    >
                        <span id="labelSelectedUserSort">Urutkan: {{ $sortLabel }}</span>
                        <svg id="arrowUserSort" class="w-4 h-4 text-gray-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    {{-- Panel Pop-up Timbul --}}
                    <div
                        id="panelFilterUser"
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


        {{-- TAMBAH User --}}
        <button
            type="button"
            onclick="openTambahModal()"
            class="h-9.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
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
                            Nama
                        </th>

                        <th class="py-2.5 px-4 ">
                            Alamat
                        </th>

                        <th class="py-2.5 px-4 ">
                            Nomor Handphone
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

                            {{-- DES --}}
                            <td class="py-3.5 px-4">
                                {{ $user->DES }}
                            </td>

                            {{-- Alamat --}}
                            <td class="py-3.5 px-4">
                                {{ $user->AL }}
                            </td>


                            {{-- Hp --}}
                            <td class="py-3.5 px-4">
                                {{ $user->HP }}
                            </td>

                            {{-- Role --}}
                            <td class="py-3.5 px-4">
                                @if($user->ROLE == 4)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">Manager</span>
                                @elseif($user->ROLE == 1 || $user->ROLE == 5 || strtolower($user->USERNAME) === 'adminhunt')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">Admin</span>
                                @elseif($user->ROLE == 3)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">Storage</span>
                                @elseif($user->ROLE == 2)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">Kasir</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">{{ $user->ROLE }}</span>
                                @endif
                            </td>


                            {{-- AKSI --}}
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $canManage = false;
                                    $authU = auth()->user();
                                    if ($authU?->isManager()) {
                                        // Manager bisa edit siapa saja kecuali Manager lain
                                        $canManage = ($user->ROLE != 4 || $user->USERNAME === $authU->USERNAME);
                                    } elseif ($authU?->isAdmin()) {
                                        // Admin hanya bisa edit/hapus Kasir dan Storage (tidak bisa Admin/Manager)
                                        $canManage = ($user->ROLE != 4 && $user->ROLE != 1);
                                    }
                                @endphp

                                @if($canManage)
                                <div class="relative inline-block">

                                    {{-- BUTTON --}}
                                    <button
                                        type="button"
                                        onclick="toggleActionMenu(this)"
                                        class="h-8 w-8 border border-gray-400 bg-white hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 rounded-md inline-flex items-center justify-center transition-all duration-200 cursor-pointer"
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
                                            data-username="{{ $user->USERNAME }}"
                                            data-al="{{ $user->AL }}"
                                            data-hp="{{ $user->HP }}"
                                            data-des="{{ $user->DES }}"
                                            data-role="{{ $user->ROLE }}"
                                            data-action="{{ route('POS.user.update', ['id' => $user->USERNAME]) }}"
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


                                        @if(strtolower($user->USERNAME) !== 'adminhunt' && $user->USERNAME !== $authU?->USERNAME && ($authU?->isManager() ? $user->ROLE != 4 : ($user->ROLE != 4 && $user->ROLE != 1)))
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
                                        @endif

                                    </div>

                                </div>
                                @else
                                    <span class="text-xs text-gray-400 select-none">-</span>
                                @endif
                            </td>

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


        {{-- FOOTER / PAGINATION --}}
        <x-pagination :paginator="$data" label="user" />


    </div>


    {{-- MODAL TAMBAH & EDIT USER --}}
    @include('POS.modals.user-modal')

</div>

@endsection

@section('js')
    @vite(['resources/js/pages/pos/user.js'])
@endsection
