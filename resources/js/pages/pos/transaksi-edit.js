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

export function recalculateTotals() {
    const rows = document.querySelectorAll('.item-row');
    let rawSubtotal = 0;
    let totalItemDiscount = 0;

    rows.forEach(row => {
        const hgjInput = row.querySelector('.input-hgj');
        const pot1Input = row.querySelector('.input-pot1');
        const potrInput = row.querySelector('.input-potr');
        const subtotalDisplay = row.querySelector('.item-subtotal-display');
        const discountNominal = row.querySelector('.item-discount-nominal');

        const hgj = parseCurrency(hgjInput ? hgjInput.value : 0);
        const pot1 = Math.min(100, Math.max(0, parseFloat(pot1Input ? pot1Input.value : 0) || 0));
        const potr = parseCurrency(potrInput ? potrInput.value : 0);

        // Hitung diskon item
        const discPctNominal = hgj * (pot1 / 100);
        const itemDiscTotal = discPctNominal + potr;
        const itemSubtotal = Math.max(0, hgj - itemDiscTotal);

        if (subtotalDisplay) {
            subtotalDisplay.innerText = formatRupiah(itemSubtotal);
        }

        if (discountNominal) {
            discountNominal.innerText = discPctNominal > 0 ? `Rp ${Math.round(discPctNominal).toLocaleString('id-ID')}` : '';
        }

        rawSubtotal += hgj;
        totalItemDiscount += itemDiscTotal;
    });

    // Global Diskon, Voucher & PPN Global
    const discPercentInput = document.getElementById('inputDiscountPercent');
    const voucherInput = document.getElementById('inputVoucher');
    const ppnInput = document.getElementById('inputPpn');

    const globalPct = Math.min(100, Math.max(0, parseFloat(discPercentInput ? discPercentInput.value : 0) || 0));
    const voucher = parseCurrency(voucherInput ? voucherInput.value : 0);

    let ppnRate = 0;
    if (ppnInput && ppnInput.value !== '' && !isNaN(ppnInput.value)) {
        ppnRate = Math.min(100, Math.max(0, parseFloat(ppnInput.value) || 0));
    }

    const subtotalAfterItemDisc = Math.max(0, rawSubtotal - totalItemDiscount);
    const globalDiscNominal = subtotalAfterItemDisc * (globalPct / 100);
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

document.addEventListener('DOMContentLoaded', function () {
    const editForm = document.getElementById('formEditTransaksi');
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
                recalculateTotals();
            }
        });

        editForm.addEventListener('submit', function () {
            editForm.querySelectorAll('.input-currency').forEach(input => {
                input.value = input.value.replace(/\./g, '');
            });
        });
    }

    recalculateTotals();
});
