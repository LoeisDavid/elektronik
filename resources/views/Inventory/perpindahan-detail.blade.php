@extends('layouts.app')

@section('title', 'Detail Perpindahan Gudang - Elektronik')
@section('header_title', 'Detail Perpindahan gudang')

@section('content')
@php
    $badgeRP = [
        'Request'    => 'bg-blue-100 border-blue-400 text-blue-700',
        'Pengiriman' => 'bg-amber-100 border-amber-400 text-amber-700',
        'Pending'    => 'bg-amber-100 border-amber-400 text-amber-700',
        'Diterima'   => 'bg-green-100 border-green-600 text-green-800',
        'Ditolak'    => 'bg-red-100 border-red-400 text-red-700',
    ];

    $badgeRPD = [
        'Ready'    => 'bg-blue-50 border-blue-300 text-blue-700',
        'Dikirim'  => 'bg-amber-100 border-amber-400 text-amber-700',
        'Diterima' => 'bg-green-100 border-green-600 text-green-800',
        'Rusak'    => 'bg-orange-100 border-orange-500 text-orange-800',
        'Hilang'   => 'bg-red-100 border-red-500 text-red-800',
    ];

    $tglRequest = $rp->DTRQ ? \Carbon\Carbon::parse($rp->DTRQ)->locale('id')->translatedFormat('d F Y') : '-';
    $tglKirim   = $rp->DTCF1 ? \Carbon\Carbon::parse($rp->DTCF1)->locale('id')->translatedFormat('d F Y') : '-';
    $tglTerima  = $rp->DTCF2 ? \Carbon\Carbon::parse($rp->DTCF2)->locale('id')->translatedFormat('d F Y') : '-';

    $namaRequest  = $rp->userRequest?->DES ?? $rp->RQ ?? '-';
    $namaPengirim = $rp->userConfirm1?->DES ?? $rp->CF1 ?? '-';
    $namaPenerima = $rp->userConfirm2?->DES ?? $rp->CF2 ?? '-';

    $isPengiriman = in_array($rp->SS, ['Pengiriman', 'Pending']);
    $isRequest    = $rp->SS === 'Request';
    $isDiterima   = $rp->SS === 'Diterima';
