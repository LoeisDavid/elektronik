@extends('layouts.app')

@section('title', 'Inventory - Supplier')
@section('header_title', 'Supplier')

@section('content')
<?php
    // ---------- Helper PHP murni ----------
    $opsiSort = [
        'a-z'     => 'Nama (A-Z)',
        'z-a'     => 'Nama (Z-A)',
        'new-old' => 'Terbaru',
        'old-new' => 'Terlama',
    ];

    $keyword = (string) request('keyword', '');
    $sort    = request('sort');
    if (!isset($opsiSort[$sort])) $sort = 'a-z';

    $halaman  = $data->currentPage();
    $totalHal = $data->lastPage();

    // URL dengan mempertahankan search & sort
    $buatUrl = function (array $ubah = []) use ($keyword, $sort, $halaman) {
        $q = array_merge(['keyword' => $keyword, 'sort' => $sort, 'page' => $halaman], $ubah);
        if ($q['sort'] === 'a-z')  unset($q['sort']);
        if ((int) $q['page'] <= 1) unset($q['page']);
        $q = array_filter($q, fn ($v) => $v !== '' && $v !== null);

        return route('inventory.supplier') . (count($q) ? '?' . http_build_query($q) : '');
    };


    $btnBase   = 'h-7 w-7 border rounded-md flex items-center justify-center text-xs transition-all duration-200';
    $btnNormal = $btnBase . ' border-[#A8B1C1] text-[#142854] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200';
    $btnAktif  = $btnBase . ' font-semibold bg-[#142854] text-white border-[#142854] hover:bg-primary-sdb transition-all duration-200';
    $btnOff    = $btnBase . ' border-[#A8B1C1] text-gray-300 pointer-events-none';

    // Nomor halaman: 1 ... (tengah) ... terakhir
    $nomor = fn ($i) => $i === $halaman
        ? '<span class="' . $btnAktif . '">' . $i . '</span>'
        : '<a href="' . e($buatUrl(['page' => $i])) . '" class="' . $btnNormal . '">' . $i . '</a>';
    $titik = '<span class="px-1">...</span>';
