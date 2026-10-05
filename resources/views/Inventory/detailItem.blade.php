@extends('layouts.app')

@section('title', 'Inventory - Detail Item')
@section('header_title', 'Detail Item')

@section('content')
<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 space-y-4">

    {{-- Kembali --}}
    <div>
        <a href="{{ route('inventory.item') }}"
           class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-black transition-colors">
            <svg class="w-4 h-4 mr-1 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            Kembali ke Inventory
        </a>
    </div>

    {{-- ===================== INFO ITEM (ITM) ===================== --}}
    <div class="border border-gray-300 rounded-2xl bg-white p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3">
            <h2 class="text-2xl font-bold text-black">{{ $item->DES }}</h2>
            <p class="text-xs text-gray-700">{{ $item->ITM }}</p>
            <p class="text-xl font-bold text-black">Rp {{ number_format($item->HRG ?? 0, 0, ',', '.') }}</p>

            <div class="border-t border-gray-300 text-xs text-black">
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span>Total Unit Masuk</span><span>{{ $summary['total'] }} Unit</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span>Tersedia (In Stok)</span><span>{{ $summary['in_stok'] }} Unit</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span>Terjual</span><span>{{ $summary['terjual'] }} Unit</span>
                </div>
            </div>
        </div>
        <div class="bg-gray-500 rounded-xl min-h-40"></div>
    </div>

    {{-- ===================== RIWAYAT (ITMDT) ===================== --}}
    <div class="border border-gray-300 rounded-2xl bg-white p-6 space-y-4">
        <h3 class="text-base font-bold text-black">Riwayat Masuk / Keluar</h3>

        {{-- Search + Filter --}}
        <form method="GET" id="filterForm" class="flex flex-wrap items-center gap-2">
            <div class="relative w-64">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-black">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama gudang / kode stok"
                       class="w-full pl-9 pr-3 py-1.5 bg-white border border-gray-400 rounded-xl text-xs text-black focus:outline-none focus:ring-1 focus:ring-primary-db">
            </div>

            <input type="date" name="tanggal" value="{{ $tanggal }}"
                   onchange="this.form.submit()"
                   class="py-1.5 px-3 bg-white border border-gray-400 rounded-xl text-xs text-black focus:outline-none focus:ring-1 focus:ring-primary-db">

            <select name="status" onchange="this.form.submit()"
                    class="py-1.5 px-3 bg-white border border-gray-400 rounded-xl text-xs text-black focus:outline-none">
                <option value="semua"   @selected($status === 'semua')>Semua Status</option>
                <option value="in_stok" @selected($status === 'in_stok')>In Stok</option>
                <option value="terjual" @selected($status === 'terjual')>Terjual</option>
            </select>

            <select name="sort" onchange="this.form.submit()"
                    class="py-1.5 px-3 bg-white border border-gray-400 rounded-xl text-xs text-black focus:outline-none">
                <option value="default"        @selected($sort === 'default')>Default (Belum Terjual & Terlama)</option>
                <option value="masuk_terbaru"  @selected($sort === 'masuk_terbaru')>Barang Masuk Terbaru</option>
                <option value="masuk_terlama"  @selected($sort === 'masuk_terlama')>Barang Masuk Terlama</option>
                <option value="keluar_terbaru" @selected($sort === 'keluar_terbaru')>Barang Keluar Terbaru</option>
                <option value="keluar_terlama" @selected($sort === 'keluar_terlama')>Barang Keluar Terlama</option>
                <option value="az"             @selected($sort === 'az')>A - Z</option>
                <option value="za"             @selected($sort === 'za')>Z - A</option>
            </select>

            <button type="submit" class="py-1.5 px-4 bg-primary-db text-white rounded-xl text-xs font-semibold">Cari</button>

            @if($q || $tanggal || $status !== 'semua' || $sort !== 'default')
                <a href="{{ url()->current() }}" class="text-xs text-gray-600 hover:text-black underline">Reset</a>
            @endif
        </form>

        {{-- Tabel --}}
        <div class="border border-gray-300 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-primary-db text-white text-xs font-semibold">
                            <th class="py-3 px-4 text-center">Kode Stok</th>
                            <th class="py-3 px-4 text-center">Tanggal Masuk</th>
                            <th class="py-3 px-4 text-center">Tanggal Keluar</th>
                            <th class="py-3 px-4 text-center">Gudang</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-xs text-black">
                        @forelse($items as $row)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-3 px-4 font-mono text-center">{{ $row->kode_stok }}</td>
                                <td class="py-3 px-4 text-center">{{ $row->tgl_masuk?->translatedFormat('d M Y') ?? '-' }}</td>
                                <td class="py-3 px-4 text-center">{{ $row->tgl_keluar?->translatedFormat('d M Y') ?? '-' }}</td>
                                <td class="py-3 px-4 text-center">{{ $row->nama_gudang }}</td>
                                <td class="py-3 px-4 text-center">
                                    @if($row->terjual)
                                        <span class="px-3 py-0.5 rounded border border-red-400 bg-red-100 text-red-700 font-semibold">Terjual</span>
                                    @else
                                        <span class="px-3 py-0.5 rounded border border-green-400 bg-green-100 text-green-700 font-semibold">IN STOK</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button
                                    type="button"
                                    onclick="openBarcodeModal(this)"
                                    data-kode="{{ $row->kode_stok }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-gray-800 hover:bg-gray-100 rounded-lg text-xs font-semibold text-black cursor-pointer transition-colors"
                                >

                                    <svg
                                        class="w-3.5 h-3.5 text-current"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm0 5.25h.007v.008H3.75V12zm0 5.25h.007v.008H3.75V12.75z"
                                        />
                                    </svg>

                                    <span>Barcode</span>

                                </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">Data tidak ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer / Pagination --}}
            <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-primary-db">
                <span>
                    Menampilkan {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }}
                    dari {{ $items->total() }} data
                </span>

                @if($items->hasPages())
                <div class="flex items-center gap-1">
                    @if($items->onFirstPage())
                        <span class="h-7 w-7 border border-gray-200 rounded-md flex items-center justify-center text-gray-300">‹</span>
                    @else
                        <a href="{{ $items->previousPageUrl() }}" class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center hover:bg-gray-100">‹</a>
                    @endif

                    @foreach(range(1, $items->lastPage()) as $p)
                        @if($p === $items->currentPage())
                            <span class="h-7 w-7 border rounded-md flex items-center justify-center font-semibold bg-primary-db text-white border-primary-db">{{ $p }}</span>
                        @else
                            <a href="{{ $items->url($p) }}" class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center hover:bg-gray-100">{{ $p }}</a>
                        @endif
                    @endforeach

                    @if($items->hasMorePages())
                        <a href="{{ $items->nextPageUrl() }}" class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center hover:bg-gray-100">›</a>
                    @else
                        <span class="h-7 w-7 border border-gray-200 rounded-md flex items-center justify-center text-gray-300">›</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Barcode -->
    <div
        id="modalBarcode"
        onclick="closeBarcodeKlikLuar(event)"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4"
    >
        <div
            onclick="event.stopPropagation()"
            class="w-full max-w-xs bg-white rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-100 animate-fade-in"
        >
            {{-- AREA BARCODE (UNTUK DIPRINT) --}}
            <div
                id="areaStrukPrint"
                class="bg-white p-4 rounded-xl border border-dashed border-gray-300 flex flex-col items-center justify-center min-h-30"
            >
                <!-- Elemen SVG untuk merender barcode -->
                <svg id="barcodeCanvas" class="w-full h-auto max-h-24"></svg>
            </div>

            {{-- TOMBOL PRINT --}}
            <div class="pt-1">
                <button
                    type="button"
                    onclick="cetakStruk()"
                    class="w-full bg-primary-db hover:bg-primary-sdb active:bg-black text-white rounded-xl py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer shadow-sm flex items-center justify-center gap-2"
                >
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4h2m0 0h10m-6 4h6a2 2 0 002-2v-4H9v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                        />
                    </svg>
                    Print Barcode
                </button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    function openBarcodeModal(btn) {
        // 1. Ambil nilai kode_stok dari atribut data-kode tombol yang diklik
        const kodeTransaksi = btn.dataset.kode;

        // Cek jika kodeTransaksi kosong agar tidak error
        if (!kodeTransaksi) {
            console.error('Kode stok tidak ditemukan pada tombol.');
            return;
        }

        const modal = document.getElementById('modalBarcode');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // 2. Render Barcode CODE128
        JsBarcode("#barcodeCanvas", kodeTransaksi, {
            format: "CODE128",       // Jenis barcode
            lineColor: "#000000",   // Warna garis barcode
            width: 2,               // Ketebalan garis
            height: 50,             // Tinggi garis barcode
            displayValue: true,     // Menampilkan teks kode di bawah garis
            fontSize: 12,           // Ukuran teks bawah
            fontOptions: "bold",
            margin: 5
        });
    }

    function closeBarcodeModal() {

        const modal =
            document.getElementById('modalBarcode');

        modal.classList.remove('flex');
        modal.classList.add('hidden');

    }


    function closeBarcodeKlikLuar(event) {

        if (event.target === event.currentTarget) {
            closeBarcodeModal();
        }

    }

</script>
@endsection