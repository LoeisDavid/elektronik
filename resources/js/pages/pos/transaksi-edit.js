/**
 * =============================================================================
 * JavaScript Halaman POS Transaksi Edit
 * Real-time calculation untuk perubahan harga jual, diskon item, diskon global, voucher, dan PPN
 * =============================================================================
 */

import { formatRupiah, parseRupiah } from '../../common/app-helper';

function parseCurrency(val) {
    if (!val) return 0;
    return Math.max(0, parseFloat(val.toString().replace(/\./g, '')) || 0);
}

function formatCurrencyInput(el) {
    if (!el) return;
    const cursorPosition = el.selectionStart;
    const originalLength = el.value.length;
    const clean = el.value.replace(/\D/g, '');
    if (!clean) {
        el.value = '';
        return;
    }
    const formatted = parseInt(clean, 10).toLocaleString('id-ID');
    el.value = formatted;
    const newLength = formatted.length;
    const newPos = Math.max(0, cursorPosition + (newLength - originalLength));
    el.setSelectionRange(newPos, newPos);
}

export function recalculateTotals(clampAllInputs = false) {
    const rows = document.querySelectorAll('.item-row');
    let rawSubtotal = 0;
    let totalItemDiscount = 0;
    let subtotalAfterItemDisc = 0;

    rows.forEach(row => {
        const hgjInput = row.querySelector('.input-hgj');
        const pot1Input = row.querySelector('.input-pot1');
        const potrInput = row.querySelector('.input-potr');
        const subtotalDisplay = row.querySelector('.item-subtotal-display');
        const discountNominal = row.querySelector('.item-discount-nominal');

        const hgj = parseCurrency(hgjInput ? hgjInput.value : 0);
        let pot1 = parseFloat(pot1Input ? pot1Input.value : 0) || 0;
        if (pot1 > 100) {
            pot1 = 100;
            if (pot1Input) pot1Input.value = 100;
        } else if (pot1 < 0) {
            pot1 = 0;
            if (pot1Input) pot1Input.value = 0;
        }

        // Hitung diskon persen item & batas maksimal voucher untuk detail ini saja
        const discPctNominal = hgj * (pot1 / 100);
        const maxPotr = Math.max(0, Math.round(hgj - discPctNominal));

        let potr = parseCurrency(potrInput ? potrInput.value : 0);
        if (potr > maxPotr) {
            potr = maxPotr;
            if (potrInput && (clampAllInputs || document.activeElement === potrInput || document.activeElement === pot1Input)) {
                potrInput.value = potr > 0 ? potr.toLocaleString('id-ID') : '';
            }
        }

        const itemDiscTotal = Math.min(hgj, discPctNominal + potr);
        const itemSubtotal = Math.max(0, hgj - itemDiscTotal);

        if (subtotalDisplay) {
            subtotalDisplay.innerText = formatRupiah(itemSubtotal);
        }

        if (discountNominal) {
            discountNominal.innerText = discPctNominal > 0 ? `Rp ${Math.round(discPctNominal).toLocaleString('id-ID')}` : '';
        }

        rawSubtotal += hgj;
        totalItemDiscount += itemDiscTotal;
        subtotalAfterItemDisc += itemSubtotal;
    });

    // Global Diskon, Voucher & PPN Global
    const discPercentInput = document.getElementById('inputDiscountPercent');
    const voucherInput = document.getElementById('inputVoucher');
    const ppnInput = document.getElementById('inputPpn');

    const globalPct = Math.min(100, Math.max(0, parseFloat(discPercentInput ? discPercentInput.value : 0) || 0));
    let voucher = parseCurrency(voucherInput ? voucherInput.value : 0);

    let ppnRate = 0;
    if (ppnInput && ppnInput.value.trim() !== '') {
        const cleanVal = ppnInput.value.replace(/[,.]/g, '').trim();
        const parsed = parseInt(cleanVal, 10);
        if (!isNaN(parsed)) {
            ppnRate = Math.min(100, Math.max(0, parsed));
        }
    }

    const globalDiscNominal = subtotalAfterItemDisc * (globalPct / 100);
    const maxVoucherGlobal = Math.max(0, Math.round(subtotalAfterItemDisc - globalDiscNominal));
    if (voucher > maxVoucherGlobal) {
        voucher = maxVoucherGlobal;
        if (voucherInput) {
            voucherInput.value = voucher > 0 ? voucher.toLocaleString('id-ID') : '';
        }
    }

    const totalAfterAllDisc = Math.max(0, subtotalAfterItemDisc - globalDiscNominal - voucher);

    const ppnNominal = ppnRate > 0 ? (totalAfterAllDisc * (ppnRate / 100)) : 0;
    const grandTotal = totalAfterAllDisc + ppnNominal;

    const elSubtotal = document.getElementById('labelSubtotal');
    if (elSubtotal) elSubtotal.innerText = formatRupiah(subtotalAfterItemDisc);

    const elPromoGlobal = document.getElementById('labelPromoGlobal');
    if (elPromoGlobal) {
        if (globalDiscNominal > 0 || voucher > 0) {
            elPromoGlobal.innerHTML = `Diskon: ${formatRupiah(globalDiscNominal)}&nbsp;&nbsp;Voucher: ${formatRupiah(voucher)}`;
            elPromoGlobal.classList.remove('hidden');
        } else {
            elPromoGlobal.classList.add('hidden');
        }
    }

    const elPPNGlobal = document.getElementById('labelPPNGlobal');
    if (elPPNGlobal) {
        if (ppnRate > 0 && ppnNominal > 0) {
            elPPNGlobal.innerHTML = `PPN (${ppnRate}%): ${formatRupiah(ppnNominal)}`;
            elPPNGlobal.classList.remove('hidden');
        } else {
            elPPNGlobal.classList.add('hidden');
        }
    }

    const elGrandTotal = document.getElementById('labelGrandTotal');
    if (elGrandTotal) elGrandTotal.innerText = formatRupiah(grandTotal);
}

