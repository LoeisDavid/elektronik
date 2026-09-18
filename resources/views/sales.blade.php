@extends('layouts.app')

@section('title', 'Sales - Elektronik')
@section('header_title', 'Sales')

@section('content')
{{--
    ===================================================================
    BACKEND DEVELOPER INTEGRATION NOTES:
    ===================================================================
    1. Sales Data:
       Replace `initialSales` in Alpine `salesApp()` function with controller data:
       `initialSales: @json($sales ?? [])`.
    2. Add/Edit/Delete Sales:
       Connect modal forms and "Hapus" button to your backend API endpoints.
    ===================================================================
--}}
<div 
    x-data="salesApp()" 
    class="flex-1 flex flex-col h-full overflow-y-auto bg-[#CECECE]/60 p-4 md:p-6 select-none"
>
    {{-- Top Action Toolbar (Search, Filter, and + Tambah sales button) --}}
    <div class="flex items-center justify-between gap-3 mb-4">
        {{-- Left: Search Input & Filter Button --}}
        <div class="flex items-center gap-2">
            {{-- Search Input with Magnifying Glass --}}
            <div class="relative w-64 md:w-80 group">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-black group-hover:text-[#2D8CFF] transition-colors">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </span>
                <input
                    type="text"
                    x-model="searchQuery"
                    placeholder="Cari sales"
                    class="w-full bg-white border border-gray-400 hover:border-[#2D8CFF] focus:border-[#2D8CFF] rounded-lg pl-10 pr-4 py-2 text-sm text-[#1A1F36] placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#2D8CFF] transition-all shadow-xs"
                />
            </div>

            {{-- Filter Dropdown Trigger & Popover --}}
            <div class="relative" @click.outside="filterOpen = false">
                <button
                    type="button"
                    @click="filterOpen = !filterOpen"
                    :class="filterOpen ? 'bg-blue-50 border-[#2D8CFF] text-[#2D8CFF] ring-1 ring-[#2D8CFF]' : 'bg-white hover:bg-blue-50 hover:border-[#2D8CFF] hover:text-[#2D8CFF] text-black border-gray-400'"
                    class="h-9.5 w-9.5 border rounded-lg flex items-center justify-center transition-all flex-shrink-0 shadow-xs cursor-pointer"
                    title="Filter Sales"
                >
                    <svg class="w-5 h-5 text-current fill-current" viewBox="0 0 24 24">
                        <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z" />
                    </svg>
                </button>

                {{-- Filter Options Popover --}}
                <div
                    x-show="filterOpen"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute left-0 mt-2 w-56 bg-[#CECECE] rounded-xl p-2 shadow-2xl space-y-1.5 border border-gray-300 z-50 select-none"
                    style="display: none;"
                >
                    <template x-for="option in filterOptions" :key="option">
                        <button
                            type="button"
                            @click="selectFilter(option)"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white hover:bg-gray-50 active:bg-gray-100 rounded-lg text-sm font-medium text-[#1A1F36] text-left transition-colors shadow-xs cursor-pointer"
                        >
                            <span x-text="option"></span>
                            <svg
                                x-show="selectedFilter === option"
                                class="w-5 h-5 text-gray-400 stroke-[2.5]"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- Right: + Tambah sales Button --}}
        <button
            type="button"
            @click="openTambahModal()"
            class="h-9.5 px-4 bg-[#2D8CFF] hover:bg-blue-600 active:bg-blue-700 text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-all shadow-xs cursor-pointer"
        >
            <span class="text-lg leading-none font-bold">+</span>
            <span>Tambah sales</span>
        </button>
    </div>

    {{-- Sales Table Container (with rounded corners & border radius matching Riwayat Transaksi) --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden flex flex-col">
        {{-- Table Content --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                {{-- Dark Navy Table Header --}}
                <thead>
                    <tr class="bg-[#242F40] text-white text-sm font-semibold tracking-wide">
                        <th class="py-3 px-4 font-bold">Kode Sales</th>
                        <th class="py-3 px-4 font-bold">Nama Sales</th>
                        <th class="py-3 px-4 font-bold">Alamat</th>
                        <th class="py-3 px-4 font-bold">Kota</th>
                        <th class="py-3 px-4 font-bold">Nomor Telepon</th>
                        <th class="py-3 px-4 font-bold text-center w-20">Aksi</th>
                    </tr>
                </thead>
                {{-- Table Body --}}
                <tbody class="divide-y divide-gray-200 text-sm text-[#1A1F36]">
                    <template x-for="(s, index) in paginatedSales" :key="s.kode">
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-medium" x-text="s.kode"></td>
                            <td class="py-3.5 px-4" x-text="s.nama"></td>
                            <td class="py-3.5 px-4" x-text="s.alamat"></td>
                            <td class="py-3.5 px-4" x-text="s.kota"></td>
                            <td class="py-3.5 px-4 font-number" x-text="s.telepon"></td>
                            <td class="py-3.5 px-4 text-center relative">
                                {{-- Pencil Action Button with Popover --}}
                                <div class="relative inline-block text-left" @click.outside="actionMenuOpenCode === s.kode && (actionMenuOpenCode = null)">
                                    <button
                                        type="button"
                                        @click="toggleActionMenu(s.kode)"
                                        :class="actionMenuOpenCode === s.kode ? 'border-[#2D8CFF] bg-blue-50 text-[#2D8CFF]' : 'border-gray-400 bg-white hover:border-black text-black hover:bg-gray-100'"
                                        class="h-8 w-8 border rounded-md inline-flex items-center justify-center transition-colors shadow-2xs cursor-pointer"
                                        title="Aksi Sales"
                                    >
                                        {{-- Pencil / Edit Icon matching mockup --}}
                                        <svg class="w-4 h-4 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                        </svg>
                                    </button>

                                    {{-- Little Box Popover (Edit, Hapus in red) --}}
                                    <div
                                        x-show="actionMenuOpenCode === s.kode"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        :class="index === paginatedSales.length - 1 && paginatedSales.length > 1 ? 'bottom-full mb-1' : 'top-full mt-1'"
                                        class="absolute right-0 w-28 bg-white border border-gray-300 rounded-lg shadow-xl py-1 z-50 text-left"
                                        style="display: none;"
                                    >
                                        {{-- Edit Button (Triggers EditSalesModal) --}}
                                        <button
                                            type="button"
                                            @click="openEditModal(s)"
                                            class="w-full px-3 py-1.5 text-xs font-semibold text-gray-800 hover:bg-gray-100 transition-colors flex items-center gap-2 cursor-pointer"
                                        >
                                            <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                            </svg>
                                            <span>Edit</span>
                                        </button>

                                        {{-- Hapus Button (in red color) --}}
                                        <button
                                            type="button"
                                            @click="deleteSales(s.kode)"
                                            class="w-full px-3 py-1.5 text-xs font-semibold text-[#EF4444] hover:bg-red-50 transition-colors flex items-center gap-2 cursor-pointer"
                                        >
                                            <svg class="w-3.5 h-3.5 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                    {{-- Empty State --}}
                    <tr x-show="filteredSales.length === 0">
                        <td colspan="6" class="py-8 text-center text-gray-500 text-sm">
                            Tidak ada sales yang cocok.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Table Footer (Pagination & Counter matching mockup) --}}
        <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-gray-600">
            <div>
                Menampilkan <span x-text="paginationInfo"></span>
            </div>
            {{-- Pagination Controls --}}
            <div class="flex items-center gap-1.5 select-none">
                {{-- Prev Button --}}
                <button
                    type="button"
                    @click="prevPage()"
                    :disabled="currentPage === 1"
                    :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100 cursor-pointer text-black'"
                    class="h-7 w-7 border border-gray-400 rounded flex items-center justify-center text-xs font-semibold transition-colors"
                >
                    ‹
                </button>

                {{-- Page Number Buttons --}}
                <template x-for="p in totalPages" :key="p">
                    <button
                        type="button"
                        @click="currentPage = p"
                        :class="currentPage === p ? 'bg-[#2D8CFF] text-white border-[#2D8CFF]' : 'bg-white text-gray-700 border-gray-400 hover:bg-gray-100'"
                        class="h-7 w-7 border rounded flex items-center justify-center text-xs font-semibold transition-colors cursor-pointer"
                        x-text="p"
                    ></button>
                </template>

                {{-- Next Button --}}
                <button
                    type="button"
                    @click="nextPage()"
                    :disabled="currentPage === totalPages"
                    :class="currentPage === totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100 cursor-pointer text-black'"
                    class="h-7 w-7 border border-gray-400 rounded flex items-center justify-center text-xs font-semibold transition-colors"
                >
                    ›
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- TAMBAH SALES MODAL (Matching Image 2)                          --}}
    {{-- ============================================================= --}}
    <div
        x-show="isTambahModalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
        style="display: none;"
    >
        {{-- Backdrop --}}
        <div class="fixed inset-0" @click="closeTambahModal()"></div>

        {{-- Modal Card --}}
        <div
            x-show="isTambahModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-sm bg-[#F8FAFC] border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-[#1A1F36] font-sans"
        >
            {{-- Modal Title --}}
            <h2 class="text-2xl font-bold text-[#1A1F36] tracking-tight mb-5">
                Tambah Sales
            </h2>

            {{-- Form Fields --}}
            <form @submit.prevent="saveNewSales()" class="space-y-4">
                {{-- Kode Sales --}}
                <div>
                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">Kode Sales</label>
                    <input
                        type="text"
                        x-model="newSales.kode"
                        placeholder="Contoh: SLS004"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#2D8CFF] focus:border-[#2D8CFF] transition-all"
                    />
                </div>

                {{-- Nama Sales --}}
                <div>
                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">Nama Sales</label>
                    <input
                        type="text"
                        x-model="newSales.nama"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#2D8CFF] focus:border-[#2D8CFF] transition-all"
                    />
                </div>

                {{-- Alamat --}}
                <div>
                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">Alamat</label>
                    <input
                        type="text"
                        x-model="newSales.alamat"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#2D8CFF] focus:border-[#2D8CFF] transition-all"
                    />
                </div>

                {{-- Kota --}}
                <div>
                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">Kota</label>
                    <input
                        type="text"
                        x-model="newSales.kota"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#2D8CFF] focus:border-[#2D8CFF] transition-all"
                    />
                </div>

                {{-- Nomor Telepon --}}
                <div>
                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">Nomor Telepon</label>
                    <input
                        type="text"
                        x-model="newSales.telepon"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] font-number focus:outline-none focus:ring-1 focus:ring-[#2D8CFF] focus:border-[#2D8CFF] transition-all"
                    />
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 flex items-center justify-between gap-3">
                    <button
                        type="button"
                        @click="closeTambahModal()"
                        class="flex-1 py-2.5 px-4 bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl text-sm font-semibold text-[#1A1F36] hover:bg-gray-100 transition-colors cursor-pointer text-center"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="flex-1 py-2.5 px-4 bg-[#2D8CFF] hover:bg-blue-600 active:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors cursor-pointer text-center shadow-xs"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- EDIT SALES MODAL (Matching Image 3)                            --}}
    {{-- ============================================================= --}}
    <div
        x-show="isEditModalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
        style="display: none;"
    >
        {{-- Backdrop --}}
        <div class="fixed inset-0" @click="closeEditModal()"></div>

        {{-- Modal Card --}}
        <div
            x-show="isEditModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-sm bg-[#F8FAFC] border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-[#1A1F36] font-sans"
        >
            {{-- Modal Title --}}
            <h2 class="text-2xl font-bold text-[#1A1F36] tracking-tight mb-5">
                Edit Sales
            </h2>

            {{-- Form Fields --}}
            <form @submit.prevent="saveEditSales()" class="space-y-4">
                {{-- Kode Sales --}}
                <div>
                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">Kode Sales</label>
                    <input
                        type="text"
                        x-model="editSales.kode"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#2D8CFF] focus:border-[#2D8CFF] transition-all"
                    />
                </div>

                {{-- Nama Sales --}}
                <div>
                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">Nama Sales</label>
                    <input
                        type="text"
                        x-model="editSales.nama"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#2D8CFF] focus:border-[#2D8CFF] transition-all"
                    />
                </div>

                {{-- Alamat --}}
                <div>
                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">Alamat</label>
                    <input
                        type="text"
                        x-model="editSales.alamat"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#2D8CFF] focus:border-[#2D8CFF] transition-all"
                    />
                </div>

                {{-- Kota --}}
                <div>
                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">Kota</label>
                    <input
                        type="text"
                        x-model="editSales.kota"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#2D8CFF] focus:border-[#2D8CFF] transition-all"
                    />
                </div>

                {{-- Nomor Telepon --}}
                <div>
                    <label class="block text-sm font-semibold text-[#1A1F36] mb-1">Nomor Telepon</label>
                    <input
                        type="text"
                        x-model="editSales.telepon"
                        required
                        class="w-full bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl px-3 py-2.5 text-sm text-[#1A1F36] font-number focus:outline-none focus:ring-1 focus:ring-[#2D8CFF] focus:border-[#2D8CFF] transition-all"
                    />
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 flex items-center justify-between gap-3">
                    <button
                        type="button"
                        @click="closeEditModal()"
                        class="flex-1 py-2.5 px-4 bg-[#F8FAFC] border border-[#1A1F36]/80 rounded-xl text-sm font-semibold text-[#1A1F36] hover:bg-gray-100 transition-colors cursor-pointer text-center"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="flex-1 py-2.5 px-4 bg-[#2D8CFF] hover:bg-blue-600 active:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors cursor-pointer text-center shadow-xs"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Alpine.js Logic for Sales Page --}}
