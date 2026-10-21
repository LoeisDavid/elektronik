/**
 * =============================================================================
 * JavaScript Halaman POS Sales
 * =============================================================================
 */

import { openModal, closeModal, toggleActionMenu } from '../../common/app-helper';

// Dropdown Sort Sales
export function toggleFilterSales(event) {
    if (event) event.stopPropagation();
    const panel = document.getElementById('panelFilterSales');
    const arrow = document.getElementById('arrowSalesSort');
    if (panel) panel.classList.toggle('hidden');
    if (arrow) arrow.classList.toggle('rotate-180');
}

// Modal Tambah Sales
export function openTambahModal() {
    openModal('tambahSalesModal');
}

export function closeTambahModal() {
    closeModal('tambahSalesModal');
}

// Modal Edit Sales
export function openEditModal(button) {
    if (!button) return;

    const slsInput = document.getElementById('editSLS');
    const desInput = document.getElementById('editDES');
    const hpInput = document.getElementById('editHP');
    const alInput = document.getElementById('editAL');
    const form = document.getElementById('editSalesForm');

    if (slsInput) slsInput.value = button.dataset.sls || '';
    if (desInput) desInput.value = button.dataset.des || '';
    if (hpInput) hpInput.value = button.dataset.hp || '';
    if (alInput) alInput.value = button.dataset.al || '';
    if (form && button.dataset.action) form.action = button.dataset.action;

    // Tutup menu aksi
    document.querySelectorAll('.action-menu').forEach(menu => menu.classList.add('hidden'));

    openModal('editSalesModal');
}

export function closeEditModal() {
    closeModal('editSalesModal');
}

// Expose ke window
if (typeof window !== 'undefined') {
    window.toggleFilterSales = toggleFilterSales;
    window.openTambahModal = openTambahModal;
    window.closeTambahModal = closeTambahModal;
    window.openEditModal = openEditModal;
    window.closeEditModal = closeEditModal;
    window.toggleActionMenu = toggleActionMenu;
}

// Event Listeners DOM
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (e) => {
        const container = document.getElementById('containerFilterSales');
        const panel = document.getElementById('panelFilterSales');
        const arrow = document.getElementById('arrowSalesSort');

        if (container && panel && !container.contains(e.target)) {
            panel.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }
    });
});
