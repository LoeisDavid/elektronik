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
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- KOLOM KIRI: DETAIL GUDANG & FOTO --}}
        <div class="lg:col-span-5 border border-gray-300 rounded-2xl bg-white p-6 space-y-5 shadow-xs">
            
            <!-- Placeholder Gambar Gudang -->
            <div class="w-full h-56 bg-[#637381] rounded-2xl flex items-center justify-center overflow-hidden">
                {{-- Contoh jika menggunakan gambar dinamis: --}}
                {{-- <img src="{{ asset('storage/' . $gd->foto) }}" class="w-full h-full object-cover"> --}}
            </div>

            <!-- Judul & Lokasi Gudang -->
            <div>
                <h2 class="text-xl font-bold text-black">{{ $gd->DES ?? 'Gudang Garam jaya' }}</h2>
                <p class="text-sm text-gray-500">{{ $gd->LOKASI ?? 'Medokan ayu, Surabaya' }}</p>
            </div>

            <!-- Keterangan -->
            <div class="space-y-1.5">
                <h3 class="text-sm font-bold text-black">Keterangan:</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                    {{ $gd->KETERANGAN ?? 'Lorem ipsum dolor sit amet consectetur adipiscing elit quisque faucibus ex sapien vitae pellentesque sem placerat in id cursus mi.' }}
                </p>
            </div>

            <!-- Garis Dekorasi -->
            <div class="space-y-2 py-1">
                <hr class="border-gray-200">
                <hr class="border-gray-200">
            </div>

            <!-- Meta Info -->
            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                    <span class="text-gray-500">Tanggal dibuat:</span>
                    <span class="font-medium text-black">
                        {{ isset($gd->created_at) ? \Carbon\Carbon::parse($gd->created_at)->format('d M Y') : '14 Sep 2026' }}
                    </span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                    <span class="text-gray-500">Lokasi</span>
                    <span class="font-medium text-black">{{ $gd->LOKASI ?? 'Medokan ayu, Surabaya' }}</span>
                </div>
                <div class="flex justify-between items-center pt-0.5">
                    <span class="text-gray-500">Status</span>
                    <span class="font-bold text-black">{{ $gd->STATUS ?? 'Aktif' }}</span>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: SUMMARY & DAFTAR BARANG --}}
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Summary Box: Total Produk -->
            <div class="border border-gray-300 rounded-2xl bg-white p-6 shadow-xs">
                <h3 class="text-base font-bold text-black">Total Produk di gudang ini</h3>
                <div class="text-4xl font-extrabold text-black my-1">
                    {{ $gd->items_count ?? 60 }}
                </div>
                <p class="text-xs text-gray-500">Semua barang</p>
            </div>

            <!-- Container Daftar Barang -->
            <div class="border border-gray-300 rounded-2xl bg-white p-6 space-y-4 shadow-xs">
                <h3 class="text-base font-bold text-black">Daftar Barang</h3>

                <!-- Border Wrapper Tabel -->
                <div class="border border-gray-300 rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            {{-- Table Header --}}
                            <thead>
                                <tr class="bg-primary-db text-white text-xs font-semibold">
                                    <th class="py-3 px-4 text-center font-semibold">Kode Barang</th>
                                    <th class="py-3 px-4 text-center font-semibold">Nama Barang</th>
                                    <th class="py-3 px-4 text-center font-semibold">Jenis</th>
                                    <th class="py-3 px-4 text-center font-semibold">Stok</th>
                                    <th class="py-3 px-4 text-center font-semibold">Harga Jual</th>
                                </tr>
                            </thead>
                            {{-- Table Body --}}
                            <tbody class="divide-y divide-gray-200 text-xs text-black">
                                {{-- Jika ada variabel koleksi item dari controller, bisa di-loop menggunakan @forelse --}}
                                @forelse($items ?? [] as $item)
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3 px-4 font-mono text-center">{{ $item->KODE_ITEM }}</td>
                                        <td class="py-3 px-4 font-semibold text-center">{{ $item->NAMA_ITEM }}</td>
                                        <td class="py-3 px-4 text-center">{{ $item->JENIS }}</td>
                                        <td class="py-3 px-4 text-center">{{ $item->STOK }}</td>
                                        <td class="py-3 px-4 text-center">Rp {{ number_format($item->HARGA_JUAL, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    {{-- Data Statis Dummy jika data item belum dilempar dari controller --}}
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3 px-4 font-mono text-center">LPTP-2026-001</td>
                                        <td class="py-3 px-4 font-semibold text-center">ASUS TUF</td>
                                        <td class="py-3 px-4 text-center">Laptop</td>
                                        <td class="py-3 px-4 text-center">45</td>
                                        <td class="py-3 px-4 text-center">Rp 20.000.000</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3 px-4 font-mono text-center">LPTP-2026-001</td>
                                        <td class="py-3 px-4 font-semibold text-center">ASUS TUF</td>
                                        <td class="py-3 px-4 text-center">Laptop</td>
                                        <td class="py-3 px-4 text-center">45</td>
                                        <td class="py-3 px-4 text-center">Rp 20.000.000</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3 px-4 font-mono text-center">LPTP-2026-001</td>
                                        <td class="py-3 px-4 font-semibold text-center">ASUS TUF</td>
                                        <td class="py-3 px-4 text-center">Laptop</td>
                                        <td class="py-3 px-4 text-center">45</td>
                                        <td class="py-3 px-4 text-center">Rp 20.000.000</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3 px-4 font-mono text-center">LPTP-2026-001</td>
                                        <td class="py-3 px-4 font-semibold text-center">ASUS TUF</td>
                                        <td class="py-3 px-4 text-center">Laptop</td>
                                        <td class="py-3 px-4 text-center">45</td>
                                        <td class="py-3 px-4 text-center">Rp 20.000.000</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3 px-4 font-mono text-center">LPTP-2026-001</td>
                                        <td class="py-3 px-4 font-semibold text-center">ASUS TUF</td>
                                        <td class="py-3 px-4 text-center">Laptop</td>
                                        <td class="py-3 px-4 text-center">45</td>
                                        <td class="py-3 px-4 text-center">Rp 20.000.000</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Table Footer / Pagination --}}
                    <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-[#142854]">
                        <span>Menampilkan 1-3 dari 6 produk</span>
                        
                        <div class="flex items-center gap-1">
                            <button class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100 transition-colors">
                                ‹
                            </button>
                            <span class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-[#142854] text-white border-[#142854]">
                                1
                            </span>
                            <button class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100 transition-colors">
                                2
                            </button>
                            <button class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100 transition-colors">
                                ›
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>
@endsection