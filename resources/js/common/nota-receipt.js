/**
 * Shared 1/3 Folio (215mm x 110mm) Nota Receipt Renderer & Print Form Helper.
 * Used by both POS Riwayat Transaksi and POS Kasir modals.
 */

export const ROWS_PER_PAGE = 4;

export function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (ch) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
    }[ch]));
}

export function formatRupiah(amount) {
    const num = Math.round(Number(amount) || 0);
    const sign = num < 0 ? '-' : '';
    const formatted = Math.abs(num)
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return `${sign}Rp ${formatted}`;
}

/**
 * Determine whether the payment method is cash (Tunai / Cash),
 * ensuring strings like "Non-Tunai / QRIS" return false.
 */
export function isTunaiMethod(metode) {
    const normalized = String(metode || '').trim().toLowerCase();
    if (!normalized || normalized.includes('non')) {
        return false;
    }
    return normalized === 'tunai' || normalized === 'cash';
}

/**
 * Deduplicate customer name, village (Desa), and address when name or address
 * simply repeats the village name.
 */
export function normalizeCustomerRows({ customerNama = '', customerDesa = '', customerAlamat = '' }) {
    const rawNama = String(customerNama || '').trim();
    const rawDesa = String(customerDesa || '').trim();
    const rawAlamat = String(customerAlamat || '').trim();

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
        showDesa = Boolean(rawDesa) && rawDesa !== '-';
    } else {
        showNama = !isNamaLikeDesa && Boolean(rawNama) && rawNama !== '-';
        showDesa = Boolean(rawDesa) && rawDesa !== '-';
        showAlamat = !isAlamatLikeDesa && Boolean(rawAlamat) && rawAlamat !== '-';
    }

    const rows = [];
    if (showNama) rows.push({ label: 'Pelanggan:', value: rawNama });
    if (showDesa) rows.push({ label: 'Desa:', value: rawDesa });
    if (showAlamat) rows.push({ label: 'Alamat:', value: rawAlamat });

    return rows;
}

function buildPreprintedGuideHtml(shop) {
    const shopName = (shop.name || 'TOKO BOGOR').toUpperCase();
    const shopAddress = (shop.address || '').toUpperCase();
    const shopPhone = shop.phone || '';
    const shopNpwp = shop.npwp || '';

    return `
        <div class="preprinted-guide" aria-hidden="true">
            <div class="pp-header">
                <div class="pp-shop">${escapeHtml(shopName)}</div>
                ${shopAddress ? `<div class="pp-meta">${escapeHtml(shopAddress)}</div>` : ''}
                ${shopPhone ? `<div class="pp-meta">${escapeHtml(shopPhone)}</div>` : ''}
                ${shopNpwp ? `<div class="pp-meta">NPWP: ${escapeHtml(shopNpwp)}</div>` : ''}
            </div>
            <div class="pp-watermark">${escapeHtml(shopName)}</div>
            <div class="pp-tagline">TERPERCAYA SEJAK TAHUN 1975</div>
            <div class="pp-ppn">HARGA SUDAH TERMASUK PPN</div>
            <div class="pp-right-strip">
                <div class="pp-right-text">
                    PERHATIAN!<br />
                    PIHAK TOKO TIDAK BERTANGGUNG JAWAB JIKA ADA<br />
                    KEKURANGAN PERLENGKAPAN (REMOTE, KARTU GARANSI, DLL)<br />
                    SETELAH BARANG DIBAWA / KELUAR DARI TOKO<br />
                    <strong>PERIKSALAH DENGAN TELITI</strong>
                </div>
            </div>
        </div>
    `;
}

