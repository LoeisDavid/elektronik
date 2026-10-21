/**
 * =============================================================================
 * JavaScript Halaman POS Customer
 * =============================================================================
 */

import { openModal, closeModal, initSearchableSelects, toggleActionMenu } from '../../common/app-helper';

// Dropdown Sort Customer
export function toggleFilterCustomer(event) {
    if (event) event.stopPropagation();
    const panel = document.getElementById('panelFilterCustomer');
    const arrow = document.getElementById('arrowCustomerSort');
    if (panel) panel.classList.toggle('hidden');
    if (arrow) arrow.classList.toggle('rotate-180');
}

// Modal Tambah
export function openTambahModal() {
    openModal('tambahCustomerModal');
}

export function closeTambahModal() {
    closeModal('tambahCustomerModal');
}

// Modal Edit
export function openEditModal(button) {
    if (!button) return;

    const cstInput = document.getElementById('editCST');
    const desInput = document.getElementById('editDES');
    const tlpInput = document.getElementById('editTLP');
    const alInput = document.getElementById('editAL');
    const form = document.getElementById('editCustomerForm');

    if (cstInput) cstInput.value = button.dataset.cst || '';
    if (desInput) desInput.value = button.dataset.des || '';
    if (tlpInput) tlpInput.value = button.dataset.tlp || '';
    if (alInput) alInput.value = button.dataset.al || '';
    if (form && button.dataset.action) form.action = button.dataset.action;

    // Tutup menu aksi
    document.querySelectorAll('.action-menu').forEach(menu => menu.classList.add('hidden'));

    openModal('editCustomerModal');

    // Set nilai select desa
    const dsVal = button.dataset.ds || '';
    const $dsSelect = jQuery('#editDS');
    if ($dsSelect.length && typeof jQuery.fn.select2 !== 'undefined') {
        $dsSelect.val(dsVal).trigger('change');
    } else {
        const dsInput = document.getElementById('editDS');
        if (dsInput) dsInput.value = dsVal;
    }
}

export function closeEditModal() {
    closeModal('editCustomerModal');
}

// Expose fungsi ke window agar atribut inline onclick tetap berjalan mulus
if (typeof window !== 'undefined') {
    window.toggleFilterCustomer = toggleFilterCustomer;
    window.openTambahModal = openTambahModal;
    window.closeTambahModal = closeTambahModal;
    window.openEditModal = openEditModal;
    window.closeEditModal = closeEditModal;
    window.toggleActionMenu = toggleActionMenu;
}

// Event Listeners DOM
document.addEventListener('DOMContentLoaded', () => {
    // Tutup panel sort saat klik di luar
    document.addEventListener('click', (e) => {
        const container = document.getElementById('containerFilterCustomer');
        const panel = document.getElementById('panelFilterCustomer');
        const arrow = document.getElementById('arrowCustomerSort');

        if (container && panel && !container.contains(e.target)) {
            panel.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }
    });

    // Inisialisasi dropdown di modal jika modal sedang terbuka (misal validasi error)
    if (!document.getElementById('tambahCustomerModal')?.classList.contains('hidden')) {
        initSearchableSelects(document.getElementById('tambahCustomerModal'));
    }
    if (!document.getElementById('editCustomerModal')?.classList.contains('hidden')) {
        initSearchableSelects(document.getElementById('editCustomerModal'));
    }
});
