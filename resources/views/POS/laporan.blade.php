@extends('layouts.app')

@section('title', 'Laporan - Laporan Kasir')
@section('header_title', 'Laporan Kasir')

@section('content')
<style>
    /* Sembunyikan scrollbar area tabel tapi tetap bisa scroll */
    .table-scroll {
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE / Edge lama */
    }
    .table-scroll::-webkit-scrollbar {
        display: none; /* Chrome / Safari / Edge */
    }
</style>
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

    $halaman   = (int) $pagination['page'];
    $totalHal  = max(1, (int) $pagination['total_pages']);

    $btnBase   = 'h-7 w-7 border rounded-md flex items-center justify-center text-xs transition-all duration-200';
    $btnNormal = $btnBase . ' border-[#A8B1C1] text-[#142854] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200';
    $btnAktif  = $btnBase . ' font-semibold bg-[#142854] text-white border-[#142854] hover:bg-primary-sdb';
    $btnOff    = $btnBase . ' border-[#A8B1C1] text-gray-300 pointer-events-none';
    $nomor     = fn ($i) => (int) $i === $halaman
        ? '<span class="' . $btnAktif . '">' . $i . '</span>'
        : '<a href="' . e($buatUrl($i)) . '" class="' . $btnNormal . '">' . $i . '</a>';
    $titik     = '<span class="px-1">...</span>';
?>

