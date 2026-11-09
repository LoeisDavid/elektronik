@extends('layouts.app')

@section('title', 'Gudang - Daftar Barang')
@section('header_title', 'Daftar Barang')

@section('content')
<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 space-y-4">

    {{-- Baris atas: kembali + search --}}
    <div class="flex items-center justify-between gap-4">
        <a 
            href="{{ route('inventory.gudang.show', $gd->GD) }}" 
            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-black transition-colors cursor-pointer"
        >
            <svg class="w-4 h-4 mr-1 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            Kembali ke Detail Gudang
        </a>

        <form method="GET" action="{{ route('inventory.gudang.daftarBarang', $gd->GD) }}" class="flex items-center gap-2">
            <div class="relative w-64">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-black">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    name="keyword"
                    value="{{ request('keyword') }}"
                    placeholder="Cari produk" 
                    class="w-full pl-9 pr-3 py-1.5 bg-white border border-gray-400 rounded-xl text-xs text-black transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db"
                >
            </div>
        </form>
    </div>

    {{-- Tabel daftar barang --}}
    <div class="space-y-6 w-full">
        <div class="border border-gray-300 rounded-2xl bg-white p-6 space-y-4 shadow-xs">
            <h3 class="text-base font-bold text-black">Daftar Barang {{ $gd->DES }}</h3>

            <div class="border border-gray-300 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-primary-db text-white text-xs font-semibold">
                                <th class="py-3 px-4 text-center font-semibold">Kode Barang</th>
                                <th class="py-3 px-4 text-center font-semibold">Nama Barang</th>
                                <th class="py-3 px-4 text-center font-semibold">Kategori</th>
                                <th class="py-3 px-4 text-center font-semibold">Supplier</th>
                                <th class="py-3 px-4 text-center font-semibold">Merek</th>
                                <th class="py-3 px-4 text-center font-semibold">Harga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-xs text-black">
                            @forelse($items as $item)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3 px-4 font-mono text-center">{{ $item->BC_OUT }}</td>
                                    <td class="py-3 px-4 text-center">{{ $item->DES }}</td>
                                    <td class="py-3 px-4 text-center">{{ $item->KTG }}</td>
                                    <td class="py-3 px-4 text-center">{{ $item->SPP }}</td>
                                    <td class="py-3 px-4 text-center">{{ $item->MRK }}</td>
                                    <td class="py-3 px-4 text-center">Rp {{ number_format($item->HRB, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 px-4 text-center text-gray-500">
                                        Tidak ada barang di gudang ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer / pagination --}}
                <x-pagination :paginator="$items" label="produk" />
            </div>
        </div>
    </div>

</div>
@endsection