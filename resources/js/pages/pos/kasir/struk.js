/**
 * =============================================================================
 * Kasir Struk / Receipt Management (Pop-up modal & Browser Nota Print)
 * =============================================================================
 */

import { state } from './config.js';
import {
    groupReceiptItemsPerBarang,
    isTunaiMethod,
    renderNotaSheets,
    submitNotaPrintForm,
} from '../../../common/nota-receipt.js';

function normalizeKasirReceiptData(transaksiData) {
    const rawItems = Array.isArray(transaksiData.items)
        ? transaksiData.items
        : Object.values(transaksiData.items || {});

    const grouped = groupReceiptItemsPerBarang(rawItems);
    const items = grouped.items;
    const rawSubtotal = grouped.rawSubtotal;
    const totalItemDisc = grouped.totalItemDisc;

    const metode = transaksiData.metode || 'Tunai';
    const finalSubtotal = transaksiData.subtotal !== undefined ? parseFloat(transaksiData.subtotal) : rawSubtotal;
    const finalItemDisc = transaksiData.item_discount !== undefined ? parseFloat(transaksiData.item_discount) : totalItemDisc;
    const globalPct = parseFloat(transaksiData.discount_percent) || 0;
    const subtotalAfterItem = Math.max(0, finalSubtotal - finalItemDisc);
    const finalGlobalDisc = transaksiData.discount_global !== undefined
        ? parseFloat(transaksiData.discount_global)
        : (subtotalAfterItem * (globalPct / 100));
    const finalVoucher = parseFloat(transaksiData.voucher) || 0;
    const finalPpnPct = transaksiData.ppn_percent !== undefined ? parseFloat(transaksiData.ppn_percent) : (state.ppnPercent || 0);
    const finalPpnNominal = transaksiData.ppn_nominal !== undefined ? parseFloat(transaksiData.ppn_nominal) : 0;
    const finalGrandTotal = transaksiData.total !== undefined
        ? parseFloat(transaksiData.total)
        : Math.max(0, subtotalAfterItem - finalGlobalDisc - finalVoucher);

    const isTunai = isTunaiMethod(metode);
    const dibayar = isTunai ? (transaksiData.dibayar ?? finalGrandTotal) : 0;
    const kembalian = isTunai ? (transaksiData.kembalian ?? Math.max(0, dibayar - finalGrandTotal)) : 0;

    return {
        refNo: transaksiData.ref_no || '-',
        tanggal: transaksiData.tanggal || new Date().toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        }),
        waktu: transaksiData.waktu || new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
        }).replace(':', '.'),
        kasir: transaksiData.kasir || 'Kasir',
        customerNama: transaksiData.customer_nama || '',
        customerDesa: transaksiData.customer_desa || '',
        customerAlamat: transaksiData.customer_alamat || '',
        metode,
        items,
        subtotal: finalSubtotal,
        discountItem: finalItemDisc,
        discountPercent: globalPct,
        discountGlobal: finalGlobalDisc,
        voucher: finalVoucher,
        ppn: finalPpnPct,
        ppnNominal: finalPpnNominal,
        total: finalGrandTotal,
        dibayar,
        kembalian,
    };
}

export function tampilModalStruk(transaksiData) {
    if (!transaksiData) return;

    const normalized = normalizeKasirReceiptData(transaksiData);
    state.strukTerakhir = normalized;

    const previewRoot = document.getElementById('notaPreviewRoot');
    renderNotaSheets(previewRoot, normalized);

    const modal = document.getElementById('modalStrukPembayaran');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

export function tutupModalStruk() {
    const modal = document.getElementById('modalStrukPembayaran');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}

export function tutupModalStrukKlikLuar(e) {
    if (e.target.id === 'modalStrukPembayaran') {
        tutupModalStruk();
    }
}

// Cetak Struk via Browser Print View
export function cetakStrukPdf() {
    submitNotaPrintForm(state.strukTerakhir);
}
