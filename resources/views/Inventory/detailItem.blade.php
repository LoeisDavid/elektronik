@extends('layouts.app')

@section('title', 'Inventory - Detail Item')
@section('header_title', 'Detail Item')

@section('content')
<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 select-none space-y-4 font-montserrat">

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
    <div class="grid grid-cols-2 gap-6 border border-gray-300 rounded-2xl bg-white p-6 space-y-5 shadow-xs w-full h-full">
        <!-- KETERANGAN -->
        <div class="col-span-1">
            <!-- Nama, Kode & Harga -->
            <div class="space-y-1">
                    <h2 class="text-3xl font-bold text-black">{{ $itm->DES ?? 'Asus TUF' }}</h2>
                    <p class="text-xl text-black">{{ $itm->ITM ?? 'LPTP-2026-001' }}</p>
                    <div class="text-2xl font-semibold text-black pt-2">
                        Rp {{ number_format($itm->HRG ?? 20000000, 0, ',', '.') }}
                    </div>
            </div>

            <!-- Keterangan / Deskripsi -->
            <div class="space-y-3 my-3">
                    <p class="text-xs text-black leading-relaxed">
                        {{ $item->KETERANGAN ?? 'Lorem ipsum dolor sit amet consectetur adipiscing elit quisque faucibus ex sapien vitae pellentesque sem placerat in id cursus mi.' }}
                    </p>
            </div>

            <!-- Meta Info (Tanggal Masuk, Gudang, Status) -->
            <div class="space-y-3 text-xs font-semibold">
                    <div class="flex justify-between items-center py-2 border-y border-black">
                        <span class="text-black">Tanggal Masuk</span>
                        <span class="font-medium text-black">
                            {{ isset($item->created_at) ? \Carbon\Carbon::parse($item->created_at)->format('d M Y') : '14 Sep 2026' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-black">
                        <span class="text-black">Gudang</span>
                        <span class="font-medium text-black">{{ $item->gudang->DES ?? 'Gudang Garam Jaya' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-black">Status</span>
                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-700 border border-emerald-400 font-semibold rounded-md text-xs">
                            {{ $item->STATUS ?? 'In stok' }}
                        </span>
                    </div>
            </div>
        </div>
        <!-- END KETERANGAN -->

        <!-- GAMBAR -->
        <div class="col-span-1">
            <!-- Placeholder Gambar Produk -->
            <div class="w-full h-64 bg-[#637381] rounded-2xl flex items-center justify-center overflow-hidden">
                {{-- Contoh jika ada gambar dinamis: --}}
                {{-- <img src="{{ asset('storage/' . $item->foto) }}" class="w-full h-full object-cover"> --}}
            </div>
        </div>
        <!-- END GAMBAR -->

        <!-- Bagian Riwayat Masuk / Keluar -->
        <div class="col-span-2 space-y-4">
            <h3 class="text-lg font-bold text-black">Riwayat Masuk / Keluar</h3>

            <!-- Search Bar -->
            <div class="relative w-48">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    placeholder="Cari" 
                    class="w-full pl-9 pr-3 py-1.5 bg-white border border-gray-800 rounded-lg text-xs text-black focus:outline-none focus:ring-1 focus:ring-[#142854]"
                >
            </div>

            <!-- Container Tabel -->
            <div class="border border-black rounded-lg overflow-hidden bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse">
                        <!-- Table Header -->
                        <thead>
                            <tr class="bg-primary-db text-white font-semibold">
                                <th class="py-2.5 px-6 font-medium text-center">Tanggal</th>
                                <th class="py-2.5 px-6 font-medium text-center">Aktivitas</th>
                                <th class="py-2.5 px-6 font-medium text-center">Kode Stok</th>
                                <th class="py-2.5 px-6 font-medium text-center">Gudang</th>
                            </tr>
                        </thead>
                        <!-- Table Body -->
                        <tbody class="divide-y divide-black text-black">
                            @forelse($riwayat ?? [] as $row)
                                <tr class="hover:bg-gray-50/80 transition-colors font-medium">
                                    <td class="py-3 px-6 text-center">{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}</td>
                                    <td class="py-3 px-6 text-center">{{ $row->aktivitas }}</td>
                                    <td class="py-3 px-6 text-center font-mono">{{ $row->kode_stok }}</td>
                                    <td class="py-3 px-6 text-center">{{ $row->nama_gudang }}</td>
                                </tr>
                            @empty
                                {{-- Data Statis Dummy Sesuai Desain --}}
                                <tr class="hover:bg-gray-50/80 transition-colors font-medium">
                                    <td class="py-2.5 px-6 text-center">14 Sep 2026</td>
                                    <td class="py-2.5 px-6 text-center">Stok Masuk</td>
                                    <td class="py-2.5 px-6 text-center font-mono">STK001</td>
                                    <td class="py-2.5 px-6 text-center">Gudang Garam Jaya</td>
                                </tr>
                                <tr class="hover:bg-gray-50/80 transition-colors font-medium">
                                    <td class="py-2.5 px-6 text-center">30 Sep 2026</td>
                                    <td class="py-2.5 px-6 text-center">Stok Masuk</td>
                                    <td class="py-2.5 px-6 text-center font-mono">STK002</td>
                                    <td class="py-2.5 px-6 text-center">Gudang Garam Jaya</td>
                                </tr>
                                <tr class="hover:bg-gray-50/80 transition-colors font-medium">
                                    <td class="py-2.5 px-6 text-center">24 Sep 2026</td>
                                    <td class="py-2.5 px-6 text-center">Stok Masuk</td>
                                    <td class="py-2.5 px-6 text-center font-mono">STK003</td>
                                    <td class="py-2.5 px-6 text-center">Gudang Garam Jaya</td>
                                </tr>
                                <tr class="hover:bg-gray-50/80 transition-colors font-medium">
                                    <td class="py-2.5 px-6 text-center">30 Sep 2026</td>
                                    <td class="py-2.5 px-6 text-center">Stok Keluar</td>
                                    <td class="py-2.5 px-6 text-center font-mono">STK004</td>
                                    <td class="py-2.5 px-6 text-center">Gudang Garam Jaya</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Bottom Right -->
            <div class="flex justify-end items-center gap-1 text-xs pt-1">
                <button class="h-7 w-7 border border-gray-400 rounded-md flex items-center justify-center text-gray-600 hover:bg-gray-100">&lsaquo;</button>
                <button class="h-7 w-7 bg-primary-db text-white rounded-md flex items-center justify-center font-semibold">1</button>
                <button class="h-7 w-7 border border-gray-400 rounded-md flex items-center justify-center text-gray-700 hover:bg-gray-100">2</button>
                <button class="h-7 w-7 border border-gray-400 rounded-md flex items-center justify-center text-gray-600 hover:bg-gray-100">&rsaquo;</button>
            </div>
        </div>
    </div>
    

</div>
@endsection