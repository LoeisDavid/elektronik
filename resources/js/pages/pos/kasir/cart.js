/**
 * =============================================================================
 * Kasir Cart Management (Item rendering, discounts, calculations, & session sync)
 * =============================================================================
 */

import { state, formatRupiah, escapeHtml } from './config.js';
import { updateTombolMetode } from './payment.js';

let syncCartDebounceTimer = null;

// Total qty seluruh item di keranjang
export function hitungTotalQty() {
    let total = 0;
    Object.keys(state.cartData).forEach(k => {
        total += (parseInt(state.cartData[k].qty) || 1);
    });
    return total;
}

// Render tampilan keranjang
export function renderCart() {
    const container = document.getElementById('cartItemsList');
    if (!container) return;

    const keys = Object.keys(state.cartData);

    // Jumlah item di judul "Keranjang"
    const elJumlah = document.getElementById('labelJumlahItem');
    if (elJumlah) elJumlah.innerText = hitungTotalQty() + ' item';

    // Jika keranjang kosong
    if (keys.length === 0) {
        container.innerHTML = `
            <div class="p-10 text-center text-gray-900 bg-white border border-dashed border-gray-400 rounded-2xl">
                <p class="text-sm font-medium">Keranjang masih kosong</p>
                <p class="text-xs mt-1 text-gray-500">Scan barcode unit produk untuk memulai</p>
            </div>
        `;
        const info = document.getElementById('infoItemTerakhir');
        if (info) {
            info.classList.add('hidden');
            info.classList.remove('flex');
        }
        hitungRingkasan();
        return;
    }

    let html = '';

    // Looping setiap item di dalam cartData
    keys.forEach(key => {
        const item = state.cartData[key];
        const itemDiscountPct = parseFloat(item.discount) || 0;
        const itemDiscountVoucher = parseFloat(item.discount_voucher) || 0;
        const itemPrice = parseFloat(item.price) || 0;
        const qty = parseInt(item.qty) || 1;

        const discFromPct = itemPrice * (itemDiscountPct / 100);
        const priceAfterDiscount = Math.max(0, itemPrice - discFromPct - itemDiscountVoucher);
        const itemTotal = priceAfterDiscount * qty;

        html += `
            <div class="grid grid-cols-[minmax(0,3.2fr)_1.3fr_1fr_1fr_1.4fr_56px] gap-4 items-center bg-white border border-black rounded-2xl px-5 py-4">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-14 h-14 flex-shrink-0 rounded-xl border border-black flex items-center justify-center text-black">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125v4.125m3-4.125v4.125M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-black truncate" title="${escapeHtml(item.description || '')}">${escapeHtml(item.name)}</p>
                        <p class="text-[11px] text-gray-500 font-mono truncate">${escapeHtml(item.code || key)}${qty > 1 ? ' · x' + qty : ''}</p>
                    </div>
                </div>

                <span class="text-sm text-gray-600">${formatRupiah(itemPrice)}</span>

                <div class="w-24 flex flex-col items-center">
                    <div class="relative w-full">
                        <input
                            type="number"
                            min="0"
                            max="100"
                            value="${itemDiscountPct > 0 ? itemDiscountPct : ''}"
                            placeholder="0"
                            title="Diskon (%)"
                            onfocus="tahanHasilDiskonItem(this)"
                            onkeydown="if(event.key==='Enter'){this.blur();}"
                            onblur="commitDiskonItemLangsung('${key}', this)"
                            class="w-full h-8 bg-white border border-black rounded-lg px-2.5 pr-6 text-xs font-semibold text-black placeholder-gray-400 outline-none text-right focus:ring-1 focus:ring-black [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-500 pointer-events-none">%</span>
                    </div>
                    ${discFromPct > 0 ? `<span class="mt-1 text-[13px] font-semibold text-gray-500 whitespace-nowrap">Rp ${Number(discFromPct * qty).toLocaleString('id-ID')}</span>` : ``}
                </div>
                
                <div class="relative w-28">
                    <input
                        type="text"
                        inputmode="numeric"
                        value="${itemDiscountVoucher > 0 ? formatRupiah(itemDiscountVoucher) : ''}"
                        placeholder="Rp 0"
                        title="Voucher (Rp)"
                        oninput="formatInputRupiah(this); ubahVoucherItemLangsung('${key}', this.value)"
                        class="input-rupiah w-full h-8 bg-white border border-black rounded-lg px-2 text-xs font-semibold text-black placeholder-gray-400 outline-none text-right focus:ring-1 focus:ring-black" />
                </div>

                <span id="subtotalItem-${key}" class="text-right text-lg font-bold text-black font-number">${formatRupiah(itemTotal)}</span>

                <button
                    type="button"
                    onclick="hapusItem('${key}')"
                    title="Hapus Item"
                    class="mx-auto w-8 h-8 bg-red-600 hover:bg-red-700 text-white rounded-md flex items-center justify-center cursor-pointer transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </button>
            </div>
        `;
    });

    container.innerHTML = html;
    hitungRingkasan();
}

