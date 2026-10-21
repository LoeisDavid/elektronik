/**
 * =============================================================================
 * Kasir Main Module (Entry Point)
 * Menggabungkan seluruh modul kasir (shift, cart, payment, struk, draft, search)
 * dan mengekspos fungsi-fungsi global ke window untuk interaksi Blade HTML.
 * =============================================================================
 */

import { state, formatRupiah, escapeHtml, formatInputRupiah } from './config.js';
import {
    bukaModalBukaKasir,
    tutupModalBukaKasir,
    simpanBukaKasir,
    modalTutupKasir,
    tutupModalTutupKasir,
    formatDanHitungSelisihTutupKasir,
    clearInputKasAkhir,
    prosesTutupKasir
} from './shift.js';
import {
    renderCart,
    hitungTotalQty,
    hitungRingkasan,
    handlePpnInput,
    hapusItem,
    resetKeranjang,
    kosongkanKeranjang,
    simpanKeSession,
    ubahDiskonItemLangsung,
    tahanHasilDiskonItem,
    commitDiskonItemLangsung,
    ubahVoucherItemLangsung,
    updateSubtotalBarisItem,
    modalDiskonGlobal,
    tutupModalDiskonGlobal,
    simpanDiskonGlobal
} from './cart.js';
import {
    modalMetodePembayaran,
    modalTutupMetodePembayaran,
    pilihMetodePembayaran,
    updateTombolMetode,
    prosesPembayaran,
    tutupModalKonfirmasiPembayaran,
    formatDanHitungUangDiterima,
    eksekusiPembayaranFinal
} from './payment.js';
import {
    tampilModalStruk,
    tutupModalStruk,
    tutupModalStrukKlikLuar,
    cetakStrukPdf
} from './struk.js';
import {
    modalTambahArsip,
    tutupModalTambahArsip,
    simpanDraftTransaksi,
    modalDraftTransaksi,
    tutupModalDraftTransaksi,
    tutupModalDraftKlikLuar,
    renderDraftList,
    muatDraft,
    hapusDraft,
    hapusDraftSetelahBayar,
    updateDraftBadgeHeader
} from './draft.js';
import {
    bukaModalBarcode,
    tutupModalBarcode,
    initBarcodeCameraListener,
    handleSearchItmInput,
    handleSearchItmFocus,
    handleSearchItmKeydown,
    clearSearchItm,
    bukaPanelSearchItm,
    tutupPanelSearchItm,
    fetchSearchItm,
    renderSearchItmResults,
    handleScanSubmit,
    tampilInfoItemTerakhir
} from './search-scan.js';

// Bind seluruh fungsi ke window agar kompatibel dengan onclick/oninput di Blade
Object.assign(window, {
    // Config & Helpers
    formatRupiah,
    escapeHtml,
    formatInputRupiah,

    // Shift Management
    bukaModalBukaKasir,
    tutupModalBukaKasir,
    simpanBukaKasir,
    modalTutupKasir,
    tutupModalTutupKasir,
    formatDanHitungSelisihTutupKasir,
    clearInputKasAkhir,
    prosesTutupKasir,

    // Cart Operations
    renderCart,
    hitungTotalQty,
    hitungRingkasan,
    handlePpnInput,
    hapusItem,
    resetKeranjang,
    kosongkanKeranjang,
    simpanKeSession,
    ubahDiskonItemLangsung,
    tahanHasilDiskonItem,
    commitDiskonItemLangsung,
    ubahVoucherItemLangsung,
    updateSubtotalBarisItem,
    modalDiskonGlobal,
    tutupModalDiskonGlobal,
    simpanDiskonGlobal,

    // Payment & Checkout
    modalMetodePembayaran,
    modalTutupMetodePembayaran,
    pilihMetodePembayaran,
    updateTombolMetode,
    prosesPembayaran,
    tutupModalKonfirmasiPembayaran,
    formatDanHitungUangDiterima,
    eksekusiPembayaranFinal,

    // Struk / Receipt
    tampilModalStruk,
    tutupModalStruk,
    tutupModalStrukKlikLuar,
    cetakStrukPdf,
    cetakStruk: cetakStrukPdf,

    // Draft Management
    modalTambahArsip,
    tutupModalTambahArsip,
    simpanDraftTransaksi,
    modalDraftTransaksi,
    tutupModalDraftTransaksi,
    tutupModalDraftKlikLuar,
    renderDraftList,
    muatDraft,
    hapusDraft,
    hapusDraftSetelahBayar,
    updateDraftBadgeHeader,

    // Search & Scan
    bukaModalBarcode,
    tutupModalBarcode,
    handleSearchItmInput,
    handleSearchItmFocus,
    handleSearchItmKeydown,
    clearSearchItm,
    bukaPanelSearchItm,
    tutupPanelSearchItm,
    fetchSearchItm,
    renderSearchItmResults,
    handleScanSubmit,
    tampilInfoItemTerakhir
});

// ESC Keyboard Shortcut untuk menutup semua modal yang terbuka
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        tutupModalDraftTransaksi();
        tutupModalBarcode();
        tutupModalDiskonGlobal();
        modalTutupMetodePembayaran();
        tutupModalTambahArsip();
        tutupModalTutupKasir();
        tutupModalKonfirmasiPembayaran();
        tutupPanelSearchItm();
    }
});

// Scan form submit listener: simpan jumlah item sebelum submit untuk deteksi item baru
document.addEventListener('submit', function(e) {
    if (e.target && e.target.querySelector && e.target.querySelector('[name="BC_OUT"]')) {
        sessionStorage.setItem('scanJumlahSebelum', String(hitungTotalQty()));
    }
});

// Klik di luar container search untuk menutup panel search ITM
document.addEventListener('click', function(e) {
    const container = document.getElementById('containerSearchUtama');
    if (container && !container.contains(e.target)) {
        tutupPanelSearchItm();
    }
});

// Inisialisasi awal saat DOM siap
document.addEventListener('DOMContentLoaded', function() {
    initBarcodeCameraListener();
    updateDraftBadgeHeader();

    if (state.isKasirOpen) {
        renderCart();

        // Cek apakah item baru ditambahkan dari scan sebelumnya
        const sebelum = sessionStorage.getItem('scanJumlahSebelum');
        if (sebelum !== null) {
            sessionStorage.removeItem('scanJumlahSebelum');
            if (hitungTotalQty() > parseInt(sebelum)) {
                tampilInfoItemTerakhir();
            }
        }

        const inputScan = document.getElementById('inputScanUtama');
        if (inputScan) inputScan.focus();
    }

    // Tangani parameter ?action=tutup_kasir saat logout dipicu dari halaman lain
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'tutup_kasir') {
        window.__shouldLogoutAfterTutup = true;
        setTimeout(() => {
            modalTutupKasir();
        }, 300);
    }
});