<script>
    function salesApp() {
        return {
            searchQuery: '',
            filterOpen: false,
            selectedFilter: 'Semua',
            filterOptions: [
                'Semua',
                'Surabaya',
                'Jakarta',
                'Semarang',
                'Nama'
            ],

            // Action Popover
            actionMenuOpenCode: null,

            // Modals
            isTambahModalOpen: false,
            isEditModalOpen: false,

            // Form states
            newSales: {
                kode: '',
                nama: '',
                alamat: '',
                kota: '',
                telepon: ''
            },
            editSales: {
                originalKode: '',
                kode: '',
                nama: '',
                alamat: '',
                kota: '',
                telepon: ''
            },

            // Pagination state (3 items per page matching mockup)
            currentPage: 1,
            itemsPerPage: 3,

            // Dummy Sales matching Image 1
            salesList: [
                { kode: 'SLS001', nama: 'Fadhil', alamat: 'Nirwana Eksekutif', kota: 'Surabaya', telepon: '0987654321' },
                { kode: 'SLS002', nama: 'Prana', alamat: 'Nirwana Eksekutif', kota: 'Jakarta', telepon: '0987654321' },
                { kode: 'SLS003', nama: 'Dzaki', alamat: 'Nirwana Eksekutif', kota: 'Semarang', telepon: '0987654321' },
                { kode: 'SLS004', nama: 'David', alamat: 'Nirwana Eksekutif', kota: 'Surabaya', telepon: '0812345678' },
                { kode: 'SLS005', nama: 'Nevil', alamat: 'Gubeng Kertajaya', kota: 'Malang', telepon: '0856789123' },
                { kode: 'SLS006', nama: 'Rian', alamat: 'Dharmahusada Indah', kota: 'Bandung', telepon: '0898765432' }
            ],

            selectFilter(option) {
                this.selectedFilter = option;
                this.filterOpen = false;
                this.currentPage = 1;
            },

            toggleActionMenu(kode) {
                this.actionMenuOpenCode = this.actionMenuOpenCode === kode ? null : kode;
            },

            // Filtered sales
            get filteredSales() {
                let list = [...this.salesList];

                // City or Sort filter
                if (this.selectedFilter === 'Nama') {
                    list.sort((a, b) => a.nama.localeCompare(b.nama));
                } else if (this.selectedFilter !== 'Semua') {
                    list = list.filter(s => s.kota.toLowerCase() === this.selectedFilter.toLowerCase());
                }

                // Search filter
                if (this.searchQuery.trim()) {
                    const q = this.searchQuery.toLowerCase().trim();
                    list = list.filter(s => 
                        s.kode.toLowerCase().includes(q) ||
                        s.nama.toLowerCase().includes(q) ||
                        s.alamat.toLowerCase().includes(q) ||
                        s.kota.toLowerCase().includes(q) ||
                        s.telepon.includes(q)
                    );
                }

                return list;
            },

            get totalPages() {
                return Math.ceil(this.filteredSales.length / this.itemsPerPage) || 1;
            },

            get paginatedSales() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredSales.slice(start, start + this.itemsPerPage);
            },

            get paginationInfo() {
                const total = this.filteredSales.length;
                if (total === 0) return '0 dari 0 sales';
                const start = (this.currentPage - 1) * this.itemsPerPage + 1;
                const end = Math.min(start + this.itemsPerPage - 1, total);
                return `${start}-${end} dari ${total} sales`;
            },

            prevPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                }
            },

            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                }
            },

            // Tambah Sales Modal controls
            openTambahModal() {
                this.newSales = {
                    kode: 'SLS00' + (this.salesList.length + 1),
                    nama: '',
                    alamat: '',
                    kota: '',
                    telepon: ''
                };
                this.isTambahModalOpen = true;
            },

            closeTambahModal() {
                this.isTambahModalOpen = false;
            },

            saveNewSales() {
                this.salesList.push({
                    kode: this.newSales.kode,
                    nama: this.newSales.nama,
                    alamat: this.newSales.alamat,
                    kota: this.newSales.kota,
                    telepon: this.newSales.telepon
                });
                this.closeTambahModal();
            },

            // Edit Sales Modal controls
            openEditModal(sales) {
                this.actionMenuOpenCode = null;
                this.editSales = {
                    originalKode: sales.kode,
                    kode: sales.kode,
                    nama: sales.nama,
                    alamat: sales.alamat,
                    kota: sales.kota,
                    telepon: sales.telepon
                };
                this.isEditModalOpen = true;
            },

            closeEditModal() {
                this.isEditModalOpen = false;
            },

            saveEditSales() {
                const idx = this.salesList.findIndex(s => s.kode === this.editSales.originalKode);
                if (idx !== -1) {
                    this.salesList[idx].kode = this.editSales.kode;
                    this.salesList[idx].nama = this.editSales.nama;
                    this.salesList[idx].alamat = this.editSales.alamat;
                    this.salesList[idx].kota = this.editSales.kota;
                    this.salesList[idx].telepon = this.editSales.telepon;
                }
                this.closeEditModal();
            },

            // Delete Sales action
            deleteSales(kode) {
                this.actionMenuOpenCode = null;
                this.salesList = this.salesList.filter(s => s.kode !== kode);
                if (this.currentPage > this.totalPages) {
                    this.currentPage = Math.max(1, this.totalPages);
                }
            }
        };
    }
</script>
@endsection