// Perhitungan Total, Diskon Global, Voucher, PPN
export function hitungRingkasan() {
    let subtotal = 0;

    // Hitung total seluruh item
    Object.keys(state.cartData).forEach(key => {
        const item = state.cartData[key];
        const itemDiscountPct = parseFloat(item.discount) || 0;
        const itemDiscountVoucher = parseFloat(item.discount_voucher) || 0;
        const itemPrice = parseFloat(item.price) || 0;
        const qty = parseInt(item.qty) || 1;

        const discFromPct = itemPrice * (itemDiscountPct / 100);
        const priceAfterDiscount = Math.max(0, itemPrice - discFromPct - itemDiscountVoucher);
        subtotal += (priceAfterDiscount * qty);
    });

    // Hitung diskon transaksi global
    const diskonTransaksiNominal = subtotal * (state.discountPercent / 100);

    // Total barang setelah mengurangi diskon yang ada
    const totalSetelahDiskon = Math.max(0, subtotal - state.voucherAmount - diskonTransaksiNominal);

    // Hitung PPN setelah total semua barang dan setelah diskon
    const inputPpnEl = document.getElementById('ppnInput');
    let currentPpnVal = (inputPpnEl && inputPpnEl.value.trim() !== '') ? parseFloat(inputPpnEl.value) : null;
    if (currentPpnVal !== null && (isNaN(currentPpnVal) || currentPpnVal < 0)) currentPpnVal = 0;
    if (currentPpnVal !== null && currentPpnVal > 100) currentPpnVal = 100;
    state.ppnPercent = currentPpnVal;

    let ppnNominal = 0;
    if (state.ppnPercent !== null && state.ppnPercent > 0) {
        ppnNominal = totalSetelahDiskon * (state.ppnPercent / 100);
    }

    // Grand Total = Total setelah diskon + PPN
    let grandTotal = totalSetelahDiskon + ppnNominal;
    state.currentGrandTotal = grandTotal;

    // Perbarui teks di bar total
    const elSubtotal = document.getElementById('labelSubtotal');
    if (elSubtotal) elSubtotal.innerText = formatRupiah(subtotal);

    const elDiskonGlobal = document.getElementById('labelDiskonGlobal');
    if (elDiskonGlobal) elDiskonGlobal.innerText = formatRupiah(diskonTransaksiNominal);

    const elVoucherGlobal = document.getElementById('labelVoucherGlobal');
    if (elVoucherGlobal) elVoucherGlobal.innerText = formatRupiah(state.voucherAmount);

    const elPromoGlobal = document.getElementById('labelPromoGlobal');
    if (elPromoGlobal) {
        if (diskonTransaksiNominal > 0 || state.voucherAmount > 0) {
            elPromoGlobal.innerHTML = `Diskon: ${formatRupiah(diskonTransaksiNominal)}&nbsp;&nbsp;Voucher: ${formatRupiah(state.voucherAmount)}`;
            elPromoGlobal.classList.remove('hidden');
        } else {
            elPromoGlobal.classList.add('hidden');
        }
    }

    const elPPNGlobal = document.getElementById('labelPPNGlobal');
    if (elPPNGlobal) {
        if (state.ppnPercent !== null && state.ppnPercent > 0) {
            elPPNGlobal.innerHTML = `PPN (${state.ppnPercent}%): ${formatRupiah(ppnNominal)}`;
            elPPNGlobal.classList.remove('hidden');
        } else {
            elPPNGlobal.classList.add('hidden');
        }
    }

    const elGrandTotal = document.getElementById('labelGrandTotal');
    if (elGrandTotal) elGrandTotal.innerText = formatRupiah(grandTotal);

    const elMetode = document.getElementById('metodePembayaran');
    if (elMetode) elMetode.innerText = state.activePaymentMethod;

    updateTombolMetode();
}

let ppnWarningTimeout = null;

export function showPpnWarningKasir(msg = 'Tidak boleh pakai koma ( , )') {
    const w = document.getElementById('ppnWarningKasir');
    if (w) {
        w.innerText = msg;
        w.classList.remove('hidden');
        clearTimeout(ppnWarningTimeout);
        ppnWarningTimeout = setTimeout(() => {
            w.classList.add('hidden');
        }, 3000);
    }
}

export function handlePpnKeydown(e) {
    if (e.key === ',' || e.key === '.') {
        e.preventDefault();
        showPpnWarningKasir('Tidak boleh pakai koma ( , )');
    }
}

// Handler input PPN langsung dari UI
export function handlePpnInput(el) {
    let rawVal = el.value;
    if (rawVal.includes(',') || rawVal.includes('.')) {
        showPpnWarningKasir('Tidak boleh pakai koma ( , )');
        rawVal = rawVal.replace(/[,.]/g, '');
        el.value = rawVal;
    }

    let val = rawVal.trim();
    if (val !== '') {
        let num = parseInt(val);
        if (isNaN(num) || num < 0) num = 0;
        if (num > 100) num = 100;
        if (el.value !== '' && parseInt(el.value) !== num) {
            el.value = num;
        }
        state.ppnPercent = num;
    } else {
        state.ppnPercent = null;
    }
    hitungRingkasan();
    simpanKeSession();
}

