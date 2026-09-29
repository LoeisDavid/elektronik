@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Elektronik')
@section('header_title', 'Riwayat Transaksi')

@section('content')
<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6 select-none">

    {{-- Toolbar --}}
    <div class="flex items-center justify-between gap-3 mb-4">
        <div class="flex items-center gap-2">

            {{-- Input Pencarian --}}
            <div class="relative w-64 md:w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </span>
                <input type="text" id="searchQuery" oninput="onSearch()"
                    placeholder="Cari kode / produk"
                    class="w-full bg-white border border-gray-800 rounded-xl pl-10 pr-4 py-2 text-sm text-black placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#1A233A]" />
            </div>

            {{-- Dropdown Urutkan --}}
            <div class="relative inline-block text-left" id="containerUrutkan">
                <button type="button" onclick="toggleDropdownUrutkan(event)"
                    class="h-[38px] px-4 bg-white border rounded-lg flex items-center justify-between gap-3 text-sm font-medium text-black hover:bg-gray-100 cursor-pointer shadow-sm">
                    <span id="labelSelectedSort">Urutkan: Tanggal</span>
                    <svg id="arrowUrutkan" class="w-4 h-4 text-gray-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>

                <div id="panelUrutkan"
                    class="hidden absolute left-0 mt-2 w-52 bg-secondary-bw rounded-2xl p-2.5 shadow-2xl border border-gray-300 z-40 space-y-2">
                    @foreach (['Tanggal' => 'checkTanggal', 'Total Tertinggi' => 'checkTotalTertinggi', 'Total Terendah' => 'checkTotalTerendah', 'Metode Pembayaran' => 'checkMetode'] as $label => $checkId)
                        <button type="button" onclick="pilihUrutkan('{{ $label }}')"
                            class="w-full flex items-center justify-between px-3.5 py-2 border bg-white hover:bg-gray-50 rounded-xl text-sm font-medium text-black text-left cursor-pointer">
                            <span>{{ $label }}</span>
                            <svg id="{{ $checkId }}" class="w-4 h-4 text-gray-600 {{ $label !== 'Tanggal' ? 'hidden' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Riwayat Transaksi --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden flex flex-col">
        <div class="overflow-y-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-primary-db text-white text-xs font-semibold">
                        <th class="py-2.5 px-4 font-semibold">Kode Transaksi</th>
                        <th class="py-2.5 px-4 font-semibold">Tanggal pembelian</th>
                        <th class="py-2.5 px-4 font-semibold">Total</th>
                        <th class="py-2.5 px-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody id="transaksiTableBody" class="divide-y divide-gray-200 text-sm text-black">
                    @foreach ($data as $item)
                        @php
                            $tglFmt    = $item->DT ? \Carbon\Carbon::parse($item->DT)->format('d/m/Y') : '-';
                            $tglTs     = $item->DT ? \Carbon\Carbon::parse($item->DT)->timestamp : 0;
                            $detailArr = collect($item->detail ?? [])->values();
                            $namaProduk = $detailArr->map(fn($d) => data_get($d, 'nama') ?? data_get($d, 'NAMA') ?? '')->implode(' ');
                            // Ganti METODE dengan nama kolom metode bayar aslimu
                            $metode = $item->METODE ?? '';
                        @endphp

                        <tr class="trx-row hover:bg-gray-50/80 transition-colors"
                            data-ref="{{ $item->REF_NO }}"
                            data-tanggal="{{ $tglFmt }}"
                            data-ts="{{ $tglTs }}"
                            data-total="{{ $item->BRT ?? 0 }}"
                            data-metode="{{ $metode }}"
                            data-search="{{ strtolower($item->REF_NO . ' ' . $tglFmt . ' ' . $namaProduk) }}"
                            data-detail="{{ json_encode($detailArr) }}">

                            <td class="py-3.5 px-4 font-medium">{{ $item->REF_NO }}</td>
                            <td class="py-3.5 px-4">{{ $tglFmt }}</td>
                            <td class="py-3.5 px-4 font-semibold font-number">
                                Rp {{ number_format($item->BRT ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <button type="button" onclick="openStrukModal(this)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-gray-800 hover:bg-gray-100 rounded-lg text-xs font-semibold text-black cursor-pointer transition-colors">
                                    <svg class="w-3.5 h-3.5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm0 5.25h.007v.008H3.75V12zm0 5.25h.007v.008H3.75V12.75z"/>
                                    </svg>
                                    <span>Struk</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach

                    <tr id="rowKosong" class="hidden">
                        <td colspan="4" class="py-6 text-center text-gray-500">Tidak ada transaksi ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-[#142854]">
            <div>Menampilkan <span id="paginationInfo">0 transaksi</span></div>
            <div class="flex items-center gap-1.5" id="paginationButtons"></div>
        </div>
    </div>

    {{-- Modal Struk --}}
    <div id="modalStrukPembayaran" onclick="closeStrukKlikLuar(event)"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">

        <div onclick="event.stopPropagation()"
            class="w-full max-w-sm bg-white rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-100 animate-fade-in">

            {{-- Area Struk --}}
            <div id="areaStrukPrint"
                class="bg-white p-4 rounded-xl border border-dashed border-gray-300 text-xs font-sans text-gray-800 space-y-3">

                <div class="text-center space-y-1">
                    <h2 class="text-xl font-extrabold text-black tracking-wider uppercase" id="strukNamaToko">NamaToko</h2>
                    <p class="text-[11px] text-gray-500 leading-tight" id="strukAlamatToko">
                        Medokan Ayu, Kec. Rungkut, Surabaya<br>
                        No Telp: 0881-0268-77983
                    </p>
                </div>

                <div class="border-b border-dashed border-gray-300 my-2"></div>

                <div class="space-y-1 text-gray-600">
                    <div class="flex justify-between">
                        <span>No. Transaksi</span>
                        <span id="strukNoNota" class="font-semibold text-gray-900">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tanggal</span>
                        <span id="strukTanggal">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Dilayani oleh</span>
                        <span id="strukKasir">User 2</span>
                    </div>
                </div>

                <div class="border-b border-dashed border-gray-300 my-2"></div>

                <div id="strukListProduk" class="space-y-2"></div>

                <div class="border-b border-dashed border-gray-300 my-2"></div>

                <div class="space-y-1.5 pt-1">
                    <div class="flex justify-between text-sm font-bold text-gray-900">
                        <span>Total</span>
                        <span id="strukTotal" class="text-base">Rp 0</span>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="grid grid-cols-2 gap-3 pt-2">
                <button type="button" onclick="downloadStruk()"
                    class="w-full bg-white border active:bg-black text-black rounded-xl py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer shadow-sm flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0z" fill="none" />
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15V3m0 12l-4-4m4 4l4-4M2 17l.621 2.485A2 2 0 0 0 4.561 21h14.877a2 2 0 0 0 1.94-1.515L22 17"/>
                    </svg>
                    Download
                </button>

                <button type="button" onclick="cetakStruk()"
                    class="w-full bg-primary-db hover:bg-primary-sdb active:bg-black text-white rounded-xl py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4h2m0 0h10m-6 4h6a2 2 0 002-2v-4H9v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Struk
                </button>
            </div>
        </div>
    </div>
</div>

{{-- CSS khusus cetak: hanya area struk yang tercetak --}}
<style>
    @media print {
        body * { visibility: hidden !important; }

        #areaStrukPrint,
        #areaStrukPrint * { visibility: visible !important; }

        #areaStrukPrint {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            max-width: 350px;
            border: none !important;
        }
    }
</style>

{{-- html2canvas untuk tombol Download --}}
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
    const PER_PAGE = 10; // jumlah transaksi per halaman
    let currentPage = 1;
    let currentSort = 'Tanggal';

    const tbody = document.getElementById('transaksiTableBody');
    const allRows = Array.from(tbody.querySelectorAll('tr.trx-row'));

    /* ============ Tabel: cari, urutkan, pagination ============ */
    function getFilteredSortedRows() {
        const q = document.getElementById('searchQuery').value.trim().toLowerCase();
        const rows = allRows.filter(r => r.dataset.search.includes(q));

        rows.sort((a, b) => {
            switch (currentSort) {
                case 'Total Tertinggi':   return Number(b.dataset.total) - Number(a.dataset.total);
                case 'Total Terendah':    return Number(a.dataset.total) - Number(b.dataset.total);
                case 'Metode Pembayaran': return a.dataset.metode.localeCompare(b.dataset.metode);
                default:                  return Number(b.dataset.ts) - Number(a.dataset.ts);
            }
        });
        return rows;
    }

    function render() {
        const rows = getFilteredSortedRows();
        const total = rows.length;
        const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * PER_PAGE;
        const end = start + PER_PAGE;

        allRows.forEach(r => r.style.display = 'none');
        rows.forEach((r, i) => {
            tbody.appendChild(r);
            r.style.display = (i >= start && i < end) ? '' : 'none';
        });

        const kosong = document.getElementById('rowKosong');
        kosong.classList.toggle('hidden', total > 0);
        tbody.appendChild(kosong);

        document.getElementById('paginationInfo').textContent =
            total === 0 ? '0 transaksi'
                        : (start + 1) + '-' + Math.min(end, total) + ' dari ' + total + ' transaksi';

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        const base   = 'h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold cursor-pointer ';
        const normal = 'border-[#A8B1C1] text-[#142854] hover:bg-gray-100';
        const active = 'bg-[#142854] text-white border-[#142854]';

        let html = '<button type="button" onclick="prevPage()" class="' + base + normal + '">&lsaquo;</button>';
        for (let p = 1; p <= totalPages; p++) {
            html += '<button type="button" onclick="goPage(' + p + ')" class="' + base + (p === currentPage ? active : normal) + '">' + p + '</button>';
        }
        html += '<button type="button" onclick="nextPage()" class="' + base + normal + '">&rsaquo;</button>';
        document.getElementById('paginationButtons').innerHTML = html;
    }

    function goPage(p)  { currentPage = p; render(); }
    function prevPage() { if (currentPage > 1) { currentPage--; render(); } }
    function nextPage() { currentPage++; render(); } // render() membatasi ke halaman terakhir
    function onSearch() { currentPage = 1; render(); }

    /* ============ Dropdown Urutkan ============ */
    function toggleDropdownUrutkan(event) {
        if (event) event.stopPropagation();
        document.getElementById('panelUrutkan').classList.toggle('hidden');
        document.getElementById('arrowUrutkan').classList.toggle('rotate-180');
    }

    function tutupDropdownUrutkan() {
        document.getElementById('panelUrutkan').classList.add('hidden');
        document.getElementById('arrowUrutkan').classList.remove('rotate-180');
    }

    function pilihUrutkan(option) {
        currentSort = option;
        currentPage = 1;
        document.getElementById('labelSelectedSort').innerText = 'Urutkan: ' + option;

        const checks = {
            'Tanggal': 'checkTanggal',
            'Total Tertinggi': 'checkTotalTertinggi',
            'Total Terendah': 'checkTotalTerendah',
            'Metode Pembayaran': 'checkMetode'
        };
        Object.entries(checks).forEach(([key, id]) => {
            document.getElementById(id).classList.toggle('hidden', key !== option);
        });

        tutupDropdownUrutkan();
        render();
    }

    /* ============ Modal Struk ============ */
    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }

    function formatRupiah(value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    }

    function openStrukModal(btn) {
        const row = btn.closest('tr');

        let items = [];
        try { items = JSON.parse(row.dataset.detail || '[]'); } catch (e) { items = []; }

        document.getElementById('strukNoNota').textContent  = row.dataset.ref;
        document.getElementById('strukTanggal').textContent = row.dataset.tanggal;
        document.getElementById('strukTotal').textContent   = formatRupiah(row.dataset.total);

        const list = document.getElementById('strukListProduk');
        list.innerHTML = '';

        if (!Array.isArray(items) || items.length === 0) {
            list.innerHTML = '<p class="text-gray-400 text-center">Tidak ada detail produk</p>';
        } else {
            items.forEach(d => {
                // Sesuaikan nama field dengan tabel detailmu (cek console.log(d))
                const qty      = Number(d.qty ?? d.QTY ?? 1);
                const nama     = d.nama ?? d.NAMA ?? '-';
                const harga    = d.harga ?? d.HARGA ?? null;
                const subtotal = d.subtotal ?? d.SUBTOTAL ?? (harga !== null ? harga * qty : null);

                const hargaHtml = subtotal !== null
                    ? '<span class="font-semibold text-gray-900">' + formatRupiah(subtotal) + '</span>'
                    : '';

                list.innerHTML +=
                    '<div class="flex justify-between items-start">' +
                        '<p class="font-semibold text-gray-900">' + escapeHtml(qty) + 'x ' + escapeHtml(nama) + '</p>' +
                        hargaHtml +
                    '</div>';
            });
        }

        const modal = document.getElementById('modalStrukPembayaran');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeStrukModal() {
        const modal = document.getElementById('modalStrukPembayaran');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function closeStrukKlikLuar(event) {
        if (event.target === event.currentTarget) closeStrukModal();
    }

    /* ============ Download & Print ============ */
    function downloadStruk() {
        if (typeof html2canvas === 'undefined') {
            alert('Library html2canvas gagal dimuat.');
            return;
        }
        const area = document.getElementById('areaStrukPrint');
        html2canvas(area, { scale: 2, backgroundColor: '#ffffff' }).then(canvas => {
            const a = document.createElement('a');
            a.download = 'struk-' + document.getElementById('strukNoNota').textContent + '.png';
            a.href = canvas.toDataURL('image/png');
            a.click();
        });
    }

    function cetakStruk() {
        window.print(); // yang tercetak hanya #areaStrukPrint (lihat @media print di atas)
    }

    /* ============ Event global ============ */
    document.addEventListener('click', function (e) {
        const c = document.getElementById('containerUrutkan');
        if (c && !c.contains(e.target)) tutupDropdownUrutkan();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { tutupDropdownUrutkan(); closeStrukModal(); }
    });

    render();
</script>
@endsection