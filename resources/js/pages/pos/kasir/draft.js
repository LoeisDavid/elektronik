/**
 * =============================================================================
 * Kasir Draft Management (LocalStorage Save, Restore, Delete, & Badge)
 * =============================================================================
 */

import { state, formatRupiah, escapeHtml } from './config.js';
import { resetKeranjang, renderCart, simpanKeSession } from './cart.js';

// Update badge count di header POS
export function updateDraftBadgeHeader(count) {
    let n = count;
    if (typeof n === 'undefined' || n === null) {
        try {
            const drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
            n = Array.isArray(drafts) ? drafts.length : 0;
        } catch (e) {
            n = 0;
        }
    }
    const badgeHeader = document.getElementById('DraftCountBadgeHeader');
    if (!badgeHeader) return;
    if (n > 0) {
        badgeHeader.innerText = n;
        badgeHeader.classList.remove('hidden');
        badgeHeader.classList.add('inline-flex');
    } else {
        badgeHeader.classList.add('hidden');
        badgeHeader.classList.remove('inline-flex');
    }
}

export function modalTambahArsip() {
    if (Object.keys(state.cartData).length === 0) {
        alert('Keranjang masih kosong, tidak ada yang dapat disimpan ke draft!');
        return;
    }
    const modal = document.getElementById('modalTambahArsip');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            jQuery(modal).find('select.searchable-select').each(function() {
                const $select = jQuery(this);
                $select.select2({
                    width: '100%',
                    dropdownParent: jQuery(modal),
                    matcher: function(params, data) {
                        if (jQuery.trim(params.term) === '') {
                            return data;
                        }
                        if (!data.element) {
                            return null;
                        }
                        var term = params.term.toLowerCase();
                        var text = (data.text || '').toLowerCase();
                        var phone = (jQuery(data.element).data('phone') || '').toString().toLowerCase();

                        if (text.indexOf(term) > -1 || phone.indexOf(term) > -1) {
                            return data;
                        }
                        return null;
                    },
                    language: {
                        noResults: function() {
                            if ($select.attr('id') === 'inputPelangganDraft') {
                                return jQuery(`
                                    <div class="py-2.5 px-2 text-center text-xs">
                                        <p class="text-gray-600 mb-1.5 font-medium">Customer / Desa tidak ditemukan</p>
                                        <button type="button" class="btn-tambah-cst w-full py-1.5 px-3 bg-[#101D36] hover:bg-[#1C325B] text-white rounded-lg font-bold text-xs cursor-pointer shadow-xs transition-colors">
                                            + Tambah Data Baru
                                        </button>
                                    </div>
                                `).on('click', '.btn-tambah-cst', function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    $select.select2('close');
                                    if (typeof window.openTambahModal === 'function') {
                                        window.openTambahModal('inputPelangganDraft');
                                    }
                                });
                            }
                            return 'Data tidak ditemukan';
                        }
                    },
                    escapeMarkup: function(m) {
                        return m;
                    }
                });
            });
        }
    }
}

export function tutupModalTambahArsip() {
    const modal = document.getElementById('modalTambahArsip');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}

export function simpanDraftTransaksi() {
    const pelanggan = document.getElementById('inputPelangganDraft')?.value || '';
    const sales = document.getElementById('inputSalesDraft')?.value || '';

    if (!pelanggan) {
        alert('Silakan pilih atau isi nama Pelanggan terlebih dahulu!');
        document.getElementById('inputPelangganDraft')?.focus();
        return;
    }
    if (!sales) {
        alert('Silakan pilih atau isi nama Sales terlebih dahulu!');
        document.getElementById('inputSalesDraft')?.focus();
        return;
    }

    let drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
    drafts.push({
        id: Date.now(),
        pelanggan: pelanggan,
        sales: sales,
        cart: JSON.parse(JSON.stringify(state.cartData)),
        voucher: state.voucherAmount,
        discount_percent: state.discountPercent,
        ppn: state.ppnPercent,
        time: new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        }) + ' WIB'
    });

    localStorage.setItem('pos_drafts', JSON.stringify(drafts));
    tutupModalTambahArsip();

    ['inputPelangganDraft', 'inputSalesDraft'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el) {
            el.value = '';
            if (typeof jQuery !== 'undefined' && jQuery(el).hasClass('select2-hidden-accessible')) {
                jQuery(el).val('').trigger('change');
            }
        }
    });

    resetKeranjang(true);
    updateDraftBadgeHeader(drafts.length);
    alert('Draft transaksi berhasil disimpan!');
}