<div class="flex-1 flex flex-col h-full min-h-0 overflow-hidden bg-secondary-bw p-6 space-y-6">

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

                {{-- Cetak Laporan Transaksi / Penjualan (Bisa diakses Kasir, Admin, dan Manager) --}}
                <button
                    type="button"
                    onclick="openModalPrintLaporan()"
                    class="ml-auto flex items-center justify-center gap-2 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl px-4 py-2.5 text-xs font-semibold cursor-pointer transition-all duration-200 shadow-xs"
                    title="Cetak Laporan Penjualan / Transaksi (Range Tanggal)"
                >
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.673-2.684-1.28-4.329m13.12 4.329c.24-1.076.673-2.684 1.28-4.329m-14.4 0a17.962 17.962 0 0114.4 0m-14.4 0a17.962 17.962 0 0014.4 0M6 18h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zM6 14h12M9 10V6a2 2 0 012-2h2a2 2 0 012 2v4" />
                    </svg>
                    <span>Print Laporan Transaksi</span>
                </button>
            </form>
        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- SECTION BAWAH: TABEL LAPORAN KASIR --}}
    {{-- ========================================================= --}}
    <div class="flex-1 min-h-0 border border-gray-300 rounded-2xl bg-white overflow-hidden shadow-xs flex flex-col">
        <div class="table-scroll flex-1 min-h-0 overflow-y-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-primary-db text-white text-xs font-semibold">
                        <th class="py-3 px-4 font-semibold">Kasir</th>
                        <th class="py-3 px-4 font-semibold">Waktu Buka</th>
                        <th class="py-3 px-4 font-semibold">Waktu Tutup</th>
                        <th class="py-3 px-4 font-semibold">Kas Awal</th>
                        <th class="py-3 px-4 font-semibold">Total Penjualan</th>
                        <th class="py-3 px-4 font-semibold">Total Tunai</th>
                        <th class="py-3 px-4 font-semibold">Total Non Tunai</th>
                        <th class="py-3 px-4 font-semibold">Uang Akhir Tunai</th>
                        <th class="py-3 px-4 font-semibold">Selisih</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 text-xs text-black">
                    <?php if (count($laporan) > 0): ?>
                        <?php foreach ($laporan as $row): ?>
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-medium"><?= e($row->kasir) ?></td>
                                <td class="py-3.5 px-4"><?= e($jam($row->waktu_buka)) ?></td>
                                <td class="py-3.5 px-4"><?= $row->sudah_tutup ? e($jam($row->waktu_tutup)) : '<span class="text-amber-600">Belum ditutup</span>' ?></td>
                                <td class="py-3.5 px-4"><?= e($rp($row->kas_awal)) ?></td>
                                <td class="py-3.5 px-4"><?= e($rp($row->total_penjualan)) ?></td>
                                <td class="py-3.5 px-4"><?= e($rp($row->tunai)) ?></td>
                                <td class="py-3.5 px-4"><?= e($rp($row->non_tunai)) ?></td>
                                <td class="py-3.5 px-4"><?= $row->sudah_tutup ? e($rp($row->uang_akhir_tunai)) : '-' ?></td>
                                <td class="py-3.5 px-4">
                                    <?php if ($row->selisih === null): ?>
                                        -
                                    <?php else: ?>
                                        <span class="<?= $row->selisih < 0 ? 'text-red-600 font-semibold' : ($row->selisih > 0 ? 'text-green-600 font-semibold' : '') ?>">
                                            <?= e($rp(abs($row->selisih))) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="py-8 px-4 text-center text-gray-500">Tidak ada data laporan kasir.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer / Pagination -->
        <div class="shrink-0 px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-[#142854]">
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

                <!-- Next -->
                <?php if ($halaman < $totalHal): ?>
                    <a href="<?= e($buatUrl($halaman + 1)) ?>" class="<?= $btnNormal ?>">›</a>
                <?php else: ?>
                    <span class="<?= $btnOff ?>">›</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL PRINT LAPORAN PENJUALAN (RANGE TANGGAL)             --}}
    {{-- ========================================================= --}}
    <div
        id="modalPrintLaporan"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 transition-opacity"
        onclick="if(event.target === this) closeModalPrintLaporan()"
    >
        <div class="bg-white rounded-2xl border border-gray-300 w-full max-w-md p-6 shadow-2xl space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-primary-db/10 text-primary-db flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.673-2.684-1.28-4.329m13.12 4.329c.24-1.076.673-2.684 1.28-4.329m-14.4 0a17.962 17.962 0 0114.4 0m-14.4 0a17.962 17.962 0 0014.4 0M6 18h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zM6 14h12M9 10V6a2 2 0 012-2h2a2 2 0 012 2v4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 leading-tight">Cetak Laporan Penjualan</h3>
                        <p class="text-xs text-gray-500">Pilih rentang tanggal transaksi untuk dicetak</p>
                    </div>
                </div>
                <button
                    type="button"
                    onclick="closeModalPrintLaporan()"
                    class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 transition-colors cursor-pointer"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('POS.transaksi.cetak') }}" method="GET" target="_blank" data-no-lock="true" onsubmit="setTimeout(closeModalPrintLaporan, 50)">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Dari Tanggal
                        </label>
                        <input
                            type="date"
                            name="tanggal_dari"
                            value="{{ $tanggal ?: now()->subDays(30)->toDateString() }}"
                            class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db shadow-xs"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Sampai Tanggal
                        </label>
                        <input
                            type="date"
                            name="tanggal_sampai"
                            value="{{ $tanggal ?: now()->toDateString() }}"
                            class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db shadow-xs"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-5 mt-5 border-t border-gray-200">
                    <button
                        type="button"
                        onclick="closeModalPrintLaporan()"
                        class="px-4 py-2 border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 rounded-xl text-xs font-semibold transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer flex items-center gap-1.5"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.673-2.684-1.28-4.329m13.12 4.329c.24-1.076.673-2.684 1.28-4.329m-14.4 0a17.962 17.962 0 0114.4 0m-14.4 0a17.962 17.962 0 0014.4 0M6 18h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zM6 14h12M9 10V6a2 2 0 012-2h2a2 2 0 012 2v4" />
                        </svg>
                        <span>Cetak Laporan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('js')
<script>
    function openModalPrintLaporan() {
        const modal = document.getElementById('modalPrintLaporan');
        if (modal) modal.classList.remove('hidden');
    }

    function closeModalPrintLaporan() {
        const modal = document.getElementById('modalPrintLaporan');
        if (modal) modal.classList.add('hidden');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModalPrintLaporan();
        }
    });
</script>
@endsection