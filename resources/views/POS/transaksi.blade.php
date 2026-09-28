@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Elektronik')
@section('header_title', 'Riwayat Transaksi')

@section('content')
<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 select-none">
    
    {{-- Toolbar (Search & Dropdown Urutkan) --}}
    <div class="flex items-center justify-between gap-3 mb-4">
    
        {{-- Input Pencarian --}}
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
                        placeholder="Cari Transaksi"
                        class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db"
                        value="{{ request('keyword') }}"
                        name="keyword"
                    >
                </form>
            </div>

        {{-- Dropdown Urutkan Popup Timbul --}}
        <div class="relative inline-block text-left" id="containerUrutkan">
            <button
                type="button"
                onclick="toggleDropdownUrutkan(event)"
                class="h-9.5 px-4 bg-white border rounded-lg flex items-center justify-between gap-3 text-sm font-medium text-black hover:bg-gray-100 cursor-pointer shadow-xs"
            >
                <span id="labelSelectedSort">Urutkan: Tanggal</span>
                <svg id="arrowUrutkan" class="w-4 h-4 text-gray-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            {{-- Panel Dropdown Popup --}}
            <div
                id="panelUrutkan"
                class="hidden absolute left-0 mt-2 w-48 bg-secondary-bw rounded-2xl p-2.5 shadow-2xl border border-gray-300 z-50 space-y-2"
            >
                <button
                    type="button"
                    onclick="pilihUrutkan('Tanggal')"
                    class="w-full flex items-center justify-between px-3.5 py-2 border bg-white hover:bg-gray-50 rounded-xl text-sm font-medium text-black text-left cursor-pointer"
                >
                    <span>Tanggal</span>
                    <svg id="checkTanggal" class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </button>

                <button
                    type="button"
                    onclick="pilihUrutkan('Total Tertinggi')"
                    class="w-full flex items-center justify-between px-3.5 py-2 border bg-white hover:bg-gray-50 rounded-xl text-sm font-medium text-black text-left cursor-pointer"
                >
                    <span>Total Tertinggi</span>
                    <svg id="checkTotalTertinggi" class="w-4 h-4 text-gray-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </button>

                <button
                    type="button"
                    onclick="pilihUrutkan('Total Terendah')"
                    class="w-full flex items-center justify-between px-3.5 py-2 border bg-white hover:bg-gray-50 rounded-xl text-sm font-medium text-black text-left cursor-pointer"
                >
                    <span>Total Terendah</span>
                    <svg id="checkTotalTerendah" class="w-4 h-4 text-gray-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </button>
            </div>
        </div>
        </div>

    </div>

    {{-- Tabel Riwayat Transaksi --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden flex flex-col">
        <div class="overflow-y-auto">
            <table class="w-full text-left border-collapse">

                <thead>
                    <tr class="bg-primary-db text-white text-xs font-semibold">
                        <th class="py-2.5 px-4 font-semibold">Kode Transaksi</th>
                        <th class="py-2.5 px-4 font-semibold">Tanggal pembelian</th>
                        <th class="py-2.5 px-4 font-semibold">Total</th>
                        <th class="py-2.5 px-4 font-semibold">Kasir</th>
                        <th class="py-2.5 px-4 font-semibold">Customer</th>
                        <th class="py-2.5 px-4 font-semibold">Sales</th>
                        <th class="py-2.5 px-4 font-semibold">Status</th>
                        <th class="py-2.5 px-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody id="transaksiTableBody" class="divide-y divide-gray-200 text-sm text-black">
                    {{-- Di-render dinamis oleh Vanilla JS atau Blade loop server-side --}}
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="py-3.5 px-4 font-medium">2026-0003</td>
                        <td class="py-3.5 px-4">13/09/2026</td>
                        <td class="py-3.5 px-4 font-semibold">Rp 20.000.000</td>
                        <td class="py-3.5 px-4">Fadil</td>
                        <td class="py-3.5 px-4">David</td>
                        <td class="py-3.5 px-4">Kezia</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-3 py-0.5 rounded-md text-xs font-semibold bg-[#A2E9B0] text-[#14532D]">
                                Selesai
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <button
                                type="button"
                                onclick="openStrukModal()"
                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-gray-800 hover:bg-gray-100 rounded-lg text-xs font-semibold text-black cursor-pointer transition-colors"
                            >
                                <svg class="w-3.5 h-3.5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm0 5.25h.007v.008H3.75V12zm0 5.25h.007v.008H3.75v-.008z" />
                                </svg>
                                <span>Struk</span>
                            </button>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>

        {{-- Pagination Footer --}}
        <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-[#142854]">
            <div>
                Menampilkan <span id="paginationInfo">1-3 dari 3 produk</span>
            </div>

            <div class="flex items-center gap-1.5">
                <button
                    type="button"
                    onclick="prevPage()"
                    class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100 cursor-pointer"
                >
                    ‹
                </button>

                <button
                    type="button"
                    class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-[#142854] text-white border-[#142854]"
                >
                    1
                </button>

                <button
                    type="button"
                    onclick="nextPage()"
                    class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100 cursor-pointer"
                >
                    ›
                </button>
            </div>
        </div>
    </div>

    {{-- Struk Modal (Vanilla JS Modal) --}}
    <div 
        id="modalStrukPembayaran" 
        onclick="closeStrukKlikLuar(event)"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
        
        <!-- Card Modal -->
        <div 
            onclick="event.stopPropagation()"
            class="w-full max-w-sm bg-white rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-100 animate-fade-in">

            <!-- Tampilan Struk (Area yang akan di-print/download) -->
            <div id="areaStrukPrint" class="bg-white p-4 rounded-xl border border-dashed border-gray-300 text-xs font-sans text-gray-800 space-y-3">
                
                <!-- Header Toko -->
                <div class="text-center space-y-1">
                    <h2 class="text-xl font-extrabold text-black tracking-wider uppercase" id="strukNamaToko">NamaToko</h2>
                    <p class="text-[11px] text-gray-500 leading-tight" id="strukAlamatToko">
                        Medokan Ayu, Kec. Rungkut, Surabaya<br>
                        No Telp: 0881-0268-77983
                    </p>
                </div>

                <div class="border-b border-dashed border-gray-300 my-2"></div>

                <!-- Detail Transaksi -->
                <div class="space-y-1 text-gray-600">
                    <div class="flex justify-between">
                        <span>No. Transaksi</span>
                        <span id="strukNoNota" class="font-semibold text-black">2026-0001</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tanggal</span>
                        <span id="strukTanggal">13/09/2026 (23:01)</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Dilayani oleh</span>
                        <span id="strukKasir">User 2</span>
                    </div>
                </div>

                <div class="border-b border-dashed border-gray-300 my-2"></div>

                <!-- Item / Produk -->
                <div id="strukListProduk" class="space-y-2">
                    <!-- Diisi secara dinamis via JavaScript -->
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-semibold text-black">1x NamaProduk</p>
                        </div>
                        <span class="font-semibold text-black">Rp 20.000.000</span>
                    </div>
                </div>

                <div class="border-b border-dashed border-gray-300 my-2"></div>

                <!-- Total & Pembayaran -->
                <div class="space-y-1.5 pt-1">
                    <div class="flex justify-between text-sm font-bold text-black">
                        <span>Total</span>
                        <span id="strukTotal" class="text-base">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Metode pembayaran</span>
                        <span id="strukMetode" class="font-semibold text-black">Tunai</span>
                    </div>
                    <div id="wrapperDetailTunai" class="hidden space-y-1 pt-1 border-t border-gray-100 text-gray-600">
                        <div class="flex justify-between">
                            <span>Dibayar</span>
                            <span id="strukDibayar" class="font-semibold text-black">Rp 0</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kembali</span>
                            <span id="strukKembali" class="font-semibold text-black">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi (Print & Download / Tutup) -->
            <div class="grid grid-cols-2 gap-3 pt-2">
                <button 
                    type="button" 
                    onclick="" 
                    class="w-full bg-white border active:bg-black text-black rounded-xl py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer shadow-sm flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0z" fill="none" />
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15V3m0 12l-4-4m4 4l4-4M2 17l.621 2.485A2 2 0 0 0 4.561 21h14.877a2 2 0 0 0 1.94-1.515L22 17" />
                    </svg>
                    Download
                </button>
                <button 
                    type="button" 
                    onclick="cetakStruk()" 
                    class="w-full bg-primary-db hover:bg-primary-sdb active:bg-black text-white rounded-xl py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print Struk
                </button>
            </div>

        </div>
    </div>

</div>

{{-- Vanilla JavaScript --}}
<script>
    // 1. Function Dropdown Urutkan
    function toggleDropdownUrutkan(event) {
        if (event) event.stopPropagation();
        const panel = document.getElementById('panelUrutkan');
        const arrow = document.getElementById('arrowUrutkan');
        if (panel) {
            panel.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    function tutupDropdownUrutkan() {
        const panel = document.getElementById('panelUrutkan');
        const arrow = document.getElementById('arrowUrutkan');
        if (panel) panel.classList.add('hidden');
        if (arrow) arrow.classList.remove('rotate-180');
    }

    function pilihUrutkan(option) {
        document.getElementById('labelSelectedSort').innerText = 'Urutkan: ' + option;
        
        // Toggle centang
        const checks = {
            'Tanggal': document.getElementById('checkTanggal'),
            'Total Tertinggi': document.getElementById('checkTotalTertinggi'),
            'Total Terendah': document.getElementById('checkTotalTerendah'),
        };

        Object.entries(checks).forEach(([key, el]) => {
            if (el) {
                el.classList.toggle('hidden', key !== option);
            }
        });

        tutupDropdownUrutkan();
    }

    // 2. Function Modal Struk
    function openStrukModal() {

        const modal = document.getElementById('modalStrukPembayaran');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeStrukModal() {
        const modal = document.getElementById('modalStrukPembayaran');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function closeStrukKlikLuar(event) {
        if (event.target.id === 'modalStrukPembayaran') {
            closeStrukModal();
        }
    }

    // Event listener global untuk klik luar & tombol ESC
    document.addEventListener('click', function(event) {
        const container = document.getElementById('containerUrutkan');
        if (container && !container.contains(event.target)) {
            tutupDropdownUrutkan();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            tutupDropdownUrutkan();
            closeStrukModal();
        }
    });
</script>
@endsection