// Ekstrak seluruh barcode unik (BC_OUT) yang dipilih di dalam sebuah draft
export function ambilBarcodeDariDraft(draft) {
    if (!draft || !draft.cart || typeof draft.cart !== 'object') return [];
    const barcodes = new Set();
    const items = Array.isArray(draft.cart) ? draft.cart : Object.entries(draft.cart);

    items.forEach(entry => {
        const item = Array.isArray(draft.cart) ? entry : entry[1];
        const key = Array.isArray(draft.cart) ? '' : entry[0];

        if (item && typeof item === 'object') {
            if (item.code) barcodes.add(String(item.code).trim());
            if (item.BC_OUT) barcodes.add(String(item.BC_OUT).trim());
            if (item.bc_out) barcodes.add(String(item.bc_out).trim());
        }
        if (key && typeof key === 'string' && key.trim() !== '') {
            barcodes.add(key.trim());
        }
    });

    return Array.from(barcodes).filter(Boolean);
}

/**
 * Menghapus otomatis draft transaksi yang memilih barang dengan barcode (BC_OUT) yang telah terjual / ada di STS.
 * @param {Array<string>} soldBarcodes - Daftar barcode BC_OUT yang sudah terjual
 * @param {Object} options - Opsi { render: boolean }
 * @returns {number} Jumlah draft yang otomatis terhapus
 */
export function hapusDraftDenganBarcodes(soldBarcodes, options = { render: true }) {
    if (!Array.isArray(soldBarcodes) || soldBarcodes.length === 0) return 0;

    const soldSet = new Set(soldBarcodes.map(b => String(b).trim().toLowerCase()));
    let drafts = [];
    try {
        drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
    } catch (e) {
        drafts = [];
    }

    if (!Array.isArray(drafts) || drafts.length === 0) {
        updateDraftBadgeHeader(0);
        return 0;
    }

    const initialLength = drafts.length;
    const filteredDrafts = drafts.filter(draft => {
        const barcodes = ambilBarcodeDariDraft(draft);
        const hasSoldItem = barcodes.some(bc => soldSet.has(bc.toLowerCase()));
        return !hasSoldItem;
    });

    const deletedCount = initialLength - filteredDrafts.length;
    if (deletedCount > 0) {
        localStorage.setItem('pos_drafts', JSON.stringify(filteredDrafts));
        updateDraftBadgeHeader(filteredDrafts.length);
        if (options.render) {
            renderDraftList();
        }
        console.info(`[POS Draft] ${deletedCount} draft otomatis terhapus karena unit barang (BC_OUT: ${soldBarcodes.join(', ')}) telah terjual di STS.`);
    }

    return deletedCount;
}

/**
 * Memeriksa seluruh barcode yang ada di semua draft ke tabel STS di database.
 * Jika ditemukan barang yang sudah ada di STS, draft yang memilih barang tersebut otomatis terhapus.
 */
export async function sinkronkanDraftDenganSTS() {
    let drafts = [];
    try {
        drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
    } catch (e) {
        drafts = [];
    }

    if (!Array.isArray(drafts) || drafts.length === 0) {
        updateDraftBadgeHeader(0);
        return [];
    }

    const allBarcodes = new Set();
    drafts.forEach(d => {
        ambilBarcodeDariDraft(d).forEach(bc => allBarcodes.add(bc));
    });

    if (allBarcodes.size === 0) return [];

    try {
        const checkUrl = state.routes?.draftsCheckSts || '/POS/drafts/check-sts';
        const res = await fetch(checkUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': state.csrfToken
            },
            body: JSON.stringify({
                barcodes: Array.from(allBarcodes)
            })
        });

        const data = await res.json();
        if (data.success && Array.isArray(data.sold_barcodes) && data.sold_barcodes.length > 0) {
            hapusDraftDenganBarcodes(data.sold_barcodes, { render: true });
            return data.sold_barcodes;
        }
    } catch (err) {
        console.error('Error saat sinkronisasi draft dengan STS:', err);
    }
    return [];
}

export function modalDraftTransaksi() {
    renderDraftList();
    const modal = document.getElementById('modalDraftTransaksi');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    // Cek dan sinkronkan dengan database STS secara otomatis saat modal dibuka
    sinkronkanDraftDenganSTS();
}

export function tutupModalDraftTransaksi() {
    const modal = document.getElementById('modalDraftTransaksi');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}

export function tutupModalDraftKlikLuar(event) {
    if (event.target.id === 'modalDraftTransaksi') {
        tutupModalDraftTransaksi();
    }
}

