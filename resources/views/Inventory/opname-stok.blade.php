{{--
    =========================================================================
    HALAMAN: Cek Stok (Opname Stok)
    =========================================================================
    Simpan di: resources/views/inventory/opname-stok.blade.php

    Alur (SATU file untuk dua kondisi):
    1. route('inventory.opname') -> index() render view ini TANPA $items
       -> tampil layar pembukaan dengan tombol "Mulai".
    2. Tombol Mulai -> route('inventory.opname.pull') -> pull() render view ini
       DENGAN $items -> tampil tabel Cek Stok.
    3. SEMUA perubahan status (checking -> belum_terjual) HANYA terjadi di
       browser (JavaScript, in-memory). Tidak ada AJAX, tidak ada simpan ke DB.
    4. Search & filter status murni JavaScript.
    5. Daftar SELALU diurutkan: Checking -> Belum Terjual -> Sudah Terjual.
    6. Selesai -> ringkasan -> Tutup -> redirect ke route('inventory.gudang').

    INPUT KODE (semua diperlakukan sebagai STRING, bukan angka):
      - Scanner fisik (keyboard wedge): mengetik ke kolom teks lalu Enter.
      - Kamera live (HTTPS / localhost) lewat partial barcode-camera.
      - Foto barcode (kamera HP, jalan di HTTP biasa) lewat partial yang sama.

    Palet warna:
      Kuning  #F2B80C | Hijau #327437 | Abu #6B7280
      Merah   #D02231 | Navy gelap #0D1C43 | Navy #22396F
    Font: Montserrat
    =========================================================================
--}}

@extends('layouts.app') {{-- sesuaikan nama layout kalau bukan 'layouts.app' --}}

