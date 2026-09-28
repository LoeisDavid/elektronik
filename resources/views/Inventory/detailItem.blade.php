@extends('layouts.app')

@section('title', 'Inventory - Detail Item')
@section('header_title', 'Detail Item')

@section('content')
<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 select-none space-y-4">

    {{-- ========================================================= --}}
    {{-- BACK BUTTON / LINK KEMBALI --}}
    {{-- ========================================================= --}}
    <div>
        <a 
            href="{{ route('inventory.item') }}" 
            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-black transition-colors cursor-pointer"
        >
            <svg class="w-4 h-4 mr-1 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            Kembali ke Inventory
        </a>
    </div>

    {{-- ========================================================= --}}
    {{-- MAIN CONTENT GRID --}}
    {{-- ========================================================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- KOLOM KIRI: FOTO & DETAIL PRODUK --}}
        <div class="lg:col-span-5 border border-gray-300 rounded-2xl bg-white p-6 space-y-5 shadow-xs">
            
            <!-- Placeholder Gambar Produk -->
            <div class="w-full h-64 bg-[#637381] rounded-2xl flex items-center justify-center overflow-hidden">
                {{-- Contoh jika ada gambar dinamis: --}}
                {{-- <img src="{{ asset('storage/' . $item->foto) }}" class="w-full h-full object-cover"> --}}
            </div>

            <!-- Nama, Kode & Harga -->
            <div class="space-y-1">
                <h2 class="text-xl font-bold text-black">{{ $item->DES ?? 'Asus TUF' }}</h2>
                <p class="text-xs font-mono text-gray-500">{{ $item->ITM ?? 'LPTP-2026-001' }}</p>
                <div class="text-2xl font-bold text-black pt-2">
                    Rp {{ number_format($item->HARGA ?? 20000000, 0, ',', '.') }}
                </div>
            </div>

            <!-- Keterangan / Deskripsi -->
            <div class="space-y-1.5">
                <p class="text-xs text-gray-600 leading-relaxed">
                    {{ $item->KETERANGAN ?? 'Lorem ipsum dolor sit amet consectetur adipiscing elit quisque faucibus ex sapien vitae pellentesque sem placerat in id cursus mi.' }}
                </p>
            </div>

            <!-- Garis Dekorasi -->
            <div class="space-y-2 py-1">
                <hr class="border-gray-200">
                <hr class="border-gray-200">
            </div>

            <!-- Meta Info (Tanggal Masuk, Gudang, Status) -->
            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                    <span class="text-gray-500">Tanggal Masuk</span>
                    <span class="font-medium text-black">
                        {{ isset($item->created_at) ? \Carbon\Carbon::parse($item->created_at)->format('d M Y') : '14 Sep 2026' }}
                    </span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                    <span class="text-gray-500">Gudang</span>
                    <span class="font-medium text-black">{{ $item->gudang->DES ?? 'Gudang Garam Jaya' }}</span>
                </div>
                <div class="flex justify-between items-center pt-0.5">
                    <span class="text-gray-500">Status</span>
                    <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-700 border border-emerald-400 font-semibold rounded-md text-xs">
                        {{ $item->STATUS ?? 'In stok' }}
                    </span>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: RINGKASAN STOK & RIWAYAT MASUK / KELUAR --}}
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Ringkasan Stok (3 Card) -->
            <div class="border border-gray-300 rounded-2xl bg-white p-6 shadow-xs space-y-4">
                <h3 class="text-base font-bold text-black">Ringkasan Stok</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Total Produk -->
                    <div class="border border-gray-300 rounded-xl p-4 bg-white">
                        <p class="text-xs font-semibold text-black">Total Produk</p>
                        <div class="text-3xl font-extrabold text-black mt-2">
                            {{ $stok_total ?? 35 }}
                        </div>
                    </div>

                    <!-- Stok Menipis -->
                    <div class="border border-gray-300 rounded-xl p-4 bg-white">
                        <p class="text-xs font-semibold text-black">Stok menipis</p>
                        <div class="text-3xl font-extrabold text-amber-500 mt-2">
                            {{ $stok_menipis ?? 2 }}
                        </div>
                    </div>

                    <!-- Stok Habis -->
                    <div class="border border-gray-300 rounded-xl p-4 bg-white">
                        <p class="text-xs font-semibold text-black">Stok Habis</p>
                        <div class="text-3xl font-extrabold text-red-500 mt-2">
                            {{ $stok_habis ?? 6 }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Masuk / Keluar -->
            <div class="border border-gray-300 rounded-2xl bg-white p-6 shadow-xs space-y-4">
                <h3 class="text-base font-bold text-black">Riwayat Masuk / Keluar</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse">
                        <!-- Table Header -->
                        <thead>
                            <tr class="border-b border-gray-300 text-gray-700 font-semibold">
                                <th class="py-2.5 px-4">Tanggal</th>
                                <th class="py-2.5 px-4">Aktivitas</th>
                                <th class="py-2.5 px-4">Kode Stok</th>
                                <th class="py-2.5 px-4">Gudang</th>
                            </tr>
                        </thead>
                        <!-- Table Body -->
                        <tbody class="divide-y divide-gray-200 text-black">
                            @forelse($riwayat ?? [] as $row)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3 px-4">{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}</td>
                                    <td class="py-3 px-4">{{ $row->aktivitas }}</td>
                                    <td class="py-3 px-4 font-mono">{{ $row->kode_stok }}</td>
                                    <td class="py-3 px-4">{{ $row->nama_gudang }}</td>
                                </tr>
                            @empty
                                {{-- Data Statis Dummy --}}
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3 px-4">14 Sep 2026</td>
                                    <td class="py-3 px-4">Stok Masuk</td>
                                    <td class="py-3 px-4 font-mono">STK001</td>
                                    <td class="py-3 px-4">Gudang Garam Jaya</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

    </div>

</div>
@endsection