// Hapus Item dari keranjang
export function hapusItem(key) {
    if (state.cartData[key]) {
        delete state.cartData[key];
        renderCart();
        simpanKeSession();
    }
}

// Reset isi keranjang
export function resetKeranjang(skipConfirm = false) {
    if (Object.keys(state.cartData).length === 0) return;
    if (skipConfirm || confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
        state.cartData = {};
        state.voucherAmount = 0;
        state.discountPercent = 0;
        state.ppnPercent = null;
        const inputPpnEl = document.getElementById('ppnInput');
        if (inputPpnEl) inputPpnEl.value = '';
        state.activePaymentMethod = 'Cash';
        state.selectedBankName = '';
        state.hasPickedPaymentMethod = false;
        state.isLoadedFromDraft = false;
        state.currentDraftPelanggan = '';
        state.currentDraftSales = '';
        const elMetode = document.getElementById('metodePembayaran');
        if (elMetode) elMetode.innerText = 'Cash';
        renderCart();
        updateTombolMetode();
        simpanKeSession();
    }
}

export function kosongkanKeranjang() {
    resetKeranjang(false);
}

// Sinkronisasi data keranjang ke session backend
export function simpanKeSession() {
    fetch(state.routes.cartSync, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': state.csrfToken
        },
        body: JSON.stringify({
            cart: state.cartData,
            voucher: state.voucherAmount,
            discount_percent: state.discountPercent,
            ppn: (state.ppnPercent !== null && state.ppnPercent !== '') ? state.ppnPercent : null
        })
    }).catch(err => console.error('Gagal sinkronisasi session cart:', err));
}

export function ubahDiskonItemLangsung(key, inputEl) {
    commitDiskonItemLangsung(key, inputEl);
}

export function tahanHasilDiskonItem(inputEl) {
    if (inputEl) inputEl.dataset.mengetik = '1';
}

export function commitDiskonItemLangsung(key, inputEl) {
    if (!state.cartData[key]) return;
    if (inputEl) delete inputEl.dataset.mengetik;

    let rawVal = (typeof inputEl === 'object' && inputEl !== null) ? inputEl.value : inputEl;
    let val = parseFloat(rawVal) || 0;

    if (val > 100) {
        val = 100;
        if (typeof inputEl === 'object' && inputEl !== null) inputEl.value = 100;
    } else if (val < 0) {
        val = 0;
        if (typeof inputEl === 'object' && inputEl !== null) inputEl.value = 0;
    }

    if ((parseFloat(state.cartData[key].discount) || 0) === val) {
        hitungRingkasan();
        return;
    }

    state.cartData[key].discount = val;
    renderCart();
    simpanKeSession();
}

export function ubahVoucherItemLangsung(key, rawVal) {
    if (!state.cartData[key]) return;
    let cleaned = typeof rawVal === 'string' ? rawVal.replace(/\D/g, '') : rawVal;
    let val = parseFloat(cleaned) || 0;
    val = Math.max(0, val);
    state.cartData[key].discount_voucher = val;

    updateSubtotalBarisItem(key);
    hitungRingkasan();
    triggerSyncCartDebounced();
}

export function updateSubtotalBarisItem(key) {
    const item = state.cartData[key];
    if (!item) return;

    const itemDiscountPct = parseFloat(item.discount) || 0;
    const itemDiscountVoucher = parseFloat(item.discount_voucher) || 0;
    const itemPrice = parseFloat(item.price) || 0;
    const qty = parseInt(item.qty) || 1;

    const discFromPct = itemPrice * (itemDiscountPct / 100);
    const priceAfterDiscount = Math.max(0, itemPrice - discFromPct - itemDiscountVoucher);
    const itemTotal = priceAfterDiscount * qty;

    const el = document.getElementById('subtotalItem-' + key);
    if (el) {
        el.innerText = formatRupiah(itemTotal);
    }
}

export function triggerSyncCartDebounced() {
    clearTimeout(syncCartDebounceTimer);
    syncCartDebounceTimer = setTimeout(() => {
        simpanKeSession();
    }, 400);
}

// Diskon Global Modal Handlers
export function modalDiskonGlobal() {
    document.getElementById('inputDiskon').value = state.discountPercent || '';
    document.getElementById('inputVoucher').value = state.voucherAmount > 0 ? formatRupiah(state.voucherAmount) : '';

    const modal = document.getElementById('modalDiskonGlobal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('inputDiskon').focus();
    }
}

export function tutupModalDiskonGlobal() {
    const modal = document.getElementById('modalDiskonGlobal');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}

export function simpanDiskonGlobal() {
    const rawDiskon = parseFloat(document.getElementById('inputDiskon').value) || 0;
    state.discountPercent = Math.min(100, Math.max(0, rawDiskon));

    const rawVoucherStr = document.getElementById('inputVoucher').value || '';
    const rawVoucher = parseFloat(rawVoucherStr.replace(/\D/g, '')) || 0;
    state.voucherAmount = Math.max(0, rawVoucher);

    renderCart();
    simpanKeSession();
    tutupModalDiskonGlobal();
}
