@extends('layouts.app')

@section('title', 'Inventory - Supplier')
@section('header_title', 'Supplier')

@section('content')
<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 select-none">

    {{-- ========================================================= --}}
    {{-- TOOLBAR (SEARCH, SORT DROPDOWN & TAMBAH SUPPLIER) --}}
    {{-- ========================================================= --}}
    <div class="flex items-center justify-between gap-3 mb-4">

        {{-- SEARCH & SORT CONTAINER --}}
        <div class="flex items-center gap-3">

            {{-- INPUT SEARCH --}}
            <div class="relative w-64 md:w-80">
                <form action="{{ route('inventory.supplier') }}" method="GET">
                    <input
                        type="hidden"
                        name="sort"
                        value="{{ request('sort', 'asc') }}"
                    >
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
                        placeholder="Cari produk"
                        value="{{ request('keyword') }}"
                        class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db"
                    >
                </form>
            </div>

            {{-- DROPDOWN FILTER SUPPLIER TIMBUL --}}
            <div class="relative inline-block text-left" id="containerFilterSupplier">
                
                {{-- Tombol Trigger Dropdown --}}
                <button
                    type="button"
                    onclick="toggleFilterSupplier(event)"
                    class="h-9.5 px-3.5 bg-white border border-gray-800 rounded-xl flex items-center justify-between gap-2.5 text-sm font-medium text-black hover:bg-gray-50 transition-colors cursor-pointer shadow-xs min-w-45"
                >
                    <span id="labelSelectedSupplierSort">Urutkan: {{ request('sort') === 'desc' ? 'Nama (Z-A)' : 'Nama (A-Z)' }}</span>
                    <svg id="arrowSupplierSort" class="w-4 h-4 text-gray-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                {{-- Panel Pop-up Timbul --}}
                <div
                    id="panelFilterSupplier"
                    class="hidden absolute left-0 mt-2 w-48 bg-secondary-bw rounded-2xl p-2 shadow-2xl border border-gray-300 z-50 space-y-1.5"
                >
                    <!-- Opsi 1: Nama (A-Z) -->
                    <button
                        type="button"
                        onclick="pilihFilterSupplier('asc')"
                        class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer shadow-xs"
                    >
                        <span>Nama (A-Z)</span>
                        <svg
                            id="checkSupplierAsc"
                            class="w-4 h-4 text-black {{ request('sort', 'asc') === 'asc' ? '' : 'hidden' }}"
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

                    <!-- Opsi 2: Nama (Z-A) -->
                    <button
                        type="button"
                        onclick="pilihFilterSupplier('desc')"
                        class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer shadow-xs"
                    >
                        <span>Nama (Z-A)</span>
                        <svg
                            id="checkSupplierDesc"
                            class="w-4 h-4 text-black {{ request('sort') === 'desc' ? '' : 'hidden' }}"
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
                </div>

            </div>

        </div>

        {{-- BUTTON TAMBAH SUPPLIER --}}
        <button
            type="button"
            onclick="openTambahSupplierModal()"
            class="h-9.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-[#0f172a] text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-colors cursor-pointer"
        >
            <span class="text-lg leading-none font-bold">+</span>
            <span>Tambah supplier</span>
        </button>

    </div>

    {{-- ========================================================= --}}
    {{-- TABLE SUPPLIER --}}
    {{-- ========================================================= --}}

    <div class="bg-white rounded-lg shadow-sm border border-gray-300 flex flex-col">

        {{-- WRAPPER TABEL: Gunakan 'overflow-visible' agar pop-up tidak terpotong --}}
        <div class="overflow-visible">

            <table class="w-full text-left border-collapse">

                {{-- HEADER --}}
                <thead>
                    <tr class="bg-primary-db text-white text-xs font-semibold rounded-t-lg">
                        <th class="py-2.5 px-4 font-semibold text-center rounded-tl-lg">
                            Kode Supplier
                        </th>
                        <th class="py-2.5 px-4 font-semibold text-center">
                            Nama Supplier
                        </th>
                        <th class="py-2.5 px-4 font-semibold text-center">
                            Nomor Telepon
                        </th>
                        <th class="py-2.5 px-4 font-semibold text-center w-20 rounded-tr-lg">
                            Aksi
                        </th>
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody class="divide-y divide-gray-200 text-sm text-black" id="supplierTableBody">

                    @forelse ($data as $supplier)
                        <tr class="hover:bg-gray-50/80 transition-colors">

                            {{-- Kode Supplier --}}
                            <td class="py-3.5 px-4 font-medium text-center">
                                {{ $supplier->SPP }}
                            </td>

                            {{-- Nama Supplier --}}
                            <td class="py-3.5 px-4 text-center">
                                {{ $supplier->DES }}
                            </td>

                            {{-- Nomor Telepon --}}
                            <td class="py-3.5 px-4 text-center font-mono">
                                {{ $supplier->TLP }}
                            </td>

                            {{-- AKSI --}}
                            <td class="py-3.5 px-4 text-center overflow-visible">

                                <div class="relative inline-block">

                                    {{-- BUTTON TRIGGER --}}
                                    <button
                                        type="button"
                                        onclick="toggleActionMenu(this)"
                                        class="h-8 w-8 border border-gray-400 bg-white hover:bg-gray-100 hover:border-primary-db rounded-md inline-flex items-center justify-center transition-colors cursor-pointer"
                                        title="Aksi Supplier"
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

                                    {{-- ACTION MENU (Menggunakan z-[999] agar berada di paling depan) --}}
                                    <div
                                        class="action-menu absolute right-0 top-full mt-1 w-28 bg-white border border-gray-300 rounded-lg shadow-2xl py-1 z-[999] text-left hidden"
                                    >

                                        {{-- EDIT --}}
                                        <button
                                            type="button"
                                            data-spp="{{ $supplier->SPP }}"
                                            data-des="{{ $supplier->DES }}"
                                            data-tlp="{{ $supplier->TLP }}"
                                            onclick="openEditSupplierModal(this)"
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
                                            action="{{ route('inventory.supplier.destroy', ['spp' => $supplier->SPP]) }}"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                onclick="return confirm('Hapus supplier ini?')"
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
                            <td colspan="4" class="py-8 text-center text-gray-500 text-sm">
                                Tidak ada data supplier.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- FOOTER / PAGINATION --}}
        <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-[#142854] rounded-b-lg">

            {{-- INFO --}}
            <div class="whitespace-nowrap">
                Menampilkan
                {{ $data->firstItem() ?? 0 }}
                -
                {{ $data->lastItem() ?? 0 }}
                dari
                {{ $data->total() ?? 0 }}
                supplier
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

                        <span class="px-1">...</span>

                        <a
                            href="{{ $data->url($last) }}"
                            class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100"
                        >
                            {{ $last }}
                        </a>
                    @elseif ($current >= $last - 2)
                        <span class="px-1">...</span>

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
                        <span class="px-1">...</span>

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

                        <span class="px-1">...</span>

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
    {{-- MODAL TAMBAH SUPPLIER --}}
    {{-- ========================================================= --}}
    <div
        id="tambahSupplierModal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
    >
        <div class="absolute inset-0" onclick="closeTambahSupplierModal()"></div>

        <div class="relative w-full max-w-sm bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto">
            <h2 class="text-2xl font-bold mb-5">Tambah Supplier</h2>

            <form method="POST" action="{{ route('inventory.supplier.store') }}" class="space-y-4">
                @csrf

                {{-- Kode Supplier --}}
                <div>
                    <label class="block text-sm font-semibold mb-1">Kode Supplier</label>
                    <input
                        type="text"
                        name="SPP"
                        placeholder="Contoh: SPP001"
                        required
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    >
                </div>

                {{-- Nama Supplier --}}
                <div>
                    <label class="block text-sm font-semibold mb-1">Nama Supplier</label>
                    <input
                        type="text"
                        name="DES"
                        placeholder="Contoh: PT. Jaya Elektronik"
                        required
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    >
                </div>

                {{-- Nomor Telepon --}}
                <div>
                    <label class="block text-sm font-semibold mb-1">Nomor Telepon</label>
                    <input
                        type="text"
                        name="TLP"
                        placeholder="Contoh: 081234567890"
                        required
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    >
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 flex items-center gap-3">
                    <button
                        type="button"
                        onclick="closeTambahSupplierModal()"
                        class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold hover:bg-gray-100 cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="flex-1 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb text-white rounded-xl text-sm font-semibold cursor-pointer"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL EDIT SUPPLIER --}}
    {{-- ========================================================= --}}
    <div
        id="editSupplierModal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
    >
        <div class="absolute inset-0" onclick="closeEditSupplierModal()"></div>

        <div class="relative w-full max-w-sm bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto">
            <h2 class="text-2xl font-bold mb-5">Edit Supplier</h2>

            <form id="editSupplierForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Kode Supplier --}}
                <div>
                    <label class="block text-sm font-semibold mb-1">Kode Supplier</label>
                    <input
                        id="editKodeSupplier"
                        type="text"
                        name="SPP"
                        readonly
                        class="w-full bg-gray-100 border border-black/80 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 cursor-not-allowed focus:outline-none"
                    >
                </div>

                {{-- Nama Supplier --}}
                <div>
                    <label class="block text-sm font-semibold mb-1">Nama Supplier</label>
                    <input
                        id="editNamaSupplier"
                        type="text"
                        name="DES"
                        required
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    >
                </div>

                {{-- Nomor Telepon --}}
                <div>
                    <label class="block text-sm font-semibold mb-1">Nomor Telepon</label>
                    <input
                        id="editTelpSupplier"
                        type="text"
                        name="TLP"
                        required
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    >
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 flex items-center gap-3">
                    <button
                        type="button"
                        onclick="closeEditSupplierModal()"
                        class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold hover:bg-gray-100 transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="flex-1 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb text-white rounded-xl text-sm font-semibold transition-colors cursor-pointer"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>

    function toggleActionMenu(button) {

        const menu = button.parentElement.querySelector('.action-menu');

        document.querySelectorAll('.action-menu').forEach(function(item) {

            if (item !== menu) {
                item.classList.add('hidden');
            }

        });

        menu.classList.toggle('hidden');
    }

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

    // Toggle Pop-up Dropdown Filter
    function toggleFilterSupplier(event) {
        event.stopPropagation();
        const panel = document.getElementById('panelFilterSupplier');
        const arrow = document.getElementById('arrowSupplierSort');
        panel.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }

    // Pilih Opsi Filter & Update Checkmark
    function pilihFilterSupplier(pilihan) {

        const tbody = document.getElementById('supplierTableBody');

        // Ambil semua baris supplier
        const rows = Array.from(
            tbody.querySelectorAll('tr')
        );

        // Jangan lakukan sorting kalau tidak ada data
        if (rows.length === 0) {
            return;
        }

        rows.sort(function (a, b) {

            // Kolom Nama Supplier = kolom ke-2
            const namaA = a.children[1].textContent
                .trim()
                .toLowerCase();

            const namaB = b.children[1].textContent
                .trim()
                .toLowerCase();

            if (pilihan === 'asc') {
                return namaA.localeCompare(namaB);
            }

            return namaB.localeCompare(namaA);
        });

        // Render ulang baris ke tabel
        rows.forEach(function (row) {
            tbody.appendChild(row);
        });

        // Update label
        const label = document.getElementById(
            'labelSelectedSupplierSort'
        );

        label.innerText =
            pilihan === 'asc'
                ? 'Urutkan: Nama (A-Z)'
                : 'Urutkan: Nama (Z-A)';

        // Update checkmark
        const checkAsc =
            document.getElementById('checkSupplierAsc');

        const checkDesc =
            document.getElementById('checkSupplierDesc');

        if (pilihan === 'asc') {
            checkAsc.classList.remove('hidden');
            checkDesc.classList.add('hidden');
        } else {
            checkAsc.classList.add('hidden');
            checkDesc.classList.remove('hidden');
        }

        // Tutup dropdown
        document
            .getElementById('panelFilterSupplier')
            .classList.add('hidden');

        document
            .getElementById('arrowSupplierSort')
            .classList.remove('rotate-180');
    }
    
    // Tutup Pop-up saat klik di luar
    document.addEventListener('click', function(event) {
        const container = document.getElementById('containerFilterSupplier');
        if (container && !container.contains(event.target)) {
            document.getElementById('panelFilterSupplier').classList.add('hidden');
            document.getElementById('arrowSupplierSort').classList.remove('rotate-180');
        }
    });

    // Function Modal Tambah
    function openTambahSupplierModal() {
        document.getElementById('tambahSupplierModal').classList.remove('hidden');
        document.getElementById('tambahSupplierModal').classList.add('flex');
    }

    function closeTambahSupplierModal() {
        document.getElementById('tambahSupplierModal').classList.add('hidden');
        document.getElementById('tambahSupplierModal').classList.remove('flex');
    }

    // Function Modal Edit
    function openEditSupplierModal(btn) {
        const SPP = btn.getAttribute('data-spp');
        const DES = btn.getAttribute('data-des');
        const TLP = btn.getAttribute('data-tlp');

        document.getElementById('editKodeSupplier').value = SPP;
        document.getElementById('editNamaSupplier').value = DES;
        document.getElementById('editTelpSupplier').value = TLP;

        // Set action form edit jika diperlukan
        // Set URL action secara dinamis menggantikan placeholder ':spp'
        const updateUrl = "{{ route('inventory.supplier.update', ':spp') }}".replace(':spp', encodeURIComponent(SPP));
        document.getElementById('editSupplierForm').action = updateUrl;

        document.getElementById('editSupplierModal').classList.remove('hidden');
        document.getElementById('editSupplierModal').classList.add('flex');
    }

    function closeEditSupplierModal() {
        document.getElementById('editSupplierModal').classList.add('hidden');
        document.getElementById('editSupplierModal').classList.remove('flex');
    }
</script>
@endpush
@endsection