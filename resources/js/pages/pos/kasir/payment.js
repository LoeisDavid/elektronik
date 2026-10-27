/**
 * =============================================================================
 * Kasir Payment Management (Payment methods, checkout modal, & submit payment)
 * =============================================================================
 */

import { state, formatRupiah } from './config.js';
import { hitungTotalQty, resetKeranjang, renderCart } from './cart.js';
import { tampilModalStruk } from './struk.js';
import { hapusDraftSetelahBayar } from './draft.js';

export function modalMetodePembayaran() {
    const modal = document.getElementById('modalMetodePembayaran');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

export function modalTutupMetodePembayaran() {
    const modal = document.getElementById('modalMetodePembayaran');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}

export function pilihMetodePembayaran(method) {
    state.activePaymentMethod = method;
    state.hasPickedPaymentMethod = true;
    if (['BNI', 'BCA', 'BRI', 'Seabank', 'JAGO'].includes(method)) {
        state.selectedBankName = method;
    } else {
        state.selectedBankName = '';
    }

    const elMetode = document.getElementById('metodePembayaran');
    if (elMetode) elMetode.innerText = method;

    updateTombolMetode();
    modalTutupMetodePembayaran();
}

// Sinkronkan tampilan tombol metode pembayaran
export function updateTombolMetode() {
    const btnTunai = document.getElementById('btnMetodeTunai');
    const btnNon = document.getElementById('btnMetodeNonTunai');
    const btnSingle = document.getElementById('btnMetodePembayaran');
    const labelSingle = document.getElementById('labelMetodeAktif');

    const isTunai = (String(state.activePaymentMethod || 'cash').toLowerCase() === 'cash' || String(state.activePaymentMethod || '').toLowerCase() === 'tunai');

    if (btnTunai && btnNon) {
        const aktif = ['bg-primary-db', 'text-white', 'border-black', 'font-bold'];
        const nonaktif = ['bg-white', 'text-gray-500', 'border-gray-400', 'font-medium'];

        btnTunai.classList.remove(...(isTunai ? nonaktif : aktif));
        btnTunai.classList.add(...(isTunai ? aktif : nonaktif));

        btnNon.classList.remove(...(isTunai ? aktif : nonaktif));
        btnNon.classList.add(...(isTunai ? nonaktif : aktif));

        btnNon.innerText = isTunai ? 'Non-tunai' : state.activePaymentMethod;
    }

    if (labelSingle) {
        labelSingle.innerText = state.hasPickedPaymentMethod ? state.activePaymentMethod : 'Metode Pembayaran';
    }
    if (btnSingle) {
        if (state.hasPickedPaymentMethod) {
            btnSingle.classList.remove('bg-white', 'text-black', 'hover:text-primary-db', 'hover:bg-[#EEF2FF]');
            btnSingle.classList.add('bg-primary-db', 'text-white', 'hover:bg-primary-sdb', 'hover:border-primary-sdb', 'hover:text-white');
        } else {
            btnSingle.classList.remove('bg-primary-db', 'text-white', 'hover:bg-primary-sdb', 'hover:border-primary-sdb', 'hover:text-white');
            btnSingle.classList.add('bg-white', 'text-black', 'hover:text-primary-db', 'hover:bg-[#EEF2FF]');
        }
    }
}

// Buka Modal Konfirmasi Pembayaran
export function prosesPembayaran() {
    const keys = Object.keys(state.cartData);
    if (keys.length === 0) {
        alert('Keranjang masih kosong!');
        return;
    }

    const totalItems = hitungTotalQty();
    const subtitleEl = document.getElementById('konfirmasiSubtitle');
    if (subtitleEl) {
        subtitleEl.innerText = `Selesaikan transaksi sebesar ${formatRupiah(state.currentGrandTotal)}, untuk ${totalItems} item?`;
    }

    const wrapperUangDiterima = document.getElementById('wrapperUangDiterima');
    const inputUangDiterima = document.getElementById('inputUangDiterima');
    const isTunai = (state.activePaymentMethod.toLowerCase() === 'cash' || state.activePaymentMethod.toLowerCase() === 'tunai');

    if (isTunai) {
        if (wrapperUangDiterima) wrapperUangDiterima.classList.remove('hidden');
        if (inputUangDiterima) {
            inputUangDiterima.value = formatRupiah(state.currentGrandTotal);
        }
    } else {
        if (wrapperUangDiterima) wrapperUangDiterima.classList.add('hidden');
    }

    // Auto-fill draft Pelanggan & Sales
    const inputPelanggan = document.getElementById('inputPelangganKonfirmasi');
    const inputSales = document.getElementById('inputSalesKonfirmasi');

    if (state.isLoadedFromDraft) {
        if (inputPelanggan) {
            inputPelanggan.value = state.currentDraftPelanggan;
            if (typeof jQuery !== 'undefined') jQuery(inputPelanggan).val(state.currentDraftPelanggan).trigger('change');
        }
        if (inputSales) {
            inputSales.value = state.currentDraftSales;
            if (typeof jQuery !== 'undefined') jQuery(inputSales).val(state.currentDraftSales).trigger('change');
        }
    } else {
        if (inputPelanggan) {
            inputPelanggan.value = '';
            if (typeof jQuery !== 'undefined') jQuery(inputPelanggan).val('').trigger('change');
        }
        if (inputSales) {
            inputSales.value = '';
            if (typeof jQuery !== 'undefined') jQuery(inputSales).val('').trigger('change');
        }
    }

    const modal = document.getElementById('modalKonfirmasiPembayaran');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            jQuery(modal).find('select.searchable-select').each(function() {
                const $select = jQuery(this);
                $select.select2({
                    width: '100%',
                    dropdownParent: jQuery(modal),
                    matcher: function(params, data) {
                        if (jQuery.trim(params.term) === '') {
                            return data;
                        }
                        if (!data.element) {
                            return null;
                        }
                        var term = params.term.toLowerCase();
                        var text = (data.text || '').toLowerCase();
                        var phone = (jQuery(data.element).data('phone') || '').toString().toLowerCase();

                        if (text.indexOf(term) > -1 || phone.indexOf(term) > -1) {
                            return data;
                        }
                        return null;
                    },
                    language: {
                        noResults: function() {
                            if ($select.attr('id') === 'inputPelangganKonfirmasi') {
                                return jQuery(`
                                    <div class="py-2.5 px-2 text-center text-xs">
                                        <p class="text-gray-600 mb-1.5 font-medium">Customer / Desa tidak ditemukan</p>
                                        <button type="button" class="btn-tambah-cst w-full py-1.5 px-3 bg-[#101D36] hover:bg-[#1C325B] text-white rounded-lg font-bold text-xs cursor-pointer shadow-xs transition-colors">
                                            + Tambah Data Baru
                                        </button>
                                    </div>
                                `).on('click', '.btn-tambah-cst', function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    $select.select2('close');
                                    if (typeof window.openTambahModal === 'function') {
                                        window.openTambahModal();
                                    }
                                });
                            }
                            return 'Data tidak ditemukan';
                        }
                    },
                    escapeMarkup: function(m) {
                        return m;
                    }
                });
            });
        }
    }
}

