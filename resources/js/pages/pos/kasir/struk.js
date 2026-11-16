/**
 * =============================================================================
 * Kasir Struk / Receipt Management (Pop-up modal & DomPDF printing)
 * =============================================================================
 */

import { state, formatRupiah, escapeHtml } from './config.js';

function formatPersen(val) {
    const rounded = Math.round((Number(val) || 0) * 100) / 100;
    return String(rounded).replace('.', ',');
}

function groupStrukItemsPerBarang(rawItems) {
    const groups = new Map();
    let rawSubtotal = 0;
    let totalItemDisc = 0;

    (rawItems || []).forEach(item => {
        const qty = parseInt(item.qty) || 1;
        const nama = item.nama || item.nama_produk || item.name || 'Produk';
        const groupKey = String(item.id || item.itm || nama).trim().toLowerCase();
        const price = Math.max(0, parseFloat(item.price) || 0);
        const discPct = Math.min(100, Math.max(0, parseFloat(item.discount) || 0));

        const itemGross = price * qty;
        const discPctNominalTotal = item.discount_pct_nominal !== undefined
            ? Math.min(itemGross, Math.max(0, parseFloat(item.discount_pct_nominal) || 0))
            : (price * (discPct / 100)) * qty;

        const maxVchTotal = Math.max(0, itemGross - discPctNominalTotal);
        const rawVchTotal = item.discount_voucher_total !== undefined
            ? (parseFloat(item.discount_voucher_total) || 0)
            : (parseFloat(item.discount_voucher) || 0) * qty;
        const discVchTotal = Math.min(maxVchTotal, Math.max(0, rawVchTotal));

        const itemDiscTotal = Math.min(itemGross, discPctNominalTotal + discVchTotal);
        const itemSubtotal = item.subtotal !== undefined && item.discount_pct_nominal !== undefined
            ? Math.max(0, parseFloat(item.subtotal) || 0)
            : Math.max(0, itemGross - itemDiscTotal);

        rawSubtotal += itemGross;
        totalItemDisc += itemDiscTotal;

        if (!groups.has(groupKey)) {
            groups.set(groupKey, {
                id: item.id || item.itm || nama,
                nama: nama,
                qty: 0,
                sumGross: 0,
                sumPctWeighted: 0,
                sumDiscPctNominal: 0,
                sumDiscVch: 0,
                subtotal: 0
            });
        }

        const g = groups.get(groupKey);
        g.qty += qty;
        g.sumGross += itemGross;
        g.sumPctWeighted += (discPct * qty);
        g.sumDiscPctNominal += discPctNominalTotal;
        g.sumDiscVch += discVchTotal;
        g.subtotal += itemSubtotal;
    });

    const groupedItems = [];
    groups.forEach(g => {
        const qty = Math.max(1, g.qty);
        const unitPrice = g.sumGross / qty;
        const avgPct = Math.round((g.sumPctWeighted / qty) * 100) / 100;
        const avgPctFmt = formatPersen(avgPct);
        const discPctNom = g.sumDiscPctNominal;
        const discVch = g.sumDiscVch;

        let discInfoText = '';
        if (avgPct > 0 && discVch > 0) {
            discInfoText = `Diskon: -${avgPctFmt}% (-${formatRupiah(discPctNom)}) + Voucher item: -${formatRupiah(discVch)}`;
        } else if (avgPct > 0) {
            discInfoText = `Diskon: -${avgPctFmt}% (-${formatRupiah(discPctNom)})`;
        } else if (discVch > 0) {
            discInfoText = `Voucher item: -${formatRupiah(discVch)}`;
        }

        groupedItems.push({
            id: g.id,
            nama: g.nama,
            qty: qty,
            price: unitPrice,
            discount: avgPct,
            discount_pct_nominal: discPctNom,
            discount_voucher: discVch,
            discount_voucher_total: discVch,
            discount_info: discInfoText,
            subtotal: g.subtotal
        });
    });

    return {
        items: groupedItems,
        rawSubtotal: rawSubtotal,
        totalItemDisc: totalItemDisc
    };
}