@section('title', 'Cek Stok')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .font-mont { font-family: 'Montserrat', ui-sans-serif, system-ui, sans-serif; }

    /* Scrollbar tipis biar rapi */
    #opnameScroll::-webkit-scrollbar { width: 8px; height: 8px; }
    #opnameScroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    #opnameScroll::-webkit-scrollbar-track { background: transparent; }

    .row-flash { animation: rowFlash 1s ease-out; }
    @keyframes rowFlash {
        0%   { background-color: #F2B80C55; }
        100% { background-color: transparent; }
    }

    .modal-backdrop { background: rgba(13, 28, 67, 0.55); }

    /* Preview kamera live: pastikan video memenuhi lebar kotak */
    .bcam video { width: 100% !important; object-fit: cover; }
</style>
@endpush

@section('content')
@if (!isset($items))

{{-- ================= LAYAR PEMBUKAAN (belum ada data) ================= --}}
<div class="font-mont flex-1 flex flex-col items-center justify-center h-full min-h-0 bg-[#F7F8FD]">

    @if (session('error'))
        <div class="mb-4 px-4 py-2 rounded-md border border-[#D02231] bg-[#D02231]/10 text-[#D02231] text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <a id="btnMulai"
       href="{{ route('inventory.opname.pull') }}"
       class="px-12 py-2.5 rounded-md text-base font-semibold text-white bg-[#0D1C43] hover:bg-[#22396F] transition shadow-sm">
        Mulai
    </a>
</div>

@else
<div class="font-mont flex-1 flex flex-col h-full min-h-0 bg-[#F7F8FD]">

    {{-- ================= TOP BAR: STATISTIK + TOMBOL ================= --}}
    <div class="flex items-center justify-between px-6 pt-4 shrink-0">
        <p class="text-xs text-[#6B7280]" id="statCounter">Memuat data...</p>
        <div class="flex items-center gap-2">
            <button id="btnSelesai"
                class="px-4 py-1.5 rounded-md text-xs font-semibold text-[#D02231] border border-[#D02231] bg-white hover:bg-[#D02231]/10 transition">
                Selesai
            </button>
            <button id="btnScan"
                class="px-4 py-1.5 rounded-md text-xs font-semibold text-white bg-[#0D1C43] hover:bg-[#22396F] transition shadow-sm">
                Scan / Input Kode
            </button>
        </div>
    </div>

    {{-- ================= SEARCH & FILTER (murni JS) ================= --}}
    <div class="flex items-center gap-3 px-6 pt-3 shrink-0">
        <div class="relative w-64 md:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[#0D1C43]">
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </span>
            <input
                type="text"
                id="inputCari"
                autocomplete="off"
                placeholder="Cari kode / nama barang"
                class="w-full bg-white border border-gray-300 rounded-md pl-9 pr-4 py-1.5 text-xs text-[#0D1C43] placeholder-[#6B7280] focus:outline-none focus:ring-2 focus:ring-[#22396F]/40 focus:border-[#22396F]"
            >
        </div>

        <select
            id="filterStatus"
            class="bg-white border border-gray-300 rounded-md px-3 py-1.5 text-xs font-medium text-[#0D1C43] focus:outline-none focus:ring-2 focus:ring-[#22396F]/40 focus:border-[#22396F] cursor-pointer"
        >
            <option value="semua">Status: Semua</option>
            <option value="checking">Status: Checking</option>
            <option value="belum_terjual">Status: Belum Terjual</option>
            <option value="sudah_terjual">Status: Sudah Terjual</option>
        </select>
    </div>

    {{-- ================= TABLE (VIRTUAL SCROLL) ================= --}}
    <div class="flex-1 min-h-0 px-6 py-3">
        <div id="opnameScroll"
             class="relative h-full overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-sm">

            {{-- Header sticky --}}
            <div class="sticky top-0 z-20 grid grid-cols-3 bg-[#22396F] text-white text-xs font-semibold text-center">
                <div class="px-4 py-3 min-w-0 truncate">Kode Barcode</div>
                <div class="px-4 py-3 min-w-0 truncate">Nama Barang</div>
                <div class="px-4 py-3 min-w-0 truncate">Status</div>
            </div>

            {{-- Spacer total tinggi semua baris --}}
            <div id="virtualSpacer" class="relative">
                <div id="virtualRows" class="absolute left-0 right-0"></div>
            </div>

            {{-- Kondisi kosong --}}
            <div id="emptyState" class="hidden text-center text-sm text-[#6B7280] py-10">
                Tidak ada data untuk di-opname.
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL: SCAN / INPUT KODE ================= --}}
<div id="modalScan" class="font-mont hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop">
    <div class="bg-white w-full max-w-md rounded-xl shadow-xl p-6 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-[#0D1C43]">Scan / Input Kode Barang</h2>
            <button id="btnCloseScan" type="button" class="text-[#6B7280] hover:text-[#0D1C43] text-xl leading-none">&times;</button>
        </div>

        {{-- Kamera: live (HTTPS/localhost) atau foto (HTTP) --}}
        <div class="mb-3">
            @include('inventory.partials.barcode-camera', ['cameraId' => 'opname'])
        </div>

        {{-- Scanner fisik / ketik manual. type="text": kode diterima sebagai string --}}
        <form id="formScan" class="space-y-3">
            <input
                type="text"
                id="inputKode"
                inputmode="text"
                autocomplete="off"
                autocapitalize="off"
                spellcheck="false"
                placeholder="Scan barcode atau ketik kode lalu Enter..."
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-xs text-[#0D1C43] placeholder-[#6B7280] focus:outline-none focus:ring-2 focus:ring-[#22396F]/40 focus:border-[#22396F]"
            >
            <button type="submit" class="w-full bg-[#0D1C43] text-white text-xs font-semibold py-2.5 rounded-md hover:bg-[#22396F]">
                Cek Kode
            </button>
        </form>

        {{-- Feedback hasil scan terakhir --}}
        <div id="scanFeedback" class="hidden mt-4 text-xs rounded-md px-4 py-3"></div>

        <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-3 gap-2 text-center text-[11px]">
            <div>
                <div class="font-semibold text-[#6B7280]" id="fbCountChecking">0</div>
                <div class="text-[#6B7280]">Belum Terscan</div>
            </div>
            <div>
                <div class="font-semibold text-[#F2B80C]" id="fbCountBelum">0</div>
                <div class="text-[#F2B80C]">Belum Terjual</div>
            </div>
            <div>
                <div class="font-semibold text-[#327437]" id="fbCountSudah">0</div>
                <div class="text-[#327437]">Sudah Terjual</div>
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL: KONFIRMASI SELESAI ================= --}}
<div id="modalConfirm" class="font-mont hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop">
    <div class="bg-white w-full max-w-sm rounded-xl shadow-xl p-6 text-center">
        <div class="w-10 h-10 mx-auto rounded-full border-2 border-[#F2B80C] flex items-center justify-center mb-3">
            <span class="text-[#F2B80C] text-xl font-bold leading-none">!</span>
        </div>
        <h2 class="text-sm font-bold text-[#0D1C43] mb-1">Akhiri Sesi Cek Stok?</h2>
        <p class="text-xs text-[#6B7280] mb-5">
            Setelah selesai, sesi cek stok ini dianggap berakhir. Pastikan semua barang sudah di-scan.
        </p>
        <div class="flex gap-3">
            <button id="btnBatalSelesai" class="flex-1 py-2 rounded-md text-xs font-semibold border border-[#0D1C43] text-[#0D1C43] bg-white hover:bg-gray-50">
                Batal
            </button>
            <button id="btnYaSelesai" class="flex-1 py-2 rounded-md text-xs font-semibold border border-[#D02231] bg-[#D02231]/10 text-[#D02231] hover:bg-[#D02231]/20">
                Ya, Selesai
            </button>
        </div>
    </div>
