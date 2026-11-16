@extends('layouts.app')

@section('title', 'Detail Perpindahan Gudang - Elektronik')
@section('header_title', 'Detail Perpindahan gudang')

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
                <span class="text-gray-700 font-medium">Tanggal Perpindahan</span>
                <span class="font-semibold text-gray-900">{{ $tglPerpindahan }}</span>
            </div>
            <div class="py-3 flex items-center justify-between">
                <span class="text-gray-700 font-medium">User Pembuat Perpindahan</span>
                <span class="font-semibold text-gray-900">{{ $namaUser }}</span>
            </div>
        </div>

        {{-- SEKSI BARANG YANG DIPINDAH --}}
        <div class="pt-4 space-y-4">
            <h2 class="text-lg font-bold text-gray-900">Barang Yang Dipindah</h2>

            {{-- TOOLBAR: CARI & TOMBOL CETAK --}}
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
                <x-pagination :paginator="$itemsPaginated" label="barang" />
            </div>
        </div>
    </div>
</div>
@endsection
