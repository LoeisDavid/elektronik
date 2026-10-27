@extends('layouts.app')

@section('title', 'Perpindahan Gudang - Elektronik')
@section('header_title', 'Perpindahan Gudang')

@section('content')
@php
    $badge = [
        'Request'    => 'bg-blue-100 border-blue-400 text-blue-700',
        'Pengiriman' => 'bg-amber-100 border-amber-400 text-amber-700',
        'Pending'    => 'bg-amber-100 border-amber-400 text-amber-700',
        'Diterima'   => 'bg-green-100 border-green-600 text-green-800',
        'Ditolak'    => 'bg-red-100 border-red-400 text-red-700',
    ];

    $btnBase   = 'h-7 w-7 border rounded-md flex items-center justify-center text-xs transition-all duration-200';
    $btnNormal = $btnBase . ' border-[#A8B1C1] text-[#142854] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200';
    $btnAktif  = $btnBase . ' font-semibold bg-[#142854] text-white border-[#142854] hover:bg-primary-sdb';
    $btnOff    = $btnBase . ' border-[#A8B1C1] text-gray-300 pointer-events-none';
@endphp

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 space-y-4">
    {{-- TOOLBAR ATAS: SEARCH, SORT & TOMBOL PINDAH --}}
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            {{-- SEARCH --}}
            <form action="{{ route('inventory.perpindahan') }}" method="GET" class="relative w-64 md:w-80">
                @if($sort !== 'terbaru')
                    <input type="hidden" name="sort" value="{{ $sort }}">
                @endif
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-black">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                </span>
                <input
                    id="searchPerpindahan"
                    type="text"
                    name="keyword"
                    value="{{ $keyword }}"
                    placeholder="Cari kode, gudang, user, status..."
                    class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 hover:border-primary-db focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db"
                >
            </form>

            {{-- SORT DROPDOWN --}}
            {{-- SORT DROPDOWN --}}
            <details id="sortDropdown" class="relative group">
                <summary class="list-none [&::-webkit-details-marker]:hidden h-9.5 px-3.5 bg-white border border-gray-800 rounded-xl flex items-center justify-between gap-2.5 text-sm font-medium text-black hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 cursor-pointer shadow-xs min-w-[200px]">
                    <span>Urutkan: {{ $opsiSort[$sort] ?? 'Terbaru' }}</span>
                    <svg class="w-4 h-4 text-gray-600 group-open:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                </summary>
                <div class="absolute left-0 mt-2 w-52 bg-secondary-bw rounded-2xl p-2 shadow-2xl border border-gray-300 z-50 space-y-1.5">
                    @foreach ($opsiSort as $k => $v)
                        <a
                            href="{{ route('inventory.perpindahan', array_merge(request()->query(), ['sort' => $k, 'page' => 1])) }}"
                            class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 shadow-xs cursor-pointer"
                        >
                            <span>{{ $v }}</span>
                            @if($sort === $k)
                                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            @endif
                        </a>
                    @endforeach
                </div>
            </details>
        </div>

        <button
            type="button"
            onclick="bukaModalPindahBarang()"
            class="h-9.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
        >
            <span class="text-lg leading-none font-bold">+</span>
            <span>Pindah Barang</span>
        </button>
    </div>

    {{-- TABEL DATA PERPINDAHAN --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 flex flex-col">
        <div class="overflow-visible">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-primary-db text-white text-xs font-semibold">
                        <th class="py-2.5 px-4 rounded-tl-lg">Kode Perpindahan</th>
                        <th class="py-2.5 px-4">Tanggal Request</th>
                        <th class="py-2.5 px-4">Gudang Asal</th>
                        <th class="py-2.5 px-4">Gudang Tujuan</th>
                        <th class="py-2.5 px-4 text-center">Status</th>
                        <th class="py-2.5 px-4 text-center">Jumlah Barang Dipindahkan</th>
                        <th class="py-2.5 px-4 text-center w-20 rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody id="perpindahanBody" class="divide-y divide-gray-200 text-sm text-black">
                    @forelse ($rpList as $rp)
                        @php
                            $stClass = $badge[$rp->SS] ?? 'bg-gray-100 border-gray-400 text-gray-700';
                            $tglRq   = $rp->DTRQ ? date('d/m/Y', strtotime($rp->DTRQ)) : '-';
                            $asal    = $rp->gudangAsal?->DES ?? $rp->GD_ASAL;
                            $tujuan  = $rp->gudangTujuan?->DES ?? $rp->GD_TUJUAN;
                        @endphp
                        <tr class="perpindahan-row hover:bg-gray-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono text-gray-700">{{ $rp->RP }}</td>
                            <td class="py-3.5 px-4">{{ $tglRq }}</td>
                            <td class="py-3.5 px-4 text-black">{{ $asal }}</td>
                            <td class="py-3.5 px-4 text-black">{{ $tujuan }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-block min-w-[110px] px-3.5 py-1 text-xs font-semibold rounded-full border {{ $stClass }}">
                                    {{ $rp->SS }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center text-gray-800">{{ (int) $rp->rpd_count }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="relative inline-block text-left">
                                    <button
                                        type="button"
                                        onclick="toggleActionMenu(this)"
                                        class="btn-aksi h-8 w-8 border border-gray-400 bg-white hover:bg-gray-200 hover:border-gray-900 rounded-md inline-flex items-center justify-center transition-all duration-200 shadow-2xs cursor-pointer"
                                        title="Aksi Perpindahan"
                                    >
                                        <svg class="w-4 h-4 text-primary-db" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                        </svg>
                                    </button>
                                    <div class="action-menu absolute right-0 top-full mt-1 w-52 bg-white border border-gray-300 rounded-xl shadow-xl py-1.5 z-50 text-left hidden divide-y divide-gray-100">
                                        <div class="py-1">
                                            <a
                                                href="{{ route('inventory.perpindahan.show', $rp->RP) }}"
                                                class="w-full px-3.5 py-1.5 text-xs font-semibold text-gray-800 hover:bg-primary-db/5 hover:text-primary-db transition-all duration-200 flex items-center gap-2 cursor-pointer"
                                            >
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                <span>Lihat Detail</span>
                                            </a>
                                        </div>
                                        <div class="py-1">
                                            @if($rp->SS === 'Request')
                                                <a
                                                    href="{{ route('inventory.perpindahan.cetak', $rp->RP) }}"
                                                    target="_blank"
                                                    class="w-full px-3.5 py-1.5 text-xs font-semibold text-gray-800 hover:bg-amber-50 hover:text-amber-800 transition-all duration-200 flex items-center gap-2 cursor-pointer"
                                                    title="Cetak Surat Permintaan (1 TTD Pemohon)"
                                                >
                                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                                    <span>Cetak Surat Permintaan</span>
                                                </a>
                                            @elseif($rp->SS === 'Pengiriman' || $rp->SS === 'Pending')
                                                <a
                                                    href="{{ route('inventory.perpindahan.cetak', $rp->RP) }}"
                                                    target="_blank"
                                                    class="w-full px-3.5 py-1.5 text-xs font-semibold text-gray-800 hover:bg-blue-50 hover:text-blue-800 transition-all duration-200 flex items-center gap-2 cursor-pointer"
                                                    title="Cetak Surat Jalan (Pengirim & Penerima Manual)"
                                                >
                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V4.5A2.25 2.25 0 0012 2.25H6.75A2.25 2.25 0 004.5 4.5v9.75" /></svg>
                                                    <span>Cetak Surat Jalan</span>
                                                </a>
                                            @elseif($rp->SS === 'Diterima')
                                                <a
                                                    href="{{ route('inventory.perpindahan.cetak', $rp->RP) }}"
                                                    target="_blank"
                                                    class="w-full px-3.5 py-1.5 text-xs font-semibold text-gray-800 hover:bg-green-50 hover:text-green-800 transition-all duration-200 flex items-center gap-2 cursor-pointer"
                                                    title="Cetak Bukti Penerimaan (1 TTD Penerima)"
                                                >
                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    <span>Cetak Bukti Penerimaan</span>
                                                </a>
                                            @endif
                                        </div>

                                        {{-- AKSI KONFIRMASI --}}
                                        @if($rp->SS === 'Request')
                                            <div class="py-1">
                                                <form action="{{ route('inventory.perpindahan.konfirmasi1', ['id' => $rp->RP]) }}" method="POST" onsubmit="return confirm('Konfirmasi pengiriman barang dari gudang asal?')">
                                                    @csrf
                                                    <button type="submit" class="w-full px-3.5 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 transition-colors flex items-center gap-2 cursor-pointer">
                                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V4.5A2.25 2.25 0 0012 2.25H6.75A2.25 2.25 0 004.5 4.5v9.75" /></svg>
                                                        <span>Konfirmasi 1 (Kirim)</span>
                                                    </button>
                                                </form>
                                                <form action="{{ route('inventory.perpindahan.tolak', ['id' => $rp->RP]) }}" method="POST" onsubmit="return confirm('Tolak request perpindahan barang ini?')">
                                                    @csrf
                                                    <button type="submit" class="w-full px-3.5 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors flex items-center gap-2 cursor-pointer">
                                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        <span>Tolak Request</span>
                                                    </button>
                                                </form>
                                                <form action="{{ route('inventory.perpindahan.destroy', ['id' => $rp->RP]) }}" method="POST" onsubmit="return confirm('Batalkan dan hapus request perpindahan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full px-3.5 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors flex items-center gap-2 cursor-pointer">
                                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4v3M4 7h16" /></svg>
                                                        <span>Hapus Request</span>
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif($rp->SS === 'Pengiriman' || $rp->SS === 'Pending')
                                            <div class="py-1">
                                                <form action="{{ route('inventory.perpindahan.konfirmasi2', ['id' => $rp->RP]) }}" method="POST" onsubmit="return confirm('Konfirmasi penerimaan barang untuk perpindahan {{ $rp->RP }}? Seluruh barang akan otomatis berpindah ke gudang tujuan.')">
                                                    @csrf
                                                    <button type="submit" class="w-full px-3.5 py-1.5 text-xs font-semibold text-green-700 hover:bg-green-50 transition-colors flex items-center gap-2 cursor-pointer">
                                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        <span>Konfirmasi Penerimaan</span>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                    <p class="font-medium">Belum ada data perpindahan barang</p>
                                    <p class="text-xs text-gray-400">Klik tombol "+ Pindah Barang" untuk membuat request perpindahan baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINASI --}}
        <div class="px-4 py-3 bg-white border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#142854] rounded-b-lg">
            <div>
                Menampilkan {{ $rpList->firstItem() ?? 0 }} - {{ $rpList->lastItem() ?? 0 }} dari {{ $rpList->total() }} perpindahan
            </div>
            <div>
                {{ $rpList->onEachSide(1)->links('pagination::simple-tailwind') }}
            </div>
        </div>
    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL PINDAH BARANG (REQUEST PERPINDAHAN)                --}}
{{-- ========================================================= --}}
<div id="pindahBarangModal" class="hidden fixed inset-0 z-[100] items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[92vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        {{-- HEADER MODAL --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-slate-50">
            <div>
                <h2 class="text-lg font-bold text-primary-db">Pindah Barang</h2>
                <p class="text-xs text-gray-500">Buat request perpindahan stok antar gudang</p>
            </div>
            <button type="button" onclick="tutupModal('pindahBarangModal')" class="text-2xl text-gray-500 hover:text-black cursor-pointer leading-none">&times;</button>
        </div>

        {{-- FORM BODY --}}
        <form id="formPindahBarang" method="POST" action="{{ route('inventory.perpindahan.store') }}" class="flex flex-col flex-1 overflow-hidden">
            @csrf

            <div class="p-6 space-y-4 overflow-y-auto flex-1">
                {{-- TANGGAL REQUEST (DTRQ) & GUDANG TUJUAN --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1.5">Tanggal Request (DTRQ)</label>
                        <input
                            type="date"
                            name="DTRQ"
                            id="inputDTRQ"
                            value="{{ date('Y-m-d') }}"
                            required
                            class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-black focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1.5">Gudang Tujuan</label>
                        <select
                            name="GD_TUJUAN"
                            id="selectGdTujuan"
                            required
                            onchange="onGudangTujuanChange()"
                            class="searchable-select w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-black focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db"
                            data-placeholder="Pilih gudang tujuan"
                        >
                            <option value="" disabled selected>Pilih gudang tujuan</option>
                            @foreach ($gudangs as $g)
                                <option value="{{ $g->GD }}">{{ $g->DES }} ({{ $g->GD }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- INPUT SCAN MANUAL BARCODE OUT (BC_OUT) --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1.5">Barcode Out (BC_OUT)</label>
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <input
                                type="text"
                                id="inputScanBC"
                                autocomplete="off"
                                placeholder="Scan atau ketik kode barcode (BC_OUT)..."
                                onkeydown="handleBarcodeKeyDown(event)"
                                class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-black placeholder-gray-400 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db font-mono"
                            >
                            <span id="scanLoading" class="hidden absolute right-3 top-3 text-xs text-primary-db font-semibold animate-pulse">Mengecek...</span>
                        </div>
                        <button
                            type="button"
                            onclick="prosesTambahBarcode()"
                            class="py-2.5 px-5 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-xs font-bold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
                        >
                            Tambah
                        </button>
                    </div>

                    {{-- PESAN FEEDBACK / ERROR SCAN --}}
                    <p id="scanFeedbackMsg" class="hidden text-xs mt-1.5 font-medium"></p>
                </div>

                {{-- DAFTAR BARANG YANG SUDAH DISCANNED --}}
                <div class="pt-2">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-wide">
                            Barang Siap Dipindahkan (<span id="countItemsBadge">0</span>)
                        </label>
                        <button
                            type="button"
                            onclick="kosongkanSemuaBarang()"
                            class="text-xs text-red-500 hover:text-red-700 font-semibold cursor-pointer"
                        >
                            Kosongkan
                        </button>
                    </div>

                    <div id="emptyItemsNotice" class="p-6 border border-dashed border-gray-300 rounded-xl text-center text-xs text-gray-400">
                        Belum ada barcode yang di-scan atau diinput. Ketik barcode manual pada input di atas.
                    </div>

                    <div id="scannedItemsTableWrapper" class="hidden border border-gray-300 rounded-xl max-h-56 overflow-y-auto bg-white divide-y divide-gray-100">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-100 text-gray-600 sticky top-0 font-semibold">
                                <tr>
                                    <th class="py-2 px-3">Kode Barcode</th>
                                    <th class="py-2 px-3">Nama Barang</th>
                                    <th class="py-2 px-3">Gudang Asal</th>
                                    <th class="py-2 px-3 text-center w-12">Hapus</th>
                                </tr>
                            </thead>
                            <tbody id="scannedItemsBody" class="divide-y divide-gray-100 font-medium">
                                {{-- Baris dinamis --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- FOOTER MODAL --}}
            <div class="flex gap-3 px-6 py-4 border-t border-gray-200 bg-slate-50 shrink-0">
                <button
                    type="button"
                    onclick="tutupModal('pindahBarangModal')"
                    class="flex-1 py-2.5 border border-gray-400 hover:bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold transition-colors cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    id="btnSimpanPindah"
                    disabled
                    class="flex-1 py-2.5 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Simpan Perpindahan (0)
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL DETAIL PERPINDAHAN                                  --}}
{{-- ========================================================= --}}
<div id="detailModal" class="hidden fixed inset-0 z-[100] items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-slate-50">
            <div>
                <h2 class="text-lg font-bold text-primary-db">Detail Perpindahan</h2>
                <p id="detailKodeSubtitle" class="text-xs text-gray-500 font-mono"></p>
            </div>
            <button type="button" onclick="tutupModal('detailModal')" class="text-2xl text-gray-500 hover:text-black cursor-pointer leading-none">&times;</button>
        </div>

        <div class="p-6 space-y-4 overflow-y-auto flex-1 text-sm text-black">
            {{-- RINGKASAN INFORMASI --}}
            <div class="grid grid-cols-2 gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                <div>
                    <span class="text-gray-500 block">Kode Perpindahan:</span>
                    <span id="dtlKode" class="font-bold font-mono text-primary-db text-sm"></span>
                </div>
                <div>
                    <span class="text-gray-500 block">Status:</span>
                    <span id="dtlStatus" class="inline-block px-2.5 py-0.5 rounded-full font-bold text-[11px] border mt-0.5"></span>
                </div>
                <div>
                    <span class="text-gray-500 block">Gudang Asal:</span>
                    <span id="dtlGudangAsal" class="font-semibold text-gray-800"></span>
                </div>
                <div>
                    <span class="text-gray-500 block">Gudang Tujuan:</span>
                    <span id="dtlGudangTujuan" class="font-semibold text-gray-800"></span>
                </div>
                <div>
                    <span class="text-gray-500 block">Tanggal Request (DTRQ):</span>
                    <span id="dtlDTRQ" class="font-semibold text-gray-800"></span>
                    <span id="dtlRQ" class="text-gray-500 block text-[10px]"></span>
                </div>
                <div>
                    <span class="text-gray-500 block">Konfirmasi 1 (Kirim):</span>
                    <span id="dtlDTCF1" class="font-semibold text-gray-800"></span>
                    <span id="dtlCF1" class="text-gray-500 block text-[10px]"></span>
                </div>
                <div class="col-span-2 border-t border-slate-200 pt-2">
                    <span class="text-gray-500 block">Konfirmasi 2 (Terima):</span>
                    <span id="dtlDTCF2" class="font-semibold text-gray-800"></span>
                    <span id="dtlCF2" class="text-gray-500 block text-[10px]"></span>
                </div>
            </div>

            {{-- TABEL BARANG YANG DIPINDAHKAN --}}
            <div>
                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">
                    Daftar Barang (<span id="dtlCountItems">0</span>)
                </h4>
                <div class="border border-gray-300 rounded-xl max-h-52 overflow-y-auto bg-white">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-gray-600 sticky top-0 font-semibold">
                            <tr>
                                <th class="py-2 px-3">No</th>
                                <th class="py-2 px-3">Kode Barcode</th>
                                <th class="py-2 px-3">Nama Barang</th>
                                <th class="py-2 px-3">Kategori</th>
                            </tr>
                        </thead>
                        <tbody id="dtlItemsBody" class="divide-y divide-gray-100 font-medium">
                            {{-- Dinamis --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 bg-slate-50 flex justify-end gap-2">
            <button
                type="button"
                onclick="tutupModal('detailModal')"
                class="py-2 px-5 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-xs font-bold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
            >
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- JAVASCRIPT LOGIKA MODUL PERPINDAHAN & SCANNING --}}
<script>
    // State Penyimpanan Barcode yang Sudah Discan
    let scannedList = [];

    // Helper Modal & Menu
    function toggleActionMenu(b) {
        const m = b.parentElement.querySelector('.action-menu');
        document.querySelectorAll('.action-menu').forEach(i => { if (i !== m) i.classList.add('hidden'); });
        m.classList.toggle('hidden');
    }

    function tutupSemuaMenu() {
        document.querySelectorAll('.action-menu').forEach(m => m.classList.add('hidden'));
    }

    function bukaModal(id) {
        const m = document.getElementById(id);
        m.classList.remove('hidden');
        m.classList.add('flex');
    }

    function tutupModal(id) {
        const m = document.getElementById(id);
        m.classList.remove('flex');
        m.classList.add('hidden');
    }

    document.addEventListener('click', e => {
        if (!e.target.closest('.action-menu') && !e.target.closest('.btn-aksi')) {
            tutupSemuaMenu();
        }
        const s = document.getElementById('sortDropdown');
        if (s && !e.target.closest('#sortDropdown')) {
            s.removeAttribute('open');
        }
    });

    function bukaModalPindahBarang() {
        bukaModal('pindahBarangModal');
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            const $select = $('#selectGdTujuan');
            if (!$select.hasClass('select2-hidden-accessible')) {
                $select.select2({
                    width: '100%',
                    dropdownParent: $('#pindahBarangModal'),
                    placeholder: 'Pilih gudang tujuan',
                    allowClear: false
                }).on('change', function() {
                    onGudangTujuanChange();
                });
            }
        }
        setTimeout(() => {
            document.getElementById('inputScanBC')?.focus();
        }, 150);
    }

    function handleBarcodeKeyDown(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            prosesTambahBarcode();
        }
    }

    function onGudangTujuanChange() {
        const gdTujuan = document.getElementById('selectGdTujuan').value;
        const feedback = document.getElementById('scanFeedbackMsg');
        feedback.classList.add('hidden');

        // Jika sudah ada barang yang discan, cek apakah ada barang yang lokasinya sama dengan gudang tujuan baru
        if (scannedList.length > 0 && gdTujuan) {
            const conflict = scannedList.some(item => item.gd === gdTujuan);
            if (conflict) {
                tampilkanFeedback('Ada barang di daftar yang lokasinya sudah berada di gudang tujuan ini! Daftar barang dikosongkan.', 'error');
                kosongkanSemuaBarang();
            }
        }
    }

    // Proses Cek & Tambah Barcode ke List
    function prosesTambahBarcode(manualCode) {
        const input = document.getElementById('inputScanBC');
        const bcOut = (manualCode || input.value).trim();
        const gdTujuan = document.getElementById('selectGdTujuan').value;
        const feedback = document.getElementById('scanFeedbackMsg');
        const loading = document.getElementById('scanLoading');

        feedback.classList.add('hidden');

        if (!gdTujuan) {
            tampilkanFeedback('Pilih gudang tujuan terlebih dahulu sebelum scan barang!', 'error');
            document.getElementById('selectGdTujuan').focus();
            return;
        }

        if (!bcOut) {
            tampilkanFeedback('Ketik atau scan kode barcode terlebih dahulu!', 'error');
            input.focus();
            return;
        }

        // Cek duplikasi di daftar scan saat ini
        if (scannedList.some(item => item.bc_out.toUpperCase() === bcOut.toUpperCase())) {
            tampilkanFeedback(`Barcode '${bcOut}' sudah ada dalam daftar perpindahan!`, 'error');
            input.value = '';
            input.focus();
            return;
        }

        loading.classList.remove('hidden');

        const currentGdAsal = scannedList.length > 0 ? scannedList[0].gd : '';

        fetch(`{{ route('inventory.perpindahan.check-barcode') }}?bc_out=${encodeURIComponent(bcOut)}&gd_tujuan=${encodeURIComponent(gdTujuan)}&current_gd_asal=${encodeURIComponent(currentGdAsal)}`)
            .then(res => res.json())
            .then(res => {
                loading.classList.add('hidden');
                if (res.success && res.item) {
                    scannedList.push(res.item);
                    renderScannedTable();
                    tampilkanFeedback(`✓ Berhasil menambahkan: ${res.item.des} (${res.item.bc_out}) - Asal: ${res.item.gd_des}`, 'ok');
                    input.value = '';
                    input.focus();
                } else {
                    tampilkanFeedback(res.message || 'Barcode tidak valid.', 'error');
                }
            })
            .catch(() => {
                loading.classList.add('hidden');
                tampilkanFeedback('Terjadi kesalahan koneksi server.', 'error');
            });
    }

    function tampilkanFeedback(text, type) {
        const el = document.getElementById('scanFeedbackMsg');
        el.classList.remove('hidden', 'text-red-600', 'text-green-600');
        if (type === 'ok') {
            el.classList.add('text-green-600');
        } else {
            el.classList.add('text-red-600');
        }
        el.textContent = text;
    }

    function hapusItemScanned(index) {
        scannedList.splice(index, 1);
        renderScannedTable();
    }

    function kosongkanSemuaBarang() {
        scannedList = [];
        renderScannedTable();
    }

    function renderScannedTable() {
        const tbody = document.getElementById('scannedItemsBody');
        const emptyNotice = document.getElementById('emptyItemsNotice');
        const tableWrapper = document.getElementById('scannedItemsTableWrapper');
        const badge = document.getElementById('countItemsBadge');
        const btnSimpan = document.getElementById('btnSimpanPindah');

        badge.textContent = scannedList.length;
        btnSimpan.textContent = `Simpan Perpindahan (${scannedList.length})`;
        btnSimpan.disabled = scannedList.length === 0;

        if (scannedList.length === 0) {
            emptyNotice.classList.remove('hidden');
            tableWrapper.classList.add('hidden');
            tbody.innerHTML = '';
            return;
        }

        emptyNotice.classList.add('hidden');
        tableWrapper.classList.remove('hidden');

        let html = '';
        scannedList.forEach((it, idx) => {
            html += `
                <tr class="hover:bg-slate-50">
                    <td class="py-2.5 px-3 font-mono font-bold text-primary-db">
                        ${escapeHtml(it.bc_out)}
                        <input type="hidden" name="items[]" value="${escapeHtml(it.bc_out)}">
                    </td>
                    <td class="py-2.5 px-3">${escapeHtml(it.des)}</td>
                    <td class="py-2.5 px-3 text-gray-700 font-semibold">${escapeHtml(it.gd_des)}</td>
                    <td class="py-2.5 px-3 text-center">
                        <button type="button" onclick="hapusItemScanned(${idx})" class="text-red-500 hover:text-red-700 font-bold text-sm cursor-pointer p-1">
                            &times;
                        </button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function bukaDetailPerpindahan(kodeRp) {
        bukaModal('detailModal');
        document.getElementById('detailKodeSubtitle').textContent = 'Memuat data...';
        document.getElementById('dtlKode').textContent = kodeRp;

        fetch(`{{ url('Inventory/perpindahan') }}/${encodeURIComponent(kodeRp)}`)
            .then(res => res.json())
            .then(res => {
                if (res.success && res.rp) {
                    const rp = res.rp;
                    document.getElementById('detailKodeSubtitle').textContent = kodeRp;
                    document.getElementById('dtlKode').textContent = rp.RP;
                    document.getElementById('dtlGudangAsal').textContent = rp.GD_ASAL_NAMA || rp.GD_ASAL;
                    document.getElementById('dtlGudangTujuan').textContent = rp.GD_TUJUAN_NAMA || rp.GD_TUJUAN;
                    document.getElementById('dtlDTRQ').textContent = rp.DTRQ || '-';
                    document.getElementById('dtlRQ').textContent = rp.RQ ? `Oleh: ${rp.RQ}` : '';

                    document.getElementById('dtlDTCF1').textContent = rp.DTCF1 || 'Belum dikonfirmasi';
                    document.getElementById('dtlCF1').textContent = rp.CF1 ? `Oleh: ${rp.CF1}` : '';

                    document.getElementById('dtlDTCF2').textContent = rp.DTCF2 || 'Belum dikonfirmasi';
                    document.getElementById('dtlCF2').textContent = rp.CF2 ? `Oleh: ${rp.CF2}` : '';

                    // Badge status
                    const stEl = document.getElementById('dtlStatus');
                    stEl.textContent = rp.SS;
                    stEl.className = 'inline-block px-2.5 py-0.5 rounded-full font-bold text-[11px] border mt-0.5 ' + (
                        rp.SS === 'Request' ? 'bg-blue-100 border-blue-400 text-blue-700' :
                        rp.SS === 'Pending' ? 'bg-amber-100 border-amber-400 text-amber-600' :
                        'bg-green-100 border-green-600 text-green-800'
                    );

                    // Render barang
                    document.getElementById('dtlCountItems').textContent = res.items.length;
                    const itemsBody = document.getElementById('dtlItemsBody');
                    if (res.items.length === 0) {
                        itemsBody.innerHTML = '<tr><td colspan="4" class="py-4 text-center text-gray-400">Tidak ada barang tercatat.</td></tr>';
                    } else {
                        let html = '';
                        res.items.forEach((it, idx) => {
                            html += `
                                <tr class="hover:bg-slate-50">
                                    <td class="py-2 px-3 text-gray-500">${idx + 1}</td>
                                    <td class="py-2 px-3 font-mono font-bold text-primary-db">${escapeHtml(it.BC_OUT)}</td>
                                    <td class="py-2 px-3">${escapeHtml(it.NAMA_BARANG || '-')}</td>
                                    <td class="py-2 px-3 text-gray-600">${escapeHtml(it.NAMA_KATEGORI || '-')}</td>
                                </tr>
                            `;
                        });
                        itemsBody.innerHTML = html;
                    }
                }
            })
            .catch(() => {
                document.getElementById('detailKodeSubtitle').textContent = 'Gagal memuat detail.';
            });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
</script>
@endsection
