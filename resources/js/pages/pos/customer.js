/**
 * =============================================================================
 * JavaScript Halaman POS Customer & Desa
 * =============================================================================
 */

import { openModal, closeModal, initSearchableSelects, toggleActionMenu, showToast } from '../../common/app-helper';

let activeKasirPelangganSelectId = null;
let activeDesaSelectId = 'selectDesaTambah';

// Inisialisasi Select2 untuk dropdown Desa di dalam modal Customer (Tambah & Edit)
function initDesaSelectInModal(modalId) {
    const modal = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
    if (!modal || typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') return;

    jQuery(modal).find('#selectDesaTambah, #editDS').each(function () {
        const $select = jQuery(this);
        $select.select2({
            width: '100%',
            placeholder: $select.find('option[disabled]').text() || 'Pilih Desa',
            dropdownParent: jQuery(modal),
            language: {
                noResults: function () {
                    return jQuery(`
                        <div class="py-2.5 px-2 text-center text-xs">
                            <p class="text-gray-600 mb-1.5 font-medium">Desa tidak ditemukan</p>
                            <button type="button" class="btn-tambah-desa-inline w-full py-1.5 px-3 bg-[#101D36] hover:bg-[#1C325B] text-white rounded-lg font-bold text-xs cursor-pointer shadow-xs transition-colors">
                                + Tambah Desa Baru
                            </button>
                        </div>
                    `).on('click', '.btn-tambah-desa-inline', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const searchField = document.querySelector('.select2-container--open .select2-search__field');
                        const typedName = searchField ? searchField.value.trim() : '';
                        $select.select2('close');
                        openQuickTambahDesaModal($select.attr('id'), typedName);
                    });
                }
            },
            escapeMarkup: function (m) {
                return m;
            }
        });
    });
}

export function openQuickTambahDesaModal(targetSelectId = 'selectDesaTambah', initialName = '') {
    activeDesaSelectId = targetSelectId || 'selectDesaTambah';
    const input = document.getElementById('inputQuickNamaDesa');
    if (input) {
        input.value = initialName || '';
    }
    openModal('quickTambahDesaModal');
    setTimeout(() => {
        if (input) input.focus();
    }, 50);
}

