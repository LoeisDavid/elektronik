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
                {{-- FORM SEARCH --}}
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

                {{-- TOMBOL AKSI --}}
                <div class="flex items-center gap-2">
                    {{-- TOMBOL CETAK SESUAI STATUS SAAT INI --}}
                    @if($isRequest)
                        <a
                            href="{{ route('inventory.perpindahan.cetak', $rp->RP) }}"
                            target="_blank"
                            class="h-9.5 px-3.5 bg-white border border-gray-800 hover:bg-gray-100 active:bg-gray-200 text-gray-800 rounded-lg flex items-center gap-1.5 text-xs md:text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs"
                            title="Cetak Surat Permintaan (1 TTD Pemohon)"
                        >
                            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span>Cetak Surat Permintaan</span>
                        </a>
                    @elseif($isPengiriman)
                        <a
                            href="{{ route('inventory.perpindahan.cetak', $rp->RP) }}"
                            target="_blank"
                            class="h-9.5 px-3.5 bg-white border border-gray-800 hover:bg-gray-100 active:bg-gray-200 text-gray-800 rounded-lg flex items-center gap-1.5 text-xs md:text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs"
                            title="Cetak Surat Jalan (Pengirim & Penerima Manual)"
                        >
                            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V4.5A2.25 2.25 0 0012 2.25H6.75A2.25 2.25 0 004.5 4.5v9.75" />
                            </svg>
                            <span>Cetak Surat Jalan</span>
                        </a>
                    @elseif($isDiterima)
                        <a
                            href="{{ route('inventory.perpindahan.cetak', $rp->RP) }}"
                            target="_blank"
                            class="h-9.5 px-3.5 bg-white border border-gray-800 hover:bg-gray-100 active:bg-gray-200 text-gray-800 rounded-lg flex items-center gap-1.5 text-xs md:text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs"
                            title="Cetak Bukti Penerimaan (1 TTD Penerima)"
                        >
                            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Cetak Bukti Penerimaan</span>
                        </a>
                    @endif

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
                    @elseif($isPengiriman)
                        {{-- KONFIRMASI 2: PENERIMAAN LANGSUNG SELESAI --}}
                        <form action="{{ route('inventory.perpindahan.konfirmasi2', ['id' => $rp->RP]) }}" method="POST" onsubmit="return confirm('Konfirmasi penerimaan barang? Seluruh barang yang dikirim akan otomatis berpindah ke gudang tujuan.')">
                            @csrf
                            <button
                                type="submit"
                                class="h-9.5 px-4 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                <span>Konfirmasi Penerimaan</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- TABEL DATA BARANG PERPINDAHAN --}}
            <div class="border border-gray-300 rounded-lg overflow-hidden bg-white shadow-2xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-primary-db text-white text-xs font-semibold">
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4">Kode Stok (Barcode)</th>
                                <th class="py-3 px-4">Nama Barang</th>
                                <th class="py-3 px-4">Merk</th>
                                <th class="py-3 px-4">Kategori</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-gray-900">
                            @forelse ($itemsPaginated as $idx => $item)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3.5 px-4 text-center text-xs text-gray-500 font-mono">{{ $itemsPaginated->firstItem() + $idx }}</td>
                                    <td class="py-3.5 px-4 font-mono text-xs font-semibold text-gray-800">{{ $item->BC_OUT }}</td>
                                    <td class="py-3.5 px-4 font-medium">{{ $item->NAMA_BARANG ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-gray-600">{{ $item->NAMA_MERK ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-gray-600">{{ $item->NAMA_KATEGORI ?? '-' }}</td>
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
        </div>
    </div>
</div>
@endsection