export function renderDraftList() {
    const container = document.getElementById('listDraftContainer');
    const countText = document.getElementById('draftCountText');
    if (!container) return;

    const drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');

    if (countText) {
        countText.innerText = `${drafts.length} transaksi tersimpan`;
    }
    updateDraftBadgeHeader(drafts.length);

    if (drafts.length === 0) {
        container.innerHTML = `
            <div class="p-8 text-center text-gray-400 text-sm font-medium">
                Belum ada draft transaksi yang disimpan.
            </div>
        `;
        return;
    }

    let html = '';
    drafts.forEach((d, idx) => {
        let totalItems = 0;
        let grandTotal = 0;
        if (d.cart && typeof d.cart === 'object') {
            Object.values(d.cart).forEach(item => {
                const qty = parseInt(item.qty) || 1;
                totalItems += qty;
                const price = parseFloat(item.price) || 0;
                grandTotal += (price * qty);
            });
        }

        const itemInfo = `${totalItems} item`;
        const totalFormatted = formatRupiah ? formatRupiah(grandTotal) : `Rp ${grandTotal.toLocaleString('id-ID')}`;

        html += `
            <div class="flex items-center justify-between p-3.5 bg-white border border-gray-800 rounded-2xl shadow-xs hover:border-black transition-all">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 border border-gray-800 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-50">
                        <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>

                    <div>
                        <h4 class="font-bold text-black text-sm leading-tight">${escapeHtml(d.pelanggan || 'Pelanggan Umum')}</h4>
                        <div class="flex items-center gap-1.5 text-xs text-gray-500 font-medium mt-0.5">
                            <span>${itemInfo}</span>
                            <span>•</span>
                            <span>${totalFormatted}</span>
                            <span>•</span>
                            <span>${escapeHtml(d.time || '')}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <button
                        type="button"
                        onclick="muatDraft(${idx})"
                        class="px-5 py-2 bg-[#0F1D3A] hover:bg-[#0A1428] active:bg-black text-white rounded-xl text-xs font-bold transition-colors cursor-pointer shadow-xs">
                        Lanjutkan
                    </button>
                    <button
                        type="button"
                        onclick="hapusDraft(${idx})"
                        class="w-9 h-9 bg-[#DC2626] hover:bg-[#B91C1C] text-white rounded-xl flex items-center justify-center transition-colors cursor-pointer shadow-xs"
                        title="Hapus Draft">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

export async function muatDraft(index) {
    let drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
    const draft = drafts[index];
    if (!draft) return;

    // Cek apakah ada unit barang dalam draft yang sudah tercatat di STS
    const barcodes = ambilBarcodeDariDraft(draft);
    if (barcodes.length > 0) {
        try {
            const checkUrl = state.routes?.draftsCheckSts || '/POS/drafts/check-sts';
            const res = await fetch(checkUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': state.csrfToken
                },
                body: JSON.stringify({ barcodes: barcodes })
            });
            const data = await res.json();
            if (data.success && Array.isArray(data.sold_barcodes) && data.sold_barcodes.length > 0) {
                hapusDraftDenganBarcodes(data.sold_barcodes, { render: true });
                alert(`Draft tidak dapat dilanjutkan karena barang dengan barcode "${data.sold_barcodes.join(', ')}" sudah terjual (ada di STS). Draft otomatis terhapus.`);
                return;
            }
        } catch (e) {
            console.error('Error saat validasi STS di muatDraft:', e);
        }
    }

    // Ambil ulang draft yang masih valid
    drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
    const targetDraft = drafts[index];
    if (!targetDraft) {
        renderDraftList();
        return;
    }

    state.cartData = targetDraft.cart || {};
    state.voucherAmount = targetDraft.voucher || 0;
    state.discountPercent = targetDraft.discount_percent || 0;
    state.ppnPercent = targetDraft.ppn !== undefined ? targetDraft.ppn : null;
    const inputPpnEl = document.getElementById('ppnInput');
    if (inputPpnEl) inputPpnEl.value = (state.ppnPercent !== null && state.ppnPercent !== '') ? state.ppnPercent : '';

    state.isLoadedFromDraft = true;
    state.currentDraftPelanggan = targetDraft.pelanggan || '';
    state.currentDraftSales = targetDraft.sales || '';

    drafts.splice(index, 1);
    localStorage.setItem('pos_drafts', JSON.stringify(drafts));
    updateDraftBadgeHeader(drafts.length);

    renderCart();
    simpanKeSession();
    tutupModalDraftTransaksi();
}

export function hapusDraft(index) {
    let drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
    if (index !== undefined) {
        drafts.splice(index, 1);
    } else {
        drafts = [];
    }
    localStorage.setItem('pos_drafts', JSON.stringify(drafts));
    renderDraftList();
    updateDraftBadgeHeader(drafts.length);
    resetKeranjang(true);
    tutupModalDraftTransaksi();
}

export function hapusDraftSetelahBayar(index) {
    let drafts = JSON.parse(localStorage.getItem('pos_drafts') || '[]');
    if (drafts[index] !== undefined) {
        drafts.splice(index, 1);
        localStorage.setItem('pos_drafts', JSON.stringify(drafts));
    }
    state.activeDraftIndex = null;
    state.isLoadedFromDraft = false;
}