export function closeQuickTambahDesaModal() {
    closeModal('quickTambahDesaModal');
}

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
export function openTambahModal(targetSelectId = null) {
    activeKasirPelangganSelectId = typeof targetSelectId === 'string' ? targetSelectId : null;
    pilihTipeTambah('customer');
    openModal('tambahCustomerModal');
    initDesaSelectInModal('tambahCustomerModal');
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
    initDesaSelectInModal('editCustomerModal');

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
    window.openQuickTambahDesaModal = openQuickTambahDesaModal;
    window.closeQuickTambahDesaModal = closeQuickTambahDesaModal;
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
        initDesaSelectInModal('tambahCustomerModal');
    }
    if (!document.getElementById('editCustomerModal')?.classList.contains('hidden')) {
        initSearchableSelects(document.getElementById('editCustomerModal'));
        initDesaSelectInModal('editCustomerModal');
    }

    // Tangani submit form tambah desa cepat dari dropdown Desa
    const formQuickDesa = document.getElementById('formQuickTambahDesa');
    if (formQuickDesa) {
        formQuickDesa.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(formQuickDesa);
            const btnSimpan = document.getElementById('btnSimpanQuickDesa');
            if (btnSimpan) {
                btnSimpan.disabled = true;
                btnSimpan.innerText = 'Menyimpan...';
            }

            fetch(formQuickDesa.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': formQuickDesa.querySelector('input[name="_token"]')?.value || ''
                },
                body: formData
            })
            .then(async res => {
                const data = await res.json();
                return { status: res.status, data };
            })
            .then(({ status, data }) => {
                if (btnSimpan) {
                    btnSimpan.disabled = false;
                    btnSimpan.innerText = 'Simpan';
                }
                if (status >= 200 && status < 300 && data.success && data.data) {
                    const selectTambah = document.getElementById('selectDesaTambah');
                    const selectEdit = document.getElementById('editDS');

                    [selectTambah, selectEdit].forEach(sel => {
                        if (!sel) return;
                        const isTarget = sel.id === activeDesaSelectId;
                        const opt = new Option(data.data.DES, data.data.CST, isTarget, isTarget);
                        if (typeof jQuery !== 'undefined') {
                            jQuery(sel).append(opt);
                            if (isTarget) {
                                jQuery(sel).val(data.data.CST).trigger('change');
                            }
                        } else {
                            sel.appendChild(opt);
                            if (isTarget) {
                                sel.value = data.data.CST;
                            }
                        }
                    });

                    // Tambahkan juga ke dropdown pelanggan Kasir jika berada di halaman Kasir
                    const inputKonfirmasi = document.getElementById('inputPelangganKonfirmasi');
                    const inputDraft = document.getElementById('inputPelangganDraft');
                    [inputKonfirmasi, inputDraft].forEach(sel => {
                        if (!sel) return;
                        const opt = new Option(data.data.DES, data.data.DES, false, false);
                        if (typeof jQuery !== 'undefined') {
                            jQuery(sel).append(opt).trigger('change.select2');
                        } else {
                            sel.appendChild(opt);
                        }
                    });

                    formQuickDesa.reset();
                    closeQuickTambahDesaModal();
                    if (typeof showToast === 'function') {
                        showToast(data.message || 'Desa berhasil ditambahkan.', 'success');
                    }
                } else {
                    const errMsg = data?.errors ? Object.values(data.errors).flat()[0] : (data?.message || 'Gagal menambahkan desa.');
                    alert(errMsg);
                }
            })
            .catch(err => {
                if (btnSimpan) {
                    btnSimpan.disabled = false;
                    btnSimpan.innerText = 'Simpan';
                }
                console.error(err);
                alert('Terjadi kesalahan saat menyimpan desa.');
            });
        });
    }

    // Tangani submit form tambah jika dibuka dari halaman Kasir (AJAX tanpa refresh)
    const formTambah = document.getElementById('formTambah');
    if (formTambah) {
        formTambah.addEventListener('submit', function (e) {
            const inputKonfirmasi = document.getElementById('inputPelangganKonfirmasi');
            const inputDraft = document.getElementById('inputPelangganDraft');
            if (inputKonfirmasi || inputDraft) {
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

                        // Tentukan select mana yang sedang aktif (Draft vs Konfirmasi Pembayaran)
                        let targetId = activeKasirPelangganSelectId;
                        if (!targetId) {
                            const isDraftOpen = !document.getElementById('modalTambahArsip')?.classList.contains('hidden');
                            targetId = isDraftOpen ? 'inputPelangganDraft' : 'inputPelangganKonfirmasi';
                        }

                        [inputKonfirmasi, inputDraft].forEach(selectEl => {
                            if (!selectEl) return;
                            const isTarget = selectEl.id === targetId;
                            const newOpt = new Option(optText, data.data.DES, isTarget, isTarget);
                            if (data.data.TLP) {
                                newOpt.setAttribute('data-phone', data.data.TLP);
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

                        // Jika yang ditambahkan adalah Desa baru, tambahkan juga ke opsi dropdown Desa di modal Customer
                        if (data.is_desa && data.data.CST) {
                            const selectDesaTambah = document.getElementById('selectDesaTambah');
                            if (selectDesaTambah) {
                                const desaOpt = new Option(data.data.DES, data.data.CST, false, false);
                                if (typeof jQuery !== 'undefined') {
                                    jQuery(selectDesaTambah).append(desaOpt).trigger('change.select2');
                                } else {
                                    selectDesaTambah.appendChild(desaOpt);
                                }
                            }
                        }

                        formTambah.reset();
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