function buildBottomSectionHtml(data, totals) {
    const isTunai = isTunaiMethod(data.metode);
    const diskonItem = Number(data.discountItem) || 0;
    const diskonGlobal = Number(data.discountGlobal) || 0;
    const diskonGlobalPct = Number(data.discountPercent) || 0;
    const voucher = Number(data.voucher) || 0;
    const ppn = Number(data.ppn) || 0;
    const ppnNominal = Number(data.ppnNominal) || 0;

    const paymentRowsHtml = `
        <span class="label">Pembayaran</span>
        <span class="value num">${escapeHtml(data.metode || 'Tunai')}</span>
        ${isTunai ? `
            <span class="label">Dibayar</span>
            <span class="value num">${escapeHtml(formatRupiah(totals.paid))}</span>
            <span class="label">Kembali</span>
            <span class="value num">${escapeHtml(formatRupiah(totals.change))}</span>
        ` : ''}
    `;

    const extraTotalsHtml = [
        diskonItem > 0
            ? `<span class="extra-label">Diskon Per Item</span><span class="extra-val discount num">- ${escapeHtml(formatRupiah(diskonItem))}</span>`
            : '',
        diskonGlobal > 0
            ? `<span class="extra-label">${escapeHtml(diskonGlobalPct > 0 ? `Diskon Global (${diskonGlobalPct}%)` : 'Diskon Global')}</span><span class="extra-val discount num">- ${escapeHtml(formatRupiah(diskonGlobal))}</span>`
            : '',
        voucher > 0
            ? `<span class="extra-label">Voucher</span><span class="extra-val discount num">- ${escapeHtml(formatRupiah(voucher))}</span>`
            : '',
        ppnNominal > 0
            ? `<span class="extra-label">${escapeHtml(ppn > 0 ? `PPN (${ppn}%)` : 'PPN')}</span><span class="extra-val num">+ ${escapeHtml(formatRupiah(ppnNominal))}</span>`
            : '',
    ].join('');

    return `
        <section class="bottom-section">
            <div class="payment-totals">
                <div class="payment-left">
                    ${paymentRowsHtml}
                </div>
                <div class="totals-right">
                    <span class="subtotal-label">Subtotal</span>
                    <span class="subtotal-val num">${escapeHtml(formatRupiah(totals.subtotal))}</span>
                    ${extraTotalsHtml}
                    <span class="grand-label">TOTAL</span>
                    <span class="grand-val num">${escapeHtml(formatRupiah(totals.total))}</span>
                </div>
            </div>
        </section>
    `;
}

function buildSheetHtml(data, shop, customerRows, pageItems, pageIndex, totalPages, totals) {
    const isLastPage = pageIndex === totalPages - 1;
    const startIndex = pageIndex * ROWS_PER_PAGE;
    const pageLabel = totalPages > 1 ? ` &bull; Hal. ${pageIndex + 1}/${totalPages}` : '';
    const hasExtraTotals = isLastPage && (
        (Number(data.discountItem) || 0) > 0 ||
        (Number(data.discountGlobal) || 0) > 0 ||
        (Number(data.voucher) || 0) > 0 ||
        (Number(data.ppnNominal) || 0) > 0
    );

    const customerRowsHtml = customerRows.map((row) => `
        <div class="customer-row">
            <span class="label">${escapeHtml(row.label)}</span>
            <span class="value">${escapeHtml(row.value)}</span>
        </div>
    `).join('');

    const rowsHtml = Array.from({ length: ROWS_PER_PAGE }, (_, rowOffset) => {
        const item = pageItems[rowOffset];
        if (!item) {
            return `
                <tr>
                    <td class="center">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="center">&nbsp;</td>
                    <td class="num">&nbsp;</td>
                    <td class="num">&nbsp;</td>
                </tr>
            `;
        }

        const qty = Number(item.qty) || 1;
        const unitPrice = Number(item.price) || 0;
        const lineTotal = item.subtotal != null ? Number(item.subtotal) : qty * unitPrice;
        const discBadge = item.discount_info
            ? `<span class="item-disc-inline">(${escapeHtml(item.discount_info)})</span>`
            : '';

        return `
            <tr>
                <td class="center">${startIndex + rowOffset + 1}</td>
                <td title="${escapeHtml(item.nama)}">${escapeHtml(item.nama)}${discBadge}</td>
                <td class="center">${escapeHtml(qty)}</td>
                <td class="num">${escapeHtml(formatRupiah(unitPrice))}</td>
                <td class="num">${escapeHtml(formatRupiah(lineTotal))}</td>
            </tr>
        `;
    }).join('');

    return `
        <article class="sheet">
            ${buildPreprintedGuideHtml(shop)}
            <div class="sheet-inner${hasExtraTotals ? ' has-extra-totals' : ''}">
                <div class="trx-row">
                    <span class="label">No. Transaksi: </span>
                    <span class="doc-trx mono">${escapeHtml(data.refNo || '-')}</span>
                </div>

                <section class="customer-block">
                    <div class="customer-left">
                        ${customerRowsHtml}
                    </div>
                    <div class="customer-right">
                        <div><span class="value">${escapeHtml(data.tanggal || '-')} &bull; ${escapeHtml(data.waktu || '00.00')}${pageLabel}</span></div>
                        <div><span class="label">Dilayani oleh: </span><span class="value">${escapeHtml(data.kasir || 'Kasir')}</span></div>
                    </div>
                </section>

                <section class="items-section">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th class="col-no center">No</th>
                                <th class="col-name">Nama Barang</th>
                                <th class="col-qty center">Qty</th>
                                <th class="col-price num">Harga</th>
                                <th class="col-total num">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rowsHtml}
                        </tbody>
                    </table>
                </section>

                ${isLastPage ? buildBottomSectionHtml(data, totals) : ''}
            </div>
        </article>
    `;
}

