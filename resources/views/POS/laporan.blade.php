@extends('layouts.app')

@section('title', 'Laporan - Laporan Kasir')
@section('header_title', 'Laporan Kasir')

@section('content')
<?php
    // ---------- Helper PHP murni ----------
    $rp = fn ($angka) => 'Rp ' . number_format((int) $angka, 0, ',', '.');

    $jam = fn ($dt) => !empty($dt) ? date('H:i', strtotime($dt)) . ' WIB' : '-';

    // Bangun URL dengan mempertahankan filter
    $buatUrl = function ($p) use ($tanggal, $kasir) {
        $query = array_filter([
            'tanggal' => $tanggal,
            'kasir'   => $kasir,
            'page'    => $p,
        ], fn ($v) => $v !== '' && $v !== null);

        return route('POS.laporan') . (count($query) ? '?' . http_build_query($query) : '');
    };

    // Window nomor halaman (maks 5 tombol)
    $halaman   = $pagination['page'];
    $totalHal  = $pagination['total_pages'];
    $mulai     = max(1, $halaman - 2);
    $akhir     = min($totalHal, $mulai + 4);
    $mulai     = max(1, $akhir - 4);

    $btnBase   = 'h-7 w-7 border rounded-md flex items-center justify-center text-xs transition-all duration-200';
    $btnNormal = $btnBase . ' border-[#A8B1C1] text-[#142854] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200';
    $btnAktif  = $btnBase . ' font-semibold bg-[#142854] text-white border-[#142854] hover:bg-primary-sdb';
    $btnOff    = $btnBase . ' border-[#A8B1C1] text-gray-300 pointer-events-none';
