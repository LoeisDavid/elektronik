@extends('layouts.app')

@section('title', 'Detail Perpindahan Gudang - Elektronik')
@section('header_title', 'Detail Perpindahan Gudang')

@section('content')
@php
    $tglPerpindahan = $rp->DTRQ ? \Carbon\Carbon::parse($rp->DTRQ)->locale('id')->translatedFormat('d F Y') : '-';
    $namaUser       = $rp->userRequest?->DES ?? $rp->RQ ?? '-';
@endphp

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 space-y-4">
    {{-- TOMBOL KEMBALI --}}
    <div>
        <a
            href="{{ route('inventory.perpindahan') }}"
            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-black transition-colors cursor-pointer"
        >
            <svg class="w-4 h-4 mr-1 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <span class="text-gray-700 font-medium">Tanggal Perpindahan</span>
                <span class="font-semibold text-gray-900">{{ $tglPerpindahan }}</span>
            </div>
            <div class="py-3 flex items-center justify-between">
                <span class="text-gray-700 font-medium">User Pembuat Perpindahan</span>
                <span class="font-semibold text-gray-900">{{ $namaUser }}</span>
            </div>
        </div>

        {{-- KARTU 2: SEKSI BARANG YANG DIPINDAH --}}
        <div class="border border-gray-300 rounded-2xl bg-white p-6 space-y-4 shadow-xs">
            <h3 class="text-base font-bold text-black">Barang Yang Dipindah</h3>

            {{-- TOOLBAR: CARI & TOMBOL CETAK --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                {{-- FORM SEARCH --}}
                <form action="{{ route('inventory.perpindahan.show', $rp->RP) }}" method="GET" class="relative w-full sm:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-black">
                        <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="keyword"
                        value="{{ $keyword }}"
                        placeholder="Cari barcode, barang, kategori..."
                        class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:border-primary-db focus:bg-primary-db/5 focus:ring-1 focus:ring-primary-db"
                    >
                </form>

                {{-- TOMBOL AKSI --}}
                <div class="flex items-center gap-2">
                    <a
                        href="{{ route('inventory.perpindahan.cetak', $rp->RP) }}"
                        target="_blank"
                        class="h-9.5 px-3.5 bg-white border border-gray-800 hover:bg-gray-100 active:bg-gray-200 text-gray-800 rounded-lg flex items-center gap-1.5 text-xs md:text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs"
                        title="Cetak Bukti Perpindahan"
                    >
                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Cetak Bukti Perpindahan</span>
                    </a>
                </div>
            </div>

            {{-- TABEL DATA BARANG PERPINDAHAN --}}
            <div class="border border-gray-300 rounded-lg overflow-hidden bg-white shadow-2xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-primary-db text-white text-xs font-semibold rounded-t-lg">
                                <th class="py-2.5 px-4 font-semibold w-12 text-center rounded-tl-lg">No</th>
                                <th class="py-2.5 px-4 font-semibold">Kode Stok (Barcode)</th>
                                <th class="py-2.5 px-4 font-semibold">Nama Barang</th>
                                <th class="py-2.5 px-4 font-semibold">Merk</th>
                                <th class="py-2.5 px-4 font-semibold rounded-tr-lg">Kategori</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-black">
                            @forelse ($itemsPaginated as $idx => $item)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3.5 px-4 text-center font-mono">{{ $itemsPaginated->firstItem() + $idx }}</td>
                                    <td class="py-3.5 px-4 font-mono font-medium">{{ $item->BC_OUT }}</td>
                                    <td class="py-3.5 px-4 font-medium">{{ $item->NAMA_BARANG ?? '-' }}</td>
                                    <td class="py-3.5 px-4">{{ $item->NAMA_MERK ?? '-' }}</td>
                                    <td class="py-3.5 px-4">{{ $item->NAMA_KATEGORI ?? '-' }}</td>
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
                <x-pagination :paginator="$itemsPaginated" label="barang" />
            </div>
        </div>
    </div>
</div>
@endsection
