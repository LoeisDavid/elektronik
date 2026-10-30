{{--
    =========================================================================
    HALAMAN: Cek Stok (Opname Stok)
    =========================================================================
    Simpan di: resources/views/inventory/opname-stok.blade.php

    Alur (SATU file untuk dua kondisi):
    1. route('inventory.opname') -> index() render view ini TANPA $items
       -> tampil layar pembukaan dengan tombol "Mulai".
    2. Tombol Mulai -> route('inventory.opname.pull') -> pull() render view ini
       DENGAN $items -> tampil tabel Cek Stok.
    3. SEMUA perubahan status (checking -> belum_terjual) HANYA terjadi di
       browser (JavaScript, in-memory). Tidak ada AJAX, tidak ada simpan ke DB.
    4. Search & filter status murni JavaScript.
    5. Daftar SELALU diurutkan: Checking -> Belum Terjual -> Sudah Terjual.
    6. Selesai -> ringkasan -> Tutup -> redirect ke route('inventory.gudang').

    INPUT KODE (semua diperlakukan sebagai STRING, bukan angka):
      - Scanner fisik (keyboard wedge): mengetik ke kolom teks lalu Enter.
      - Kamera live (HTTPS / localhost) lewat partial barcode-camera.
      - Foto barcode (kamera HP, jalan di HTTP biasa) lewat partial yang sama.

    Palet warna:
      Kuning  #F2B80C | Hijau #327437 | Abu #6B7280
      Merah   #D02231 | Navy gelap #0D1C43 | Navy #22396F
    Font: Montserrat
    =========================================================================
--}}

@extends('layouts.app') {{-- sesuaikan nama layout kalau bukan 'layouts.app' --}}