@endphp

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 space-y-4">
    {{-- TOMBOL KEMBALI --}}
    <div>
        <a
            href="{{ route('inventory.perpindahan') }}"
            class="inline-flex items-center gap-1.5 text-xs md:text-sm font-semibold text-gray-600 hover:text-primary-db transition-colors cursor-pointer"
        >
            <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            <span>Kembali ke Perpindahan</span>
        </a>
    </div>

    {{-- KARTU UTAMA DETAIL PERPINDAHAN --}}
    <div class="bg-white rounded-2xl border border-gray-300 p-6 md:p-8 shadow-xs space-y-6">
        {{-- JUDUL DAN SUBTITLE GUDANG --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900 leading-tight">Perpindahan {{ $rp->RP }}</h1>
            <p class="text-base font-semibold text-gray-800 mt-1">
                {{ $rp->gudangAsal?->DES ?? $rp->GD_ASAL }} - {{ $rp->gudangTujuan?->DES ?? $rp->GD_TUJUAN }}
            </p>
        </div>

        {{-- DAFTAR INFORMASI HEADER --}}
        <div class="border-t border-gray-200 divide-y divide-gray-200 text-sm">
            <div class="py-3 flex items-center justify-between">
                <span class="text-gray-700 font-medium">Tanggal Request Pertama</span>
                <span class="font-semibold text-gray-900">{{ $tglRequest }}</span>
            </div>
            <div class="py-3 flex items-center justify-between">
                <span class="text-gray-700 font-medium">Tanggal Konfirmasi Pertama (Pengiriman)</span>
                <span class="font-semibold text-gray-900">{{ $tglKirim }}</span>
            </div>
            <div class="py-3 flex items-center justify-between">
                <span class="text-gray-700 font-medium">Tanggal Konfirmasi Kedua (Diterima)</span>
                <span class="font-semibold text-gray-900">{{ $tglTerima }}</span>
            </div>
            <div class="py-3 flex items-center justify-between">
                <span class="text-gray-700 font-medium">User Pembuat Request</span>
                <span class="font-semibold text-gray-900">{{ $namaRequest }}</span>
            </div>
            <div class="py-3 flex items-center justify-between">
                <span class="text-gray-700 font-medium">Nama Pengirim (Konfirmasi Pertama)</span>
                <span class="font-semibold text-gray-900">{{ $namaPengirim }}</span>
            </div>
            <div class="py-3 flex items-center justify-between">
                <span class="text-gray-700 font-medium">Nama Penerima (Konfirmasi Kedua)</span>
                <span class="font-semibold text-gray-900">{{ $namaPenerima }}</span>
            </div>
            <div class="py-3 flex items-center justify-between">
                <span class="text-gray-700 font-medium">Status Perpindahan</span>
                <div>
                    <span class="inline-block px-4 py-1 text-xs font-semibold rounded-full border {{ $badgeRP[$rp->SS] ?? 'bg-gray-100 border-gray-400 text-gray-700' }}">
                        {{ $rp->SS === 'Pending' ? 'Pengiriman' : $rp->SS }}
                    </span>
                </div>
            </div>
        </div>

        {{-- SEKSI BARANG YANG DIPINDAH --}}
        <div class="pt-4 space-y-4">
            <h2 class="text-lg font-bold text-gray-900">Barang Yang Dipindah</h2>

            {{-- TOOLBAR: CARI & TOMBOL AKSI KONFIRMASI --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                {{-- FORM SEARCH (hanya untuk mode non-pengiriman / readonly) --}}
                @if(!$isPengiriman)
                <form action="{{ route('inventory.perpindahan.show', $rp->RP) }}" method="GET" class="relative w-full sm:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                        <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="keyword"
                        value="{{ $keyword }}"
                        placeholder="Cari barcode, barang, kategori..."
                        class="w-full bg-white border border-gray-400 rounded-lg pl-9 pr-4 py-2 text-sm text-black placeholder-gray-500 hover:border-primary-db focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db shadow-2xs"
                    >
                </form>
                @else
                <div></div>
                @endif

                {{-- TOMBOL AKSI --}}
                <div class="flex items-center gap-2">
                    {{-- TOMBOL CETAK LAPORAN (BISA KAPANPUN: REQUEST, PENGIRIMAN, DITERIMA) --}}
                    <a
                        href="{{ route('inventory.perpindahan.cetak', $rp->RP) }}"
                        target="_blank"
                        class="h-9.5 px-3.5 bg-white border border-gray-800 hover:bg-gray-100 active:bg-gray-200 text-gray-800 rounded-lg flex items-center gap-1.5 text-xs md:text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs"
                        title="Cetak Laporan Perpindahan Barang"
                    >
                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.673-2.684-1.28-4.329m13.12 4.329c.24-1.076.673-2.684 1.28-4.329m-14.4 0a17.962 17.962 0 0114.4 0m-14.4 0a17.962 17.962 0 0014.4 0M6 18h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zM6 14h12M9 10V6a2 2 0 012-2h2a2 2 0 012 2v4" />
                        </svg>
                        <span>Cetak Laporan</span>
                    </a>

                    @if($isRequest)
                        {{-- KONFIRMASI 1: PENGIRIMAN --}}
                        <form action="{{ route('inventory.perpindahan.konfirmasi1', ['id' => $rp->RP]) }}" method="POST" onsubmit="return confirm('Konfirmasi pengiriman barang dari gudang asal?')">
                            @csrf
                            <button
                                type="submit"
                                class="h-9.5 px-4 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V4.5A2.25 2.25 0 0012 2.25H6.75A2.25 2.25 0 004.5 4.5v9.75" />
                                </svg>
                                <span>Konfirmasi Pengiriman</span>
                            </button>
                        </form>

                        {{-- TOLAK REQUEST --}}
                        <form action="{{ route('inventory.perpindahan.tolak', ['id' => $rp->RP]) }}" method="POST" onsubmit="return confirm('Tolak request perpindahan barang ini?')">
                            @csrf
                            <button
                                type="submit"
                                class="h-9.5 px-3.5 bg-white border border-red-400 hover:bg-red-50 text-red-600 rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-all duration-200 cursor-pointer shadow-2xs"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>Tolak Request</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- ================================================================== --}}
            {{-- MODE PENGIRIMAN: SCAN INPUT + TABEL EDITABLE STATUS (tanpa modal)  --}}
            {{-- ================================================================== --}}
            @if($isPengiriman)
                {{-- INPUT SCAN BARCODE (seperti kasir) --}}
                <div
                    class="flex items-center gap-3 bg-white border border-black rounded-2xl pl-5 pr-3 py-3 transition-all hover:border-primary-db hover:shadow-sm focus-within:border-primary-db focus-within:ring-2 focus-within:ring-primary-db/30 focus-within:shadow-sm"
                >
                    <svg class="w-5 h-5 text-black flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M2 4h2v16H2V4zm3 0h1v16H5V4zm2 0h2v16H7V4zm3 0h1v16h-1V4zm2 0h3v16h-3V4zm4 0h1v16h-1V4zm2 0h2v16h-2V4zm3 0h1v16h-1V4z" />
                    </svg>
                    <input
                        type="text"
                        id="inputScanBarcode"
                        autocomplete="off"
                        placeholder="Scan atau ketik kode barcode barang (BC_OUT), lalu tekan Enter..."
                        onkeydown="handleScanKeyDown(event)"
                        class="flex-1 min-w-0 bg-transparent text-base text-black placeholder-gray-500 outline-none"
                    >
                </div>

                {{-- INFO ITEM TERAKHIR DISCAN --}}
                <div id="scanFeedbackMsg" class="hidden text-xs font-semibold py-2 px-3.5 rounded-lg border flex items-center gap-2"></div>

                {{-- TABEL BARANG EDITABLE STATUS --}}
                <div class="border border-gray-300 rounded-lg overflow-hidden bg-white shadow-2xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-primary-db text-white text-xs font-semibold">
                                    <th class="py-3 px-4 w-10 text-center">No</th>
                                    <th class="py-3 px-4">Kode stok</th>
                                    <th class="py-3 px-4">Nama barang</th>
                                    <th class="py-3 px-4">Kategori</th>
                                    <th class="py-3 px-4 text-center w-56">Status</th>
                                </tr>
                            </thead>
                            <tbody id="konfirmasiItemsBody" class="divide-y divide-gray-200 text-sm text-gray-900">
                                {{-- Dirender oleh JS --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINASI TABEL JS (5 DATA PER HALAMAN) --}}
                    <div class="px-4 py-3 bg-white border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#142854]">
                        <div id="konfirmasiPaginationInfo"></div>
                        <div id="konfirmasiPaginationControls" class="flex items-center gap-1"></div>
                    </div>
                </div>

                <form id="formKonfirmasi2" action="{{ route('inventory.perpindahan.konfirmasi2', ['id' => $rp->RP]) }}" method="POST">
                    @csrf
                    <div id="hiddenInputsWrapper"></div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button
                            type="button"
                            id="btnSubmitKonfirmasi"
                            onclick="submitKonfirmasi2()"
                            class="py-2.5 px-6 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-xs font-bold transition-all duration-200 shadow-xs cursor-pointer flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            <span>Selesaikan Konfirmasi Penerimaan</span>
                        </button>
                    </div>
                </form>

            @else
            {{-- ============================================================ --}}
            {{-- MODE READONLY: TABEL BIASA (Request / Diterima / Ditolak)    --}}
            {{-- ============================================================ --}}
            <div class="border border-gray-300 rounded-lg overflow-hidden bg-white shadow-2xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-primary-db text-white text-xs font-semibold">
                                <th class="py-3 px-4">Kode stok</th>
                                <th class="py-3 px-4">Nama barang</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4 text-center">Tanggal Diterima</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-gray-900">
                            @forelse ($itemsPaginated as $item)
                                @php
                                    $stBadge = $badgeRPD[$item->SS] ?? 'bg-gray-100 border-gray-400 text-gray-700';
                                    $tglItemTerima = ($item->SS === 'Diterima' && $rp->DTCF2)
                                        ? date('d/m/Y', strtotime($rp->DTCF2))
                                        : ($item->SS === 'Diterima' && $item->updated_at ? date('d/m/Y', strtotime($item->updated_at)) : '-');
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3.5 px-4 font-mono text-xs font-semibold text-gray-800">{{ $item->BC_OUT }}</td>
                                    <td class="py-3.5 px-4 font-medium">{{ $item->NAMA_BARANG ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-gray-600">{{ $item->NAMA_KATEGORI ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-center text-xs text-gray-700">{{ $tglItemTerima }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-block min-w-[90px] px-3 py-0.5 text-xs font-semibold rounded-full border {{ $stBadge }}">
                                            {{ $item->SS }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        <p class="font-medium">Tidak ada data barang perpindahan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINASI TABEL --}}
                <div class="px-4 py-3 bg-white border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#142854]">
                    <div>
                        Menampilkan {{ $itemsPaginated->firstItem() ?? 0 }} - {{ $itemsPaginated->lastItem() ?? 0 }} dari {{ $itemsPaginated->total() }} barang
                    </div>
                    <div>
                        {{ $itemsPaginated->onEachSide(1)->links('pagination::simple-tailwind') }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- JAVASCRIPT LOGIKA SCAN & STATUS (HANYA UNTUK MODE PENGIRIMAN) --}}
@if($isPengiriman)
<script>
    // Data seluruh barang kiriman dari server
    const transferItems = @json($allItems ?? []);

    // Konfigurasi Paginasi 5 Data
    const PER_PAGE = 5;
    let currentPage = 1;

    // State per item: { [BC_OUT]: { status: 'Diterima'|'Rusak'|'Hilang'|null, isScanned: boolean } }
    const itemStates = {};

    // Inisialisasi state awal
    transferItems.forEach(item => {
        const existingStatus = ['Diterima', 'Rusak', 'Hilang'].includes(item.SS) ? item.SS : null;
        itemStates[item.BC_OUT] = {
            status: existingStatus,
            isScanned: existingStatus === 'Diterima'
        };
    });

    // Auto-render saat halaman siap
    document.addEventListener('DOMContentLoaded', () => {
        renderKonfirmasiTable();

        // Auto-focus input scan
        const inp = document.getElementById('inputScanBarcode');
        if (inp) inp.focus();
    });

    // Handler Keyboard Enter pada Input Barcode
    function handleScanKeyDown(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const input = document.getElementById('inputScanBarcode');
            if (!input) return;
            const code = input.value.trim();
            if (!code) return;

            prosesBarcodeScanned(code);
            input.value = '';
            input.focus();
        }
    }

    // Memproses Barcode yang Diterima
    function prosesBarcodeScanned(code) {
        const itemIndex = transferItems.findIndex(i => i.BC_OUT.toUpperCase() === code.toUpperCase());

        if (itemIndex === -1) {
            showScanFeedback(`Barcode "${code}" tidak ditemukan dalam daftar perpindahan ini!`, 'error');
            beepError();
            return;
        }

        const matched = transferItems[itemIndex];
        const currentState = itemStates[matched.BC_OUT];

        // Otomatis pindah ke halaman di mana item ini berada
        const targetPage = Math.floor(itemIndex / PER_PAGE) + 1;
        currentPage = targetPage;

        if (currentState && currentState.status === 'Diterima' && currentState.isScanned) {
            showScanFeedback(`Barang ${matched.BC_OUT} (${matched.NAMA_BARANG}) sudah discan sebelumnya.`, 'info');
            renderKonfirmasiTable();
            highlightRow(matched.BC_OUT);
            return;
        }

        // Tandai sebagai Diterima dan terscan
        itemStates[matched.BC_OUT] = {
            status: 'Diterima',
            isScanned: true
        };

        showScanFeedback(`✓ ${matched.BC_OUT} — ${matched.NAMA_BARANG} berhasil discan sebagai Diterima`, 'success');
        beepSuccess();

        renderKonfirmasiTable();
        highlightRow(matched.BC_OUT);
    }

    // Ganti status secara manual (klik tombol di tabel)
    function setItemStatus(bcOut, newStatus) {
        if (!itemStates[bcOut]) return;

        itemStates[bcOut].status = newStatus;
        if (newStatus !== 'Diterima') {
            itemStates[bcOut].isScanned = false;
        }

        renderKonfirmasiTable();
    }

    function renderKonfirmasiTable() {
        const tbody = document.getElementById('konfirmasiItemsBody');
        if (!tbody) return;

        tbody.innerHTML = '';

        const totalItems = transferItems.length;
        const totalPages = Math.max(1, Math.ceil(totalItems / PER_PAGE));
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIndex = (currentPage - 1) * PER_PAGE;
        const endIndex = Math.min(startIndex + PER_PAGE, totalItems);
        const pageItems = transferItems.slice(startIndex, endIndex);

        if (pageItems.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-500 font-medium">
                        Tidak ada barang perpindahan
                    </td>
                </tr>
            `;
        } else {
            pageItems.forEach((item, indexOnPage) => {
                const actualIndex = startIndex + indexOnPage;
                const st = itemStates[item.BC_OUT] || { status: null, isScanned: false };
                const tr = document.createElement('tr');
                tr.id = `row-${item.BC_OUT}`;
                tr.className = `transition-colors ${st.status === 'Diterima' ? 'bg-green-50/50' : (st.status === 'Rusak' ? 'bg-orange-50/50' : (st.status === 'Hilang' ? 'bg-red-50/50' : 'hover:bg-gray-50/80'))}`;

                const isDiterima = st.status === 'Diterima';
                const isRusak = st.status === 'Rusak';
                const isHilang = st.status === 'Hilang';

                tr.innerHTML = `
                    <td class="py-3 px-4 text-center text-gray-500 text-xs font-mono">${actualIndex + 1}</td>
                    <td class="py-3 px-4 font-mono text-xs font-semibold text-gray-800">
                        ${item.BC_OUT}
                        ${st.isScanned ? '<span class="ml-1.5 text-[10px] text-green-700 font-bold bg-green-100 px-1.5 py-0.5 rounded">SCAN</span>' : ''}
                    </td>
                    <td class="py-3 px-4 font-medium text-sm">${item.NAMA_BARANG || '-'}</td>
                    <td class="py-3 px-4 text-sm text-gray-600">${item.NAMA_KATEGORI || '-'}</td>
                    <td class="py-3 px-4 text-center">
                        <div class="inline-flex rounded-lg border border-gray-300 p-0.5 bg-gray-100 text-[11px] font-semibold">
                            <button
                                type="button"
                                onclick="setItemStatus('${item.BC_OUT}', 'Diterima')"
                                class="px-2.5 py-1 rounded-md transition-all cursor-pointer ${isDiterima ? 'bg-green-600 text-white shadow-xs' : 'text-gray-600 hover:text-green-700 hover:bg-green-50'}"
                            >
                                ✓ Diterima
                            </button>
                            <button
                                type="button"
                                onclick="setItemStatus('${item.BC_OUT}', 'Rusak')"
                                class="px-2.5 py-1 rounded-md transition-all cursor-pointer ${isRusak ? 'bg-orange-600 text-white shadow-xs' : 'text-gray-600 hover:text-orange-700 hover:bg-orange-50'}"
                            >
                                Rusak
                            </button>
                            <button
                                type="button"
                                onclick="setItemStatus('${item.BC_OUT}', 'Hilang')"
                                class="px-2.5 py-1 rounded-md transition-all cursor-pointer ${isHilang ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:text-red-700 hover:bg-red-50'}"
                            >
                                Hilang
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        renderPaginationControls(totalItems, totalPages, startIndex, endIndex);
    }

    function renderPaginationControls(totalItems, totalPages, startIndex, endIndex) {
        const info = document.getElementById('konfirmasiPaginationInfo');
        const controls = document.getElementById('konfirmasiPaginationControls');
        if (!info || !controls) return;

        if (totalItems === 0) {
            info.textContent = 'Menampilkan 0 barang';
            controls.innerHTML = '';
            return;
        }

        info.textContent = `Menampilkan ${startIndex + 1} - ${endIndex} dari ${totalItems} barang`;

        let html = '';
        // Tombol Sebelumnya
        if (currentPage > 1) {
            html += `<button type="button" onclick="goToPage(${currentPage - 1})" class="px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">Sebelumnya</button>`;
        } else {
            html += `<span class="px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 text-xs font-medium text-gray-400 cursor-not-allowed">Sebelumnya</span>`;
        }

        // Nomor Halaman
        for (let p = 1; p <= totalPages; p++) {
            if (p === currentPage) {
                html += `<span class="px-3 py-1.5 rounded-lg bg-primary-db text-white text-xs font-bold">${p}</span>`;
            } else {
                html += `<button type="button" onclick="goToPage(${p})" class="px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">${p}</button>`;
            }
        }

        // Tombol Selanjutnya
        if (currentPage < totalPages) {
            html += `<button type="button" onclick="goToPage(${currentPage + 1})" class="px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">Selanjutnya</button>`;
        } else {
            html += `<span class="px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 text-xs font-medium text-gray-400 cursor-not-allowed">Selanjutnya</span>`;
        }

        controls.innerHTML = html;
    }

    function goToPage(page) {
        currentPage = page;
        renderKonfirmasiTable();
    }

    function submitKonfirmasi2() {
        let unresolved = 0;
        let diterima = 0;
        let rusak = 0;
        let hilang = 0;

        transferItems.forEach(item => {
            const st = itemStates[item.BC_OUT];
            if (!st || !st.status) unresolved++;
            else if (st.status === 'Diterima') diterima++;
            else if (st.status === 'Rusak') rusak++;
            else if (st.status === 'Hilang') hilang++;
        });

        if (unresolved > 0) {
            alert(`Masih ada ${unresolved} barang yang belum ditentukan statusnya.\nSilakan scan barcode atau pilih status (Rusak / Hilang) untuk semua barang terlebih dahulu.`);
            return;
        }

        const pesanKonfirmasi = `Konfirmasi penerimaan barang?\n- Diterima: ${diterima}\n- Rusak: ${rusak}\n- Hilang: ${hilang}\n\nStok barang yang berstatus Diterima akan otomatis berpindah ke gudang tujuan.`;
        if (!confirm(pesanKonfirmasi)) return;

        // Inject hidden inputs ke form
        const wrapper = document.getElementById('hiddenInputsWrapper');
        wrapper.innerHTML = '';

        transferItems.forEach(item => {
            const st = itemStates[item.BC_OUT];
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = `items[${item.BC_OUT}]`;
            inp.value = st.status;
            wrapper.appendChild(inp);
        });

        document.getElementById('formKonfirmasi2').submit();
    }

    function showScanFeedback(text, type) {
        const msg = document.getElementById('scanFeedbackMsg');
        if (!msg) return;

        if (type === 'success') {
            msg.className = 'text-xs font-semibold py-2 px-3.5 rounded-lg border flex items-center gap-2 bg-green-100 text-green-800 border-green-300';
        } else if (type === 'error') {
            msg.className = 'text-xs font-semibold py-2 px-3.5 rounded-lg border flex items-center gap-2 bg-red-100 text-red-800 border-red-300';
        } else {
            msg.className = 'text-xs font-semibold py-2 px-3.5 rounded-lg border flex items-center gap-2 bg-blue-100 text-blue-800 border-blue-300';
        }

        msg.textContent = text;
    }

    function highlightRow(bcOut) {
        const row = document.getElementById(`row-${bcOut}`);
        if (!row) return;
        row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        row.classList.add('ring-2', 'ring-primary-db');
        setTimeout(() => row.classList.remove('ring-2', 'ring-primary-db'), 1500);
    }

    function beepSuccess() {
        try {
            const AC = window.AudioContext || window.webkitAudioContext;
            const ctx = new AC(), osc = ctx.createOscillator();
            osc.frequency.value = 1200;
            osc.connect(ctx.destination);
            osc.start();
            setTimeout(() => { osc.stop(); ctx.close(); }, 100);
        } catch (e) {}
    }

    function beepError() {
        try {
            const AC = window.AudioContext || window.webkitAudioContext;
            const ctx = new AC(), osc = ctx.createOscillator();
            osc.frequency.value = 400;
            osc.connect(ctx.destination);
            osc.start();
            setTimeout(() => { osc.stop(); ctx.close(); }, 200);
        } catch (e) {}
    }
</script>
@endif
@endsection