?>

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-6 space-y-6">

    {{-- ========================================================= --}}
    {{-- SECTION ATAS: STATISTIK & FILTER --}}
    {{-- ========================================================= --}}
    <div class="border border-gray-300 rounded-2xl bg-white p-6 shadow-xs space-y-6">

        <div class="grid grid-cols-4 gap-4">

            <div class="border border-gray-300 rounded-xl p-4 bg-white">
                <p class="text-xs font-semibold text-black">Total Penjualan</p>
                <div class="text-2xl font-bold text-black mt-1"><?= e($rp($stat['total_penjualan'])) ?></div>
                <p class="text-[11px] text-gray-500 mt-1">Pada <?= e($stat['tanggal_label']) ?></p>
            </div>

            <div class="border border-gray-300 rounded-xl p-4 bg-white">
                <p class="text-xs font-semibold text-black">Jumlah Transaksi</p>
                <div class="text-2xl font-bold text-black mt-1"><?= (int) $stat['jumlah_transaksi'] ?> Order</div>
                <p class="text-[11px] text-gray-500 mt-1">Berhasil</p>
            </div>

            <div class="border border-gray-300 rounded-xl p-4 bg-white">
                <p class="text-xs font-semibold text-black">Total Kas Tunai</p>
                <div class="text-2xl font-bold text-black mt-1"><?= e($rp($stat['total_kas_tunai'])) ?></div>
                <p class="text-[11px] text-gray-500 mt-1"><?= (int) $stat['persen_kas_tunai'] ?>% Dari total penjualan</p>
            </div>

            <div class="border border-gray-300 rounded-xl p-4 bg-white">
                <p class="text-xs font-semibold text-black">Non Tunai</p>
                <div class="text-2xl font-bold text-black mt-1"><?= e($rp($stat['total_non_tunai'])) ?></div>
                <p class="text-[11px] text-gray-500 mt-1"><?= (int) $stat['persen_non_tunai'] ?>% Dari total penjualan</p>
            </div>

        </div>

        <!-- Filter Area -->
        <div class="space-y-2">
            <h3 class="text-base font-bold text-black">Filter:</h3>

            <form action="<?= e(route('POS.laporan')) ?>" method="GET" class="flex items-center gap-4">
                <!-- Filter Tanggal -->
                <div class="relative w-64">
                    <input
                        type="date"
                        name="tanggal"
                        value="<?= e($tanggal) ?>"
                        onchange="this.form.submit()"
                        class="w-full bg-white border border-gray-400 rounded-xl px-4 py-2.5 text-xs text-black transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:border-primary-db focus:bg-primary-db/5 focus:ring-1 focus:ring-primary-db cursor-pointer"
                    >
                </div>

                <!-- Filter Kasir -->
                <div class="relative w-64">
                    <select
                        name="kasir"
                        onchange="this.form.submit()"
                        class="searchable-select w-full bg-white border border-gray-400 rounded-xl px-4 py-2.5 text-xs text-black transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:border-primary-db focus:bg-primary-db/5 focus:ring-1 focus:ring-primary-db cursor-pointer"
                    >
                        <option value="">Semua Kasir</option>
                        <?php foreach ($daftarKasir as $kode => $nama): ?>
                            <option value="<?= e($kode) ?>" <?= (string) $kasir === (string) $kode ? 'selected' : '' ?>>
                                <?= e($nama) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($tanggal !== '' || $kasir !== ''): ?>
                    <a
                        href="<?= e(route('POS.laporan')) ?>"
                        class="flex items-center justify-center gap-2 bg-white border border-gray-400 rounded-xl px-4 py-2.5 text-xs text-black hover:bg-gray-300 hover:border-gray-900 active:bg-gray-200 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db cursor-pointer transition-all duration-200"
                    >
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- SECTION BAWAH: TABEL LAPORAN KASIR --}}
    {{-- ========================================================= --}}
    <div class="border border-gray-300 rounded-2xl bg-white overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-primary-db text-white text-xs font-semibold">
                        <th class="py-3 px-4 font-semibold">Kode</th>
                        <th class="py-3 px-4 font-semibold">Kasir</th>
                        <th class="py-3 px-4 font-semibold">Waktu Buka</th>
                        <th class="py-3 px-4 font-semibold">Waktu Tutup</th>
                        <th class="py-3 px-4 font-semibold">Kas Awal</th>
                        <th class="py-3 px-4 font-semibold">Total Penjualan</th>
                        <th class="py-3 px-4 font-semibold">Kas Akhir Rill</th>
                        <th class="py-3 px-4 font-semibold">Selisih</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 text-xs text-black">
                    <?php if (count($laporan) > 0): ?>
                        <?php foreach ($laporan as $row): ?>
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-gray-600"><?= e($row->cd ?? '-') ?></td>
                                <td class="py-3.5 px-4 font-medium"><?= e($row->kasir) ?></td>
                                <td class="py-3.5 px-4"><?= e($jam($row->waktu_buka)) ?></td>
                                <td class="py-3.5 px-4"><?= $row->sudah_tutup ? e($jam($row->waktu_tutup)) : '<span class="text-amber-600">Belum ditutup</span>' ?></td>
                                <td class="py-3.5 px-4"><?= e($rp($row->kas_awal)) ?></td>
                                <td class="py-3.5 px-4"><?= e($rp($row->total_penjualan)) ?></td>
                                <td class="py-3.5 px-4"><?= $row->sudah_tutup ? e($rp($row->kas_akhir_rill)) : '-' ?></td>
                                <td class="py-3.5 px-4">
                                    <?php if ($row->selisih === null): ?>
                                        -
                                    <?php else: ?>
                                        <span class="<?= $row->selisih < 0 ? 'text-red-600' : ($row->selisih > 0 ? 'text-green-600' : '') ?>">
                                            <?= ($row->selisih < 0 ? '-' : '') . e($rp(abs($row->selisih))) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="py-8 px-4 text-center text-gray-500">Tidak ada data laporan kasir.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer / Pagination -->
        <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-[#142854]">
            <span>
                Menampilkan <?= (int) $pagination['dari'] ?>-<?= (int) $pagination['sampai'] ?>
                dari <?= (int) $pagination['total_data'] ?> data
            </span>

            <div class="flex items-center gap-1">
                <!-- Prev -->
                <?php if ($halaman > 1): ?>
                    <a href="<?= e($buatUrl($halaman - 1)) ?>" class="<?= $btnNormal ?>">‹</a>
                <?php else: ?>
                    <span class="<?= $btnOff ?>">‹</span>
                <?php endif; ?>

                <!-- Nomor halaman -->
                <?php for ($i = $mulai; $i <= $akhir; $i++): ?>
                    <?php if ($i === $halaman): ?>
                        <span class="<?= $btnAktif ?>"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= e($buatUrl($i)) ?>" class="<?= $btnNormal ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Next -->
                <?php if ($halaman < $totalHal): ?>
                    <a href="<?= e($buatUrl($halaman + 1)) ?>" class="<?= $btnNormal ?>">›</a>
                <?php else: ?>
                    <span class="<?= $btnOff ?>">›</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
@endsection