</div>

{{-- ================= MODAL: RINGKASAN HASIL ================= --}}
<div id="modalSummary" class="font-mont hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop">
    <div class="bg-white w-full max-w-md rounded-xl shadow-xl p-6">
        <h2 class="text-base font-bold text-[#0D1C43] mb-1">Ringkasan Cek Stok</h2>
        <p class="text-xs text-[#6B7280] mb-5">Data ini hanya ditampilkan di sesi ini dan tidak disimpan otomatis.</p>

        <div class="space-y-2">
            <div class="flex items-center justify-between px-4 py-2.5 rounded-md border border-[#F2B80C] bg-[#F2B80C]/10">
                <span class="text-xs text-[#F2B80C] font-medium">Belum Terjual (ditemukan saat scan)</span>
                <span class="text-xs font-semibold text-[#F2B80C]" id="sumBelum">0</span>
            </div>
            <div class="flex items-center justify-between px-4 py-2.5 rounded-md border border-[#327437] bg-[#327437]/20">
                <span class="text-xs text-[#327437] font-medium">Sudah Terjual</span>
                <span class="text-xs font-semibold text-[#327437]" id="sumSudah">0</span>
            </div>
            <div class="flex items-center justify-between px-4 py-2.5 rounded-md border border-[#6B7280] bg-gray-100">
                <span class="text-xs text-[#6B7280] font-medium">Tidak Terscan (Checking)</span>
                <span class="text-xs font-semibold text-[#6B7280]" id="sumChecking">0</span>
            </div>
            <div class="flex items-center justify-between px-1 pt-2">
                <span class="text-xs text-[#0D1C43]">Total Data</span>
                <span class="text-xs text-[#0D1C43] font-semibold" id="sumTotal">0</span>
            </div>
        </div>

        <button id="btnTutupSummary" class="w-full mt-5 py-2.5 rounded-md text-xs font-semibold bg-[#0D1C43] text-white hover:bg-[#22396F]">
            Tutup
        </button>
    </div>
</div>
@endif
@endsection

