@extends('layouts.app')

@section('title', 'Produk - Inventory')
@section('header_title', 'Inventory')

@section('content')
<?php
    // ---------- Helper PHP murni ----------
    $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');

    $halaman  = $pagination['page'];
    $totalHal = $pagination['total_pages'];

    // URL dengan mempertahankan search & sort
    $buatUrl = function (array $ubah = []) use ($keyword, $sort, $halaman) {
        $q = array_merge(['keyword' => $keyword, 'sort' => $sort, 'page' => $halaman], $ubah);
        if ($q['sort'] === 'nama_asc') unset($q['sort']);
        if ((int) $q['page'] <= 1)     unset($q['page']);
        $q = array_filter($q, fn ($v) => $v !== '' && $v !== null);

        return route('inventory.item') . (count($q) ? '?' . http_build_query($q) : '');
    };

    // Pesan error per form (bag: tambah / edit / stok)
    $galat = fn ($bag, $field) => ($m = $errors->getBag($bag)->first($field))
        ? '<span class="text-red-500 text-xs mt-1 block">' . e($m) . '</span>'
        : '';

    // Modal otomatis terbuka lagi setelah simpan stok / error validasi
    $bukaStok   = !empty($stokList) || $errors->hasBag('stok');
    $bukaTambah = $errors->hasBag('tambah');


    $btnBase   = 'h-7 w-7 border rounded-md flex items-center justify-center text-xs transition-all duration-200';
    $btnNormal = $btnBase . ' border-[#A8B1C1] text-[#142854] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200';
    $btnAktif  = $btnBase . ' font-semibold bg-[#142854] text-white border-[#142854] hover:bg-primary-sdb transition-all duration-200';
    $btnOff    = $btnBase . ' border-[#A8B1C1] text-gray-300 pointer-events-none';

    // Nomor halaman: 1 ... (tengah) ... terakhir
    $nomor = fn ($i) => $i === $halaman
        ? '<span class="' . $btnAktif . '">' . $i . '</span>'
        : '<a href="' . e($buatUrl(['page' => $i])) . '" class="' . $btnNormal . '">' . $i . '</a>';
    $titik = '<span class="px-1">...</span>';

    $inputCls = 'w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-black transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db';
    $labelCls = 'block text-xs font-medium text-black mb-1.5';
