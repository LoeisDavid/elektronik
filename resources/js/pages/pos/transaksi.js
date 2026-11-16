/**
 * =============================================================================
 * JavaScript Halaman POS Transaksi
 * =============================================================================
 */

import { renderNotaSheets, submitNotaPrintForm } from '../../common/nota-receipt.js';

let strukAktif = null;

// Dropdown Sort Transaksi
export function toggleSortTransaksi(event) {
    if (event) event.stopPropagation();
    const panel = document.getElementById('panelSortTransaksi');
    const arrow = document.getElementById('arrowSortTransaksi');
    if (panel) panel.classList.toggle('hidden');
    if (arrow) arrow.classList.toggle('rotate-180');
}

export function tutupSortTransaksi() {
    const panel = document.getElementById('panelSortTransaksi');
    const arrow = document.getElementById('arrowSortTransaksi');
    if (panel) panel.classList.add('hidden');
    if (arrow) arrow.classList.remove('rotate-180');
}

export function pilihSortTransaksi(btn) {
    if (!btn) return;
    const inputSort = document.getElementById('inputSortTransaksi');
    const labelSort = document.getElementById('labelSortTransaksi');

    if (inputSort) inputSort.value = btn.dataset.sort || '';
    if (labelSort) labelSort.textContent = btn.dataset.label || '';

    document.querySelectorAll('#panelSortTransaksi .icon-check-sort').forEach(icon => {
        icon.classList.add('hidden');
    });

    const check = btn.querySelector('.icon-check-sort');
    if (check) check.classList.remove('hidden');

    tutupSortTransaksi();
}

// Modal Struk
export function openStrukModal(btn) {
    if (!btn) return;

    let rawItems = [];
    try {
        rawItems = JSON.parse(btn.dataset.detail || '[]');
    } catch (e) {
        rawItems = [];
    }

    const items = (Array.isArray(rawItems) ? rawItems : []).map((d) => {
        const qty = Number(d.qty ?? d.QTY ?? 1);
        const nama = d.nama ?? d.NAMA ?? '-';
        const price = Number(d.price ?? d.harga ?? d.HARGA ?? 0);
        const discInfo = d.discount_info || '';
        const subtotalItem = d.subtotal != null ? Number(d.subtotal) : price * qty;

        return {
            nama,
            qty,
            price,
            discount_info: discInfo,
            subtotal: subtotalItem,
        };
    });

    strukAktif = {
        refNo: btn.dataset.ref || '-',
        tanggal: btn.dataset.tanggal || '-',
        waktu: btn.dataset.waktu || '00.00',
        kasir: btn.dataset.kasir || 'Kasir',
        customerNama: btn.dataset.customerNama || '',
        customerDesa: btn.dataset.customerDesa || '',
        customerAlamat: btn.dataset.customerAlamat || '',
        metode: btn.dataset.metode || 'Tunai',
        subtotal: parseFloat(btn.dataset.subtotal) || 0,
        discountItem: parseFloat(btn.dataset.diskonItem) || 0,
        discountPercent: parseFloat(btn.dataset.diskonGlobalPct) || 0,
        discountGlobal: parseFloat(btn.dataset.diskonGlobal) || 0,
        voucher: parseFloat(btn.dataset.voucher) || 0,
        ppn: parseFloat(btn.dataset.ppn) || 0,
        ppnNominal: parseFloat(btn.dataset.ppnNominal) || 0,
        total: parseFloat(btn.dataset.total) || 0,
        dibayar: parseFloat(btn.dataset.dibayar) || 0,
        kembalian: parseFloat(btn.dataset.kembalian) || 0,
        items,
    };

    const previewRoot = document.getElementById('notaPreviewRoot');
    renderNotaSheets(previewRoot, strukAktif);

    const modal = document.getElementById('modalStrukPembayaran');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

export function closeStrukModal() {
    const modal = document.getElementById('modalStrukPembayaran');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}

export function cetakStruk() {
    submitNotaPrintForm(strukAktif);
}

// Modal Print Laporan
export function openModalPrintLaporan() {
    const modal = document.getElementById('modalPrintLaporan');
    if (modal) modal.classList.remove('hidden');
}

export function closeModalPrintLaporan() {
    const modal = document.getElementById('modalPrintLaporan');
    if (modal) modal.classList.add('hidden');
}

// Expose ke window
if (typeof window !== 'undefined') {
    window.toggleSortTransaksi = toggleSortTransaksi;
    window.tutupSortTransaksi = tutupSortTransaksi;
    window.pilihSortTransaksi = pilihSortTransaksi;
    window.openStrukModal = openStrukModal;
    window.closeStrukModal = closeStrukModal;
    window.tutupModalStruk = closeStrukModal;
    window.cetakStruk = cetakStruk;
    window.cetakStrukPdf = cetakStruk;
    window.openModalPrintLaporan = openModalPrintLaporan;
    window.closeModalPrintLaporan = closeModalPrintLaporan;
}

// Global click outside & escape
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (e) => {
        const container = document.getElementById('containerSortTransaksi');
        if (container && !container.contains(e.target)) {
            tutupSortTransaksi();
        }
        if (e.target && e.target.id === 'modalStrukPembayaran') {
            closeStrukModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            tutupSortTransaksi();
            closeStrukModal();
            closeModalPrintLaporan();
        }
    });
});
