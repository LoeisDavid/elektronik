/**
 * =============================================================================
 * Kasir Struk / Receipt Management (Pop-up modal & DomPDF printing)
 * =============================================================================
 */

import { state, formatRupiah, escapeHtml } from './config.js';

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

    // Render daftar produk yang dibeli + breakdown diskon
    const listContainer = document.getElementById('strukListProduk');
    let rawSubtotal = 0;
    let totalItemDisc = 0;

    const items = Array.isArray(transaksiData.items) ?
        transaksiData.items :
        Object.values(transaksiData.items || {});

    if (listContainer) {
        listContainer.innerHTML = '';

        items.forEach(item => {
            const qty = parseInt(item.qty) || 1;
            const nama = item.nama || item.nama_produk || item.name || 'Produk';
            const price = parseFloat(item.price) || 0;
            const discPct = parseFloat(item.discount) || 0;
            const discVch = parseFloat(item.discount_voucher) || 0;

            const discPctNominal = price * (discPct / 100);
            const itemDiscUnit = discPctNominal + discVch;
            const itemSubtotal = Math.max(0, (price - itemDiscUnit) * qty);

            rawSubtotal += (price * qty);
            totalItemDisc += (itemDiscUnit * qty);

            let discInfoText = '';
            if (discPct > 0 && discVch > 0) {
                discInfoText = `Diskon: -${discPct}% (-${formatRupiah(discPctNominal)}) + Voucher item: -${formatRupiah(discVch)}`;
            } else if (discPct > 0) {
                discInfoText = `Diskon: -${discPct}% (-${formatRupiah(discPctNominal)})`;
            } else if (discVch > 0) {
                discInfoText = `Voucher item: -${formatRupiah(discVch)}`;
            }

            listContainer.innerHTML += `
                <div class="flex justify-between items-start text-xs pt-1">
                    <div class="min-w-0 pr-2">
                        <p class="font-bold text-gray-900 truncate">${escapeHtml(nama)}</p>
                        <p class="text-[11px] text-gray-500 font-medium">${qty}x ${formatRupiah(price)}</p>
                        ${discInfoText ? `<p class="text-[10.5px] text-red-600 font-medium mt-0.5">${escapeHtml(discInfoText)}</p>` : ''}
                    </div>
                    <span class="font-bold text-gray-900 shrink-0 text-right">${formatRupiah(itemSubtotal)}</span>
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

    let rawSubtotal = 0;
    let totalItemDisc = 0;

    const items = rawItems.map(function(item) {
        const qty = parseInt(item.qty) || 1;
        const nama = item.nama || item.nama_produk || item.name || 'Produk';
        const price = parseFloat(item.price) || 0;
        const pct = parseFloat(item.discount) || 0;
        const vch = parseFloat(item.discount_voucher) || 0;
        const discPctNominal = price * (pct / 100);
        const itemDiscUnit = discPctNominal + vch;
        const hargaAkhir = Math.max(0, price - itemDiscUnit);
        const subtotal = hargaAkhir * qty;

        rawSubtotal += (price * qty);
        totalItemDisc += (itemDiscUnit * qty);

        let discInfo = '';
        if (pct > 0 && vch > 0) {
            discInfo = `Diskon: -${pct}% (-${formatRupiah(discPctNominal)}) + Voucher item: -${formatRupiah(vch)}`;
        } else if (pct > 0) {
            discInfo = `Diskon: -${pct}% (-${formatRupiah(discPctNominal)})`;
        } else if (vch > 0) {
            discInfo = `Voucher item: -${formatRupiah(vch)}`;
        }

        return {
            nama: nama,
            qty: qty,
            price: price,
            discount_info: discInfo,
            subtotal: subtotal
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
