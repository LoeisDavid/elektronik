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

let activeKasirSalesSelectId = null;

// Modal Tambah Sales
export function openTambahSalesModal(targetSelectId = null) {
    activeKasirSalesSelectId = typeof targetSelectId === 'string' ? targetSelectId : null;
    openModal('tambahSalesModal');
}

export function closeTambahSalesModal() {
    closeModal('tambahSalesModal');
}

// Modal Edit Sales
export function openEditSalesModal(button) {
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

export function closeEditSalesModal() {
    closeModal('editSalesModal');
}

// Expose ke window
if (typeof window !== 'undefined') {
    window.toggleFilterSales = toggleFilterSales;
    window.openTambahSalesModal = openTambahSalesModal;
    window.closeTambahSalesModal = closeTambahSalesModal;
    window.openEditSalesModal = openEditSalesModal;
    window.closeEditSalesModal = closeEditSalesModal;
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

    // Tangani submit form tambah sales jika dibuka dari halaman Kasir (AJAX tanpa refresh)
    const formTambahSales = document.getElementById('formTambahSales');
    if (formTambahSales) {
        formTambahSales.addEventListener('submit', function (e) {
            const inputKonfirmasi = document.getElementById('inputSalesKonfirmasi');
            const inputDraft = document.getElementById('inputSalesDraft');
            if (inputKonfirmasi || inputDraft) {
                e.preventDefault();
                const formData = new FormData(formTambahSales);
                const btnSimpan = formTambahSales.querySelector('button[type="submit"]');
                if (btnSimpan) {
                    btnSimpan.disabled = true;
                    btnSimpan.innerText = 'Menyimpan...';
                }

                fetch(formTambahSales.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': formTambahSales.querySelector('input[name="_token"]')?.value || ''
                    },
                    body: formData
                })
                .then(async res => {
                    const data = await res.json();
                    return { ok: res.ok, data };
                })
                .then(({ ok, data }) => {
                    if (btnSimpan) {
                        btnSimpan.disabled = false;
                        btnSimpan.innerText = 'Simpan';
                    }
                    if (ok && data.success && data.data) {
                        const optText = `${data.data.DES} ${data.data.HP ? '- ' + data.data.HP : ''}`.trim();

                        let targetId = activeKasirSalesSelectId;
                        if (!targetId) {
                            const isDraftOpen = !document.getElementById('modalTambahArsip')?.classList.contains('hidden');
                            targetId = isDraftOpen ? 'inputSalesDraft' : 'inputSalesKonfirmasi';
                        }

                        [inputKonfirmasi, inputDraft].forEach(selectEl => {
                            if (!selectEl) return;
                            const isTarget = selectEl.id === targetId;
                            const newOpt = new Option(optText, data.data.DES, isTarget, isTarget);
                            if (data.data.HP) {
                                newOpt.setAttribute('data-phone', data.data.HP);
                            }
                            if (typeof jQuery !== 'undefined') {
                                jQuery(selectEl).append(newOpt);
                                if (isTarget) {
                                    jQuery(selectEl).val(data.data.DES).trigger('change');
                                }
                            } else {
                                selectEl.appendChild(newOpt);
                                if (isTarget) {
                                    selectEl.value = data.data.DES;
                                }
                            }
                        });

                        formTambahSales.reset();
                        closeTambahSalesModal();
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Sales berhasil ditambahkan.', 'success');
                        }
                    } else {
                        const errMsg = data?.errors ? Object.values(data.errors).flat()[0] : (data?.message || 'Gagal menyimpan data sales.');
                        alert(errMsg);
                    }
                })
                .catch(err => {
                    if (btnSimpan) {
                        btnSimpan.disabled = false;
                        btnSimpan.innerText = 'Simpan';
                    }
                    console.error(err);
                    alert('Terjadi kesalahan saat menyimpan data sales.');
                });
            }
        });
    }
});
