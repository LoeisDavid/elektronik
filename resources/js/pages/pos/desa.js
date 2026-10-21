/**
 * =============================================================================
 * JavaScript Halaman POS Desa
 * =============================================================================
 */

import { openModal, closeModal, toggleActionMenu } from '../../common/app-helper';

// Dropdown Sort Desa
export function toggleFilterDesa(event) {
    if (event) event.stopPropagation();
    const panel = document.getElementById('panelFilterDesa');
    const arrow = document.getElementById('arrowDesaSort');
    if (panel) panel.classList.toggle('hidden');
    if (arrow) arrow.classList.toggle('rotate-180');
}

// Modal Tambah Desa
export function openTambahModal() {
    openModal('tambahDesaModal');
}

export function closeTambahModal() {
    closeModal('tambahDesaModal');
}

// Modal Edit Desa
export function openEditModal(button) {
    if (!button) return;

    const dsInput = document.getElementById('editDS');
    const desInput = document.getElementById('editDES');
    const displayKode = document.getElementById('displayKodeDesa');
    const form = document.getElementById('editDesaForm');

    const dsVal = button.dataset.ds || '';
    if (dsInput) dsInput.value = dsVal;
    if (displayKode) displayKode.innerText = dsVal;
    if (desInput) desInput.value = button.dataset.des || '';
    if (form && button.dataset.action) form.action = button.dataset.action;

    // Tutup menu aksi
    document.querySelectorAll('.action-menu').forEach(menu => menu.classList.add('hidden'));

    openModal('editDesaModal');
}

export function closeEditModal() {
    closeModal('editDesaModal');
}

// Expose ke window
if (typeof window !== 'undefined') {
    window.toggleFilterDesa = toggleFilterDesa;
    window.openTambahModal = openTambahModal;
    window.closeTambahModal = closeTambahModal;
    window.openEditModal = openEditModal;
    window.closeEditModal = closeEditModal;
    window.toggleActionMenu = toggleActionMenu;
}

// Event Listeners DOM
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (e) => {
        const container = document.getElementById('containerFilterDesa');
        const panel = document.getElementById('panelFilterDesa');
        const arrow = document.getElementById('arrowDesaSort');

        if (container && panel && !container.contains(e.target)) {
            panel.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }
    });
});
