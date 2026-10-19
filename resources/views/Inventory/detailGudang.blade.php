@extends('layouts.app')

@section('title', 'Gudang - Detail Gudang')
@section('header_title', 'Detail Gudang')

@section('content')
@php
    $perPage = 10;
    $sortOptions = [
        'old-new' => 'Terlama',
        'new-old' => 'Terbaru',
        'a-z'     => 'A - Z',
        'z-a'     => 'Z - A',
    ];
@endphp

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

        {{-- Daftar Barang --}}
        <div class="border border-gray-300 rounded-2xl bg-white p-6 space-y-4 shadow-xs">
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-base font-bold text-black">Daftar Barang</h3>

                <div class="flex items-center gap-2">
                    {{-- Search --}}
                    <form method="GET" action="{{ route('inventory.gudang.show', $gd->GD) }}" class="flex items-center">
                        {{-- pertahankan sort saat search --}}
                        <input type="hidden" name="sort" value="{{ $sort }}">

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

                    {{-- Tombol filter / sort --}}
                    <div class="relative" id="sort-wrapper">
                        <button
                            type="button"
                            id="sort-toggle"
                            class="flex items-center justify-center w-9 h-8 bg-white border border-gray-400 rounded-xl text-black hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 cursor-pointer"
                            aria-haspopup="true"
                            aria-expanded="false"
                            title="Urutkan"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 4.5A1.5 1.5 0 014.5 3h15A1.5 1.5 0 0121 4.5c0 .4-.16.78-.44 1.06L14.5 11.6V19a1 1 0 01-1.45.9l-3-1.5a1 1 0 01-.55-.9v-5.9L3.44 5.56A1.5 1.5 0 013 4.5z"/>
                            </svg>
                        </button>

                        <div
                            id="sort-menu"
                            class="hidden absolute right-0 mt-2 w-40 bg-white border border-gray-300 rounded-xl shadow-lg z-20 overflow-hidden"
                        >
                            <div class="px-3 py-2 text-[10px] font-semibold uppercase tracking-wide text-gray-500 border-b border-gray-200">
                                Urutkan
                            </div>
                            @foreach($sortOptions as $value => $label)
                                <a
                                    href="{{ request()->fullUrlWithQuery(['sort' => $value, 'page' => null]) }}"
                                    class="flex items-center justify-between px-3 py-2 text-xs transition-colors
                                        {{ $sort === $value ? 'bg-primary-db text-white font-semibold hover:bg-primary-sdb transition-all duration-200' : 'text-black hover:bg-primary-db/5 transition-all duration-200' }}"
                                >
                                    {{ $label }}
                                    @if($sort === $value)
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="border border-gray-300 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-primary-db text-white text-xs font-semibold">
                                <th class="py-3 px-4 text-center font-semibold">Kode Barang</th>
                                <th class="py-3 px-4 text-center font-semibold">Nama Barang</th>
                                <th class="py-3 px-4 text-center font-semibold">QTY</th>
                                <th class="py-3 px-4 text-center font-semibold">Kategori</th>
                                <th class="py-3 px-4 text-center font-semibold">Merek</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-300 text-xs text-black">
                            @forelse($items as $item)
                                <tr class="h-10 hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 font-mono text-center">{{ $item->ITM }}</td>
                                    <td class="px-4 text-center">{{ $item->DES }}</td>
                                    <td class="px-4 text-center">{{ $item->QTY }}</td>
                                    <td class="px-4 text-center">{{ $item->KTG }}</td>
                                    <td class="px-4 text-center">{{ $item->MRK }}</td>
                                </tr>
                            @empty
                                <tr class="h-10">
                                    <td colspan="7" class="px-4 text-center text-gray-500">
                                        {{ request()->filled('keyword') ? 'Barang tidak ditemukan.' : 'Tidak ada barang di gudang ini.' }}
                                    </td>
                                </tr>
                            @endforelse

                            {{-- Baris kosong agar tabel selalu setinggi $perPage baris (sesuai desain) --}}
                            @for($i = max($items->count(), 1); $i < $perPage; $i++)
                                <tr class="h-10">
                                    <td colspan="7">&nbsp;</td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                {{-- Footer / pagination --}}
                <div class="px-4 py-3 bg-white border-t border-gray-300 flex items-center justify-between text-[10px] text-black">
                    <span>Menampilkan {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} produk</span>

                    {{ $items->links('vendor.pagination.gudang') }}
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    (function () {
        const toggle = document.getElementById('sort-toggle');
        const menu = document.getElementById('sort-menu');
        const wrapper = document.getElementById('sort-wrapper');
        if (!toggle || !menu) return;

        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            const hidden = menu.classList.toggle('hidden');
            toggle.setAttribute('aria-expanded', String(!hidden));
        });

        document.addEventListener('click', function (e) {
            if (!wrapper.contains(e.target)) {
                menu.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    })();
</script>
@endsection