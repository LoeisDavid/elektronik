/**
 * =============================================================================
 * JavaScript Halaman POS Transaksi
 * =============================================================================
 */

let strukAktif = null;

// Escape HTML Helper
function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, function(c) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[c];
    });
}

// Format Rupiah Helper
function formatRupiahLocal(value) {
    return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
}

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

    let items = [];
    try {
        items = JSON.parse(btn.dataset.detail || '[]');
    } catch (e) {
        items = [];
    }

    const refNo = btn.dataset.ref || '-';
    const tanggal = btn.dataset.tanggal || '-';
    const waktu = btn.dataset.waktu || '00.00';
    const kasir = btn.dataset.kasir || 'Kasir';
    const metode = btn.dataset.metode || 'Tunai';
    const subtotal = parseFloat(btn.dataset.subtotal) || 0;
    const diskonItem = parseFloat(btn.dataset.diskonItem) || 0;
    const diskonGlobalPct = parseFloat(btn.dataset.diskonGlobalPct) || 0;
    const diskonGlobal = parseFloat(btn.dataset.diskonGlobal) || 0;
    const voucher = parseFloat(btn.dataset.voucher) || 0;
    const ppn = parseFloat(btn.dataset.ppn) || 0;
    const ppnNominal = parseFloat(btn.dataset.ppnNominal) || 0;
    const total = parseFloat(btn.dataset.total) || 0;
    const dibayar = parseFloat(btn.dataset.dibayar) || 0;
    const kembalian = parseFloat(btn.dataset.kembalian) || 0;
    const customerNama = btn.dataset.customerNama || '';
    const customerDesa = btn.dataset.customerDesa || '';
    const customerAlamat = btn.dataset.customerAlamat || '';

    strukAktif = {
        ref: refNo,
        tanggal: tanggal,
        waktu: waktu,
        kasir: kasir,
        customer_nama: customerNama,
        customer_desa: customerDesa,
        customer_alamat: customerAlamat,
        metode: metode,
        subtotal: subtotal,
        diskonItem: diskonItem,
        diskonGlobalPct: diskonGlobalPct,
        diskonGlobal: diskonGlobal,
        voucher: voucher,
        ppn: ppn,
        ppnNominal: ppnNominal,
        total: total,
        dibayar: dibayar,
        kembalian: kembalian,
        items: items
    };

    const setEl = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    };

    setEl('strukNoNota', refNo);
    setEl('strukTanggal', tanggal);
    setEl('strukWaktu', waktu);
    setEl('strukKasir', kasir);
    setEl('strukMetode', metode);

    // Render Info Pelanggan
    const wrapPelanggan = document.getElementById('wrapperStrukPelanggan');
    const rowCstNama = document.getElementById('rowStrukCustomerNama');
    const elCstNama = document.getElementById('strukCustomerNama');
    const rowCstDesa = document.getElementById('rowStrukCustomerDesa');
    const elCstDesa = document.getElementById('strukCustomerDesa');
    const rowCstAlamat = document.getElementById('rowStrukCustomerAlamat');
    const elCstAlamat = document.getElementById('strukCustomerAlamat');

    if (wrapPelanggan) {
        const rawNama = customerNama.trim();
        const rawDesa = customerDesa.trim();
        const rawAlamat = customerAlamat.trim();

        const norm = (s) => (s || '').toLowerCase().replace(/^(ds\.|desa\s+|dusun\s+)/i, '').replace(/[^a-z0-9]/g, '');
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

    // Render Produk
    const list = document.getElementById('strukListProduk');
    if (list) {
        list.innerHTML = '';
        if (!Array.isArray(items) || items.length === 0) {
            list.innerHTML = '<p class="text-gray-400 text-center py-2">Tidak ada detail produk</p>';
        } else {
            items.forEach(function(d) {
                const qty = Number(d.qty ?? d.QTY ?? 1);
                const nama = d.nama ?? d.NAMA ?? '-';
                const harga = d.price ?? d.harga ?? d.HARGA ?? 0;
                const discInfo = d.discount_info || '';
                const subtotalItem = d.subtotal ?? (harga * qty);

                list.innerHTML +=
                    '<div class="flex justify-between items-start text-xs pt-1">' +
                        '<div class="min-w-0 pr-2">' +
                            '<p class="font-bold text-gray-900 truncate">' + escapeHtml(nama) + '</p>' +
                            '<p class="text-[11px] text-gray-500 font-medium">' + qty + 'x ' + formatRupiahLocal(harga) + '</p>' +
                            (discInfo ? '<p class="text-[10.5px] text-red-600 font-medium mt-0.5">' + escapeHtml(discInfo) + '</p>' : '') +
                        '</div>' +
                        '<span class="font-bold text-gray-900 shrink-0 text-right">' + formatRupiahLocal(subtotalItem) + '</span>' +
                    '</div>';
            });
        }
    }

    setEl('strukSubtotal', formatRupiahLocal(subtotal));

    // Diskon Item
    const elWrapDiscItem = document.getElementById('wrapperStrukDiskonItem');
    const elDiscItem = document.getElementById('strukDiskonItem');
    if (diskonItem > 0) {
        if (elDiscItem) elDiscItem.textContent = '- ' + formatRupiahLocal(diskonItem);
        if (elWrapDiscItem) {
            elWrapDiscItem.classList.remove('hidden');
            elWrapDiscItem.classList.add('flex');
        }
    } else if (elWrapDiscItem) {
        elWrapDiscItem.classList.remove('flex');
        elWrapDiscItem.classList.add('hidden');
    }

    // Diskon Global
    const elWrapDiscGlobal = document.getElementById('wrapperStrukDiskonGlobal');
    const elDiscGlobal = document.getElementById('strukDiskonGlobal');
    const elLabelDiscGlobal = document.getElementById('labelStrukDiskonGlobal');
    if (diskonGlobal > 0) {
        if (elLabelDiscGlobal) elLabelDiscGlobal.textContent = diskonGlobalPct > 0 ? `Diskon Global (${diskonGlobalPct}%)` : 'Diskon Global';
        if (elDiscGlobal) elDiscGlobal.textContent = '- ' + formatRupiahLocal(diskonGlobal);
        if (elWrapDiscGlobal) {
            elWrapDiscGlobal.classList.remove('hidden');
            elWrapDiscGlobal.classList.add('flex');
        }
    } else if (elWrapDiscGlobal) {
        elWrapDiscGlobal.classList.remove('flex');
        elWrapDiscGlobal.classList.add('hidden');
    }

    // Voucher
    const elWrapVoucher = document.getElementById('wrapperStrukVoucher');
    const elVoucher = document.getElementById('strukVoucher');
    if (voucher > 0) {
        if (elVoucher) elVoucher.textContent = '- ' + formatRupiahLocal(voucher);
        if (elWrapVoucher) {
            elWrapVoucher.classList.remove('hidden');
            elWrapVoucher.classList.add('flex');
        }
    } else if (elWrapVoucher) {
        elWrapVoucher.classList.remove('flex');
        elWrapVoucher.classList.add('hidden');
    }

    // PPN
    const elWrapPpn = document.getElementById('wrapperStrukPPN');
    const elPpn = document.getElementById('strukPPN');
    const elLabelPpn = document.getElementById('labelStrukPPN');
    if (ppnNominal > 0) {
        if (elLabelPpn) elLabelPpn.textContent = ppn > 0 ? `PPN (${ppn}%)` : 'PPN';
        if (elPpn) elPpn.textContent = '+ ' + formatRupiahLocal(ppnNominal);
        if (elWrapPpn) {
            elWrapPpn.classList.remove('hidden');
            elWrapPpn.classList.add('flex');
        }
    } else if (elWrapPpn) {
        elWrapPpn.classList.remove('flex');
        elWrapPpn.classList.add('hidden');
    }

    setEl('strukTotal', formatRupiahLocal(total));

    // Detail Tunai
    const elWrapTunai = document.getElementById('wrapperDetailTunai');
    const isTunai = (metode || '').toLowerCase().includes('tunai') || (metode || '').toLowerCase().includes('cash');
    if (isTunai) {
        setEl('strukDibayar', formatRupiahLocal(dibayar));
        setEl('strukKembali', formatRupiahLocal(kembalian));
        if (elWrapTunai) elWrapTunai.classList.remove('hidden');
    } else if (elWrapTunai) {
        elWrapTunai.classList.add('hidden');
    }

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
    if (!strukAktif) {
        alert('Data struk tidak ditemukan.');
        return;
    }

    const items = (strukAktif.items || []).map(function(d) {
        const qty = Number(d.qty ?? d.QTY ?? 1);
        const nama = d.nama ?? d.NAMA ?? '-';
        const harga = d.price ?? d.harga ?? d.HARGA ?? 0;
        const discInfo = d.discount_info || '';
        const subtotal = d.subtotal ?? (harga * qty);

        return {
            nama: nama,
            qty: qty,
            price: harga,
            discount_info: discInfo,
            subtotal: subtotal
        };
    });

    const isTunai = (strukAktif.metode || '').toLowerCase().includes('tunai') || (strukAktif.metode || '').toLowerCase().includes('cash');

    const setField = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = (val !== null && val !== undefined) ? val : '';
    };

    setField('printRefNo', strukAktif.ref);
    setField('printTanggal', strukAktif.tanggal);
    setField('printWaktu', strukAktif.waktu);
    setField('printKasir', strukAktif.kasir);
    setField('printMetode', strukAktif.metode);
    setField('printSubtotal', strukAktif.subtotal);
    setField('printDiscountItem', strukAktif.diskonItem);
    setField('printDiscountGlobal', strukAktif.diskonGlobal);
    setField('printVoucher', strukAktif.voucher);
    setField('printPpn', strukAktif.ppn || '');
    setField('printPpnNominal', strukAktif.ppnNominal || 0);
    setField('printTotal', strukAktif.total);
    setField('printDibayar', isTunai ? strukAktif.dibayar : '');
    setField('printKembalian', isTunai ? strukAktif.kembalian : '');
    setField('printCustomerNama', strukAktif.customer_nama || '');
    setField('printCustomerDesa', strukAktif.customer_desa || '');
    setField('printCustomerAlamat', strukAktif.customer_alamat || '');
    setField('printItems', JSON.stringify(items));

    const formPrint = document.getElementById('formPrintStruk');
    if (formPrint) formPrint.submit();
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
