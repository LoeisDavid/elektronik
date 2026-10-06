@extends('layouts.app')

@section('title', 'Gudang - Detail Gudang')
@section('header_title', 'Detail Gudang')

@section('content')
<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 space-y-4">

    {{-- Kembali --}}
    <div>
        <a 
            href="{{ route('inventory.gudang') }}" 
            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-black transition-colors cursor-pointer"
        >
            <svg class="w-4 h-4 mr-1 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            Kembali ke Gudang
        </a>
    </div>

    <div class="space-y-6 w-full">

        {{-- Info Gudang --}}
        <div class="border border-gray-300 rounded-2xl bg-white p-6 shadow-xs w-full space-y-4">
            <div>
                <h2 class="text-2xl font-bold text-black">{{ $gd->DES }}</h2>
            </div>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center py-2 border-y border-gray-400">
                    <span class="text-black">Kode Gudang</span>
                    <span class="font-medium text-black font-mono">{{ $gd->GD }}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-gray-400">
                    <span class="text-black">Nama Gudang</span>
                    <span class="font-medium text-black">{{ $gd->DES }}</span>
                </div>
            </div>
        </div>

        {{-- Tombol lihat daftar barang --}}
        <div>
            <a 
                href="{{ route('inventory.gudang.daftarBarang', $gd->GD) }}"
                class="inline-flex items-center justify-center bg-primary-db hover:bg-primary-sdb text-white text-base font-bold px-8 py-3.5 rounded-lg shadow-md transition-colors cursor-pointer"
            >
                Lihat Daftar Barang
            </a>
        </div>

    </div>
</div>
@endsection