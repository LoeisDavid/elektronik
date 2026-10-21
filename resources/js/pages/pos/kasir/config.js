/**
 * =============================================================================
 * Kasir State & Configuration
 * Menyimpan data keranjang, status kasir, dan route endpoint dari Laravel
 * =============================================================================
 */

const initialConfig = window.__KASIR_CONFIG__ || {};

export const state = {
    cartData: (initialConfig.cart && typeof initialConfig.cart === 'object') ? initialConfig.cart : {},
    voucherAmount: Number(initialConfig.voucher || 0),
    discountPercent: Number(initialConfig.discountPercent || 0),
    ppnPercent: initialConfig.ppn !== null && initialConfig.ppn !== undefined ? Number(initialConfig.ppn) : null,
    isKasirOpen: Boolean(initialConfig.isKasirOpen),
    activeItemKeyForDiscount: null,
    currentGrandTotal: 0,
    activePaymentMethod: 'Cash',
    selectedBankName: '',
    hasPickedPaymentMethod: false,

    // State draft transaksi
    isLoadedFromDraft: false,
    activeDraftIndex: null,
    currentDraftPelanggan: '',
    currentDraftSales: '',

    // Data struk terakhir (untuk DomPDF print)
    strukTerakhir: null,

    // CSRF & Routes
    csrfToken: initialConfig.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    routes: initialConfig.routes || {
        bukaKasir: '/POS/buka-kasir',
        kasStatus: '/POS/kas-status',
        tutupKasir: '/POS/tutup-kasir',
        cartSync: '/POS/cart/sync',
        bayar: '/POS/bayar',
        searchItems: '/POS/search-items',
    }
};

window.isKasirOpen = state.isKasirOpen;

export function formatRupiah(number) {
    if (number === null || number === undefined || isNaN(number) || number === '') return 'Rp 0';
    return 'Rp ' + Math.round(Number(number)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

export function escapeHtml(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

export function formatInputRupiah(input) {
    if (!input) return;
    let rawVal = input.value.replace(/\D/g, '');
    if (!rawVal) {
        input.value = '';
        return;
    }
    input.value = formatRupiah(parseFloat(rawVal) || 0);
}