?>

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 space-y-4">

    {{-- ========================================================= --}}
    {{-- STAT CARDS --}}
    {{-- ========================================================= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="border border-gray-300 rounded-xl p-4 bg-white">
            <p class="text-xs font-semibold text-black">Total Produk</p>
            <div class="text-3xl font-bold text-black mt-1"><?= (int) $stat['total'] ?></div>
            <p class="text-[11px] text-gray-500 mt-1">Semua barang</p>
        </div>

        <div class="border border-gray-300 rounded-xl p-4 bg-white">
            <p class="text-xs font-semibold text-black">Stok menipis</p>
            <div class="text-3xl font-bold text-amber-500 mt-1"><?= (int) $stat['menipis'] ?></div>
            <p class="text-[11px] text-gray-500 mt-1">Perlu restock</p>
        </div>

        <div class="border border-gray-300 rounded-xl p-4 bg-white">
            <p class="text-xs font-semibold text-black">Stok Habis</p>
            <div class="text-3xl font-bold text-red-500 mt-1"><?= (int) $stat['habis'] ?></div>
            <p class="text-[11px] text-gray-500 mt-1">Tidak tersedia</p>
        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- TOOLBAR --}}
    {{-- ========================================================= --}}
    <div class="flex items-center justify-between gap-3">

        <div class="flex items-center gap-2">

            {{-- SEARCH --}}
            <form action="<?= e(route('inventory.item')) ?>" method="GET" class="relative w-64 md:w-80">
                <?php if ($sort !== 'nama_asc'): ?>
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
                    placeholder="Cari produk"
                    class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:border-primary-db focus:bg-primary-db/5 focus:ring-1 focus:ring-primary-db"
                >
            </form>

            {{-- SORT (HTML murni: <details> + link) --}}
            <details class="relative group">
                <summary class="list-none [&::-webkit-details-marker]:hidden h-9.5 px-3 bg-white border border-gray-400 rounded-lg flex items-center justify-between gap-2.5 text-sm font-medium text-black hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 cursor-pointer shadow-xs min-w-42.5">
                    <span>Urutkan: <?= e($opsiSort[$sort]) ?></span>
                    <svg class="w-4 h-4 text-gray-600 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </summary>

                <div class="absolute left-0 mt-2 w-60 bg-secondary-bw rounded-2xl p-2 shadow-2xl border border-gray-300 z-40 space-y-1.5">
                    <?php foreach ($opsiSort as $kode => $label): ?>
                        <a
                            href="<?= e($buatUrl(['sort' => $kode, 'page' => 1])) ?>"
                            class="w-full bg-white border border-gray-300 text-black rounded-xl px-3.5 py-2 text-left font-medium text-sm flex items-center justify-between hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200"
                        >
                            <span><?= e($label) ?></span>
                            <?php if ($sort === $kode): ?>
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </details>

        </div>

        {{-- TOMBOL TAMBAH --}}
        <div class="flex items-center gap-2">
            <button
                type="button"
                onclick="openStokModal()"
                class="h-9.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
            >
                <span class="text-lg leading-none font-bold">+</span>
                <span>Tambah stok</span>
            </button>

            <button
                type="button"
                onclick="bukaModal('tambahITMModal')"
                class="h-9.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
            >
                <span class="text-lg leading-none font-bold">+</span>
                <span>Tambah barang</span>
            </button>
        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- TABEL --}}
    {{-- ========================================================= --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 flex flex-col">

        <div class="overflow-visible">
            <table class="w-full text-center border-collapse">

                <thead>
                    <tr class="bg-primary-db text-white text-xs font-semibold">
                        <th class="py-2.5 px-4 rounded-tl-lg">Kode Item</th>
                        <th class="py-2.5 px-4">Nama item</th>
                        <th class="py-2.5 px-4">Kategori</th>
                        <th class="py-2.5 px-4">Merk</th>
                        <th class="py-2.5 px-4">Supplier</th>
                        <th class="py-2.5 px-4">Stok</th>
                        <th class="py-2.5 px-4">Harga</th>
                        <th class="py-2.5 px-4 w-32 rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 text-xs text-black">
                    <?php if (count($produk) > 0): ?>
                        <?php foreach ($produk as $r): ?>
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-3.5 px-4"><?= e($r->itm) ?></td>
                                <td class="py-3.5 px-4"><?= e($r->des) ?></td>
                                <td class="py-3.5 px-4"><?= e($r->ktg_nama) ?></td>
                                <td class="py-3.5 px-4"><?= e($r->mrk_nama) ?></td>
                                <td class="py-3.5 px-4"><?= e($r->spp_nama) ?></td>
                                <td class="py-3.5 px-4"><?= (int) $r->stok ?> Unit</td>
                                <td class="py-3.5 px-4"><?= e($rp($r->hrg)) ?></td>
                                <td class="py-3.5 px-4 text-center">

                                {{-- DROPDOWN AKSI --}}
                                <div class="relative inline-block">

                                    {{-- BUTTON AKSI --}}
                                    <button
                                        type="button"
                                        onclick="toggleActionMenu(this)"
                                        class="h-8 w-8 border border-gray-400 bg-white hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 rounded-md inline-flex items-center justify-center transition-all duration-200 shadow-2xs cursor-pointer"
                                        title="Aksi Item"
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
                                        {{-- DETAIL --}}
                                            <a
                                                href="<?= e(route('inventory.item.show', $r->itm)) ?>"
                                                class="w-full px-3 py-1.5 text-xs font-semibold text-gray-800 hover:bg-primary-db/5 hover:text-primary-db transition-all duration-200 flex items-center gap-2 cursor-pointer"
                                            >
                                                <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span>Detail</span>
                                            </a>

                                        {{-- EDIT --}}
                                        <button
                                            type="button"
                                            data-itm="{{ $r->itm }}"
                                            data-des="{{ $r->des }}"
                                            data-hrg="{{ $r->hrg }}"
                                            data-ktg="{{ $r->ktg }}"
                                            data-mrk="{{ $r->mrk }}"
                                            data-spp="{{ $r->spp }}"
                                            data-action="{{ route('inventory.item.update', ['id' => $r->itm]) }}"
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
                                            action="{{ route('inventory.item.destroy', ['id' => $r->itm]) }}"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                onclick="return confirm('Hapus Item ini?')"
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
                            <td colspan="8" class="py-8 text-center text-gray-500 text-sm">Tidak ada produk.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>

        {{-- FOOTER / PAGINATION --}}
        <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-[#142854] rounded-b-lg">

            <div class="whitespace-nowrap">
                Menampilkan <?= (int) $pagination['dari'] ?>-<?= (int) $pagination['sampai'] ?>
                dari <?= (int) $pagination['total_data'] ?> produk
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
    {{-- MODAL TAMBAH STOK --}}
    {{-- ========================================================= --}}
    <div
        id="tambahStokModal"
        class="fixed inset-0 z-50 <?= $bukaStok ? 'flex' : 'hidden' ?> items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
    >
        <div class="absolute inset-0" onclick="tutupModal('tambahStokModal')"></div>

        <div class="relative w-full max-w-2xl bg-secondary-bw border border-gray-200 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[92vh] overflow-y-auto">

            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold">Tambah Stok</h2>
                <button type="button" onclick="tutupModal('tambahStokModal')" class="text-gray-400 hover:text-gray-700 text-2xl font-bold leading-none cursor-pointer">&times;</button>
            </div>

            <form id="formStok" method="POST" action="<?= e(route('inventory.item.stok')) ?>" class="space-y-4">
                @csrf

                {{-- NAMA BARANG --}}
                <div>
                    <label class="<?= $labelCls ?>">Nama Barang</label>
                    <select name="stok_itm" required class="searchable-select <?= $inputCls ?>">
                        <option value="" disabled <?= old('stok_itm') ? '' : 'selected' ?>>Pilih barang</option>
                        <?php foreach ($daftarITM as $it): ?>
                            <option value="<?= e($it->itm) ?>" <?= old('stok_itm') == $it->itm ? 'selected' : '' ?>>
                                <?= e($it->des) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                {{-- GUDANG & HARGA BELI --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="<?= $labelCls ?>">Gudang</label>
                        <select name="stok_gd" required class="searchable-select <?= $inputCls ?>">
                            <option value="" disabled <?= old('stok_gd') ? '' : 'selected' ?>>Pilih gudang</option>
                            <?php foreach ($GD as $g): ?>
                                <option value="<?= e($g->GD) ?>" <?= old('stok_gd') == $g->GD ? 'selected' : '' ?>>
                                    <?= e($g->DES) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= $galat('stok', 'stok_gd') ?>
                    </div>

                    <div>
                        <label class="<?= $labelCls ?>">Harga Beli</label>
                        <input
                            type="text"
                            inputmode="numeric"
                            name="stok_hrb"
                            required
                            value="<?= e(old('stok_hrb') ? ('Rp ' . number_format((float)old('stok_hrb'), 0, ',', '.')) : '') ?>"
                            placeholder="Rp 0"
                            oninput="formatInputRupiah(this)"
                            class="input-rupiah <?= $inputCls ?>"
                        >
                        <?= $galat('stok', 'stok_hrb') ?>
                    </div>
                </div>

                {{-- SCAN KAMERA (live bila HTTPS/localhost, foto bila HTTP) --}}
                @include('inventory.partials.barcode-camera', ['cameraId' => 'stok'])

                {{-- BARCODE + SIMPAN --}}
                {{-- type="text" (bukan number): barcode diterima sebagai string/varchar --}}
                <div>
                    <div class="flex items-center gap-2">
                        <input
                            id="stokBarcode"
                            type="text"
                            inputmode="text"
                            name="stok_bc"
                            maxlength="20"
                            required
                            autocomplete="off"
                            autocapitalize="off"
                            spellcheck="false"
                            <?= $bukaStok ? 'autofocus' : '' ?>
                            placeholder="Scan atau ketik kode manual..."
                            class="<?= $inputCls ?>"
                        >
                        <button
                            type="submit"
                            class="py-2 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-lg text-xs font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
                        >
                            Simpan
                        </button>
                    </div>

                    <?php if (!empty($stokList)): ?>
                        <div class="mt-2 border border-gray-300 bg-white rounded-lg max-h-32 overflow-y-auto divide-y divide-gray-100">
                            <?php foreach ($stokList as $s): ?>
                                <div class="px-3 py-1.5 text-xs flex items-center justify-between gap-2">
                                    <span class="truncate"><?= e($s['des']) ?> · <?= e($s['gd']) ?></span>
                                    <span class="font-mono text-green-600"><?= e($s['bc_out']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1"><?= count($stokList) ?> kode menunggu di-generate</p>
                    <?php endif; ?>
                    <?= $galat('stok', 'stok_itm') ?>
                    <?= $galat('stok', 'stok_bc') ?>
                </div>

            </form>

            <form method="POST" action="<?= e(route('inventory.item.generate')) ?>" class="mt-3">
                @csrf
                <button
                    type="submit"
                    <?= empty($stokList) ? 'disabled' : '' ?>
                    class="w-full py-2 px-4 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-semibold hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 transition-all duration-200 cursor-pointer text-center disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Generate Barcode<?= !empty($stokList) ? ' (' . count($stokList) . ')' : '' ?>
                </button>
            </form>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- MODAL TAMBAH BARANG --}}
    {{-- ========================================================= --}}
    <div
        id="tambahITMModal"
        class="fixed inset-0 z-50 <?= $bukaTambah ? 'flex' : 'hidden' ?> items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
    >
        <div class="absolute inset-0" onclick="tutupModal('tambahITMModal')"></div>

        <div class="relative w-full max-w-2xl bg-secondary-bw border border-gray-200 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto">

            <h2 class="text-xl font-bold mb-6">Tambah Barang</h2>

            <form method="POST" id="formTambah" action="<?= e(route('inventory.item.store')) ?>" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">

                    <div>
                        <label class="<?= $labelCls ?>">Nama Barang</label>
                        <input type="text" name="DES" maxlength="150" required
                               value="<?= e(old('DES')) ?>" class="<?= $inputCls ?>">
                        <?= $galat('tambah', 'DES') ?>
                    </div>

                    <div>
                        <label class="<?= $labelCls ?>">Merek</label>
                        <select name="MRK" required class="searchable-select <?= $inputCls ?>">
                            <option value="" disabled <?= old('MRK') ? '' : 'selected' ?>>Pilih Merk</option>
                            <?php foreach ($MRK as $m): ?>
                                <option value="<?= e($m->MRK) ?>" <?= old('MRK') == $m->MRK ? 'selected' : '' ?>><?= e($m->DES) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= $galat('tambah', 'MRK') ?>
                    </div>

                    <div>
                        <label class="<?= $labelCls ?>">Harga Jual</label>
                        <input type="text" inputmode="numeric" name="HRG" required
                               value="<?= e(old('HRG') ? ('Rp ' . number_format((float)old('HRG'), 0, ',', '.')) : '') ?>"
                               placeholder="Rp 0" oninput="formatInputRupiah(this)" class="input-rupiah <?= $inputCls ?>">
                        <?= $galat('tambah', 'HRG') ?>
                    </div>

                    <div>
                        <label class="<?= $labelCls ?>">Kategori</label>
                        <select name="KTG" required class="searchable-select <?= $inputCls ?>">
                            <option value="" disabled <?= old('KTG') ? '' : 'selected' ?>>Pilih Kategori</option>
                            <?php foreach ($KTG as $k): ?>
                                <option value="<?= e($k->KTG) ?>" <?= old('KTG') == $k->KTG ? 'selected' : '' ?>><?= e($k->DES) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= $galat('tambah', 'KTG') ?>
                    </div>

                    <div>
                        <label class="<?= $labelCls ?>">Supplier</label>
                        <select name="SPP" required class="searchable-select <?= $inputCls ?>">
                            <option value="" disabled <?= old('SPP') ? '' : 'selected' ?>>Pilih Supplier</option>
                            <?php foreach ($SPP as $s): ?>
                                <option value="<?= e($s->SPP) ?>" <?= old('SPP') == $s->SPP ? 'selected' : '' ?>><?= e($s->DES) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= $galat('tambah', 'SPP') ?>
                    </div>

                </div>

                <div class="pt-2 flex items-center gap-4 w-full">
                    <button type="button" onclick="tutupModal('tambahITMModal')"
                            class="w-1/2 py-2.5 px-4 bg-white border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 transition-all duration-200 cursor-pointer text-center">
                        Batal
                    </button>
                    <button
                    type="submit"
                    id="btnTambahITM"
                    class="w-1/2 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow text-center">
                        Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- MODAL EDIT BARANG --}}
    {{-- ========================================================= --}}
    <div
        id="editITMModal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
    >
        <div class="absolute inset-0" onclick="tutupModal('editITMModal')"></div>

        <div class="relative w-full max-w-2xl bg-secondary-bw border border-gray-200 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto">

            <h2 class="text-xl font-bold mb-6">Edit Barang</h2>

            <form id="editITMForm" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">

                    <div>
                        <label class="<?= $labelCls ?>">Kode Item</label>
                        <input id="editITM" type="text" name="ITM" readonly required
                               class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-600 cursor-not-allowed focus:outline-none font-mono">
                    </div>

                    <div>
                        <label class="<?= $labelCls ?>">Nama Item</label>
                        <input id="editDES" type="text" name="DES" maxlength="150" required placeholder="Masukkan nama item"
                               class="<?= $inputCls ?>">
                    </div>

                    <div>
                        <label class="<?= $labelCls ?>">Merk</label>
                        <select id="editMRK" name="MRK" required class="searchable-select <?= $inputCls ?>">
                            <?php foreach ($MRK as $m): ?>
                                <option value="<?= e($m->MRK) ?>"><?= e($m->DES) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="<?= $labelCls ?>">Harga</label>
                        <input id="editHRG" type="text" inputmode="numeric" name="HRG" required
                               placeholder="Rp 0" oninput="formatInputRupiah(this)" class="input-rupiah <?= $inputCls ?>">
                    </div>

                    <div>
                        <label class="<?= $labelCls ?>">Kategori</label>
                        <select id="editKTG" name="KTG" required class="searchable-select <?= $inputCls ?>">
                            <?php foreach ($KTG as $k): ?>
                                <option value="<?= e($k->KTG) ?>"><?= e($k->DES) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="<?= $labelCls ?>">Supplier</label>
                        <select id="editSPP" name="SPP" required class="searchable-select <?= $inputCls ?>">
                            <?php foreach ($SPP as $s): ?>
                                <option value="<?= e($s->SPP) ?>"><?= e($s->DES) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <div class="pt-2 flex items-center gap-4 w-full">
                    <button type="button" onclick="tutupModal('editITMModal')"
                            class="w-1/2 py-2.5 px-4 bg-white border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 transition-all duration-200 cursor-pointer text-center">
                        Batal
                    </button>
                    <button type="submit"
                            class="w-1/2 py-2.5 px-4 bg-[#1E295D] hover:bg-[#161F48] text-white rounded-xl text-sm font-semibold transition-colors cursor-pointer text-center">
                        Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>


{{-- ============================================================= --}}
{{-- JAVASCRIPT: modal, menu aksi, dan sambungan ke scanner kamera --}}
{{-- ============================================================= --}}
<script>
    function bukaModal(id) {
        const m = document.getElementById(id);
        m.classList.remove('hidden');
        m.classList.add('flex');
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            $('#' + id).find('select.searchable-select').each(function() {
                $(this).select2({
                    width: '100%',
                    dropdownParent: $('#' + id)
                });
            });
        }
    }

    function tutupModal(id) {
        const m = document.getElementById(id);
        m.classList.remove('flex');
        m.classList.add('hidden');

        // Modal stok ditutup -> matikan kamera supaya lampu kamera padam
        if (id === 'tambahStokModal' && window.BarcodeCamera) {
            BarcodeCamera.stop('stok');
        }
    }

    // ---------- Menu aksi ----------
    function toggleActionMenu(button) {
        const menu = button.parentElement.querySelector('.action-menu');

        document.querySelectorAll('.action-menu').forEach(function (item) {
            if (item !== menu) item.classList.add('hidden');
        });

        menu.classList.toggle('hidden');
    }

    // ---------- Tutup menu aksi saat klik di luar ----------
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.action-menu') && !event.target.closest('button[onclick="toggleActionMenu(this)"]')) {
            document.querySelectorAll('.action-menu').forEach(function (menu) {
                menu.classList.add('hidden');
            });
        }
    });

    function openStokModal() {
        bukaModal('tambahStokModal');
        setTimeout(function () {
            document.getElementById('stokBarcode').focus();
        }, 50);
    }

    function openEditModal(btn) {
        const d = btn.dataset;

        document.getElementById('editITM').value = d.itm;
        document.getElementById('editDES').value = d.des;
        document.getElementById('editHRG').value = d.hrg ? formatRupiah(d.hrg) : '';
        document.getElementById('editITMForm').action = d.action;

        // tutup menu aksi baris ini
        document.querySelectorAll('.action-menu').forEach(function (menu) {
            menu.classList.add('hidden');
        });
        bukaModal('editITMModal');

        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            $('#editMRK').val(d.mrk).trigger('change');
            $('#editKTG').val(d.ktg).trigger('change');
            $('#editSPP').val(d.spp).trigger('change');
        } else {
            document.getElementById('editKTG').value = d.ktg;
            document.getElementById('editMRK').value = d.mrk;
            document.getElementById('editSPP').value = d.spp;
        }
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            ['tambahStokModal', 'tambahITMModal', 'editITMModal'].forEach(tutupModal);
        }
    });

    // ---------- Barcode: scanner fisik + kamera, selalu sebagai STRING ----------
    (function () {
        const form  = document.getElementById('formStok');
        const input = document.getElementById('stokBarcode');
        const cam   = document.getElementById('bcam-stok');

        // Bersihkan karakter kontrol & spasi (sebagian scanner menambah prefix/suffix)
        function bersihkan(v) {
            return String(v).replace(/[\u0000-\u001F\u007F]/g, '').trim();
        }

        // Scanner fisik mengetik cepat lalu Enter -> form submit biasa.
        // Value dibersihkan dulu tepat sebelum terkirim.
        form.addEventListener('submit', function () {
            input.value = bersihkan(input.value);
        });

        // Hasil dari kamera (live maupun foto)
        cam.addEventListener('barcode:scan', function (e) {
            const kode = bersihkan(e.detail.code);

            if (input.maxLength > 0 && kode.length > input.maxLength) {
                alert('Kode "' + kode + '" terlalu panjang (maks ' + input.maxLength + ' karakter).');
                return;
            }

            input.value = kode;

            // Nama barang & gudang harus sudah dipilih; kalau belum, browser
            // menampilkan pesan validasi dan kode tetap terisi di kolom.
            if (form.checkValidity()) {
                // keepResume: setelah halaman reload, kamera live menyala lagi
                BarcodeCamera.stop('stok', { keepResume: true });
            }
            form.requestSubmit();
        });
    })();

    // Mencegah double submit
    document.getElementById('formTambah').addEventListener('submit', function (event) {
        const btn = document.getElementById('btnTambahITM');

        if (btn.disabled) {
            event.preventDefault();
            return;
        }

        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        btn.innerText = 'Menyimpan...';
    });
</script>

@endsection