export function tutupModalKonfirmasiPembayaran() {
    const modal = document.getElementById('modalKonfirmasiPembayaran');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}

export function formatDanHitungUangDiterima(input) {
    let rawVal = input.value.replace(/\D/g, '');
    if (!rawVal) {
        input.value = '';
        return;
    }
    let numVal = parseFloat(rawVal) || 0;
    input.value = formatRupiah(numVal);
}

// Eksekusi Simpan Transaksi ke Backend
export function eksekusiPembayaranFinal() {
    const isTunai = (state.activePaymentMethod.toLowerCase() === 'cash' || state.activePaymentMethod.toLowerCase() === 'tunai');
    let nominalBayarClean = state.currentGrandTotal;

    if (isTunai) {
        const inputUang = document.getElementById('inputUangDiterima');
        if (inputUang && inputUang.value) {
            nominalBayarClean = parseFloat(inputUang.value.replace(/\D/g, '')) || 0;
        }
        if (nominalBayarClean < state.currentGrandTotal) {
            alert('Uang yang diterima kurang dari total transaksi!');
            return;
        }
    }

    const pelangganVal = document.getElementById('inputPelangganKonfirmasi')?.value || '';
    const salesVal = document.getElementById('inputSalesKonfirmasi')?.value || '';

    if (!state.isLoadedFromDraft) {
        if (!pelangganVal) {
            alert('Silakan pilih atau isi nama Pelanggan terlebih dahulu!');
            document.getElementById('inputPelangganKonfirmasi')?.focus();
            return;
        }
        if (!salesVal) {
            alert('Silakan pilih atau isi nama Sales terlebih dahulu!');
            document.getElementById('inputSalesKonfirmasi')?.focus();
            return;
        }
    }

    const btn = document.getElementById('btnEksekusiBayar');
    if (btn) {
        btn.disabled = true;
        btn.innerText = 'Memproses...';
    }

    fetch(state.routes.bayar, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': state.csrfToken
        },
        body: JSON.stringify({
            cart: state.cartData,
            voucher: state.voucherAmount,
            discount_percent: state.discountPercent,
            ppn: (state.ppnPercent !== null && state.ppnPercent !== '') ? state.ppnPercent : null,
            payment_method: state.activePaymentMethod,
            bank_name: state.selectedBankName,
            nominal_bayar: nominalBayarClean,
            pelanggan: pelangganVal,
            sales: salesVal
        })
    })
    .then(res => res.json())
    .then(data => {
        if (btn) {
            btn.disabled = false;
            btn.innerText = 'Konfirmasi';
        }

        if (data.success) {
            if (state.isLoadedFromDraft && state.activeDraftIndex !== null) {
                hapusDraftSetelahBayar(state.activeDraftIndex);
            }
            tutupModalKonfirmasiPembayaran();

            // Tampilkan Struk Pembayaran
            tampilModalStruk({
                ref_no: data.ref_no,
                tanggal: data.tanggal || new Date().toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                }),
                waktu: data.waktu || new Date().toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                }).replace(':', '.'),
                kasir: data.kasir || 'Kasir',
                customer_nama: data.customer_nama || '',
                customer_desa: data.customer_desa || '',
                customer_alamat: data.customer_alamat || '',
                metode: state.activePaymentMethod,
                items: Object.values(state.cartData),
                subtotal: data.subtotal,
                item_discount: data.item_discount,
                discount_percent: state.discountPercent,
                discount_global: data.discount_global,
                voucher: state.voucherAmount,
                ppn_percent: data.ppn_percent !== undefined ? data.ppn_percent : state.ppnPercent,
                ppn_nominal: data.ppn_nominal !== undefined ? data.ppn_nominal : 0,
                total: data.grand_total || state.currentGrandTotal,
                dibayar: nominalBayarClean,
                kembalian: data.kembalian || 0
            });

            resetKeranjang(true);
            renderCart();
        } else {
            alert(data.message || 'Gagal memproses pembayaran!');
        }
    })
    .catch(err => {
        if (btn) {
            btn.disabled = false;
            btn.innerText = 'Konfirmasi';
        }
        console.error('Error proses pembayaran:', err);
        alert('Terjadi kesalahan saat memproses pembayaran.');
    });
}
