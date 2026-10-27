/**
 * =============================================================================
 * JavaScript Halaman POS Customer & Desa
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

// Switcher Tipe Tambah (Customer vs Desa)
export function pilihTipeTambah(tipe) {
    const btnCustomer = document.getElementById('btnTabTambahCustomer');
    const btnDesa = document.getElementById('btnTabTambahDesa');
    const inputTipe = document.getElementById('inputTipeTambah');
    const judulModal = document.getElementById('judulTambahModal');
    const labelDES = document.getElementById('labelTambahDES');
    const inputDES = document.getElementById('inputTambahDES');
    const fieldsCustomer = document.getElementById('fieldsCustomerTambah');
    const inputTLP = document.getElementById('inputTambahTLP');
    const selectDS = document.getElementById('selectDesaTambah');
    const inputAL = document.getElementById('inputTambahAL');

    if (tipe === 'desa') {
        if (inputTipe) inputTipe.value = 'desa';
        if (judulModal) judulModal.innerText = 'Tambah Desa';
        if (labelDES) labelDES.innerText = 'Nama Desa';
        if (inputDES) inputDES.placeholder = 'Masukkan nama desa';
        if (fieldsCustomer) fieldsCustomer.classList.add('hidden');

        if (inputTLP) inputTLP.removeAttribute('required');
        if (selectDS) selectDS.removeAttribute('required');
        if (inputAL) inputAL.removeAttribute('required');

        if (btnDesa) {
            btnDesa.className = 'flex-1 py-1.5 rounded-lg text-center transition-all duration-150 bg-primary-db text-white shadow-xs cursor-pointer';
        }
        if (btnCustomer) {
            btnCustomer.className = 'flex-1 py-1.5 rounded-lg text-center transition-all duration-150 text-gray-700 hover:text-black hover:bg-white/50 cursor-pointer';
        }
    } else {
        if (inputTipe) inputTipe.value = 'customer';
        if (judulModal) judulModal.innerText = 'Tambah Customer';
        if (labelDES) labelDES.innerText = 'Nama Customer';
        if (inputDES) inputDES.placeholder = 'Masukkan nama customer';
        if (fieldsCustomer) fieldsCustomer.classList.remove('hidden');

        if (inputTLP) inputTLP.setAttribute('required', 'required');
        if (selectDS) selectDS.setAttribute('required', 'required');
        if (inputAL) inputAL.setAttribute('required', 'required');

        if (btnCustomer) {
            btnCustomer.className = 'flex-1 py-1.5 rounded-lg text-center transition-all duration-150 bg-primary-db text-white shadow-xs cursor-pointer';
        }
        if (btnDesa) {
            btnDesa.className = 'flex-1 py-1.5 rounded-lg text-center transition-all duration-150 text-gray-700 hover:text-black hover:bg-white/50 cursor-pointer';
        }
    }
}

// Modal Tambah
export function openTambahModal() {
    pilihTipeTambah('customer');
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
    const editTipe = document.getElementById('editTipe');
    const judulEdit = document.getElementById('judulEditModal');
    const labelEditCST = document.getElementById('labelEditCST');
    const labelEditDES = document.getElementById('labelEditDES');
    const fieldsEdit = document.getElementById('fieldsCustomerEdit');

    const isDesa = button.dataset.isDesa === '1' || !button.dataset.ds;

    if (cstInput) cstInput.value = button.dataset.cst || '';
    if (desInput) desInput.value = button.dataset.des || '';
    if (tlpInput) tlpInput.value = button.dataset.tlp || '';
    if (alInput) alInput.value = button.dataset.al || '';
    if (form && button.dataset.action) form.action = button.dataset.action;

    if (isDesa) {
        if (editTipe) editTipe.value = 'desa';
        if (judulEdit) judulEdit.innerText = 'Edit Desa';
        if (labelEditCST) labelEditCST.innerText = 'Kode Desa';
        if (labelEditDES) labelEditDES.innerText = 'Nama Desa';
        if (fieldsEdit) fieldsEdit.classList.add('hidden');
        if (tlpInput) tlpInput.removeAttribute('required');
        if (alInput) alInput.removeAttribute('required');
    } else {
        if (editTipe) editTipe.value = 'customer';
        if (judulEdit) judulEdit.innerText = 'Edit Customer';
        if (labelEditCST) labelEditCST.innerText = 'Kode Customer';
        if (labelEditDES) labelEditDES.innerText = 'Nama Customer';
        if (fieldsEdit) fieldsEdit.classList.remove('hidden');
        if (tlpInput) tlpInput.setAttribute('required', 'required');
        if (alInput) alInput.setAttribute('required', 'required');
    }

    // Tutup menu aksi
    document.querySelectorAll('.action-menu').forEach(menu => menu.classList.add('hidden'));

    openModal('editCustomerModal');

    // Set nilai select desa jika customer
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

// Expose fungsi ke window
if (typeof window !== 'undefined') {
    window.toggleFilterCustomer = toggleFilterCustomer;
    window.pilihTipeTambah = pilihTipeTambah;
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

    // Inisialisasi dropdown di modal jika modal sedang terbuka
    if (!document.getElementById('tambahCustomerModal')?.classList.contains('hidden')) {
        initSearchableSelects(document.getElementById('tambahCustomerModal'));
    }
    if (!document.getElementById('editCustomerModal')?.classList.contains('hidden')) {
        initSearchableSelects(document.getElementById('editCustomerModal'));
    }

    // Tangani submit form tambah jika dibuka dari halaman Kasir (AJAX tanpa refresh)
    const formTambah = document.getElementById('formTambah');
    if (formTambah) {
        formTambah.addEventListener('submit', function (e) {
            const inputKasir = document.getElementById('inputPelangganKonfirmasi');
            if (inputKasir) {
                e.preventDefault();
                const formData = new FormData(formTambah);
                const btnSimpan = formTambah.querySelector('button[type="submit"]');
                if (btnSimpan) {
                    btnSimpan.disabled = true;
                    btnSimpan.innerText = 'Menyimpan...';
                }

                fetch(formTambah.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || ''
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (btnSimpan) {
                        btnSimpan.disabled = false;
                        btnSimpan.innerText = 'Simpan';
                    }
                    if (data.success && data.data) {
                        const optText = data.is_desa 
                            ? data.data.DES 
                            : `${data.data.DES} ${data.data.TLP ? '- ' + data.data.TLP : ''}`;
                        const newOpt = new Option(optText, data.data.DES, true, true);
                        if (typeof jQuery !== 'undefined') {
                            jQuery(inputKasir).append(newOpt).val(data.data.DES).trigger('change');
                        } else {
                            inputKasir.appendChild(newOpt);
                            inputKasir.value = data.data.DES;
                        }
                        closeTambahModal();
                    } else {
                        alert(data.message || 'Gagal menyimpan data.');
                    }
                })
                .catch(err => {
                    if (btnSimpan) {
                        btnSimpan.disabled = false;
                        btnSimpan.innerText = 'Simpan';
                    }
                    console.error(err);
                    alert('Terjadi kesalahan saat menyimpan data.');
                });
            }
        });
    }
});