?>

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6">

    {{-- ========================================================= --}}
    {{-- TOOLBAR --}}
    {{-- ========================================================= --}}
    <div class="flex items-center justify-between gap-3 mb-4">

        <div class="flex items-center gap-3">

            {{-- SEARCH --}}
            <form action="<?= e(route('inventory.supplier')) ?>" method="GET" class="relative w-64 md:w-80">
                <?php if ($sort !== 'a-z'): ?>
                    <input type="hidden" name="sort" value="<?= e($sort) ?>">
                <?php endif; ?>

                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-black">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </span>

                <input
                    type="text"
                    name="keyword"
                    value="<?= e($keyword) ?>"
                    placeholder="Cari kode atau nama supplier"
                    class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:border-primary-db focus:bg-primary-db/5 focus:ring-1 focus:ring-primary-db"
                >
            </form>

            {{-- SORT (HTML murni: <details> + link) --}}
            <details class="relative group">
                <summary class="list-none [&::-webkit-details-marker]:hidden h-9.5 px-3.5 bg-white border border-gray-800 rounded-xl flex items-center justify-between gap-2.5 text-sm font-medium text-black hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 cursor-pointer shadow-xs min-w-45">
                    <span>Urutkan: <?= e($opsiSort[$sort]) ?></span>
                    <svg class="w-4 h-4 text-gray-600 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </summary>

                <div class="absolute left-0 mt-2 w-48 bg-secondary-bw rounded-2xl p-2 shadow-2xl border border-gray-300 z-50 space-y-1.5">
                    <?php foreach ($opsiSort as $kode => $label): ?>
                        <a
                            href="<?= e($buatUrl(['sort' => $kode, 'page' => 1])) ?>"
                            class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left font-medium text-sm flex items-center justify-between hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 shadow-xs"
                        >
                            <span><?= e($label) ?></span>
                            <?php if ($sort === $kode): ?>
                                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </details>

        </div>

        {{-- BUTTON TAMBAH SUPPLIER --}}
        <button
            type="button"
            onclick="bukaModal('tambahSupplierModal')"
            class="h-9.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
        >
            <span class="text-lg leading-none font-bold">+</span>
            <span>Tambah supplier</span>
        </button>

    </div>

    {{-- ========================================================= --}}
    {{-- TABEL --}}
    {{-- ========================================================= --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 flex flex-col">

        <div class="overflow-visible">
            <table class="w-full text-left border-collapse">

                <thead>
                    <tr class="bg-primary-db text-white text-xs font-semibold rounded-t-lg">
                        <th class="py-2.5 px-4 font-semibold rounded-tl-lg">Kode Supplier</th>
                        <th class="py-2.5 px-4 font-semibold">Nama Supplier</th>
                        <th class="py-2.5 px-4 font-semibold">Nomor Telepon</th>
                        <th class="py-2.5 px-4 font-semibold text-center w-20 rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 text-sm text-black">
                    <?php if (count($data) > 0): ?>
                        <?php foreach ($data as $supplier): ?>
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-medium"><?= e($supplier->SPP) ?></td>
                                <td class="py-3.5 px-4"><?= e($supplier->DES) ?></td>
                                <td class="py-3.5 px-4"><?= e($supplier->TLP) ?></td>

                                <td class="py-3.5 px-4 text-center overflow-visible">

                                <div class="relative inline-block">

                                    {{-- BUTTON AKSI --}}
                                    <button
                                        type="button"
                                        onclick="toggleActionMenu(this)"
                                        class="h-8 w-8 border border-gray-400 bg-white hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 rounded-md inline-flex items-center justify-center transition-all duration-200 shadow-2xs cursor-pointer"
                                        title="Aksi Sales"
                                    >
                                        <svg
                                            class="w-4 h-4 text-primary-db"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"
                                            />
                                        </svg>
                                    </button>


                                    {{-- ACTION MENU --}}
                                    <div
                                        class="action-menu absolute right-0 top-full mt-1 w-28 bg-white border border-gray-300 rounded-lg shadow-xl py-1 z-50 text-left hidden"
                                    >

                                        {{-- EDIT --}}
                                        <button
                                            type="button"
                                            data-spp="{{ $supplier->SPP }}"
                                            data-des="{{ $supplier->DES }}"
                                            data-tlp="{{ $supplier->TLP }}"
                                            data-action="{{ route('inventory.supplier.update', ['spp' => $supplier->SPP]) }}"
                                            onclick="openEditModal(this)"
                                            class="w-full px-3 py-1.5 text-xs font-semibold text-gray-800 hover:bg-primary-db/5 hover:text-primary-db transition-all duration-200 flex items-center gap-2 cursor-pointer"
                                        >

                                            <svg
                                                class="w-3.5 h-3.5 text-gray-600"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                                stroke-width="2"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"
                                                />
                                            </svg>

                                            <span>Edit</span>

                                        </button>


                                        {{-- HAPUS --}}
                                        <form
                                            method="POST"
                                            action="{{ route('inventory.supplier.destroy', ['spp' => $supplier->SPP]) }}"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                onclick="return confirm('Hapus Supplier ini?')"
                                                class="w-full px-3 py-1.5 text-xs font-semibold text-red-500 hover:bg-red-50 transition-colors flex items-center gap-2 cursor-pointer"
                                            >

                                                <svg
                                                    class="w-3.5 h-3.5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="2"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4v3M4 7h16"
                                                    />
                                                </svg>

                                                <span>Hapus</span>

                                            </button>

                                        </form>

                                    </div>

                                </div>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500 text-sm">Tidak ada data supplier.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>

        {{-- FOOTER / PAGINATION --}}
        <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-[#142854] rounded-b-lg">

            <div class="whitespace-nowrap">
                Menampilkan <?= (int) ($data->firstItem() ?? 0) ?>-<?= (int) ($data->lastItem() ?? 0) ?>
                dari <?= (int) $data->total() ?> supplier
            </div>

                <div class="flex items-center gap-1">

                    <?php if ($halaman > 1): ?>
                        <a href="<?= e($buatUrl(['page' => $halaman - 1])) ?>" class="<?= $btnNormal ?>">‹</a>
                    <?php else: ?>
                        <span class="<?= $btnOff ?>">‹</span>
                    <?php endif; ?>

                    <?= $nomor(1) ?>

                <?php if ($totalHal > 3): ?>

                    <?php if ($halaman <= 2): ?>
                        <?= $nomor(2) ?>
                        <?= $nomor(3) ?>
                    <?php elseif ($halaman >= $totalHal - 1): ?>
                        <?= $titik ?>
                        <?= $nomor($totalHal - 2) ?>
                        <?= $nomor($totalHal - 1) ?>
                    <?php else: ?>
                        <?= $titik ?>
                        <?= $nomor($halaman - 1) ?>
                        <?= $nomor($halaman) ?>
                        <?= $nomor($halaman + 1) ?>
                    <?php endif; ?>

                    <?php if ($halaman <= $totalHal - 2): ?>
                        <?= $titik ?>
                    <?php endif; ?>

                    <?= $nomor($totalHal) ?>

                <?php elseif ($totalHal == 3): ?>
                    <?= $nomor(2) ?>
                    <?= $nomor(3) ?>
                <?php elseif ($totalHal == 2): ?>
                    <?= $nomor(2) ?>
                <?php endif; ?>

                    <?php if ($halaman < $totalHal): ?>
                        <a href="<?= e($buatUrl(['page' => $halaman + 1])) ?>" class="<?= $btnNormal ?>">›</a>
                    <?php else: ?>
                        <span class="<?= $btnOff ?>">›</span>
                    <?php endif; ?>

                </div>

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- ========================================================= --}}
    {{-- MODAL TAMBAH SUPPLIER --}}
    {{-- ========================================================= --}}
    <div
        id="tambahSupplierModal"
        class="fixed inset-0 z-50 {{ $errors->any() ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
    >
        <div class="absolute inset-0" onclick="tutupModal('tambahSupplierModal')"></div>

        <div class="relative w-full max-w-sm bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto">
            <h2 class="text-2xl font-bold mb-5">Tambah Supplier</h2>

            <form method="POST" id="formTambah" action="<?= e(route('inventory.supplier.store')) ?>" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold mb-1">Nama Supplier</label>
                    <input
                        type="text"
                        name="DES"
                        placeholder="Contoh: PT. Jaya Elektronik"
                        required
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        value="{{ old('DES') }}"
                    >
                    @error('DES')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Nomor Telepon</label>
                    <input
                        type="text"
                        name="TLP"
                        placeholder="Contoh: 081234567890"
                        required
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        value="{{ old('TLP') }}"
                    >
                    @error('TLP')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 flex items-center gap-3">
                    <button
                        type="button"
                        onclick="tutupModal('tambahSupplierModal')"
                        class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 transition-all duration-200 cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        id="btnTambah"
                        class="flex-1 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL EDIT SUPPLIER --}}
    {{-- ========================================================= --}}
    <div
        id="editSupplierModal"
        class="fixed inset-0 z-50 {{ $errors->editSupplier->any() ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
    >
        <div class="absolute inset-0" onclick="tutupModal('editSupplierModal')"></div>

        <div class="relative w-full max-w-sm bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto">
            <h2 class="text-2xl font-bold mb-5">Edit Supplier</h2>

            <form
                id="editSupplierForm"
                method="POST"
                class="space-y-4"
                action="{{ $errors->editSupplier->any() && old('SPP') ? route('inventory.supplier.update', ['spp' => old('SPP')]) : '' }}"
            >
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold mb-1">Kode Supplier</label>
                    <input
                        id="editKodeSupplier"
                        type="text"
                        name="SPP"
                        readonly
                        class="w-full bg-gray-100 border border-black/80 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 cursor-not-allowed focus:outline-none"
                        value="{{ $errors->editSupplier->any() ? old('SPP') : '' }}"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Nama Supplier</label>
                    <input
                        id="editNamaSupplier"
                        type="text"
                        name="DES"
                        required
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        value="{{ $errors->editSupplier->any() ? old('DES') : '' }}"
                    >
                    @error('DES', 'editSupplier')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Nomor Telepon</label>
                    <input
                        id="editTelpSupplier"
                        type="text"
                        name="TLP"
                        required
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        value="{{ $errors->editSupplier->any() ? old('TLP') : '' }}"
                    >
                    @error('TLP', 'editSupplier')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 flex items-center gap-3">
                    <button
                        type="button"
                        onclick="tutupModal('editSupplierModal')"
                        class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 transition-all duration-200 cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="flex-1 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // ---------- Menu aksi ----------
    function toggleActionMenu(button) {
        const menu = button.parentElement.querySelector('.action-menu');

        document.querySelectorAll('.action-menu').forEach(function (item) {
            if (item !== menu) item.classList.add('hidden');
        });

        menu.classList.toggle('hidden');
    }

    // ---------- Tutup menu/dropdown saat klik di luar ----------
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.action-menu') && !event.target.closest('button[onclick="toggleActionMenu(this)"]')) {
            document.querySelectorAll('.action-menu').forEach(function (menu) {
                menu.classList.add('hidden');
            });
        }

        if (!event.target.closest('#containerFilterSales')) {
            document.getElementById('panelFilterSales').classList.add('hidden');
            document.getElementById('arrowSalesSort').classList.remove('rotate-180');
        }
    });

    // Hanya menangani modal
    function bukaModal(id) {
        const m = document.getElementById(id);
        m.classList.remove('hidden');
        m.classList.add('flex');
    }

    function tutupModal(id) {
        const m = document.getElementById(id);
        m.classList.remove('flex');
        m.classList.add('hidden');
    }

    // Tutup menu <details> milik tombol yang diklik
    function tutupMenu(btn) {
        const d = btn.closest('details');
        if (d) d.removeAttribute('open');
    }

    function openEditModal(btn) {
        const d = btn.dataset;

        document.getElementById('editKodeSupplier').value = d.spp;
        document.getElementById('editNamaSupplier').value = d.des;
        document.getElementById('editTelpSupplier').value = d.tlp;
        document.getElementById('editSupplierForm').action = d.action;

        tutupMenu(btn);
        bukaModal('editSupplierModal');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            ['tambahSupplierModal', 'editSupplierModal'].forEach(tutupModal);
        }
    });

    // Mencegah double submit (ditaruh di luar fungsi agar aktif otomatis)
    document.getElementById('formTambah').addEventListener('submit', function (event) {
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