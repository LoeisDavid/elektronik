@extends('layouts.app')

@section('title', 'Laporan - Laporan Kasir')
@section('header_title', 'Laporan Kasir')

@section('content')
<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-6 select-none space-y-6">

    {{-- ========================================================= --}}
    {{-- SECTION ATAS: STATISTIK & FILTER --}}
    {{-- ========================================================= --}}
    <div class="border border-gray-300 rounded-2xl bg-white p-6 shadow-xs space-y-6">
        
        <!-- 4 Grid Stat Cards (Khusus Desktop) -->
        <div class="grid grid-cols-4 gap-4">
            
            <!-- Total Penjualan -->
            <div class="border border-gray-300 rounded-xl p-4 bg-white">
                <p class="text-xs font-semibold text-black">Total Penjualan</p>
                <div class="text-2xl font-bold text-black mt-1">
                    Rp {{ number_format($total_penjualan ?? 76800000, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-gray-500 mt-1">
                    Pada {{ $tanggal_penjualan ?? '16/09/2026' }}
                </p>
            </div>

            <!-- Jumlah Transaksi -->
            <div class="border border-gray-300 rounded-xl p-4 bg-white">
                <p class="text-xs font-semibold text-black">Jumlah Transaksi</p>
                <div class="text-2xl font-bold text-black mt-1">
                    {{ $jumlah_transaksi ?? 3 }} Order
                </div>
                <p class="text-[11px] text-gray-500 mt-1">Berhasil</p>
            </div>

            <!-- Total Kas Tunai -->
            <div class="border border-gray-300 rounded-xl p-4 bg-white">
                <p class="text-xs font-semibold text-black">Total Kas Tunai</p>
                <div class="text-2xl font-bold text-black mt-1">
                    Rp {{ number_format($total_kas_tunai ?? 40000000, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-gray-500 mt-1">
                    {{ $persen_kas_tunai ?? '52%' }} Dari total penjualan
                </p>
            </div>

            <!-- Non Tunai -->
            <div class="border border-gray-300 rounded-xl p-4 bg-white">
                <p class="text-xs font-semibold text-black">Non Tunai</p>
                <div class="text-2xl font-bold text-black mt-1">
                    Rp {{ number_format($qris_bank ?? 36800000, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-gray-500 mt-1">
                    {{ $persen_qris_bank ?? '48%' }} Dari total penjualan
                </p>
            </div>

        </div>

        <!-- Filter Area -->
        <div class="space-y-2">
            <h3 class="text-base font-bold text-black">Filter:</h3>
            
            <form action="{{ route('POS.laporan') }}" method="GET" class="flex items-center gap-4">
                <!-- Filter Tanggal -->
                <div class="relative w-64">
                    <input 
                        type="date" 
                        name="tanggal" 
                        value="{{ request('tanggal') }}"
                        class="w-full bg-white border border-gray-400 rounded-xl px-4 py-2.5 text-xs text-black focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db cursor-pointer"
                        placeholder="Tanggal"
                    >
                </div>

                <!-- Filter Kasir -->
                <div class="relative w-64">
                    <select 
                        name="kasir"
                        class="w-full bg-white border border-gray-400 rounded-xl px-4 py-2.5 text-xs text-black focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db cursor-pointer appearance-none"
                    >
                        <option value="">Kasir</option>
                        <option value="user_1" {{ request('kasir') == 'user_1' ? 'selected' : '' }}>User 1</option>
                        <option value="user_2" {{ request('kasir') == 'user_2' ? 'selected' : '' }}>User 2</option>
                        <option value="kezia" {{ request('kasir') == 'kezia' ? 'selected' : '' }}>Kezia</option>
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </span>
                </div>
            </form>
        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- SECTION BWAH: TABEL LAPORAN KASIR --}}
    {{-- ========================================================= --}}
    <div class="border border-gray-300 rounded-2xl bg-white overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                {{-- Header Tabel --}}
                <thead>
                    <tr class="bg-primary-db text-white text-xs font-semibold">
                        <th class="py-3 px-4 font-semibold">Kasir</th>
                        <th class="py-3 px-4 font-semibold">Waktu Buka</th>
                        <th class="py-3 px-4 font-semibold">Waktu Tutup</th>
                        <th class="py-3 px-4 font-semibold">Kas Awal</th>
                        <th class="py-3 px-4 font-semibold">Total Penjualan</th>
                        <th class="py-3 px-4 font-semibold">Kas Akhir Rill</th>
                        <th class="py-3 px-4 font-semibold">Selisih</th>
                    </tr>
                </thead>

                {{-- Body Tabel --}}
                <tbody class="divide-y divide-gray-200 text-xs text-black">
                    @forelse($laporan ?? [] as $row)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-medium">{{ $row->shift }}</td>
                            <td class="py-3.5 px-4">{{ $row->kasir }}</td>
                            <td class="py-3.5 px-4">{{ $row->waktu_buka }}</td>
                            <td class="py-3.5 px-4">{{ $row->waktu_tutup }}</td>
                            <td class="py-3.5 px-4">Rp {{ number_format($row->kas_awal, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4">Rp {{ number_format($row->total_penjualan, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4">Rp {{ number_format($row->kas_akhir_rill, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4">Rp {{ number_format($row->selisih, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        {{-- Data Statis Dummy sesuai gambar --}}
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="py-3.5 px-4">User 2</td>
                            <td class="py-3.5 px-4">20:00 WIB</td>
                            <td class="py-3.5 px-4">00:00 WIB</td>
                            <td class="py-3.5 px-4">Rp 0</td>
                            <td class="py-3.5 px-4">Rp 20.000.000</td>
                            <td class="py-3.5 px-4">Rp 20.000.000</td>
                            <td class="py-3.5 px-4">Rp 0</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="py-3.5 px-4">User 1</td>
                            <td class="py-3.5 px-4">15:00 WIB</td>
                            <td class="py-3.5 px-4">19:00 WIB</td>
                            <td class="py-3.5 px-4">Rp 0</td>
                            <td class="py-3.5 px-4">Rp 20.000.000</td>
                            <td class="py-3.5 px-4">Rp 20.000.000</td>
                            <td class="py-3.5 px-4">Rp 20.000</td>
                        </tr>
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="py-3.5 px-4">Kezia</td>
                            <td class="py-3.5 px-4">09:00 WIB</td>
                            <td class="py-3.5 px-4">14:00 WIB</td>
                            <td class="py-3.5 px-4">Rp 30.000</td>
                            <td class="py-3.5 px-4">Rp 36.800.000</td>
                            <td class="py-3.5 px-4">Rp 36.800.000</td>
                            <td class="py-3.5 px-4">Rp 36</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer / Pagination --}}
        <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-[#142854]">
            <span>Menampilkan 1-2 dari 4 produk</span>
            
            <div class="flex items-center gap-1">
                <button class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100 transition-colors cursor-pointer">
                    ‹
                </button>
                <span class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-[#142854] text-white border-[#142854]">
                    1
                </span>
                <button class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100 transition-colors cursor-pointer">
                    2
                </button>
                <button class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100 transition-colors cursor-pointer">
                    ›
                </button>
            </div>
        </div>
    </div>

</div>
@endsection