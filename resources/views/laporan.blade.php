@extends('layouts.app')

@section('title', 'Laporan Kasir - Elektronik')
@section('header_title', 'Laporan Kasir')

@section('content')
{{--
    ===================================================================
    BACKEND DEVELOPER INTEGRATION NOTES:
    ===================================================================
    1. Report Summary Data:
       Replace Alpine stats (`totalPenjualan`, `jumlahTransaksi`, etc.)
       with server-calculated aggregate numbers for the filtered date/shift/kasir.
    2. Table Data:
       Replace `shifts` array with backend controller pagination data:
       `shifts: @json($shifts ?? [])`.
    ===================================================================
--}}
<div 
    x-data="laporanApp()" 
    class="flex-1 flex flex-col h-full overflow-y-auto bg-[#CECECE]/60 p-4 md:p-6 select-none"
>
    {{-- Top Filter & Status Card Container --}}
    <div class="bg-white/40 border border-gray-400/80 rounded-xl p-4 md:p-5 mb-5 shadow-xs">
        {{-- 4 Dashboard Status Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Card 1: Total Penjualan --}}
            <div class="bg-white border border-gray-300 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-gray-700 block">Total Penjualan</span>
                <span class="text-xl md:text-2xl font-bold font-number text-[#1A1F36] mt-1 block" x-text="formatRupiah(summary.totalPenjualan)"></span>
                <span class="text-[11px] text-gray-500 mt-1 block" x-text="'Pada ' + selectedDateFormatted"></span>
            </div>

            {{-- Card 2: Jumlah Transaksi --}}
            <div class="bg-white border border-gray-300 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-gray-700 block">Jumlah Transaksi</span>
                <span class="text-xl md:text-2xl font-bold font-number text-[#1A1F36] mt-1 block" x-text="summary.jumlahTransaksi + ' Order'"></span>
                <span class="text-[11px] text-gray-500 mt-1 block">Berhasil</span>
            </div>

            {{-- Card 3: Total Kas Tunai --}}
            <div class="bg-white border border-gray-300 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-gray-700 block">Total Kas Tunai</span>
                <span class="text-xl md:text-2xl font-bold font-number text-[#1A1F36] mt-1 block" x-text="formatRupiah(summary.kasTunai)"></span>
                <span class="text-[11px] text-gray-500 mt-1 block" x-text="summary.persenTunai + '% Dari total penjualan'"></span>
            </div>

            {{-- Card 4: QRIS & Bank --}}
            <div class="bg-white border border-gray-300 rounded-xl p-4 shadow-xs">
                <span class="text-xs font-semibold text-gray-700 block">QRIS & Bank</span>
                <span class="text-xl md:text-2xl font-bold font-number text-[#1A1F36] mt-1 block" x-text="formatRupiah(summary.qrisBank)"></span>
                <span class="text-[11px] text-gray-500 mt-1 block" x-text="summary.persenNonTunai + '% Dari total penjualan'"></span>
            </div>
        </div>

        {{-- Filter Section Header --}}
        <div class="mt-5 mb-2">
            <h3 class="text-base font-bold text-[#1A1F36]">Filter:</h3>
        </div>

        {{-- Filter Controls (Date, Shift, Kasir) --}}
        <div class="flex flex-wrap items-center gap-3">
            {{-- 1. Date Filter (Tanggal) --}}
            <div class="relative w-48 group">
                <input
                    type="date"
                    x-model="filterDate"
                    class="w-full bg-white border border-gray-400 hover:border-[#2D8CFF] focus:border-[#2D8CFF] rounded-lg px-3 py-2 text-sm text-[#1A1F36] focus:outline-none focus:ring-1 focus:ring-[#2D8CFF] shadow-2xs transition-all cursor-pointer"
                />
            </div>

            {{-- 2. Shift Filter Dropdown --}}
            <div class="relative w-44" @click.outside="shiftDropdownOpen = false">
                <button
                    type="button"
                    @click="shiftDropdownOpen = !shiftDropdownOpen"
                    class="w-full bg-white border border-gray-400 hover:border-[#2D8CFF] focus:border-[#2D8CFF] rounded-lg px-3 py-2 text-sm text-[#1A1F36] flex items-center justify-between shadow-2xs transition-all cursor-pointer"
                >
                    <span x-text="filterShift === 'Semua' ? 'Shift' : filterShift" :class="filterShift === 'Semua' ? 'text-gray-600' : 'font-medium text-[#1A1F36]'"></span>
                    {{-- Hourglass / Filter Icon matching mockup --}}
                    <svg class="w-4 h-4 text-black flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 2v6h.01L6 8.01 10 12l-4 4 .01.01H6V22h12v-5.99h-.01L18 16l-4-4 4-3.99-.01-.01H18V2H6zm10 14.5V20H8v-3.5l4-4 4 4zm-4-5l-4-4V4h8v3.5l-4 4z" />
                    </svg>
                </button>

                {{-- Shift Dropdown Menu --}}
                <div
                    x-show="shiftDropdownOpen"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute left-0 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-xl py-1 z-50 text-sm"
                    style="display: none;"
                >
                    <template x-for="s in ['Semua', 'Shift pagi', 'Shift sore', 'Shift malam']" :key="s">
                        <button
                            type="button"
                            @click="filterShift = s; shiftDropdownOpen = false"
                            :class="filterShift === s ? 'bg-blue-50 text-[#2D8CFF] font-semibold' : 'text-gray-700 hover:bg-gray-100'"
                            class="w-full text-left px-3 py-2 transition-colors cursor-pointer"
                            x-text="s === 'Semua' ? 'Semua Shift' : s"
                        ></button>
                    </template>
                </div>
            </div>

            {{-- 3. Kasir Filter Dropdown --}}
            <div class="relative w-44" @click.outside="kasirDropdownOpen = false">
                <button
                    type="button"
                    @click="kasirDropdownOpen = !kasirDropdownOpen"
                    class="w-full bg-white border border-gray-400 hover:border-[#2D8CFF] focus:border-[#2D8CFF] rounded-lg px-3 py-2 text-sm text-[#1A1F36] flex items-center justify-between shadow-2xs transition-all cursor-pointer"
                >
                    <span x-text="filterKasir === 'Semua' ? 'Kasir' : filterKasir" :class="filterKasir === 'Semua' ? 'text-gray-600' : 'font-medium text-[#1A1F36]'"></span>
                    {{-- User / Person Icon matching mockup --}}
                    <svg class="w-4 h-4 text-black flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </button>

                {{-- Kasir Dropdown Menu --}}
                <div
                    x-show="kasirDropdownOpen"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute left-0 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-xl py-1 z-50 text-sm"
                    style="display: none;"
                >
                    <template x-for="k in ['Semua', 'User 2', 'User 1', 'Kezia']" :key="k">
                        <button
                            type="button"
                            @click="filterKasir = k; kasirDropdownOpen = false"
                            :class="filterKasir === k ? 'bg-blue-50 text-[#2D8CFF] font-semibold' : 'text-gray-700 hover:bg-gray-100'"
                            class="w-full text-left px-3 py-2 transition-colors cursor-pointer"
                            x-text="k === 'Semua' ? 'Semua Kasir' : k"
                        ></button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Laporan Table Container (Rounded-lg, border-radius matching design) --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden flex flex-col">
        {{-- Table Content --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                {{-- Dark Navy Table Header --}}
                <thead>
                    <tr class="bg-[#242F40] text-white text-sm font-semibold tracking-wide">
                        <th class="py-3 px-4 font-bold">Sesi/Shift</th>
                        <th class="py-3 px-4 font-bold">Kasir</th>
                        <th class="py-3 px-4 font-bold">Waktu Buka</th>
                        <th class="py-3 px-4 font-bold">Waktu Tutup</th>
                        <th class="py-3 px-4 font-bold">Kas Awal</th>
                        <th class="py-3 px-4 font-bold">Total Penjualan</th>
                        <th class="py-3 px-4 font-bold">Kas Akhir Rill</th>
                        <th class="py-3 px-4 font-bold">Selisih</th>
                    </tr>
                </thead>
                {{-- Table Body --}}
                <tbody class="divide-y divide-gray-200 text-sm text-[#1A1F36]">
                    <template x-for="item in paginatedShifts" :key="item.id">
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-medium" x-text="item.shift"></td>
                            <td class="py-3.5 px-4" x-text="item.kasir"></td>
                            <td class="py-3.5 px-4" x-text="item.waktuBuka"></td>
                            <td class="py-3.5 px-4" x-text="item.waktuTutup"></td>
                            <td class="py-3.5 px-4 font-number" x-text="formatRupiah(item.kasAwal)"></td>
                            <td class="py-3.5 px-4 font-number" x-text="formatRupiah(item.totalPenjualan)"></td>
                            <td class="py-3.5 px-4 font-number" x-text="formatRupiah(item.kasAkhir)"></td>
                            <td class="py-3.5 px-4 font-number" x-text="formatRupiah(item.selisih)"></td>
                        </tr>
                    </template>
                    {{-- Empty State --}}
                    <tr x-show="filteredShifts.length === 0">
                        <td colspan="8" class="py-8 text-center text-gray-500 text-sm">
                            Tidak ada data shift untuk filter yang dipilih.
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
</div>

{{-- Alpine.js Logic for Laporan Kasir --}}
<script>
    function laporanApp() {
        return {
            // Filter states
            filterDate: '2026-09-16',
            filterShift: 'Semua',
            filterKasir: 'Semua',

            shiftDropdownOpen: false,
            kasirDropdownOpen: false,

            // Pagination state
            currentPage: 1,
            itemsPerPage: 3,

            // Summary Stats (matching image status cards)
            summary: {
                totalPenjualan: 76800000,
                jumlahTransaksi: 3,
                kasTunai: 40000000,
                persenTunai: 52,
                qrisBank: 36800000,
                persenNonTunai: 48
            },

            // Dummy Shifts (only for today: 16/09/2026, matching image)
            shifts: [
                {
                    id: 1,
                    shift: 'Shift malam',
                    kasir: 'User 2',
                    waktuBuka: '20:00 WIB',
                    waktuTutup: '00:00 WIB',
                    kasAwal: 0,
                    totalPenjualan: 20000000,
                    kasAkhir: 20000000,
                    selisih: 0
                },
                {
                    id: 2,
                    shift: 'Shift sore',
                    kasir: 'User 1',
                    waktuBuka: '15:00 WIB',
                    waktuTutup: '19:00 WIB',
                    kasAwal: 0,
                    totalPenjualan: 20000000,
                    kasAkhir: 20000000,
                    selisih: 20000
                },
                {
                    id: 3,
                    shift: 'Shift pagi',
                    kasir: 'Kezia',
                    waktuBuka: '09:00 WIB',
                    waktuTutup: '14:00 WIB',
                    kasAwal: 30000,
                    totalPenjualan: 36800000,
                    kasAkhir: 36800000,
                    selisih: 36
                }
            ],

            get selectedDateFormatted() {
                if (!this.filterDate) return '16/09/2026';
                const parts = this.filterDate.split('-');
                if (parts.length === 3) {
                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                }
                return '16/09/2026';
            },

            // Filtered shifts based on shift and cashier dropdown
            get filteredShifts() {
                let list = [...this.shifts];

                if (this.filterShift !== 'Semua') {
                    list = list.filter(item => item.shift.toLowerCase() === this.filterShift.toLowerCase());
                }

                if (this.filterKasir !== 'Semua') {
                    list = list.filter(item => item.kasir.toLowerCase() === this.filterKasir.toLowerCase());
                }

                return list;
            },

            get totalPages() {
                return Math.ceil(this.filteredShifts.length / this.itemsPerPage) || 1;
            },

            get paginatedShifts() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredShifts.slice(start, start + this.itemsPerPage);
            },

            get paginationInfo() {
                const total = this.filteredShifts.length;
                if (total === 0) return '0 dari 0 shift';
                const start = (this.currentPage - 1) * this.itemsPerPage + 1;
                const end = Math.min(start + this.itemsPerPage - 1, total);
                return `${start}-${end} dari ${total} shift`;
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

            formatRupiah(num) {
                return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
            }
        };
    }
</script>
@endsection
