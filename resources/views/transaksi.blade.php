@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Elektronik')
@section('header_title', 'Riwayat Transaksi')

@section('content')
{{--
    ===================================================================
    BACKEND DEVELOPER INTEGRATION NOTES:
    ===================================================================
    1. Transaction Data:
       Replace `initialTransactions` in the Alpine `transaksiApp()` function
       below with controller data: `initialTransactions: @json($transactions ?? [])`.
    2. Filters & Search:
       Alpine handles real-time client-side search & sort.
       For server-side pagination/filtering, adapt routes & query parameters.
    ===================================================================
--}}
<div 
    x-data="transaksiApp()" 
    class="flex-1 flex flex-col h-full overflow-y-auto bg-[#CECECE]/60 p-4 md:p-6 select-none"
>
    {{-- Top Action Toolbar (Search & Filter) --}}
    <div class="flex items-center gap-2 mb-4">
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
                placeholder="Cari produk"
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
                title="Filter Transaksi"
            >
                <svg class="w-5 h-5 text-current fill-current" viewBox="0 0 24 24">
                    <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z" />
                </svg>
            </button>

            {{-- Dropdown Menu (Only: Nama, Total tertinggi, Total terendah, Metode pembayaran) --}}
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
                <template x-for="option in sortOptions" :key="option">
                    <button
                        type="button"
                        @click="selectSort(option)"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white hover:bg-gray-50 active:bg-gray-100 rounded-lg text-sm font-medium text-[#1A1F36] text-left transition-colors shadow-xs cursor-pointer"
                    >
                        <span x-text="option"></span>
                        <svg
                            x-show="selectedSort === option"
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

    {{-- Transaction Table Container --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden flex flex-col">
        {{-- Table Content --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                {{-- Dark Navy Table Header --}}
                <thead>
                    <tr class="bg-[#242F40] text-white text-sm font-semibold tracking-wide">
                        <th class="py-3 px-4 font-bold">Kode Transaksi</th>
                        <th class="py-3 px-4 font-bold">Tanggal pembelian</th>
                        <th class="py-3 px-4 font-bold">Waktu</th>
                        <th class="py-3 px-4 font-bold">Total</th>
                        <th class="py-3 px-4 font-bold">Metode pembayaran</th>
                        <th class="py-3 px-4 font-bold">Status</th>
                        <th class="py-3 px-4 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                {{-- Table Body --}}
                <tbody class="divide-y divide-gray-200 text-sm text-[#1A1F36]">
                    <template x-for="trx in paginatedTransactions" :key="trx.kode">
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-medium" x-text="trx.kode"></td>
                            <td class="py-3.5 px-4" x-text="trx.tanggal"></td>
                            <td class="py-3.5 px-4" x-text="trx.waktu"></td>
                            <td class="py-3.5 px-4 font-medium font-number" x-text="formatRupiah(trx.total)"></td>
                            <td class="py-3.5 px-4" x-text="trx.metode"></td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-[#34D399] text-white">
                                    Selesai
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <button
                                    type="button"
                                    @click="openStrukModal(trx)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-gray-400 hover:border-black rounded-md text-xs font-semibold text-black hover:bg-gray-100 transition-colors shadow-2xs cursor-pointer"
                                >
                                    {{-- Document / Struk Icon --}}
                                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Struk</span>
                                </button>
                            </td>
                        </tr>
                    </template>
                    {{-- Empty State --}}
                    <tr x-show="filteredTransactions.length === 0">
                        <td colspan="7" class="py-8 text-center text-gray-500 text-sm">
                            Tidak ada transaksi yang cocok.
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
    {{-- STRUK MODAL (INVOICE POPUP)                                   --}}
    {{-- ============================================================= --}}
    <div
        x-show="isStrukOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
        style="display: none;"
    >
        {{-- Modal Backdrop Click to Close --}}
        <div class="fixed inset-0" @click="closeStrukModal()"></div>

        {{-- Invoice Modal Card --}}
        <div
            x-show="isStrukOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-xl bg-white p-6 md:p-8 rounded-lg shadow-2xl z-10 max-h-[92vh] overflow-y-auto text-black font-sans"
        >
            {{-- Close X Button --}}
            <button
                type="button"
                @click="closeStrukModal()"
                class="absolute top-4 right-4 text-gray-400 hover:text-black transition-colors p-1 rounded-md hover:bg-gray-100 cursor-pointer"
                title="Tutup Modal"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Store Header --}}
            <div class="flex items-start justify-between gap-4 pr-6">
                <h2 class="text-3xl md:text-4xl font-bold text-black tracking-tight font-sans">
                    NamaToko
                </h2>
                <div class="text-right text-xs text-gray-800 leading-tight">
                    <p>Medokan Ayu, Kec. Rungkut,</p>
                    <p>Surabaya</p>
                    <p>No Telp: 0881-0268-77983</p>
                </div>
            </div>

            {{-- Dotted Separator --}}
            <div class="border-b-2 border-dotted border-black my-4"></div>

            {{-- Transaction Info --}}
            <div class="space-y-1 text-sm text-black">
                <div class="flex items-center justify-between">
                    <span x-text="'Tanggal: ' + (activeTrx ? activeTrx.tanggal + ' ' + activeTrx.waktu : '')"></span>
                    <span class="font-medium font-number" x-text="'No: ' + (activeTrx ? activeTrx.kode : '')"></span>
                </div>
                <div>
                    <span>Dilayani oleh: User 2</span>
                </div>
            </div>

            {{-- Dotted Separator --}}
            <div class="border-b-2 border-dotted border-black my-4"></div>

            {{-- Line Items --}}
            <div class="space-y-3 text-base">
                <template x-for="(item, idx) in (activeTrx ? activeTrx.items : [])" :key="idx">
                    <div class="flex items-center justify-between font-bold text-black">
                        <span x-text="item.qty + 'x ' + item.name"></span>
                        <span class="font-number" x-text="formatRupiah(item.price * item.qty)"></span>
                    </div>
                </template>
            </div>

            {{-- Dotted Separator --}}
            <div class="border-b-2 border-dotted border-black my-4"></div>

            {{-- Total & Payment Method --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between text-2xl font-bold text-black">
                    <span>Total:</span>
                    <span class="font-number" x-text="activeTrx ? formatRupiah(activeTrx.total) : 'Rp 0'"></span>
                </div>
                <div class="flex items-center justify-between text-base text-gray-700">
                    <span>Metode pembayaran:</span>
                    <span class="font-bold text-black uppercase" x-text="activeTrx ? activeTrx.metode : ''"></span>
                </div>
            </div>

            {{-- Download & Print Buttons --}}
            <div class="mt-8 pt-4 flex items-center justify-center gap-6">
                <button 
                    type="button"
                    @click="downloadInvoice()"
                    class="px-8 py-2.5 bg-white border-2 border-black rounded-md text-base font-bold text-black hover:bg-gray-100 active:bg-gray-200 transition-colors shadow-2xs cursor-pointer min-w-[140px] text-center"
                >
                    Download
                </button>
                <button 
                    type="button"
                    @click="window.print()"
                    class="px-8 py-2.5 bg-white border-2 border-black rounded-md text-base font-bold text-black hover:bg-gray-100 active:bg-gray-200 transition-colors shadow-2xs cursor-pointer min-w-[140px] text-center"
                >
                    Print
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Alpine.js Logic for Riwayat Transaksi --}}
<script>
    function transaksiApp() {
        return {
            searchQuery: '',
            filterOpen: false,
            selectedSort: 'Nama',
            sortOptions: [
                'Nama',
                'Total tertinggi',
                'Total terendah',
                'Metode pembayaran'
            ],

            // Struk Modal state
            isStrukOpen: false,
            activeTrx: null,

            // Pagination state (3 items per page matching mockup)
            currentPage: 1,
            itemsPerPage: 3,

            // Initial Dummy Data matching mockup
            transactions: [
                {
                    kode: '2026-0003',
                    tanggal: '13/09/2026',
                    waktu: '12:00',
                    total: 20000000,
                    metode: 'QRIS',
                    items: [
                        { name: 'Kulkas Samsung 2 Pintu Inverter', qty: 2, price: 10000000 }
                    ]
                },
                {
                    kode: '2026-0002',
                    tanggal: '13/09/2026',
                    waktu: '10:00',
                    total: 20000000,
                    metode: 'Tunai',
                    items: [
                        { name: 'Smart TV LG 55 Inch 4K UHD', qty: 2, price: 10000000 }
                    ]
                },
                {
                    kode: '2026-0001',
                    tanggal: '13/09/2026',
                    waktu: '09:23',
                    total: 36800000,
                    metode: 'Debit BNI',
                    items: [
                        { name: 'AC Daikin Inverter 1 PK', qty: 4, price: 7200000 },
                        { name: 'Microwave Panasonic 25L', qty: 2, price: 4000000 }
                    ]
                },
                {
                    kode: '2026-0004',
                    tanggal: '12/09/2026',
                    waktu: '15:30',
                    total: 18500000,
                    metode: 'BCA',
                    items: [
                        { name: 'Mesin Cuci Sharp Front Loading', qty: 1, price: 18500000 }
                    ]
                },
                {
                    kode: '2026-0005',
                    tanggal: '12/09/2026',
                    waktu: '14:15',
                    total: 9600000,
                    metode: 'Tunai',
                    items: [
                        { name: 'Air Purifier Philips Series 2000', qty: 2, price: 4800000 }
                    ]
                },
                {
                    kode: '2026-0006',
                    tanggal: '11/09/2026',
                    waktu: '11:00',
                    total: 45000000,
                    metode: 'Mandiri',
                    items: [
                        { name: 'OLED TV Sony Bravia 65 Inch', qty: 1, price: 45000000 }
                    ]
                }
            ],

            selectSort(option) {
                this.selectedSort = option;
                this.filterOpen = false;
                this.currentPage = 1;
            },

            // Filtered & Sorted Transactions
            get filteredTransactions() {
                let list = [...this.transactions];

                // Search query filter
                if (this.searchQuery.trim()) {
                    const q = this.searchQuery.toLowerCase().trim();
                    list = list.filter(trx => 
                        trx.kode.toLowerCase().includes(q) ||
                        trx.metode.toLowerCase().includes(q) ||
                        trx.tanggal.includes(q) ||
                        trx.items.some(it => it.name.toLowerCase().includes(q))
                    );
                }

                // Sorting
                if (this.selectedSort === 'Nama') {
                    list.sort((a, b) => a.kode.localeCompare(b.kode));
                } else if (this.selectedSort === 'Total tertinggi') {
                    list.sort((a, b) => b.total - a.total);
                } else if (this.selectedSort === 'Total terendah') {
                    list.sort((a, b) => a.total - b.total);
                } else if (this.selectedSort === 'Metode pembayaran') {
                    list.sort((a, b) => a.metode.localeCompare(b.metode));
                }

                return list;
            },

            get totalPages() {
                return Math.ceil(this.filteredTransactions.length / this.itemsPerPage) || 1;
            },

            get paginatedTransactions() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredTransactions.slice(start, start + this.itemsPerPage);
            },

            get paginationInfo() {
                const total = this.filteredTransactions.length;
                if (total === 0) return '0 dari 0 produk';
                const start = (this.currentPage - 1) * this.itemsPerPage + 1;
                const end = Math.min(start + this.itemsPerPage - 1, total);
                return `${start}-${end} dari ${total} produk`;
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

            // Modal Controls
            openStrukModal(trx) {
                this.activeTrx = trx;
                this.isStrukOpen = true;
            },

            closeStrukModal() {
                this.isStrukOpen = false;
                this.activeTrx = null;
            },

            // Download Invoice Action
            downloadInvoice() {
                if (!this.activeTrx) return;
                const trx = this.activeTrx;
                let itemsText = trx.items.map(it => `${it.qty}x ${it.name} - ${this.formatRupiah(it.price * it.qty)}`).join('\n');
                let content = `NamaToko\nMedokan Ayu, Kec. Rungkut, Surabaya\nNo Telp: 0881-0268-77983\n` +
                              `--------------------------------\n` +
                              `Tanggal: ${trx.tanggal} ${trx.waktu}\nNo: ${trx.kode}\nDilayani oleh: User 2\n` +
                              `--------------------------------\n` +
                              `${itemsText}\n` +
                              `--------------------------------\n` +
                              `Total: ${this.formatRupiah(trx.total)}\nMetode Pembayaran: ${trx.metode}\n`;

                const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `Invoice-${trx.kode}.txt`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            },

            formatRupiah(num) {
                return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
            }
        };
    }
</script>
@endsection