let ppnWarningEditTimeout = null;
function showPpnWarningEdit() {
    const w = document.getElementById('ppnWarningEdit');
    if (w) {
        w.classList.remove('hidden');
        clearTimeout(ppnWarningEditTimeout);
        ppnWarningEditTimeout = setTimeout(() => {
            w.classList.add('hidden');
        }, 3000);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const editForm = document.getElementById('formEditTransaksi');
    const ppnInput = document.getElementById('inputPpn');

    if (ppnInput) {
        ppnInput.addEventListener('keydown', function (e) {
            if (e.key === ',' || e.key === '.') {
                e.preventDefault();
                showPpnWarningEdit();
            }
        });

        ppnInput.addEventListener('input', function () {
            if (this.value.includes(',') || this.value.includes('.')) {
                showPpnWarningEdit();
                this.value = this.value.replace(/[,.]/g, '');
            }
        });
    }

    if (editForm) {
        editForm.addEventListener('input', function (e) {
            if (e.target.matches('.input-currency')) {
                formatCurrencyInput(e.target);
            }
            if (e.target.matches('.input-hgj, .input-pot1, .input-potr, #inputDiscountPercent, #inputVoucher, #inputPpn')) {
                recalculateTotals();
            }
        });

        editForm.addEventListener('change', function (e) {
            if (e.target.matches('.input-hgj, .input-pot1, .input-potr, #inputDiscountPercent, #inputVoucher, #inputPpn')) {
                recalculateTotals(true);
            }
        });

        editForm.addEventListener('click', function (e) {
            const btnHapusItem = e.target.closest('.btn-hapus-item-transaksi');
            if (btnHapusItem) {
                const nama = btnHapusItem.dataset.nama || 'Barang ini';
                const barcode = btnHapusItem.dataset.barcode || '-';
                const yakin = confirm(
                    `PERINGATAN: Apakah Anda yakin ingin menghapus barang "${nama}" (${barcode}) dari transaksi ini?\n\nStok barang akan dikembalikan saat perubahan disimpan.`
                );
                if (!yakin) return;

                const row = btnHapusItem.closest('.item-row');
                if (row) {
                    row.remove();
                }

                const remainingRows = editForm.querySelectorAll('.item-row');
                const emptyRow = document.getElementById('emptyItemRow');
                if (emptyRow) {
                    if (remainingRows.length === 0) {
                        emptyRow.classList.remove('hidden');
                    } else {
                        emptyRow.classList.add('hidden');
                    }
                }

                recalculateTotals(true);
            }
        });

        editForm.addEventListener('submit', function () {
            editForm.querySelectorAll('.input-currency').forEach(input => {
                input.value = input.value.replace(/\./g, '');
            });
            if (ppnInput) {
                ppnInput.value = ppnInput.value.replace(/[,.]/g, '').trim();
            }
        });
    }

    const btnHapusTransaksi = document.getElementById('btnHapusTransaksi');
    const formDeleteTransaksi = document.getElementById('formDeleteTransaksi');
    if (btnHapusTransaksi && formDeleteTransaksi) {
        btnHapusTransaksi.addEventListener('click', function () {
            const refNo = this.dataset.ref || '';
            const yakin = confirm(
                `PERINGATAN: Apakah Anda yakin ingin menghapus transaksi ${refNo} secara permanen?\n\nSeluruh stok barang pada transaksi ini akan otomatis dikembalikan ke stok.`
            );
            if (yakin) {
                formDeleteTransaksi.submit();
            }
        });
    }

    recalculateTotals();
});