export function tampilModalStruk(transaksiData) {
    if (!transaksiData) return;

    state.strukTerakhir = JSON.parse(JSON.stringify(transaksiData));

    // Fill data header & info transaksi
    const elNoNota = document.getElementById('strukNoNota');
    const elTanggal = document.getElementById('strukTanggal');
    const elWaktu = document.getElementById('strukWaktu');
    const elKasir = document.getElementById('strukKasir');
    const elSubtotal = document.getElementById('strukSubtotal');
    const elTotal = document.getElementById('strukTotal');
    const elMetode = document.getElementById('strukMetode');

    if (elNoNota) elNoNota.innerText = transaksiData.ref_no || '-';
    if (elTanggal) elTanggal.innerText = transaksiData.tanggal || new Date().toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
    if (elWaktu) elWaktu.innerText = transaksiData.waktu || new Date().toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    }).replace(':', '.');
    if (elKasir) elKasir.innerText = transaksiData.kasir || 'Kasir';
    if (elMetode) elMetode.innerText = (transaksiData.metode || 'Tunai').toUpperCase();

    // Render Info Pelanggan, Desa & Alamat
    const wrapPelanggan = document.getElementById('wrapperStrukPelanggan');
    const rowCstNama = document.getElementById('rowStrukCustomerNama');
    const elCstNama = document.getElementById('strukCustomerNama');
    const rowCstDesa = document.getElementById('rowStrukCustomerDesa');
    const elCstDesa = document.getElementById('strukCustomerDesa');
    const rowCstAlamat = document.getElementById('rowStrukCustomerAlamat');
    const elCstAlamat = document.getElementById('strukCustomerAlamat');

    if (wrapPelanggan) {
        const rawNama = (transaksiData.customer_nama || '').trim();
        const rawDesa = (transaksiData.customer_desa || '').trim();
        const rawAlamat = (transaksiData.customer_alamat || '').trim();

        const norm = (s) => (s || '')
            .toLowerCase()
            .replace(/^(ds\.|desa\s+|dusun\s+)/i, '')
            .replace(/[^a-z0-9]/g, '');

        const normDesa = norm(rawDesa);
        const normNama = norm(rawNama);
        const normAlamat = norm(rawAlamat);

        const isNamaLikeDesa = !normNama || normNama === '-' || (normDesa && (normNama === normDesa || normNama.includes(normDesa)));
        const isAlamatLikeDesa = !normAlamat || normAlamat === '-' || (normDesa && (normAlamat === normDesa || normAlamat.includes(normDesa)));

        let showNama = false;
        let showDesa = false;
        let showAlamat = false;

        if (isNamaLikeDesa && isAlamatLikeDesa) {
            showDesa = !!rawDesa && rawDesa !== '-';
        } else {
            showNama = !isNamaLikeDesa && !!rawNama && rawNama !== '-';
            showDesa = !!rawDesa && rawDesa !== '-';
            showAlamat = !isAlamatLikeDesa && !!rawAlamat && rawAlamat !== '-';
        }

        if (showNama && elCstNama && rowCstNama) {
            elCstNama.textContent = rawNama;
            rowCstNama.classList.remove('hidden');
        } else if (rowCstNama) {
            rowCstNama.classList.add('hidden');
        }

        if (showDesa && elCstDesa && rowCstDesa) {
            elCstDesa.textContent = rawDesa;
            rowCstDesa.classList.remove('hidden');
        } else if (rowCstDesa) {
            rowCstDesa.classList.add('hidden');
        }

        if (showAlamat && elCstAlamat && rowCstAlamat) {
            elCstAlamat.textContent = rawAlamat;
            rowCstAlamat.classList.remove('hidden');
        } else if (rowCstAlamat) {
            rowCstAlamat.classList.add('hidden');
        }

        if (showNama || showDesa || showAlamat) {
            wrapPelanggan.classList.remove('hidden');
        } else {
            wrapPelanggan.classList.add('hidden');
        }
    }

    // Render daftar produk yang dibeli (digabung per barang + akumulasi diskon)
    const listContainer = document.getElementById('strukListProduk');

    const rawItems = Array.isArray(transaksiData.items) ?
        transaksiData.items :
        Object.values(transaksiData.items || {});

    const grouped = groupStrukItemsPerBarang(rawItems);
    const items = grouped.items;
    const rawSubtotal = grouped.rawSubtotal;
    const totalItemDisc = grouped.totalItemDisc;

    if (listContainer) {
        listContainer.innerHTML = '';

        items.forEach(item => {
            const discInfoText = item.discount_info || '';

            listContainer.innerHTML += `
                <div class="flex justify-between items-start text-xs pt-1">
                    <div class="min-w-0 pr-2">
                        <p class="font-bold text-gray-900 truncate">${escapeHtml(item.nama)}</p>
                        <p class="text-[11px] text-gray-500 font-medium">${item.qty}x ${formatRupiah(item.price)}</p>
                        ${discInfoText ? `<p class="text-[10.5px] text-red-600 font-medium mt-0.5">${escapeHtml(discInfoText)}</p>` : ''}
                    </div>
                    <span class="font-bold text-gray-900 shrink-0 text-right">${formatRupiah(item.subtotal)}</span>
                </div>
            `;
        });
    }

    const finalSubtotal = transaksiData.subtotal !== undefined ? parseFloat(transaksiData.subtotal) : rawSubtotal;
    if (elSubtotal) elSubtotal.innerText = formatRupiah(finalSubtotal);

    // Diskon per item
    const finalItemDisc = transaksiData.item_discount !== undefined ? parseFloat(transaksiData.item_discount) : totalItemDisc;
    const wrapperDiscItem = document.getElementById('wrapperStrukDiskonItem');
    const elDiscItem = document.getElementById('strukDiskonItem');
    if (wrapperDiscItem && elDiscItem) {
        if (finalItemDisc > 0) {
            elDiscItem.innerText = `- ${formatRupiah(finalItemDisc)}`;
            wrapperDiscItem.classList.remove('hidden');
            wrapperDiscItem.classList.add('flex');
        } else {
            wrapperDiscItem.classList.add('hidden');
            wrapperDiscItem.classList.remove('flex');
        }
    }

    // Diskon global (%)
    const globalPct = parseFloat(transaksiData.discount_percent) || 0;
    const subtotalAfterItem = Math.max(0, finalSubtotal - finalItemDisc);
    const finalGlobalDisc = transaksiData.discount_global !== undefined ?
        parseFloat(transaksiData.discount_global) :
        (subtotalAfterItem * (globalPct / 100));

    const wrapperDiscGlobal = document.getElementById('wrapperStrukDiskonGlobal');
    const labelDiscGlobal = document.getElementById('labelStrukDiskonGlobal');
    const elDiscGlobal = document.getElementById('strukDiskonGlobal');
    if (wrapperDiscGlobal && elDiscGlobal) {
        if (finalGlobalDisc > 0) {
            if (labelDiscGlobal) labelDiscGlobal.innerText = globalPct > 0 ? `Diskon Global (${globalPct}%)` : 'Diskon Global';
            elDiscGlobal.innerText = `- ${formatRupiah(finalGlobalDisc)}`;
            wrapperDiscGlobal.classList.remove('hidden');
            wrapperDiscGlobal.classList.add('flex');
        } else {
            wrapperDiscGlobal.classList.add('hidden');
            wrapperDiscGlobal.classList.remove('flex');
        }
    }

    // Voucher global (Rp)
    const finalVoucher = parseFloat(transaksiData.voucher) || 0;
    const wrapperVoucher = document.getElementById('wrapperStrukVoucher');
    const elVoucher = document.getElementById('strukVoucher');
    if (wrapperVoucher && elVoucher) {
        if (finalVoucher > 0) {
            elVoucher.innerText = `- ${formatRupiah(finalVoucher)}`;
            wrapperVoucher.classList.remove('hidden');
            wrapperVoucher.classList.add('flex');
        } else {
            wrapperVoucher.classList.add('hidden');
            wrapperVoucher.classList.remove('flex');
        }
    }

    // PPN
    const finalPpnPct = transaksiData.ppn_percent !== undefined ? parseFloat(transaksiData.ppn_percent) : (state.ppnPercent || 0);
    const finalPpnNominal = transaksiData.ppn_nominal !== undefined ? parseFloat(transaksiData.ppn_nominal) : 0;
    const wrapperPpn = document.getElementById('wrapperStrukPPN');
    const labelPpn = document.getElementById('labelStrukPPN');
    const elPpn = document.getElementById('strukPPN');
    if (wrapperPpn && elPpn) {
        if (finalPpnNominal > 0) {
            if (labelPpn) labelPpn.innerText = finalPpnPct > 0 ? `PPN (${finalPpnPct}%)` : 'PPN';
            elPpn.innerText = `+ ${formatRupiah(finalPpnNominal)}`;
            wrapperPpn.classList.remove('hidden');
            wrapperPpn.classList.add('flex');
        } else {
            wrapperPpn.classList.add('hidden');
            wrapperPpn.classList.remove('flex');
        }
    }

    // Grand Total
    const finalGrandTotal = transaksiData.total !== undefined ?
        parseFloat(transaksiData.total) :
        Math.max(0, subtotalAfterItem - finalGlobalDisc - finalVoucher);
    if (elTotal) elTotal.innerText = formatRupiah(finalGrandTotal);

    // Detail Pembayaran Tunai
    const wrapperTunai = document.getElementById('wrapperDetailTunai');
    const elDibayar = document.getElementById('strukDibayar');
    const elKembali = document.getElementById('strukKembali');

    const isTunai = (transaksiData.metode && (
        transaksiData.metode.toLowerCase() === 'cash' ||
        transaksiData.metode.toLowerCase() === 'tunai'
    ));

    if (isTunai && wrapperTunai) {
        wrapperTunai.classList.remove('hidden');
        if (elDibayar) elDibayar.innerText = formatRupiah(transaksiData.dibayar || finalGrandTotal);
        if (elKembali) elKembali.innerText = formatRupiah(transaksiData.kembalian || 0);
    } else if (wrapperTunai) {
        wrapperTunai.classList.add('hidden');
    }

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

