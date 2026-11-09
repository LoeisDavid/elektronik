/**
 * =============================================================================
 * Kasir Item Search & Barcode Scan Management
 * =============================================================================
 */

import { state, formatRupiah, escapeHtml } from './config.js';

let searchItmDebounceTimer = null;
let searchResultItems = [];

export function bukaModalBarcode() {
    const modal = document.getElementById('modalBarcode');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            const input = document.getElementById('inputManualBarcode');
            if (input) input.focus();
        }, 100);
    }
}

export function tutupModalBarcode() {
    const modal = document.getElementById('modalBarcode');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
    if (window.BarcodeCamera) window.BarcodeCamera.stop('kasir');
}

export function initBarcodeCameraListener() {
    const camKasir = document.getElementById('bcam-kasir');
    if (!camKasir) return;

    camKasir.addEventListener('barcode:scan', function(e) {
        var kode = String(e.detail.code || '').replace(/[\u0000-\u001F\u007F]/g, '').trim();
        if (!kode) return;

        var inputModal = document.getElementById('inputManualBarcode');
        if (inputModal) inputModal.value = kode;

        var inputUtama = document.getElementById('inputScanUtama');
        if (inputUtama) inputUtama.value = kode;

        var form = document.getElementById('formScanKasir');
        if (form) {
            if (window.BarcodeCamera) {
                window.BarcodeCamera.stop('kasir', { keepResume: true });
            }
            form.requestSubmit();
        }
    });
}

export function handleSearchItmInput(val) {
    val = (val || '').trim();
    const btnClear = document.getElementById('btnClearSearch');
    if (btnClear) {
        if (val.length > 0) {
            btnClear.classList.remove('hidden');
        } else {
            btnClear.classList.add('hidden');
        }
    }

    clearTimeout(searchItmDebounceTimer);
    if (val.length < 1) {
        tutupPanelSearchItm();
        searchResultItems = [];
        return;
    }

    searchItmDebounceTimer = setTimeout(() => {
        fetchSearchItm(val);
    }, 200);
}

export function handleSearchItmFocus() {
    const val = (document.getElementById('inputScanUtama')?.value || '').trim();
    if (val.length > 0 && searchResultItems.length > 0) {
        bukaPanelSearchItm();
    }
}

export function handleSearchItmKeydown(e) {
    if (e.key === 'Escape') {
        tutupPanelSearchItm();
    }
}

export function clearSearchItm() {
    const input = document.getElementById('inputScanUtama');
    if (input) {
        input.value = '';
        input.focus();
    }
    const btnClear = document.getElementById('btnClearSearch');
    if (btnClear) btnClear.classList.add('hidden');
    tutupPanelSearchItm();
    searchResultItems = [];
}

export function bukaPanelSearchItm() {
    const panel = document.getElementById('panelHasilSearchItm');
    if (panel) panel.classList.remove('hidden');
}

export function tutupPanelSearchItm() {
    const panel = document.getElementById('panelHasilSearchItm');
    if (panel) panel.classList.add('hidden');
}

export function fetchSearchItm(q) {
    const panel = document.getElementById('panelHasilSearchItm');
    const list = document.getElementById('listHasilSearchItm');
    const loading = document.getElementById('loadingSearchItm');

    if (!panel || !list) return;

    bukaPanelSearchItm();
    if (loading) loading.classList.remove('hidden');
    list.innerHTML = '';

    fetch(state.routes.searchItems + '?q=' + encodeURIComponent(q))
        .then(res => res.json())
        .then(data => {
            if (loading) loading.classList.add('hidden');
            if (data.success && Array.isArray(data.items)) {
                searchResultItems = data.items;
                renderSearchItmResults(data.items);
            } else {
                searchResultItems = [];
                renderSearchItmResults([]);
            }
        })
        .catch(err => {
            if (loading) loading.classList.add('hidden');
            console.error('Error search ITM:', err);
            list.innerHTML = '<div class="p-4 text-center text-sm text-red-500 font-medium">Gagal memuat hasil pencarian.</div>';
        });
}

export function renderSearchItmResults(items) {
    const list = document.getElementById('listHasilSearchItm');
    if (!list) return;

    if (items.length === 0) {
        list.innerHTML = '<div class="p-5 text-center text-sm text-gray-500 font-medium">Tidak ada produk ditemukan di tabel ITM.</div>';
        return;
    }

    let html = `
        <div class="px-4 py-2 bg-gray-50 border-b border-gray-100 flex items-center justify-between text-[11px] text-gray-500 font-semibold tracking-wide uppercase">
            <span>Informasi Produk &amp; Harga (Tabel ITM)</span>
            <span>${items.length} produk ditemukan</span>
        </div>
    `;

    items.forEach(item => {
        const availableCount = item.available_count || 0;
        const inStock = availableCount > 0;

        let badgeStok = '';
        if (inStock) {
            badgeStok = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Stok: ${availableCount} unit</span>`;
        } else {
            badgeStok = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">Stok Habis</span>`;
        }

        html += `
            <div class="flex items-center justify-between p-3.5 hover:bg-gray-50/80 transition-colors">
                <div class="min-w-0 flex-1 pr-4">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="text-sm font-bold text-gray-900">${escapeHtml(item.des)}</span>
                        <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded font-mono font-medium">${escapeHtml(item.itm)}</span>
                        ${badgeStok}
                    </div>
                    <p class="text-xs text-gray-500">Kode: <span class="font-mono text-gray-700">${escapeHtml(item.itm)}</span></p>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-[11px] text-gray-400 font-medium block">Harga Satuan</span>
                    <span class="text-base font-extrabold text-black font-number">${formatRupiah(item.hrg)}</span>
                </div>
            </div>
        `;
    });

    list.innerHTML = html;
}

export function handleScanSubmit(event) {
    const input = document.getElementById('inputScanUtama');
    const val = input ? input.value.trim() : '';
    if (!val) {
        event.preventDefault();
        return false;
    }
    return true;
}

export function tampilInfoItemTerakhir() {
    const keys = Object.keys(state.cartData);
    const el = document.getElementById('infoItemTerakhir');
    const txt = document.getElementById('infoItemTerakhirText');
    if (!el || !txt || keys.length === 0) return;

    const key = keys[keys.length - 1];
    const item = state.cartData[key];
    txt.innerText = (item.code || key) + ' · ' + (item.name || '') + ' — ditambahkan ke keranjang';
    el.classList.remove('hidden');
    el.classList.add('flex');
}
