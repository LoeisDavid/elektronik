{{--
    =========================================================================
    HALAMAN: Opname Stok
    =========================================================================
    Cara pakai:
    1. Simpan file ini di: resources/views/inventory/opname-stok.blade.php
       (sesuaikan folder dengan struktur project-mu)
    2. Di controller, kirim variabel $items berupa array/collection, contoh:

        $items = DB::table('produk_barcode') // atau tabel apapun sumbernya
            ->select('kode_barcode', 'nama_barang')
            ->get()
            ->map(function ($row) {
                return [
                    'kode_barcode' => $row->kode_barcode,
                    'nama_barang'  => $row->nama_barang,
                    // status awal HANYA boleh 'checking' atau 'sudah_terjual'
                    // 'belum_terjual' baru muncul setelah proses scan di halaman ini
                    'status'       => 'checking',
                ];
            });

        return view('inventory.opname-stok', compact('items'));

    3. SEMUA perubahan status (checking -> belum_terjual, dsb) HANYA terjadi
       di browser (JavaScript, in-memory). Tidak ada request AJAX ke server,
       tidak ada yang disimpan ke database. Refresh halaman = data balik ke
       kondisi awal yang dikirim controller.
    4. Search & filter status juga murni JavaScript (tidak ada request ke
       controller).
    5. Daftar SELALU diurutkan: Checking -> Belum Terjual -> Sudah Terjual
       (berlaku saat halaman dibuka, setelah scan berhasil, search, & filter).
    =========================================================================
--}}

@extends('layouts.app') {{-- sesuaikan nama layout kalau bukan 'layouts.app' --}}

@section('title', 'Opname Stok')