// Cetak Struk via DomPDF
export function cetakStrukPdf() {
    if (!state.strukTerakhir) {
        alert('Data struk tidak ditemukan.');
        return;
    }

    const rawItems = Array.isArray(state.strukTerakhir.items) ?
        state.strukTerakhir.items :
        Object.values(state.strukTerakhir.items || {});

    const grouped = groupStrukItemsPerBarang(rawItems);
    const rawSubtotal = grouped.rawSubtotal;
    const totalItemDisc = grouped.totalItemDisc;

    const items = grouped.items.map(function(item) {
        return {
            nama: item.nama,
            qty: item.qty,
            price: item.price,
            discount_info: item.discount_info,
            subtotal: item.subtotal
        };
    });

    const metode = state.strukTerakhir.metode || 'Tunai';
    const isTunai = metode.toLowerCase() === 'cash' || metode.toLowerCase() === 'tunai';

    const finalSubtotal = state.strukTerakhir.subtotal !== undefined ? state.strukTerakhir.subtotal : rawSubtotal;
    const finalItemDisc = state.strukTerakhir.item_discount !== undefined ? state.strukTerakhir.item_discount : totalItemDisc;
    const globalPct = parseFloat(state.strukTerakhir.discount_percent) || 0;
    const finalGlobalDisc = state.strukTerakhir.discount_global !== undefined ?
        state.strukTerakhir.discount_global :
        Math.max(0, (finalSubtotal - finalItemDisc) * (globalPct / 100));
    const finalVoucher = parseFloat(state.strukTerakhir.voucher) || 0;

    const setField = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = (val !== null && val !== undefined) ? val : '';
    };

    setField('printRefNo', state.strukTerakhir.ref_no || '');
    setField('printTanggal', state.strukTerakhir.tanggal || '');
    setField('printWaktu', state.strukTerakhir.waktu || '');
    setField('printKasir', state.strukTerakhir.kasir || '');
    setField('printMetode', metode);
    setField('printSubtotal', finalSubtotal);
    setField('printDiscountItem', finalItemDisc);
    setField('printDiscountGlobal', finalGlobalDisc);
    setField('printVoucher', finalVoucher);
    setField('printPpn', state.strukTerakhir.ppn_percent || '');
    setField('printPpnNominal', state.strukTerakhir.ppn_nominal || 0);
    setField('printTotal', state.strukTerakhir.total || 0);
    setField('printDibayar', isTunai ? (state.strukTerakhir.dibayar || 0) : '');
    setField('printKembalian', isTunai ? (state.strukTerakhir.kembalian || 0) : '');
    setField('printCustomerNama', state.strukTerakhir.customer_nama || '');
    setField('printCustomerDesa', state.strukTerakhir.customer_desa || '');
    setField('printCustomerAlamat', state.strukTerakhir.customer_alamat || '');
    setField('printItems', JSON.stringify(items));

    const formPrint = document.getElementById('formPrintStruk');
    if (formPrint) formPrint.submit();
}