@section('title', 'Cek Stok')
@section('header_title', 'Cek Stok')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .font-mont { font-family: 'Montserrat', ui-sans-serif, system-ui, sans-serif; }

    /* Scrollbar tipis biar rapi */
    #opnameScroll::-webkit-scrollbar { width: 8px; height: 8px; }
    #opnameScroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    #opnameScroll::-webkit-scrollbar-track { background: transparent; }

    .row-flash { animation: rowFlash 1s ease-out; }
    @keyframes rowFlash {
        0%   { background-color: #F2B80C55; }
        100% { background-color: transparent; }
    }

    .modal-backdrop { background: rgba(13, 28, 67, 0.55); }

    /* Preview kamera live: pastikan video memenuhi lebar kotak */
    .bcam video { width: 100% !important; object-fit: cover; }
</style>
@endpush

@section('content')
@if (!isset($items))

{{-- ================= LAYAR PEMBUKAAN: TABEL PILIH BARANG ================= --}}
<div class="font-mont flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 space-y-4">

    @if (session('error'))
        <div class="px-4 py-2.5 rounded-xl border border-[#D02231] bg-[#D02231]/10 text-[#D02231] text-sm font-medium shrink-0">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header & Search Bar --}}
    <div class="flex flex-wrap items-center justify-between gap-3 shrink-0">
        <div>
            <h1 class="text-lg font-bold text-black tracking-tight">
                Pilih Barang Cek Stok (Opname Stok)
            </h1>
            <p class="text-xs text-[#6B7280]">
                Cari dan pilih barang pada tabel di bawah, lalu klik tombol aksi <strong>Mulai Opname</strong>.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <form action="{{ route('inventory.opname') }}" method="GET" id="formCariBarangOpname" class="relative w-64 md:w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-black">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </span>
                <input
                    type="text"
                    name="keyword"
                    id="inputCariBarangOpname"
                    value="{{ $keyword ?? request('keyword') }}"
                    autocomplete="off"
                    placeholder="Cari kode, nama barang, merk..."
                    class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:border-primary-db focus:bg-primary-db/5 focus:ring-1 focus:ring-primary-db"
                >
            </form>

            @if(!empty($keyword ?? request('keyword')))
                <a
                    href="{{ route('inventory.opname') }}"
                    class="h-9.5 px-3.5 bg-white border border-gray-800 hover:bg-gray-100 text-black rounded-lg flex items-center text-xs font-semibold transition-all duration-200 cursor-pointer shadow-xs"
                >
                    Reset
                </a>
            @endif

            <a
                href="{{ route('inventory.gudang') }}"
                class="h-9.5 px-3.5 bg-white border border-gray-400 hover:bg-gray-200 text-black rounded-lg flex items-center gap-1.5 text-xs font-semibold transition-all duration-200 cursor-pointer shadow-xs"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Gudang</span>
            </a>
        </div>
    </div>

    {{-- Tabel Daftar Barang --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="bg-primary-db text-white text-xs font-semibold">
                        <th class="py-3 px-4 text-left rounded-tl-lg">Kode Barang</th>
                        <th class="py-3 px-4 text-left">Nama Barang</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Merk</th>
                        <th class="py-3 px-4">Stok</th>
                        <th class="py-3 px-4 w-40 text-center rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodyPilihBarangOpname" class="divide-y divide-gray-200 text-xs text-black">
                    @forelse ($daftarItem as $it)
                        <tr class="row-pilih-opname hover:bg-gray-50/80 transition-colors">
                            <td class="py-3.5 px-4 text-left font-mono font-semibold text-[#0D1C43]">
                                {{ $it->ITM }}
                            </td>
                            <td class="py-3.5 px-4 text-left font-medium text-black">
                                {{ $it->DES }}
                            </td>
                            <td class="py-3.5 px-4 text-gray-700">
                                {{ $it->ktg_nama ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-gray-700">
                                {{ $it->mrk_nama ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold">
                                {{ (int) ($it->STOK ?? 0) }} Unit
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a
                                    href="{{ route('inventory.opname.pull', ['itm' => $it->ITM]) }}"
                                    class="btn-mulai-opname-row inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-[#0D1C43] hover:bg-[#22396F] active:bg-dark-navy transition shadow-xs cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                    <span>Mulai Opname</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr id="rowEmptyServerOpname">
                            <td colspan="6" class="py-8 text-center text-gray-500 text-sm">
                                Tidak ada data barang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$daftarItem" label="barang" />
    </div>
</div>

@else

@php
    // Hitungan awal dari server, supaya chip langsung terisi
    // walaupun JavaScript belum / gagal jalan.
    $koleksi    = collect($items);
    $cTotal     = $koleksi->count();
    $cSudah     = $koleksi->where('status', 'sudah_terjual')->count();
    $cBelum     = $koleksi->where('status', 'belum_terjual')->count();
    $cChecking  = $cTotal - $cSudah - $cBelum;
@endphp

<div class="font-mont flex-1 flex flex-col h-full min-h-0 bg-[#F7F8FD]">

    @if(isset($selectedItem))
    <div class="px-6 pt-3 pb-2 shrink-0 flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 bg-white">
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-gray-500">Barang yang dicek:</span>
            <span class="text-sm font-bold text-[#0D1C43]">{{ $selectedItem->DES }}</span>
            <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-mono font-medium border border-gray-300">{{ $selectedItem->ITM }}</span>
        </div>
        <a href="{{ route('inventory.opname') }}" class="text-xs font-semibold text-[#0D1C43] hover:text-[#22396F] hover:underline flex items-center gap-1 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Ganti Barang
        </a>
    </div>
    @endif

    {{-- ================= TOP BAR: CHIP STATISTIK + TOMBOL ================= --}}
    <div class="flex flex-wrap items-center justify-between gap-3 px-6 pt-4 shrink-0">

        {{-- CHIP --}}
        <div class="flex flex-wrap items-center gap-2" id="statChips">

            <div class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full border border-[#22396F] bg-[#22396F]/10 text-[#22396F] text-xs font-semibold">
                <span>Total Produk</span>
                <span id="chipTotal" class="min-w-[28px] text-center px-2 py-0.5 rounded-full bg-[#22396F] text-white">{{ $cTotal }}</span>
            </div>

            <div class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full border border-[#327437] bg-[#327437]/10 text-[#327437] text-xs font-semibold">
                <span>Produk Terjual</span>
                <span id="chipSudah" class="min-w-[28px] text-center px-2 py-0.5 rounded-full bg-[#327437] text-white">{{ $cSudah }}</span>
            </div>

            <div class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full border border-[#F2B80C] bg-[#F2B80C]/10 text-[#0D1C43] text-xs font-semibold">
                <span>Produk Belum Terjual</span>
                <span id="chipBelum" class="min-w-[28px] text-center px-2 py-0.5 rounded-full bg-[#F2B80C] text-[#0D1C43]">{{ $cBelum }}</span>
            </div>

            <div class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1 rounded-full border border-[#6B7280] bg-gray-100 text-[#6B7280] text-xs font-semibold">
                <span>Produk Checking</span>
                <span id="chipChecking" class="min-w-[28px] text-center px-2 py-0.5 rounded-full bg-[#6B7280] text-white">{{ $cChecking }}</span>
            </div>

        </div>

        {{-- TOMBOL --}}
        <div class="flex items-center gap-2">
            <button id="btnSelesai"
                class="px-4 py-1.5 rounded-md text-xs font-semibold text-[#D02231] border border-[#D02231] bg-white hover:bg-[#D02231]/10 transition">
                Selesai
            </button>
            <button id="btnScan"
                class="px-4 py-1.5 rounded-md text-xs font-semibold text-white bg-[#0D1C43] hover:bg-[#22396F] transition shadow-sm">
                Scan / Input Kode
            </button>
        </div>
    </div>

    {{-- ================= SEARCH & FILTER (murni JS) ================= --}}
    <div class="flex items-center gap-3 px-6 pt-3 shrink-0">
        <div class="relative w-64 md:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-black">
                <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </span>
            <input
                type="text"
                id="inputCari"
                autocomplete="off"
                placeholder="Cari kode / nama barang"
                class="w-full h-10.5 bg-white border border-gray-400 rounded-xl pl-11 pr-4 text-sm text-black placeholder-gray-500 transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:border-primary-db focus:bg-primary-db/5 focus:ring-1 focus:ring-primary-db"
            >
        </div>

        <div class="relative w-48">
            <select
                id="filterStatus"
                class="w-full h-10.5 bg-white border border-gray-800 rounded-xl px-3.5 text-sm font-medium text-black transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db cursor-pointer shadow-xs"
            >
                <option value="semua">Status: Semua</option>
                <option value="checking">Status: Checking</option>
                <option value="belum_terjual">Status: Belum Terjual</option>
                <option value="sudah_terjual">Status: Sudah Terjual</option>
            </select>
        </div>
    </div>

    {{-- ================= TABLE (VIRTUAL SCROLL) ================= --}}
    <div class="flex-1 min-h-0 px-6 py-3">
        <div id="opnameScroll"
             class="relative h-full overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-sm">

            {{-- Header sticky --}}
            <div class="sticky top-0 z-20 grid grid-cols-3 bg-[#22396F] text-white text-xs font-semibold text-center">
                <div class="px-4 py-3 min-w-0 truncate">Kode Barcode</div>
                <div class="px-4 py-3 min-w-0 truncate">Nama Barang</div>
                <div class="px-4 py-3 min-w-0 truncate">Status</div>
            </div>

            {{-- Spacer total tinggi semua baris --}}
            <div id="virtualSpacer" class="relative">
                <div id="virtualRows" class="absolute left-0 right-0"></div>
            </div>

            {{-- Kondisi kosong --}}
            <div id="emptyState" class="hidden text-center text-sm text-[#6B7280] py-10">
                Tidak ada data untuk di-opname.
            </div>
        </div>
    </div>
</div>

{{-- Floating Toast untuk Feedback Scan Langsung (tanpa modal) --}}
<div id="opnameToast" class="hidden fixed top-5 left-1/2 -translate-x-1/2 z-[70] px-5 py-3 rounded-xl border-2 shadow-2xl font-mont text-xs md:text-sm font-semibold flex items-center gap-2 transition-all duration-200"></div>

{{-- ================= MODAL: SCAN / INPUT KODE ================= --}}
<div id="modalScan" class="font-mont hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl p-6 max-h-[92vh] overflow-y-auto border border-gray-200 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-[#0D1C43]">Scan Barcode / Kamera Opname Stok</h2>
            <button id="btnCloseScan" type="button" class="text-gray-400 hover:text-[#0D1C43] text-2xl font-bold leading-none cursor-pointer">&times;</button>
        </div>

        {{-- Kamera: live (HTTPS/localhost) atau foto (HTTP) --}}
        <div class="w-full">
            @include('inventory.partials.barcode-camera', ['cameraId' => 'opname'])
        </div>

        {{-- Scanner fisik / ketik manual. type="text": kode diterima sebagai string --}}
        <form id="formScan" class="space-y-3">
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    id="inputKode"
                    inputmode="text"
                    autocomplete="off"
                    autocapitalize="off"
                    spellcheck="false"
                    placeholder="Scan barcode atau ketik kode manual..."
                    class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-[#0D1C43] placeholder-[#6B7280] transition-all duration-200 hover:border-[#22396F] hover:bg-[#0D1C43]/5 focus:outline-none focus:ring-2 focus:ring-[#22396F]/40 focus:border-[#22396F]"
                >
                <button type="submit" class="bg-[#0D1C43] hover:bg-[#22396F] text-white text-sm font-semibold px-6 py-2.5 rounded-xl cursor-pointer transition-colors">
                    Cek Kode
                </button>
            </div>
        </form>

        {{-- Feedback hasil scan terakhir --}}
        <div id="scanFeedback" class="hidden mt-4 text-xs rounded-md px-4 py-3"></div>

        <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-3 gap-2 text-center text-[11px]">
            <div>
                <div class="font-semibold text-[#6B7280]" id="fbCountChecking">0</div>
                <div class="text-[#6B7280]">Belum Terscan</div>
            </div>
            <div>
                <div class="font-semibold text-[#F2B80C]" id="fbCountBelum">0</div>
                <div class="text-[#F2B80C]">Belum Terjual</div>
            </div>
            <div>
                <div class="font-semibold text-[#327437]" id="fbCountSudah">0</div>
                <div class="text-[#327437]">Sudah Terjual</div>
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL: KONFIRMASI SELESAI ================= --}}
<div id="modalConfirm" class="font-mont hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop">
    <div class="bg-white w-full max-w-sm rounded-xl shadow-xl p-6 text-center">
        <div class="w-10 h-10 mx-auto rounded-full border-2 border-[#F2B80C] flex items-center justify-center mb-3">
            <span class="text-[#F2B80C] text-xl font-bold leading-none">!</span>
        </div>
        <h2 class="text-sm font-bold text-[#0D1C43] mb-1">Akhiri Sesi Cek Stok?</h2>
        <p class="text-xs text-[#6B7280] mb-5">
            Setelah selesai, sesi cek stok ini dianggap berakhir. Pastikan semua barang sudah di-scan.
        </p>
        <div class="flex gap-3">
            <button id="btnBatalSelesai" class="flex-1 py-2 rounded-md text-xs font-semibold border border-[#0D1C43] text-[#0D1C43] bg-white hover:bg-gray-300 active:bg-gray-200 transition-all duration-200">
                Batal
            </button>
            <button id="btnYaSelesai" class="flex-1 py-2 rounded-md text-xs font-semibold border border-[#D02231] bg-[#D02231]/10 text-[#D02231] hover:bg-[#D02231]/20">
                Ya, Selesai
            </button>
        </div>
    </div>
</div>

{{-- ================= MODAL: RINGKASAN HASIL ================= --}}
<div id="modalSummary" class="font-mont hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop">
    <div class="bg-white w-full max-w-md rounded-xl shadow-xl p-6">
        <h2 class="text-base font-bold text-[#0D1C43] mb-1">Ringkasan Cek Stok</h2>
        <p class="text-xs text-[#6B7280] mb-5">Data ini hanya ditampilkan di sesi ini dan tidak disimpan otomatis.</p>

        <div class="space-y-2">
            <div class="flex items-center justify-between px-4 py-2.5 rounded-md border border-[#F2B80C] bg-[#F2B80C]/10">
                <span class="text-xs text-[#F2B80C] font-medium">Belum Terjual (ditemukan saat scan)</span>
                <span class="text-xs font-semibold text-[#F2B80C]" id="sumBelum">0</span>
            </div>
            <div class="flex items-center justify-between px-4 py-2.5 rounded-md border border-[#327437] bg-[#327437]/20">
                <span class="text-xs text-[#327437] font-medium">Sudah Terjual</span>
                <span class="text-xs font-semibold text-[#327437]" id="sumSudah">0</span>
            </div>
            <div class="flex items-center justify-between px-4 py-2.5 rounded-md border border-[#6B7280] bg-gray-100">
                <span class="text-xs text-[#6B7280] font-medium">Tidak Terscan (Checking)</span>
                <span class="text-xs font-semibold text-[#6B7280]" id="sumChecking">0</span>
            </div>
            <div class="flex items-center justify-between px-1 pt-2">
                <span class="text-xs text-[#0D1C43]">Total Data</span>
                <span class="text-xs text-[#0D1C43] font-semibold" id="sumTotal">0</span>
            </div>
        </div>

        <div class="mt-5 flex items-center gap-3">
            <button id="btnTutupSummary" type="button" class="flex-1 py-2.5 rounded-md text-xs font-semibold border border-gray-300 text-[#0D1C43] bg-white hover:bg-gray-100 cursor-pointer transition">
                Tutup
            </button>
            <button id="btnPrintSummary" type="button" class="flex-1 py-2.5 rounded-md text-xs font-semibold bg-[#0D1C43] text-white hover:bg-[#22396F] flex items-center justify-center gap-1.5 cursor-pointer transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
        </div>
    </div>
</div>

<form id="formPrintOpname" action="{{ route('inventory.opname.cetak') }}" method="POST" target="_blank" class="hidden">
    @csrf
    <input type="hidden" name="payload" id="printPayload">
</form>
@endif
@endsection

@push('scripts')
@if (!isset($items))
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-mulai-opname-row').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (this.dataset.loading) {
                e.preventDefault();
                return;
            }
            this.dataset.loading = '1';
            this.innerHTML = '<span>Memuat...</span>';
            this.classList.add('opacity-70', 'cursor-wait');
        });
    });
});
</script>
@else
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================================
       1. DATA — dikirim dari controller lewat $items, murni diolah di JS
    ===================================================================== */
    const rawData = @json($items ?? []);

    // Normalisasi + kasih index unik.
    // kode_barcode SELALU String (leading zero & huruf tetap utuh).
    const stokData = rawData.map((item, idx) => ({
        idx,
        kode_barcode: String(item.kode_barcode ?? '').trim(),
        nama_barang: item.nama_barang ?? '-',
        status: ['checking', 'sudah_terjual', 'belum_terjual'].includes(item.status)
            ? item.status
            : 'checking',
    }));

    // Escape HTML: data masuk lewat innerHTML pada virtual scroll
    const esc = (v) => String(v).replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    // Bersihkan karakter kontrol (sebagian scanner menambah prefix/suffix)
    const bersihkan = (v) => String(v ?? '').replace(/[\u0000-\u001F\u007F]/g, '').trim();

    // Data yang sedang ditampilkan (hasil search + filter + urut)
    let tampil = stokData.slice();

    // Urutan status: checking -> belum terjual -> sudah terjual
    const URUTAN_STATUS = { checking: 0, belum_terjual: 1, sudah_terjual: 2 };

    function urutkanData(arr) {
        return arr.sort((a, b) =>
            (URUTAN_STATUS[a.status] - URUTAN_STATUS[b.status]) || (a.idx - b.idx)
        );
    }

    let sesiSelesai = false;   // flag apakah sesi sudah "diselesaikan"
    let pendingNavHref = null; // tujuan setelah konfirmasi + ringkasan
    const URL_GUDANG = @json(route('inventory.gudang')); // tujuan setelah cek stok selesai

    /* =====================================================================
       2. VIRTUAL SCROLL TABLE
    ===================================================================== */
    const ROW_HEIGHT = 40;
    const scrollBox   = document.getElementById('opnameScroll');
    const spacer      = document.getElementById('virtualSpacer');
    const rowsWrap    = document.getElementById('virtualRows');
    const emptyState  = document.getElementById('emptyState');
    const HEADER_H    = 40; // tinggi header sticky (approx)

    // Chip statistik di bagian atas
    const chipTotal    = document.getElementById('chipTotal');
    const chipSudah    = document.getElementById('chipSudah');
    const chipBelum    = document.getElementById('chipBelum');
    const chipChecking = document.getElementById('chipChecking');

    const BADGE_BASE = 'inline-block min-w-[112px] px-3 py-0.5 text-xs font-semibold rounded border';

    const badge = (status) => {
        if (status === 'belum_terjual') {
            return `<span class="${BADGE_BASE} border-[#F2B80C] bg-[#F2B80C]/10 text-[#F2B80C]">Belum Terjual</span>`;
        }
        if (status === 'sudah_terjual') {
            return `<span class="${BADGE_BASE} border-[#327437] bg-[#327437]/20 text-[#327437]">Sudah Terjual</span>`;
        }
        return `<span class="${BADGE_BASE} border-[#6B7280] bg-white text-[#6B7280]">Checking</span>`;
    };

    function renderVisibleRows() {
        const total = tampil.length;
        emptyState.textContent = stokData.length === 0
            ? 'Tidak ada data untuk di-opname.'
            : 'Tidak ada data yang cocok.';
        emptyState.classList.toggle('hidden', total > 0);
        spacer.style.height = (total * ROW_HEIGHT) + 'px';

        if (total === 0) { rowsWrap.innerHTML = ''; updateHeaderCounter(); return; }

        // Fallback kalau tinggi container belum terhitung (0) supaya tetap ada baris yang dirender
        const viewportH = Math.max(scrollBox.clientHeight - HEADER_H, 400);
        const scrollTop = Math.max(0, scrollBox.scrollTop - HEADER_H);

        const buffer = 8;
        let start = Math.floor(scrollTop / ROW_HEIGHT) - buffer;
        let visibleCount = Math.ceil(viewportH / ROW_HEIGHT) + buffer * 2;
        start = Math.max(0, start);
        const end = Math.min(total, start + visibleCount);

        let html = '';
        for (let i = start; i < end; i++) {
            const item = tampil[i];
            html += `
                <div class="grid grid-cols-3 items-center text-center text-xs border-b border-gray-300 data-row"
                     data-idx="${item.idx}" style="height:${ROW_HEIGHT}px;">
                    <div class="px-4 min-w-0 truncate whitespace-nowrap text-[#0D1C43]" title="${esc(item.kode_barcode)}">${esc(item.kode_barcode)}</div>
                    <div class="px-4 min-w-0 truncate whitespace-nowrap text-[#0D1C43]" title="${esc(item.nama_barang)}">${esc(item.nama_barang)}</div>
                    <div class="px-4 min-w-0 truncate">${badge(item.status)}</div>
                </div>`;
        }
        rowsWrap.style.transform = `translateY(${start * ROW_HEIGHT}px)`;
        rowsWrap.innerHTML = html;

        updateHeaderCounter();
    }

    scrollBox.addEventListener('scroll', () => window.requestAnimationFrame(renderVisibleRows));
    window.addEventListener('resize', renderVisibleRows);

    // posisi = urutan baris pada daftar yang sedang ditampilkan (tampil)
    function scrollToIndex(posisi) {
        const targetTop = Math.max(0, (posisi * ROW_HEIGHT) - (scrollBox.clientHeight / 2));
        scrollBox.scrollTop = targetTop;
        renderVisibleRows();
        // efek flash sebentar biar user tau baris mana yang barusan berubah
        const idx = tampil[posisi].idx;
        setTimeout(() => {
            const el = rowsWrap.querySelector(`[data-idx="${idx}"]`);
            if (el) {
                el.classList.add('row-flash');
                setTimeout(() => el.classList.remove('row-flash'), 1000);
            }
        }, 50);
    }

    /* =====================================================================
       2b. SEARCH & FILTER STATUS + URUTAN (murni JS)
    ===================================================================== */
    const inputCari    = document.getElementById('inputCari');
    const filterStatus = document.getElementById('filterStatus');

    function terapkanFilter(resetScroll = true) {
        const kw = inputCari.value.trim().toLowerCase();
        const st = filterStatus.value;

        tampil = urutkanData(
            stokData.filter(d =>
                (st === 'semua' || d.status === st) &&
                (!kw ||
                    d.kode_barcode.toLowerCase().includes(kw) ||
                    String(d.nama_barang).toLowerCase().includes(kw))
            )
        );

        if (resetScroll) scrollBox.scrollTop = 0;
        renderVisibleRows();
    }

    let cariTimer = null;
    inputCari.addEventListener('input', () => {
        clearTimeout(cariTimer);
        cariTimer = setTimeout(() => terapkanFilter(), 150);
    });
    filterStatus.addEventListener('change', () => terapkanFilter());
    if (typeof jQuery !== 'undefined') {
        $(filterStatus).on('change', () => terapkanFilter());
    }

    /* =====================================================================
       3. HITUNG STATUS + UPDATE CHIP
    ===================================================================== */
    function hitungStatus() {
        const hasil = { checking: 0, belum_terjual: 0, sudah_terjual: 0 };
        stokData.forEach(item => hasil[item.status]++);
        return hasil;
    }

    function updateHeaderCounter() {
        const h = hitungStatus();
        if (chipTotal)    chipTotal.textContent    = stokData.length;
        if (chipSudah)    chipSudah.textContent    = h.sudah_terjual;
        if (chipBelum)    chipBelum.textContent    = h.belum_terjual;
        if (chipChecking) chipChecking.textContent = h.checking;
    }

    function updateModalScanCounter() {
        const h = hitungStatus();
        document.getElementById('fbCountChecking').textContent = h.checking;
        document.getElementById('fbCountBelum').textContent = h.belum_terjual;
        document.getElementById('fbCountSudah').textContent = h.sudah_terjual;
    }

    /* =====================================================================
       INIT TABEL — dijalankan SEGERA (sebelum modal/kamera di-setup)
       supaya kalau bagian lain error, tabel & chip tetap tampil.
    ===================================================================== */
    try {
        terapkanFilter();
    } catch (err) {
        console.error('Gagal merender tabel cek stok:', err);
        emptyState.textContent = 'Gagal menampilkan data. Coba muat ulang halaman.';
        emptyState.classList.remove('hidden');
    }

    /* =====================================================================
       4. MODAL: SCAN / INPUT KODE (scanner fisik + kamera)
    ===================================================================== */
    const modalScan    = document.getElementById('modalScan');
    const inputKode    = document.getElementById('inputKode');
    const formScan     = document.getElementById('formScan');
    const scanFeedback = document.getElementById('scanFeedback');
    const camOpname    = document.getElementById('bcam-opname'); // bisa null kalau partial tidak merender id ini

    function openScanModal() {
        modalScan.classList.remove('hidden');
        updateModalScanCounter();
        scanFeedback.classList.add('hidden');
        inputKode.value = '';
        setTimeout(() => inputKode.focus(), 50);
    }
    function closeScanModal() {
        modalScan.classList.add('hidden');
        // matikan kamera supaya lampu kamera padam
        if (window.BarcodeCamera) BarcodeCamera.stop('opname');
    }

    let toastTimer = null;
    function showFeedback(type, message) {
        const styles = {
            success: 'bg-[#327437]/10 text-[#327437] border border-[#327437]',
            warning: 'bg-[#D02231]/10 text-[#D02231] border border-[#D02231]',
            info:    'bg-[#22396F]/10 text-[#22396F] border border-[#22396F]',
            error:   'bg-gray-100 text-[#6B7280] border border-[#6B7280]',
        };
        const toastStyles = {
            success: 'bg-white text-[#327437] border-[#327437]',
            warning: 'bg-white text-[#D02231] border-[#D02231]',
            info:    'bg-white text-[#22396F] border-[#22396F]',
            error:   'bg-white text-[#6B7280] border-[#6B7280]',
        };

        if (scanFeedback) {
            scanFeedback.className = 'mt-4 text-xs rounded-md px-4 py-3 ' + styles[type];
            scanFeedback.textContent = message;
            scanFeedback.classList.remove('hidden');
        }

        // Tampilkan feedback mengambang (toast) di halaman utama
        const toast = document.getElementById('opnameToast');
        if (toast) {
            toast.className = 'fixed top-5 left-1/2 -translate-x-1/2 z-[70] px-5 py-3 rounded-xl border-2 shadow-2xl font-mont text-xs md:text-sm font-semibold flex items-center gap-2 transition-all duration-200 ' + toastStyles[type];
            toast.textContent = message;
            toast.classList.remove('hidden');

            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }
    }

    function prosesKode(kodeInput) {
        let kode = bersihkan(kodeInput);
        if (!kode) return;

        // 1. Cek langsung dengan kode apa adanya (seperti input manual)
        let item = stokData.find(
            d => d.kode_barcode.toLowerCase() === kode.toLowerCase()
        );

        // 2. Jika datanya gak ketemu, coba hapus char paling depan dan paling belakang
        if (!item && kode.length >= 3) {
            const stripped = kode.slice(1, -1);
            const itemCandidate = stokData.find(
                d => d.kode_barcode.toLowerCase() === stripped.toLowerCase()
            );
            if (itemCandidate) {
                item = itemCandidate;
                kode = stripped;
            }
        }

        if (!item) {
            showFeedback('error', `❌ Kode "${kode}" tidak ditemukan dalam daftar cek stok.`);
            return;
        }

        if (item.status === 'sudah_terjual') {
            showFeedback('warning', `⚠️ ${item.kode_barcode} - ${item.nama_barang}: barang ini SUDAH TERJUAL. Mohon cek kembali fisik barang!`);
        } else if (item.status === 'belum_terjual') {
            showFeedback('info', `ℹ️ ${item.kode_barcode} - ${item.nama_barang} sudah pernah discan sebelumnya (Belum Terjual).`);
        } else {
            // status masih 'checking' -> ubah jadi belum_terjual
            item.status = 'belum_terjual';
            showFeedback('success', `✅ ${item.kode_barcode} - ${item.nama_barang}: status diubah menjadi Belum Terjual.`);
        }

        updateModalScanCounter();

        // hitung ulang + urutkan ulang daftar tampil, tanpa reset scroll
        terapkanFilter(false);

        // lompat ke baris itu kalau memang sedang tampil (lolos search/filter)
        const posisi = tampil.indexOf(item);
        if (posisi !== -1) scrollToIndex(posisi);
    }

    document.getElementById('btnScan').addEventListener('click', openScanModal);
    document.getElementById('btnCloseScan').addEventListener('click', closeScanModal);
    modalScan.addEventListener('click', (e) => { if (e.target === modalScan) closeScanModal(); });

    // Scanner fisik / ketik manual di modal
    formScan.addEventListener('submit', function (e) {
        e.preventDefault();
        e.stopPropagation(); // Cegah bubble ke global anti-double-submit di app.blade.php
        const kodeValue = inputKode.value;
        inputKode.value = '';
        try {
            prosesKode(kodeValue);
        } catch (err) {
            console.error('Error prosesKode:', err);
        }
        // Re-focus input untuk scan berikutnya
        inputKode.focus();
    });

    // Hasil dari kamera (live maupun foto): langsung diproses sebagai string
    if (camOpname) {
        camOpname.addEventListener('barcode:scan', function (e) {
            prosesKode(e.detail.code);
        });
    } else {
        console.warn('Elemen #bcam-opname tidak ditemukan; scan lewat kamera dinonaktifkan. Cek partial barcode-camera.');
    }

    // =========================================================================
    // SCAN LANGSUNG DARI HALAMAN UTAMA (tanpa perlu klik tombol scan / buka modal)
    // =========================================================================
    let directScanBuffer = '';
    let directScanTimer = null;

    document.addEventListener('keydown', function (e) {
        if (e.ctrlKey || e.altKey || e.metaKey) return;

        // Jika modal scan sedang terbuka, biarkan input modal yang menangani
        if (!modalScan.classList.contains('hidden')) {
            if (e.key === 'Escape') closeScanModal();
            return;
        }

        // Jika sedang fokus di input pencarian tabel (inputCari)
        if (document.activeElement === inputCari) {
            if (e.key === 'Enter') {
                const val = inputCari.value.trim();
                if (val) {
                    const matchDirect = stokData.some(d => d.kode_barcode.toLowerCase() === val.toLowerCase());
                    const matchStripped = val.length >= 3 && stokData.some(d => d.kode_barcode.toLowerCase() === val.slice(1, -1).toLowerCase());
                    if (matchDirect || matchStripped) {
                        e.preventDefault();
                        inputCari.value = '';
                        terapkanFilter();
                        prosesKode(val);
                        return;
                    }
                }
            }
            return;
        }

        // Abaikan jika fokus sedang berada di input/select/textarea lain
        const activeTag = (document.activeElement?.tagName || '').toLowerCase();
        if (activeTag === 'input' || activeTag === 'select' || activeTag === 'textarea') {
            return;
        }

        // Tangkap input dari barcode scanner fisik (mengetik cepat lalu Enter)
        if (e.key === 'Enter') {
            if (directScanBuffer.length > 0) {
                e.preventDefault();
                const scanned = directScanBuffer;
                directScanBuffer = '';
                clearTimeout(directScanTimer);
                prosesKode(scanned);
            }
        } else if (e.key.length === 1) {
            directScanBuffer += e.key;
            clearTimeout(directScanTimer);
            // Scanner barcode mengetik sangat cepat. Jika jeda > 250ms, reset buffer (bukan scanner)
            directScanTimer = setTimeout(() => {
                directScanBuffer = '';
            }, 250);
        }
    });

    /* =====================================================================
       5. MODAL: KONFIRMASI SELESAI + RINGKASAN
          Setelah ringkasan ditutup -> redirect ke route('inventory.gudang')
          (atau ke link sidebar yang tadi diklik, kalau selesai lewat navigasi)
    ===================================================================== */
    const modalConfirm = document.getElementById('modalConfirm');
    const modalSummary = document.getElementById('modalSummary');

    function openConfirmModal() {
        modalConfirm.classList.remove('hidden');
    }
    function closeConfirmModal() {
        modalConfirm.classList.add('hidden');
    }

    function openSummaryModal() {
        const h = hitungStatus();
        document.getElementById('sumBelum').textContent = h.belum_terjual;
        document.getElementById('sumSudah').textContent = h.sudah_terjual;
        document.getElementById('sumChecking').textContent = h.checking;
        document.getElementById('sumTotal').textContent = stokData.length;
        modalSummary.classList.remove('hidden');
    }

    function selesaikanSesi() {
        sesiSelesai = true;
        closeConfirmModal();
        closeScanModal();
        openSummaryModal();
    }

    document.getElementById('btnSelesai').addEventListener('click', () => {
        pendingNavHref = URL_GUDANG; // selesai manual -> setelah ringkasan, arahkan ke gudang
        openConfirmModal();
    });
    document.getElementById('btnBatalSelesai').addEventListener('click', () => {
        pendingNavHref = null; // batal -> reset tujuan, tetap di halaman
        closeConfirmModal();
    });
    document.getElementById('btnYaSelesai').addEventListener('click', selesaikanSesi);

    const btnPrintSummary = document.getElementById('btnPrintSummary');
    if (btnPrintSummary) {
        btnPrintSummary.addEventListener('click', () => {
            const h = hitungStatus();
            const sortedItems = [...stokData].sort((a, b) => a.idx - b.idx);
            const payload = {
                petugas: @json(auth()->user()->USERNAME ?? 'admin'),
                lokasi: @json(config('perusahaan.kota', 'Sumenep')),
                summary: {
                    total: stokData.length,
                    sudah_terjual: h.sudah_terjual,
                    belum_terjual: h.belum_terjual,
                    tidak_terscan: h.checking,
                },
                items: sortedItems.map(item => ({
                    kode_barcode: item.kode_barcode,
                    nama_barang: item.nama_barang,
                    status: item.status,
                })),
            };
            document.getElementById('printPayload').value = JSON.stringify(payload);
            document.getElementById('formPrintOpname').submit();
        });
    }

    document.getElementById('btnTutupSummary').addEventListener('click', () => {
        modalSummary.classList.add('hidden');
        if (pendingNavHref) {
            const href = pendingNavHref;
            pendingNavHref = null;
            window.location.href = href;
        }
    });

    /* =====================================================================
       6. INTERCEPT NAVIGASI (klik link sidebar / link lain)
          -> dianggap "menyelesaikan" sesi juga
    ===================================================================== */
    document.addEventListener('click', function (e) {
        if (sesiSelesai) return;

        const link = e.target.closest('a[href]');
        if (!link) return;

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || link.target === '_blank') {
            return;
        }

        e.preventDefault();
        pendingNavHref = link.href;
        openConfirmModal();
    }, true);

    // Peringatan browser kalau user refresh / menutup tab sebelum sesi selesai
    window.addEventListener('beforeunload', function (e) {
        if (sesiSelesai) return;
        e.preventDefault();
        e.returnValue = '';
    });
});
</script>
@endif
@endpush