@extends('layouts.app')

@section('title', 'Gudang - Daftar Barang')
@section('header_title', 'Daftar Barang')

@section('content')
<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 select-none space-y-4">

    {{-- ========================================================= --}}
    {{-- BARIS ATAS: KEMBALI + SEARCH BAR & FILTER --}}
    {{-- ========================================================= --}}
    <div class="flex items-center justify-between gap-4">
        {{-- Kembali ke Gudang --}}
        <a 
            href="{{ route('inventory.gudang') }}" 
            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-black transition-colors cursor-pointer"
        >
            <svg class="w-4 h-4 mr-1 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            Kembali ke Gudang
        </a>

        {{-- Search Input & Filter Button --}}
        <div class="flex items-center gap-2">
            <div class="relative w-64">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-black">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    placeholder="Cari produk" 
                    class="w-full pl-9 pr-3 py-1.5 bg-white border border-gray-400 rounded-xl text-xs text-black focus:outline-none focus:ring-1 focus:ring-primary-db"
                >
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MAIN CONTENT: TABEL DAFTAR BARANG --}}
    {{-- ========================================================= --}}
    <div class="space-y-6 w-full">
        <!-- Container Daftar Barang -->
        <div class="border border-gray-300 rounded-2xl bg-white p-6 space-y-4 shadow-xs">
            <h3 class="text-base font-bold text-black">Daftar Barang Gudang Jaya</h3>
            <!-- Border Wrapper Tabel -->
            <div class="border border-gray-300 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        {{-- Table Header --}}
                        <thead>
                            <tr class="bg-primary-db text-white text-xs font-semibold">
                                <th class="py-3 px-4 text-center font-semibold">Kode Barang</th>
                                <th class="py-3 px-4 text-center font-semibold">Nama Barang</th>
                                <th class="py-3 px-4 text-center font-semibold">Kategori</th>
                                <th class="py-3 px-4 text-center font-semibold">Supplier</th>
                                <th class="py-3 px-4 text-center font-semibold">Merek</th>
                                <th class="py-3 px-4 text-center font-semibold">Stok</th>
                                <th class="py-3 px-4 text-center font-semibold">Harga</th>
                            </tr>
                        </thead>
                        {{-- Table Body --}}
                        <tbody class="divide-y divide-gray-200 text-xs text-black">
                            @forelse($items ?? [] as $item)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3 px-4 font-mono text-center">{{ $item->ITM ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center">{{ $item->DES ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center">{{ $item->kategori->DES ?? $item->KTG ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center">{{ $item->supplier->DES ?? $item->SPP ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center">{{ $item->merek->DES ?? $item->MRK ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center">{{ $item->STOK ?? 0 }} Unit</td>
                                    <td class="py-3 px-4 text-center">Rp {{ number_format($item->HRG ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                {{-- Data Statis Dummy Sesuai Gambar --}}
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3 px-4 font-mono text-center">LP-4124215</td>
                                    <td class="py-3 px-4 text-center">Asus TUF</td>
                                    <td class="py-3 px-4 text-center">Laptop</td>
                                    <td class="py-3 px-4 text-center">Asus</td>
                                    <td class="py-3 px-4 text-center">Asus</td>
                                    <td class="py-3 px-4 text-center">45 Unit</td>
                                    <td class="py-3 px-4 text-center">Rp 20.000.000</td>
                                </tr>
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3 px-4 font-mono text-center">LP-7863467</td>
                                    <td class="py-3 px-4 text-center">Lenovo LOQ</td>
                                    <td class="py-3 px-4 text-center">Laptop</td>
                                    <td class="py-3 px-4 text-center">Lenovo</td>
                                    <td class="py-3 px-4 text-center">Lenovo</td>
                                    <td class="py-3 px-4 text-center">45 Unit</td>
                                    <td class="py-3 px-4 text-center">Rp 20.000.000</td>
                                </tr>
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3 px-4 font-mono text-center">KK-7547240</td>
                                    <td class="py-3 px-4 text-center">Kulkas Sharp</td>
                                    <td class="py-3 px-4 text-center">Kulkas</td>
                                    <td class="py-3 px-4 text-center">Sharp</td>
                                    <td class="py-3 px-4 text-center">Sharp</td>
                                    <td class="py-3 px-4 text-center">45 Unit</td>
                                    <td class="py-3 px-4 text-center">Rp 20.000.000</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Table Footer / Pagination --}}
                <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-primary-db">
                    <span>Menampilkan 1-3 dari 6 produk</span>
                    
                    <div class="flex items-center gap-1">
                        <button class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-primary-db hover:bg-gray-100 transition-colors">
                            ‹
                        </button>
                        <span class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-primary-db text-white border-primary-db">
                            1
                        </span>
                        <button class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-primary-db hover:bg-gray-100 transition-colors">
                            2
                        </button>
                        <button class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-primary-db hover:bg-gray-100 transition-colors">
                            ›
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection