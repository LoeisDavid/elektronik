/**
 * =============================================================================
 * JavaScript Halaman POS User
 * =============================================================================
 */

import { openModal, closeModal, toggleActionMenu } from '../../common/app-helper';

// Dropdown Sort User
export function toggleFilterUser(event) {
    if (event) event.stopPropagation();
    const panel = document.getElementById('panelFilterUser');
    const arrow = document.getElementById('arrowUserSort');
    if (panel) panel.classList.toggle('hidden');
    if (arrow) arrow.classList.toggle('rotate-180');
}

// Modal Tambah User
export function openTambahModal() {
    openModal('tambahUserModal');
}

export function closeTambahModal() {
    closeModal('tambahUserModal');
}

// Modal Edit User
export function openEditModal(button) {
    if (!button) return;

    const usernameInput = document.getElementById('editUSERNAME');
    const desInput = document.getElementById('editDES');
    const hpInput = document.getElementById('editHP');
    const alInput = document.getElementById('editAL');
    const roleInput = document.getElementById('editROLE');
    const passInput = document.getElementById('editPASS');
    const form = document.getElementById('editUserForm');

    if (usernameInput) usernameInput.value = button.dataset.username || '';
    if (desInput) desInput.value = button.dataset.des || '';
    if (hpInput) hpInput.value = button.dataset.hp || '';
    if (alInput) alInput.value = button.dataset.al || '';
    if (roleInput) roleInput.value = button.dataset.role || '1';
    if (passInput) passInput.value = '';
    if (form && button.dataset.action) form.action = button.dataset.action;

    // Tutup menu aksi
    document.querySelectorAll('.action-menu').forEach(menu => menu.classList.add('hidden'));

    openModal('editUserModal');
}

export function closeEditModal() {
    closeModal('editUserModal');
}

// Expose ke window
if (typeof window !== 'undefined') {
    window.toggleFilterUser = toggleFilterUser;
    window.openTambahModal = openTambahModal;
    window.closeTambahModal = closeTambahModal;
    window.openEditModal = openEditModal;
    window.closeEditModal = closeEditModal;
    window.toggleActionMenu = toggleActionMenu;
}

// Event Listeners DOM
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (e) => {
        const container = document.getElementById('containerFilterUser');
        const panel = document.getElementById('panelFilterUser');
        const arrow = document.getElementById('arrowUserSort');

        if (container && panel && !container.contains(e.target)) {
            panel.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }
    });
});