@push('styles')
<style>
    /* Scrollbar tipis biar rapi */
    #opnameScroll::-webkit-scrollbar { width: 8px; height: 8px; }
    #opnameScroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    #opnameScroll::-webkit-scrollbar-track { background: transparent; }

    .row-flash {
        animation: rowFlash 1s ease-out;
    }
    @keyframes rowFlash {
        0%   { background-color: #fef08a; }
        100% { background-color: transparent; }
    }

    .modal-backdrop {
        background: rgba(15, 23, 42, 0.55);
    }
</style>
@endpush

@section('content')
<div class="flex-1 flex flex-col h-full min-h-0 bg-light-bg">

    {{-- ================= TOP BAR ================= --}}
    <div class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200 shrink-0">
        <div>
            <h1 class="text-lg font-semibold text-heading-dark">Opname Stok</h1>
            <p class="text-xs text-gray-400 mt-0.5" id="statCounter">Memuat data...</p>
        </div>
        <div class="flex items-center gap-3">
            <button id="btnSelesai"
                class="px-4 py-2 rounded-lg text-sm font-semibold text-red-600 border border-red-300 bg-red-50 hover:bg-red-100 transition">
                Selesai
            </button>
            <button id="btnScan"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-[#1e2a4a] hover:bg-[#28365f] transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h1v12H4V6zm3 0h1v12H7V6zm3 0h2v12h-2V6zm4 0h1v12h-1V6zm3 0h2v12h-2V6z"/>
                </svg>
                Scan / Input Kode
            </button>
        </div>
    </div>

    {{-- ================= SEARCH & FILTER (murni JS) ================= --}}
    <div class="flex items-center gap-3 px-6 pt-4 shrink-0">
        <div class="relative w-64 md:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-black">
                <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </span>
            <input
                type="text"
                id="inputCari"
                autocomplete="off"
                placeholder="Cari kode / nama barang"
                class="w-full bg-white border border-gray-300 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#1e2a4a]/40 focus:border-[#1e2a4a]"
            >
        </div>

        <select
            id="filterStatus"
            class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm font-medium text-black focus:outline-none focus:ring-2 focus:ring-[#1e2a4a]/40 focus:border-[#1e2a4a] cursor-pointer"
        >
            <option value="semua">Status: Semua</option>
            <option value="checking">Status: Checking</option>
            <option value="belum_terjual">Status: Belum Terjual</option>
            <option value="sudah_terjual">Status: Sudah Terjual</option>
        </select>
    </div>

    {{-- ================= TABLE (VIRTUAL SCROLL) ================= --}}
    <div class="flex-1 min-h-0 px-6 py-4">
        <div id="opnameScroll"
             class="relative h-full overflow-y-auto bg-white border border-gray-200 rounded-xl shadow-sm">

            {{-- Header sticky --}}
            <div class="sticky top-0 z-20 grid grid-cols-[70px_220px_1fr_170px] bg-[#1e2a4a] text-white text-xs font-semibold uppercase tracking-wide">
                <div class="px-4 py-3 min-w-0 truncate">No</div>
                <div class="px-4 py-3 min-w-0 truncate">Kode Barcode</div>
                <div class="px-4 py-3 min-w-0 truncate">Nama Barang</div>
                <div class="px-4 py-3 min-w-0 truncate text-center">Status</div>
            </div>

            {{-- Spacer total tinggi semua baris (biar scrollbar proporsional dgn 10.000 data) --}}
            <div id="virtualSpacer" class="relative">
                <div id="virtualRows" class="absolute left-0 right-0"></div>
            </div>

            {{-- Kondisi kosong --}}
            <div id="emptyState" class="hidden text-center text-sm text-gray-400 py-10">
                Tidak ada data untuk di-opname.
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL: SCAN / INPUT KODE ================= --}}
<div id="modalScan" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop">
    <div class="bg-white w-full max-w-md rounded-xl shadow-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-heading-dark">Scan / Input Kode Barang</h2>
            <button id="btnCloseScan" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>

        <form id="formScan" class="space-y-3">
            <input
                type="text"
                id="inputKode"
                autocomplete="off"
                placeholder="Scan barcode atau ketik kode lalu Enter..."
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e2a4a]/40 focus:border-[#1e2a4a]"
            >
            <button type="submit" class="w-full bg-[#1e2a4a] text-white text-sm font-semibold py-2.5 rounded-lg hover:bg-[#28365f]">
                Cek Kode
            </button>
        </form>

        {{-- Feedback hasil scan terakhir --}}
        <div id="scanFeedback" class="hidden mt-4 text-sm rounded-lg px-4 py-3"></div>

        <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-3 gap-2 text-center text-xs">
            <div>
                <div class="font-semibold text-gray-700" id="fbCountChecking">0</div>
                <div class="text-gray-400">Belum Terscan</div>
            </div>
            <div>
                <div class="font-semibold text-amber-600" id="fbCountBelum">0</div>
                <div class="text-gray-400">Belum Terjual</div>
            </div>
            <div>
                <div class="font-semibold text-emerald-600" id="fbCountSudah">0</div>
                <div class="text-gray-400">Sudah Terjual</div>
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL: KONFIRMASI SELESAI ================= --}}
<div id="modalConfirm" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop">
    <div class="bg-white w-full max-w-sm rounded-xl shadow-xl p-6 text-center">
        <div class="w-12 h-12 mx-auto rounded-full bg-amber-100 flex items-center justify-center mb-3">
            <span class="text-amber-500 text-2xl">!</span>
        </div>
        <h2 class="text-base font-semibold text-heading-dark mb-1">Akhiri Sesi Opname Stok?</h2>
        <p class="text-sm text-gray-500 mb-5">
            Setelah selesai, sesi opname ini dianggap berakhir. Pastikan semua barang sudah di-scan.
        </p>
        <div class="flex gap-3">
            <button id="btnBatalSelesai" class="flex-1 py-2.5 rounded-lg text-sm font-semibold border border-gray-300 text-gray-600 hover:bg-gray-50">
                Batal
            </button>
            <button id="btnYaSelesai" class="flex-1 py-2.5 rounded-lg text-sm font-semibold bg-red-500 text-white hover:bg-red-600">
                Ya, Selesai
            </button>
        </div>
    </div>
</div>

