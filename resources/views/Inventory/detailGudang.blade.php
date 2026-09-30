@extends('layouts.app')

@section('title', 'Gudang - Detail Gudang')
@section('header_title', 'Detail Gudang')

@section('content')
<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 select-none space-y-4">

    {{-- ========================================================= --}}
    {{-- BACK BUTTON / LINK KEMBALI --}}
    {{-- ========================================================= --}}
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

    {{-- ========================================================= --}}
    {{-- MAIN CONTENT GRID --}}
    {{-- ========================================================= --}}
        <div class="space-y-6 w-full">

            {{-- KONTEN UTAMA (GRID 2 KOLOM) --}}
            <div class="grid grid-cols-2 gap-6 border border-gray-300 rounded-2xl bg-white p-6 shadow-xs w-full">
                
                {{-- KOLOM KIRI --}}
                <div class="col-span-1 space-y-4">
                    <!-- Judul & Lokasi Gudang -->
                    <div>
                        <h2 class="text-2xl font-bold text-black">{{ $gd->DES ?? 'Gudang Garam jaya' }}</h2>
                        <p class="text-sm font-medium text-black">{{ $gd->LOKASI ?? 'Medokan ayu, Surabaya' }}</p>
                    </div>

                    <!-- Keterangan -->
                    <div class="my-4">
                        <h3 class="text-base font-bold text-black">Keterangan:</h3>
                        <p class="text-xs text-black leading-relaxed">
                            {{ $gd->KETERANGAN ?? 'Lorem ipsum dolor sit amet consectetur adipiscing elit quisque faucibus ex sapien vitae pellentesque sem placerat in id cursus mi.' }}
                        </p>
                    </div>

                    <!-- Meta Info -->
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center py-2 border-y border-gray-400">
                            <span class="text-black">Tanggal dibuat:</span>
                            <span class="font-medium text-black">
                                {{ isset($gd->created_at) ? \Carbon\Carbon::parse($gd->created_at)->format('d M Y') : '14 Sep 2026' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-400">
                            <span class="text-black">Lokasi</span>
                            <span class="font-medium text-black">{{ $gd->LOKASI ?? 'Medokan ayu, Surabaya' }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-0.5">
                            <span class="text-black">Status</span>
                            <span class="font-bold text-black">{{ $gd->STATUS ?? 'Aktif' }}</span>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN (GAMBAR GUDANG) --}}
                <div class="col-span-1">
                    <div class="w-full h-64 bg-[#637381] rounded-2xl flex items-center justify-center overflow-hidden">
                        {{-- <img src="{{ asset('storage/' . $gd->foto) }}" class="w-full h-full object-cover"> --}}
                    </div>
                </div>

            </div>

            {{-- TOMBOL BUTTON LIHAT DAFTAR BARANG --}}
            <div>
                <a 
                    href="{{ route('inventory.gudang.daftarBarang') }}"
                    class="inline-flex items-center justify-center bg-primary-db hover:bg-primary-sdb text-white text-base font-bold px-8 py-3.5 rounded-lg shadow-md transition-colors cursor-pointer"
                >
                    Lihat Daftar Barang
                </a>
            </div>

        </div>

</div>
@endsection