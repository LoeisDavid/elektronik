@extends('layouts.app')

@section('title', 'Inventory - Kategori')
@section('header_title', 'Kategori')

@section('content')
<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6">

    {{-- ========================================================= --}}
    {{-- TOOLBAR (SEARCH, SORT DROPDOWN & TAMBAH KATEGORI) --}}
    {{-- ========================================================= --}}
    <div class="flex items-center justify-between gap-3 mb-4">

        {{-- SEARCH & SORT CONTAINER --}}
        <div class="flex items-center gap-3">

            {{-- INPUT SEARCH (tekan Enter untuk mencari) --}}
            <div class="relative w-64 md:w-80">
                <form action="{{ route('inventory.kategori') }}" method="GET">
                    <input type="hidden" name="sort" value="{{ $sort }}">

                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-black">
                        <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </span>

                    <input
                        type="text"
                        name="keyword"
                        placeholder="Cari kode atau nama kategori"
                        value="{{ request('keyword') }}"
                        class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:border-primary-db focus:bg-primary-db/5 focus:ring-1 focus:ring-primary-db">
                </form>
            </div>

            {{-- DROPDOWN URUTKAN (tanpa JS: memakai <details> + link, sorting dilakukan di server) --}}
            <details class="relative inline-block text-left group">

                <summary
                    class="list-none [&::-webkit-details-marker]:hidden h-9.5 px-3.5 bg-white border border-gray-800 rounded-xl flex items-center justify-between gap-2.5 text-sm font-medium text-black hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 cursor-pointer shadow-xs min-w-45">
                    <span>Urutkan: {{ $sort === 'desc' ? 'Nama (Z-A)' : 'Nama (A-Z)' }}</span>
                    <svg
                        class="w-4 h-4 text-gray-600 transition-transform duration-200 group-open:rotate-180"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </summary>

                <div class="absolute left-0 mt-2 w-48 bg-secondary-bw rounded-2xl p-2 shadow-2xl border border-gray-300 z-50 space-y-1.5">

                    {{-- Opsi 1: Nama (A-Z) --}}
                    <a
                        href="{{ request()->fullUrlWithQuery(['sort' => 'asc', 'page' => null]) }}"
                        class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 cursor-pointer shadow-xs">
                        <span>Nama (A-Z)</span>
                        @if ($sort === 'asc')
                        <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        @endif
                    </a>

                    {{-- Opsi 2: Nama (Z-A) --}}
                    <a
                        href="{{ request()->fullUrlWithQuery(['sort' => 'desc', 'page' => null]) }}"
                        class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 cursor-pointer shadow-xs">
                        <span>Nama (Z-A)</span>
                        @if ($sort === 'desc')
                        <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        @endif
                    </a>
                </div>
            </details>

        </div>

        {{-- BUTTON TAMBAH KATEGORI --}}
        <button
            type="button"
            onclick="openTambahKategoriModal()"
            class="h-9.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow">
            <span class="text-lg leading-none font-bold">+</span>
            <span>Tambah kategori</span>
        </button>

    </div>

    {{-- ========================================================= --}}
    {{-- TABLE KATEGORI --}}
    {{-- ========================================================= --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 flex flex-col">

        {{-- overflow-visible agar menu aksi tidak terpotong --}}
        <div class="overflow-visible">

            <table class="w-full table-fixed text-left border-collapse">

                {{-- Lebar kolom dikunci agar posisi header tetap saat data diurutkan --}}
                <colgroup>
                    <col class="w-48">
                    <col>
                    <col class="w-48">
                    <col class="w-24">
                </colgroup>

                {{-- HEADER --}}
                <thead>
                    <tr class="bg-primary-db text-white text-xs font-semibold rounded-t-lg">
                        <th class="py-2.5 px-4 font-semibold text-center rounded-tl-lg">Kode Kategori</th>
                        <th class="py-2.5 px-4 font-semibold text-center">Nama Kategori</th>
                        <th class="py-2.5 px-4 font-semibold text-center">Total Aset</th>
                        <th class="py-2.5 px-4 font-semibold text-center rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>

                {{-- BODY --}}
                <tbody class="divide-y divide-gray-200 text-sm text-black">

                    @forelse ($data as $kategori)
                    <tr class="hover:bg-gray-50/80 transition-colors">

                        {{-- Kode Kategori --}}
                        <td class="py-3.5 px-4 font-medium text-center font-mono">
                            {{ $kategori->KTG }}
                        </td>

                        {{-- Nama Kategori --}}
                        <td class="py-3.5 px-4 text-center truncate">
                            {{ $kategori->DES }}
                        </td>

                        {{-- Total Aset --}}
                        <td class="py-3.5 px-4 text-center font-medium">
                            Rp {{ number_format((float) ($asetPerKtg[$kategori->KTG] ?? 0), 0, ',', '.') }}
                        </td>

                        {{-- AKSI --}}
                        <td class="py-3.5 px-4 text-center overflow-visible">

                            <div class="relative inline-block">

                                {{-- BUTTON TRIGGER --}}
                                <button
                                    type="button"
                                    onclick="toggleActionMenu(this)"
                                    class="h-8 w-8 border border-gray-400 bg-white hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 rounded-md inline-flex items-center justify-center transition-all duration-200 cursor-pointer"
                                    title="Aksi Kategori">
                                    <svg
                                        class="w-4 h-4 text-primary-db"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        stroke-width="2">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                    </svg>
                                </button>

                                {{-- ACTION MENU (z-[999] agar berada di paling depan) --}}
                                <div
                                    class="action-menu absolute right-0 top-full mt-1 w-28 bg-white border border-gray-300 rounded-lg shadow-2xl py-1 z-6 text-left hidden">

                                    {{-- EDIT --}}
                                    <button
                                        type="button"
                                        data-ktg="{{ $kategori->KTG }}"
                                        data-des="{{ $kategori->DES }}"
                                        onclick="openEditKategoriModal(this)"
                                        class="w-full px-3 py-1.5 text-xs font-semibold text-gray-800 hover:bg-primary-db/5 hover:text-primary-db transition-all duration-200 flex items-center gap-2 cursor-pointer">
                                        <svg
                                            class="w-3.5 h-3.5 text-gray-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            stroke-width="2">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                        </svg>

                                        <span>Edit</span>
                                    </button>

                                    {{-- DELETE --}}
                                    <form
                                        method="POST"
                                        action="{{ route('inventory.kategori.destroy', ['ktg' => $kategori->KTG]) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            onclick="return confirm('Hapus kategori ini?')"
                                            class="w-full px-3 py-1.5 text-xs font-semibold text-red-500 hover:bg-red-50 transition-colors flex items-center gap-2 cursor-pointer">
                                            <svg
                                                class="w-3.5 h-3.5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                                stroke-width="2">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4v3M4 7h16" />
                                            </svg>

                                            <span>Hapus</span>
                                        </button>
                                    </form>

                                </div>

                            </div>

                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-gray-500 text-sm">
                            Tidak ada data kategori.
                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- FOOTER / PAGINATION --}}
        <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-[#142854] rounded-b-lg">

            {{-- INFO --}}
            <div class="whitespace-nowrap">
                Menampilkan
                {{ $data->firstItem() ?? 0 }}
                -
                {{ $data->lastItem() ?? 0 }}
                dari
                {{ $data->total() ?? 0 }}
                kategori
            </div>

            {{-- PAGINATION --}}
            @if ($data->lastPage() > 1)

            @php
            $current = $data->currentPage();
            $last = $data->lastPage();

            if ($last <= 5) {
                $pages=range(1, $last);
                } elseif ($current <=3) {
                $pages=[1, 2, 3, '...' , $last];
                } elseif ($current>= $last - 2) {
                $pages = [1, '...', $last - 2, $last - 1, $last];
                } else {
                $pages = [1, '...', $current - 1, $current, $current + 1, '...', $last];
                }
                @endphp

                <div class="flex items-center gap-1">

                    {{-- PREVIOUS --}}
                    @if ($data->onFirstPage())
                    <span class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#A8B1C1]">‹</span>
                    @else
                    <a
                        href="{{ $data->previousPageUrl() }}"
                        class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200">‹</a>
                    @endif

                    {{-- NOMOR HALAMAN --}}
                    @foreach ($pages as $page)
                    @if ($page === '...')
                    <span class="px-1">...</span>
                    @else
                    <a
                        href="{{ $data->url($page) }}"
                        class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold
                                {{ $current == $page
                                    ? 'bg-[#142854] text-white border-[#142854] hover:bg-primary-sdb transition-all duration-200'
                                    : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200' }}">
                        {{ $page }}
                    </a>
                    @endif
                    @endforeach

                    {{-- NEXT --}}
                    @if ($data->hasMorePages())
                    <a
                        href="{{ $data->nextPageUrl() }}"
                        class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200">›</a>
                    @else
                    <span class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#A8B1C1]">›</span>
                    @endif

                </div>

                @endif

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- MODAL TAMBAH KATEGORI --}}
    {{-- ========================================================= --}}
    <div
        id="tambahKategoriModal"
        class="fixed inset-0 z-50 {{ $errors->any() ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
        <div class="absolute inset-0" onclick="closeTambahKategoriModal()"></div>

        <div class="relative w-full max-w-sm bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto">
            <h2 class="text-2xl font-bold mb-5">Tambah Kategori</h2>

            <form method="POST" id="formTambah" action="{{ route('inventory.kategori.store') }}" class="space-y-4">
                @csrf

                {{-- Nama Kategori --}}
                <div>
                    <label class="block text-sm font-semibold mb-1">Nama Kategori</label>
                    <input
                        type="text"
                        name="DES"
                        placeholder="Contoh: Elektronik"
                        required
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        value="{{ old('DES') }}">
                    @error('DES')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 flex items-center gap-3">
                    <button
                        type="button"
                        onclick="closeTambahKategoriModal()"
                        class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 transition-all duration-200 cursor-pointer">
                        Batal
                    </button>
                    <button
                        type="submit"
                        id="btnTambah"
                        class="flex-1 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL EDIT KATEGORI --}}
    {{-- ========================================================= --}}
    <div
        id="editKategoriModal"
        class="fixed inset-0 z-50 {{ $errors->editKategori->any() ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
        <div class="absolute inset-0" onclick="closeEditKategoriModal()"></div>

        <div class="relative w-full max-w-sm bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto">
            <h2 class="text-2xl font-bold mb-5">Edit Kategori</h2>

            <form
                id="editKategoriForm"
                method="POST"
                class="space-y-4"
                action="{{ $errors->editKategori->any() && old('KTG') ? route('inventory.kategori.update', ['ktg' => old('KTG')]) : '' }}">
                @csrf
                @method('PUT')

                {{-- Kode Kategori (tidak bisa diubah) --}}
                <div>
                    <label class="block text-sm font-semibold mb-1">Kode Kategori</label>
                    <input
                        id="editKodeKategori"
                        type="text"
                        name="KTG"
                        readonly
                        class="w-full bg-gray-100 border border-black/80 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 cursor-not-allowed focus:outline-none"
                        value="{{ $errors->editKategori->any() ? old('KTG') : '' }}">
                </div>

                {{-- Nama Kategori --}}
                <div>
                    <label class="block text-sm font-semibold mb-1">Nama Kategori</label>
                    <input
                        id="editNamaKategori"
                        type="text"
                        name="DES"
                        required
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        value="{{ $errors->editKategori->any() ? old('DES') : '' }}">
                    @error('DES', 'editKategori')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 flex items-center gap-3">
                    <button
                        type="button"
                        onclick="closeEditKategoriModal()"
                        class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 transition-all duration-200 cursor-pointer">
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="flex-1 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Toggle menu aksi (Edit / Hapus)
    function toggleActionMenu(button) {

        const menu = button.parentElement.querySelector('.action-menu');

        document.querySelectorAll('.action-menu').forEach(function(item) {
            if (item !== menu) {
                item.classList.add('hidden');
            }
        });

        menu.classList.toggle('hidden');
    }

    // Tutup menu aksi saat klik di luar
    document.addEventListener('click', function(event) {

        if (
            !event.target.closest('.action-menu') &&
            !event.target.closest('button[onclick="toggleActionMenu(this)"]')
        ) {
            document.querySelectorAll('.action-menu').forEach(function(menu) {
                menu.classList.add('hidden');
            });
        }

    });

    // Function Modal Tambah
    function openTambahKategoriModal() {
        document.getElementById('tambahKategoriModal').classList.remove('hidden');
        document.getElementById('tambahKategoriModal').classList.add('flex');
    }

    function closeTambahKategoriModal() {
        document.getElementById('tambahKategoriModal').classList.add('hidden');
        document.getElementById('tambahKategoriModal').classList.remove('flex');
    }

    // Function Modal Edit
    function openEditKategoriModal(btn) {
        const KTG = btn.getAttribute('data-ktg');
        const DES = btn.getAttribute('data-des');

        document.getElementById('editKodeKategori').value = KTG;
        document.getElementById('editNamaKategori').value = DES;

        // Set URL action secara dinamis menggantikan placeholder ':ktg'
        const updateUrl = "{{ route('inventory.kategori.update', ':ktg') }}".replace(':ktg', encodeURIComponent(KTG));
        document.getElementById('editKategoriForm').action = updateUrl;

        document.getElementById('editKategoriModal').classList.remove('hidden');
        document.getElementById('editKategoriModal').classList.add('flex');
    }

    function closeEditKategoriModal() {
        document.getElementById('editKategoriModal').classList.add('hidden');
        document.getElementById('editKategoriModal').classList.remove('flex');
    }

    // Mencegah double submit (ditaruh di luar fungsi agar aktif otomatis)
    document.getElementById('formTambah').addEventListener('submit', function(event) {
        const btn = document.getElementById('btnTambah');

        // 1. Cek apakah tombol sudah dalam kondisi disabled
        if (btn.disabled) {
            event.preventDefault();
            return;
        }

        // 2. Matikan tombol dan ganti teksnya agar user tahu sedang memproses
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed'); // Opsional: Tambah efek visual redup Tailwind
        btn.innerText = 'Menyimpan...';
    });
</script>
@endpush
@endsection