/**
 * Render paginated 1/3 folio receipt sheets into the given container element.
 */
export function renderNotaSheets(rootEl, data) {
    if (!rootEl || !data) return;

    const shop = {
        name: rootEl.dataset.shopName || 'Toko Bogor',
        address: rootEl.dataset.shopAddress || '',
        phone: rootEl.dataset.shopPhone || '',
        npwp: rootEl.dataset.shopNpwp || '',
    };

    const items = Array.isArray(data.items) ? data.items : [];
    const pages = [];
    for (let i = 0; i < items.length; i += ROWS_PER_PAGE) {
        pages.push(items.slice(i, i + ROWS_PER_PAGE));
    }
    if (pages.length === 0) {
        pages.push([]);
    }

    const subtotal = Number(data.subtotal) || 0;
    const total = Number(data.total) || 0;
    const paid = Number(data.dibayar) || 0;
    const change = data.kembalian != null ? Number(data.kembalian) : Math.max(0, paid - total);

    const totals = { subtotal, total, paid, change };
    const customerRows = normalizeCustomerRows({
        customerNama: data.customerNama,
        customerDesa: data.customerDesa,
        customerAlamat: data.customerAlamat,
    });

    rootEl.innerHTML = pages
        .map((pageItems, idx) => buildSheetHtml(data, shop, customerRows, pageItems, idx, pages.length, totals))
        .join('');
}

/**
 * Populate the hidden print form and submit to POS.struk.print.
 */
export function submitNotaPrintForm(data, formId = 'formPrintStruk') {
    if (!data) {
        alert('Data struk tidak ditemukan.');
        return;
    }

    const isTunai = isTunaiMethod(data.metode);
    const setField = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = (val !== null && val !== undefined) ? val : '';
    };

    setField('printRefNo', data.refNo || '');
    setField('printTanggal', data.tanggal || '');
    setField('printWaktu', data.waktu || '');
    setField('printKasir', data.kasir || '');
    setField('printMetode', data.metode || 'Tunai');
    setField('printSubtotal', data.subtotal ?? 0);
    setField('printDiscountItem', data.discountItem ?? 0);
    setField('printDiscountPercent', data.discountPercent ?? 0);
    setField('printDiscountGlobal', data.discountGlobal ?? 0);
    setField('printVoucher', data.voucher ?? 0);
    setField('printPpn', data.ppn || '');
    setField('printPpnNominal', data.ppnNominal || 0);
    setField('printTotal', data.total ?? 0);
    setField('printDibayar', isTunai ? (data.dibayar ?? 0) : '');
    setField('printKembalian', isTunai ? (data.kembalian ?? 0) : '');
    setField('printCustomerNama', data.customerNama || '');
    setField('printCustomerDesa', data.customerDesa || '');
    setField('printCustomerAlamat', data.customerAlamat || '');
    setField('printItems', JSON.stringify(data.items || []));

    const formPrint = document.getElementById(formId);
    if (formPrint) formPrint.submit();
}