{{-- ================= MODAL: RINGKASAN HASIL ================= --}}
<div id="modalSummary" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop">
    <div class="bg-white w-full max-w-md rounded-xl shadow-xl p-6">
        <h2 class="text-base font-semibold text-heading-dark mb-1">Ringkasan Opname Stok</h2>
        <p class="text-xs text-gray-400 mb-5">Data ini hanya ditampilkan di sesi ini dan tidak disimpan otomatis.</p>

        <div class="space-y-2">
            <div class="flex items-center justify-between px-4 py-3 rounded-lg bg-amber-50">
                <span class="text-sm text-amber-700 font-medium">Belum Terjual (ditemukan saat scan)</span>
                <span class="text-lg font-bold text-amber-700" id="sumBelum">0</span>
            </div>
            <div class="flex items-center justify-between px-4 py-3 rounded-lg bg-emerald-50">
                <span class="text-sm text-emerald-700 font-medium">Sudah Terjual</span>
                <span class="text-lg font-bold text-emerald-700" id="sumSudah">0</span>
            </div>
            <div class="flex items-center justify-between px-4 py-3 rounded-lg bg-gray-100">
                <span class="text-sm text-gray-600 font-medium">Tidak Terscan (Checking)</span>
                <span class="text-lg font-bold text-gray-700" id="sumChecking">0</span>
            </div>
            <div class="flex items-center justify-between px-4 py-1 mt-1">
                <span class="text-xs text-gray-400">Total Data</span>
                <span class="text-xs text-gray-500 font-semibold" id="sumTotal">0</span>
            </div>
        </div>

        <button id="btnTutupSummary" class="w-full mt-5 py-2.5 rounded-lg text-sm font-semibold bg-[#1e2a4a] text-white hover:bg-[#28365f]">
            Tutup
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================================
       1. DATA — dikirim dari controller lewat $items, murni diolah di JS
          (TIDAK ada AJAX, TIDAK ada simpan ke DB)
    ===================================================================== */
    const rawData = @json($items ?? []);

    // Normalisasi + kasih index unik
    const stokData = rawData.map((item, idx) => ({
        idx,
        kode_barcode: String(item.kode_barcode ?? '').trim(),
        nama_barang: item.nama_barang ?? '-',
        status: ['checking', 'sudah_terjual', 'belum_terjual'].includes(item.status)
            ? item.status
            : 'checking',
    }));

    // Data yang sedang ditampilkan (hasil search + filter + urut). Diisi saat INIT.
    let tampil = stokData.slice();

    // Urutan status: checking dulu, lalu belum terjual, terakhir sudah terjual
    const URUTAN_STATUS = { checking: 0, belum_terjual: 1, sudah_terjual: 2 };

    function urutkanData(arr) {
        return arr.sort((a, b) =>
            (URUTAN_STATUS[a.status] - URUTAN_STATUS[b.status]) || (a.idx - b.idx)
        );
    }

    let sesiSelesai = false;   // flag apakah sesi sudah "diselesaikan"
    let pendingNavHref = null; // menyimpan link yang mau dituju setelah konfirmasi

    /* =====================================================================
       2. VIRTUAL SCROLL TABLE (biar tetap ringan meski 10.000 baris)
    ===================================================================== */
    const ROW_HEIGHT = 44;
    const scrollBox   = document.getElementById('opnameScroll');
    const spacer      = document.getElementById('virtualSpacer');
    const rowsWrap    = document.getElementById('virtualRows');
    const emptyState  = document.getElementById('emptyState');
    const HEADER_H    = 40; // tinggi header sticky (approx)

    const badge = (status) => {
        if (status === 'belum_terjual') {
            return `<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-amber-400 text-white">Belum Terjual</span>`;
        }
        if (status === 'sudah_terjual') {
            return `<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-emerald-500 text-white">Sudah Terjual</span>`;
        }
        return `<span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-600">Checking</span>`;
    };

    function renderVisibleRows() {
        const total = tampil.length;
        emptyState.textContent = stokData.length === 0
            ? 'Tidak ada data untuk di-opname.'
            : 'Tidak ada data yang cocok.';
        emptyState.classList.toggle('hidden', total > 0);
        spacer.style.height = (total * ROW_HEIGHT) + 'px';

        if (total === 0) { rowsWrap.innerHTML = ''; updateHeaderCounter(); return; }

        const viewportH = scrollBox.clientHeight - HEADER_H;
        const scrollTop = Math.max(0, scrollBox.scrollTop - HEADER_H);

        const buffer = 8;
        let start = Math.floor(scrollTop / ROW_HEIGHT) - buffer;
        let visibleCount = Math.ceil(viewportH / ROW_HEIGHT) + buffer * 2;
        start = Math.max(0, start);
        const end = Math.min(total, start + visibleCount);

        let html = '';
        for (let i = start; i < end; i++) {
            const item = tampil[i];
            html += `
                <div class="grid grid-cols-[70px_220px_1fr_170px] items-center text-sm border-b border-gray-100 data-row"
                     data-idx="${item.idx}" style="height:${ROW_HEIGHT}px;">
                    <div class="px-4 min-w-0 truncate whitespace-nowrap text-gray-400">${i + 1}</div>
                    <div class="px-4 min-w-0 truncate whitespace-nowrap text-gray-700 font-medium" title="${item.kode_barcode}">${item.kode_barcode}</div>
                    <div class="px-4 min-w-0 truncate whitespace-nowrap text-gray-600" title="${item.nama_barang}">${item.nama_barang}</div>
                    <div class="px-4 min-w-0 truncate text-center">${badge(item.status)}</div>
                </div>`;
        }
        rowsWrap.style.transform = `translateY(${start * ROW_HEIGHT}px)`;
        rowsWrap.innerHTML = html;

        updateHeaderCounter();
    }

    scrollBox.addEventListener('scroll', () => window.requestAnimationFrame(renderVisibleRows));
    window.addEventListener('resize', renderVisibleRows);

    // posisi = urutan baris pada daftar yang sedang ditampilkan (tampil)
    function scrollToIndex(posisi) {
        const targetTop = Math.max(0, (posisi * ROW_HEIGHT) - (scrollBox.clientHeight / 2));
        scrollBox.scrollTop = targetTop;
        renderVisibleRows();
        // efek flash sebentar biar user tau baris mana yang barusan berubah
        const idx = tampil[posisi].idx;
        setTimeout(() => {
            const el = rowsWrap.querySelector(`[data-idx="${idx}"]`);
            if (el) {
                el.classList.add('row-flash');
                setTimeout(() => el.classList.remove('row-flash'), 1000);
            }
        }, 50);
    }

    /* =====================================================================
       2b. SEARCH & FILTER STATUS + URUTAN (murni JS, tanpa controller)
    ===================================================================== */
    const inputCari    = document.getElementById('inputCari');
    const filterStatus = document.getElementById('filterStatus');

    function terapkanFilter(resetScroll = true) {
        const kw = inputCari.value.trim().toLowerCase();
        const st = filterStatus.value;

        tampil = urutkanData(
            stokData.filter(d =>
                (st === 'semua' || d.status === st) &&
                (!kw ||
                    d.kode_barcode.toLowerCase().includes(kw) ||
                    String(d.nama_barang).toLowerCase().includes(kw))
            )
        );

        if (resetScroll) scrollBox.scrollTop = 0;
        renderVisibleRows();
    }

    let cariTimer = null;
    inputCari.addEventListener('input', () => {
        clearTimeout(cariTimer);
        cariTimer = setTimeout(() => terapkanFilter(), 150);
    });
    filterStatus.addEventListener('change', () => terapkanFilter());

    /* =====================================================================
       3. HITUNG STATUS (dipakai header counter, modal scan, & summary)
    ===================================================================== */
    function hitungStatus() {
        const hasil = { checking: 0, belum_terjual: 0, sudah_terjual: 0 };
        stokData.forEach(item => hasil[item.status]++);
        return hasil;
    }

    function updateHeaderCounter() {
        const h = hitungStatus();
        document.getElementById('statCounter').textContent =
            `Total ${stokData.length} barang • Belum terscan: ${h.checking} • Belum terjual: ${h.belum_terjual} • Sudah terjual: ${h.sudah_terjual}`;
    }

    function updateModalScanCounter() {
        const h = hitungStatus();
        document.getElementById('fbCountChecking').textContent = h.checking;
        document.getElementById('fbCountBelum').textContent = h.belum_terjual;
        document.getElementById('fbCountSudah').textContent = h.sudah_terjual;
    }

    /* =====================================================================
       4. MODAL: SCAN / INPUT KODE
    ===================================================================== */
    const modalScan   = document.getElementById('modalScan');
    const inputKode   = document.getElementById('inputKode');
    const formScan    = document.getElementById('formScan');
    const scanFeedback = document.getElementById('scanFeedback');

    function openScanModal() {
        modalScan.classList.remove('hidden');
        updateModalScanCounter();
        scanFeedback.classList.add('hidden');
        inputKode.value = '';
        setTimeout(() => inputKode.focus(), 50);
    }
    function closeScanModal() {
        modalScan.classList.add('hidden');
    }

    function showFeedback(type, message) {
        const styles = {
            success: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            warning: 'bg-red-50 text-red-700 border border-red-200',
            info:    'bg-blue-50 text-blue-700 border border-blue-200',
            error:   'bg-gray-100 text-gray-600 border border-gray-200',
        };
        scanFeedback.className = 'mt-4 text-sm rounded-lg px-4 py-3 ' + styles[type];
        scanFeedback.textContent = message;
        scanFeedback.classList.remove('hidden');
    }

    function prosesKode(kodeInput) {
        const kode = kodeInput.trim();
        if (!kode) return;

        const item = stokData.find(
            d => d.kode_barcode.toLowerCase() === kode.toLowerCase()
        );

        if (!item) {
            showFeedback('error', `❌ Kode "${kode}" tidak ditemukan dalam daftar opname.`);
            return;
        }

        if (item.status === 'sudah_terjual') {
            showFeedback('warning', `⚠️ ${item.kode_barcode} - ${item.nama_barang}: barang ini SUDAH TERJUAL. Mohon cek kembali fisik barang!`);
        } else if (item.status === 'belum_terjual') {
            showFeedback('info', `ℹ️ ${item.kode_barcode} - ${item.nama_barang} sudah pernah discan sebelumnya (Belum Terjual).`);
        } else {
            // status masih 'checking' -> ubah jadi belum_terjual
            item.status = 'belum_terjual';
            showFeedback('success', `✅ ${item.kode_barcode} - ${item.nama_barang}: status diubah menjadi Belum Terjual.`);
        }

        updateModalScanCounter();

        // hitung ulang + urutkan ulang daftar tampil (checking -> belum terjual -> terjual),
        // tanpa reset scroll
        terapkanFilter(false);

        // lompat ke baris itu kalau memang sedang tampil (lolos search/filter)
        const posisi = tampil.indexOf(item);
        if (posisi !== -1) scrollToIndex(posisi);
    }

    document.getElementById('btnScan').addEventListener('click', openScanModal);
    document.getElementById('btnCloseScan').addEventListener('click', closeScanModal);
    modalScan.addEventListener('click', (e) => { if (e.target === modalScan) closeScanModal(); });

    formScan.addEventListener('submit', function (e) {
        e.preventDefault();
        prosesKode(inputKode.value);
        inputKode.value = '';
        inputKode.focus();
    });

    /* =====================================================================
       5. MODAL: KONFIRMASI SELESAI + RINGKASAN
    ===================================================================== */
    const modalConfirm = document.getElementById('modalConfirm');
    const modalSummary = document.getElementById('modalSummary');

    function openConfirmModal() {
        modalConfirm.classList.remove('hidden');
    }
    function closeConfirmModal() {
        modalConfirm.classList.add('hidden');
    }

    function openSummaryModal() {
        const h = hitungStatus();
        document.getElementById('sumBelum').textContent = h.belum_terjual;
        document.getElementById('sumSudah').textContent = h.sudah_terjual;
        document.getElementById('sumChecking').textContent = h.checking;
        document.getElementById('sumTotal').textContent = stokData.length;
        modalSummary.classList.remove('hidden');
    }

    function selesaikanSesi() {
        sesiSelesai = true;
        closeConfirmModal();
        closeScanModal();
        openSummaryModal();
    }

    document.getElementById('btnSelesai').addEventListener('click', () => {
        pendingNavHref = null; // selesai manual, bukan karena mau pindah halaman
        openConfirmModal();
    });
    document.getElementById('btnBatalSelesai').addEventListener('click', closeConfirmModal);
    document.getElementById('btnYaSelesai').addEventListener('click', selesaikanSesi);

    document.getElementById('btnTutupSummary').addEventListener('click', () => {
        modalSummary.classList.add('hidden');
        // kalau summary ini muncul karena user klik link navigasi, lanjutkan pindah halaman
        if (pendingNavHref) {
            const href = pendingNavHref;
            pendingNavHref = null;
            window.location.href = href;
        }
    });

    /* =====================================================================
       6. INTERCEPT NAVIGASI (klik link sidebar / link lain)
          -> dianggap "menyelesaikan" sesi opname juga
    ===================================================================== */
    document.addEventListener('click', function (e) {
        if (sesiSelesai) return; // sudah selesai sebelumnya, biarkan navigasi normal

        const link = e.target.closest('a[href]');
        if (!link) return;

        const href = link.getAttribute('href');
        // abaikan anchor kosong / javascript: / target blank
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || link.target === '_blank') {
            return;
        }

        e.preventDefault();
        pendingNavHref = link.href;
        openConfirmModal();
    }, true);

    // Peringatan browser kalau user refresh / menutup tab sebelum sesi selesai
    window.addEventListener('beforeunload', function (e) {
        if (sesiSelesai) return;
        e.preventDefault();
        e.returnValue = '';
    });

    /* =====================================================================
       INIT — terapkanFilter() supaya urutan status langsung berlaku
    ===================================================================== */
    terapkanFilter();
});
</script>
@endpush