@push('scripts')
@if (!isset($items))
<script>
document.getElementById('btnMulai').addEventListener('click', function (e) {
    // cegah klik ganda selagi data sedang di-pull
    if (this.dataset.loading) { e.preventDefault(); return; }
    this.dataset.loading = '1';
    this.textContent = 'Memuat data...';
    this.classList.add('opacity-70', 'cursor-wait');
});
</script>
@else
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================================
       1. DATA — dikirim dari controller lewat $items, murni diolah di JS
    ===================================================================== */
    const rawData = @json($items ?? []);

    // Normalisasi + kasih index unik.
    // kode_barcode SELALU String (leading zero & huruf tetap utuh).
    const stokData = rawData.map((item, idx) => ({
        idx,
        kode_barcode: String(item.kode_barcode ?? '').trim(),
        nama_barang: item.nama_barang ?? '-',
        status: ['checking', 'sudah_terjual', 'belum_terjual'].includes(item.status)
            ? item.status
            : 'checking',
    }));

    // Escape HTML: data masuk lewat innerHTML pada virtual scroll
    const esc = (v) => String(v).replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    // Bersihkan karakter kontrol (sebagian scanner menambah prefix/suffix)
    const bersihkan = (v) => String(v ?? '').replace(/[\u0000-\u001F\u007F]/g, '').trim();

    // Data yang sedang ditampilkan (hasil search + filter + urut)
    let tampil = stokData.slice();

    // Urutan status: checking -> belum terjual -> sudah terjual
    const URUTAN_STATUS = { checking: 0, belum_terjual: 1, sudah_terjual: 2 };

    function urutkanData(arr) {
        return arr.sort((a, b) =>
            (URUTAN_STATUS[a.status] - URUTAN_STATUS[b.status]) || (a.idx - b.idx)
        );
    }

    let sesiSelesai = false;   // flag apakah sesi sudah "diselesaikan"
    let pendingNavHref = null; // tujuan setelah konfirmasi + ringkasan
    const URL_GUDANG = @json(route('inventory.gudang')); // tujuan setelah cek stok selesai

    /* =====================================================================
       2. VIRTUAL SCROLL TABLE
    ===================================================================== */
    const ROW_HEIGHT = 40;
    const scrollBox   = document.getElementById('opnameScroll');
    const spacer      = document.getElementById('virtualSpacer');
    const rowsWrap    = document.getElementById('virtualRows');
    const emptyState  = document.getElementById('emptyState');
    const HEADER_H    = 40; // tinggi header sticky (approx)

    const BADGE_BASE = 'inline-block min-w-[112px] px-3 py-0.5 text-xs font-semibold rounded border';

    const badge = (status) => {
        if (status === 'belum_terjual') {
            return `<span class="${BADGE_BASE} border-[#F2B80C] bg-[#F2B80C]/10 text-[#F2B80C]">Belum Terjual</span>`;
        }
        if (status === 'sudah_terjual') {
            return `<span class="${BADGE_BASE} border-[#327437] bg-[#327437]/20 text-[#327437]">Sudah Terjual</span>`;
        }
        return `<span class="${BADGE_BASE} border-[#6B7280] bg-white text-[#6B7280]">Checking</span>`;
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
                <div class="grid grid-cols-3 items-center text-center text-xs border-b border-gray-300 data-row"
                     data-idx="${item.idx}" style="height:${ROW_HEIGHT}px;">
                    <div class="px-4 min-w-0 truncate whitespace-nowrap text-[#0D1C43]" title="${esc(item.kode_barcode)}">${esc(item.kode_barcode)}</div>
                    <div class="px-4 min-w-0 truncate whitespace-nowrap text-[#0D1C43]" title="${esc(item.nama_barang)}">${esc(item.nama_barang)}</div>
                    <div class="px-4 min-w-0 truncate">${badge(item.status)}</div>
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
       2b. SEARCH & FILTER STATUS + URUTAN (murni JS)
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
       3. HITUNG STATUS
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
       4. MODAL: SCAN / INPUT KODE (scanner fisik + kamera)
    ===================================================================== */
    const modalScan    = document.getElementById('modalScan');
    const inputKode    = document.getElementById('inputKode');
    const formScan     = document.getElementById('formScan');
    const scanFeedback = document.getElementById('scanFeedback');
    const camOpname    = document.getElementById('bcam-opname');

    function openScanModal() {
        modalScan.classList.remove('hidden');
        updateModalScanCounter();
        scanFeedback.classList.add('hidden');
        inputKode.value = '';
        setTimeout(() => inputKode.focus(), 50);
    }
    function closeScanModal() {
        modalScan.classList.add('hidden');
        // matikan kamera supaya lampu kamera padam
        if (window.BarcodeCamera) BarcodeCamera.stop('opname');
    }

    function showFeedback(type, message) {
        const styles = {
            success: 'bg-[#327437]/10 text-[#327437] border border-[#327437]',
            warning: 'bg-[#D02231]/10 text-[#D02231] border border-[#D02231]',
            info:    'bg-[#22396F]/10 text-[#22396F] border border-[#22396F]',
            error:   'bg-gray-100 text-[#6B7280] border border-[#6B7280]',
        };
        scanFeedback.className = 'mt-4 text-xs rounded-md px-4 py-3 ' + styles[type];
        scanFeedback.textContent = message;
        scanFeedback.classList.remove('hidden');
    }

    function prosesKode(kodeInput) {
        const kode = bersihkan(kodeInput);
        if (!kode) return;

        // Perbandingan murni string (tanpa konversi angka)
        const item = stokData.find(
            d => d.kode_barcode.toLowerCase() === kode.toLowerCase()
        );

        if (!item) {
            showFeedback('error', `❌ Kode "${kode}" tidak ditemukan dalam daftar cek stok.`);
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

        // hitung ulang + urutkan ulang daftar tampil, tanpa reset scroll
        terapkanFilter(false);

        // lompat ke baris itu kalau memang sedang tampil (lolos search/filter)
        const posisi = tampil.indexOf(item);
        if (posisi !== -1) scrollToIndex(posisi);
    }

    document.getElementById('btnScan').addEventListener('click', openScanModal);
    document.getElementById('btnCloseScan').addEventListener('click', closeScanModal);
    modalScan.addEventListener('click', (e) => { if (e.target === modalScan) closeScanModal(); });

    // Scanner fisik / ketik manual
    formScan.addEventListener('submit', function (e) {
        e.preventDefault();
        prosesKode(inputKode.value);
        inputKode.value = '';
        inputKode.focus();
    });

    // Hasil dari kamera (live maupun foto): langsung diproses sebagai string
    camOpname.addEventListener('barcode:scan', function (e) {
        prosesKode(e.detail.code);
    });

    // Scanner fisik tetap bisa dipakai walau fokus sedang di tombol kamera:
    // ketikan pertama otomatis dialihkan ke kolom kode.
    modalScan.addEventListener('keydown', function (e) {
        if (e.ctrlKey || e.metaKey || e.altKey) return;
        const tag = (e.target.tagName || '').toLowerCase();
        if (tag === 'input' || tag === 'textarea' || tag === 'select') return;
        if (e.key.length === 1) inputKode.focus(); // karakter akan masuk ke input
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modalScan.classList.contains('hidden')) closeScanModal();
    });

    /* =====================================================================
       5. MODAL: KONFIRMASI SELESAI + RINGKASAN
          Setelah ringkasan ditutup -> redirect ke route('inventory.gudang')
          (atau ke link sidebar yang tadi diklik, kalau selesai lewat navigasi)
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
        pendingNavHref = URL_GUDANG; // selesai manual -> setelah ringkasan, arahkan ke gudang
        openConfirmModal();
    });
    document.getElementById('btnBatalSelesai').addEventListener('click', () => {
        pendingNavHref = null; // batal -> reset tujuan, tetap di halaman
        closeConfirmModal();
    });
    document.getElementById('btnYaSelesai').addEventListener('click', selesaikanSesi);

    document.getElementById('btnTutupSummary').addEventListener('click', () => {
        modalSummary.classList.add('hidden');
        if (pendingNavHref) {
            const href = pendingNavHref;
            pendingNavHref = null;
            window.location.href = href;
        }
    });

    /* =====================================================================
       6. INTERCEPT NAVIGASI (klik link sidebar / link lain)
          -> dianggap "menyelesaikan" sesi juga
    ===================================================================== */
    document.addEventListener('click', function (e) {
        if (sesiSelesai) return;

        const link = e.target.closest('a[href]');
        if (!link) return;

        const href = link.getAttribute('href');
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
       INIT
    ===================================================================== */
    terapkanFilter();
});
</script>
@endif
@endpush