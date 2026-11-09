/**
 * =============================================================================
 * Kasir Shift Management (Buka & Tutup Kasir)
 * =============================================================================
 */

import { state, formatRupiah, formatInputRupiah } from './config.js';
import { renderCart } from './cart.js';
import { updateDraftBadgeHeader, hapusDraft } from './draft.js';

// Buka Modal Buka Kasir
export function bukaModalBukaKasir() {
    const modal = document.getElementById('modalBukaKasir');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            const inp = document.getElementById('kasOpening');
            if (inp) inp.focus();
        }, 100);
    }
}

// Tutup Modal Buka Kasir
export function tutupModalBukaKasir() {
    const modal = document.getElementById('modalBukaKasir');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// Simpan Buka Kasir via Backend
export function simpanBukaKasir() {
    const rawKasOpening = document.getElementById('kasOpening').value;
    const cleanOpening = parseFloat(rawKasOpening.replace(/\D/g, '')) || 0;

    if (rawKasOpening === '' || cleanOpening < 0) {
        alert('Silakan masukkan nominal Kas Opening yang valid!');
        return;
    }

    const btn = document.getElementById('btnEksekusiBukaKasir');
    if (btn) {
        btn.disabled = true;
        btn.innerText = 'Membuka...';
    }

    fetch(state.routes.bukaKasir, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': state.csrfToken
        },
        body: JSON.stringify({
            kas_opening: cleanOpening
        })
    })
    .then(res => res.json())
    .then(data => {
        if (btn) {
            btn.disabled = false;
            btn.innerText = 'Buka kasir';
        }

        if (data.success) {
            state.isKasirOpen = true;
            window.isKasirOpen = true;

            // Sembunyikan tampilan awal "Buka Kasir"
            const viewAwal = document.getElementById('viewBukaKasir');
            if (viewAwal) {
                viewAwal.classList.add('hidden');
                viewAwal.classList.remove('flex');
            }

            // Tampilkan area utama (scan & keranjang) & bar total
            const mainCenter = document.getElementById('mainCenterSection');
            if (mainCenter) {
                mainCenter.classList.remove('hidden');
                mainCenter.classList.add('flex');
            }

            const rightPanel = document.getElementById('rightOrderPanel');
            if (rightPanel) {
                rightPanel.classList.remove('hidden');
                rightPanel.classList.add('flex');
            }

            // Tampilkan tombol Tutup Kasir di Header
            const btnHeader = document.getElementById('btnHeaderTutupKasir');
            if (btnHeader) {
                btnHeader.classList.remove('hidden');
            }

            // Tampilkan tombol Draft Transaksi di Header
            const btnHeaderDraft = document.getElementById('btnHeaderDraft');
            if (btnHeaderDraft) {
                btnHeaderDraft.classList.remove('hidden');
                btnHeaderDraft.classList.add('flex');
            }
            updateDraftBadgeHeader();

            tutupModalBukaKasir();
            renderCart();

            const inputScan = document.getElementById('inputScanUtama');
            if (inputScan) inputScan.focus();
        } else {
            alert(data.message || 'Gagal membuka kasir!');
        }
    })
    .catch(err => {
        if (btn) {
            btn.disabled = false;
            btn.innerText = 'Buka kasir';
        }
        console.error('Error buka kasir:', err);
        alert('Terjadi kesalahan saat membuka kasir.');
    });
}

let currentExpectedCashEnding = 0;

// Buka Modal Tutup Kasir & Muat Data Kas
export function modalTutupKasir() {
    // Reset form input & selisih
    const inputKas = document.getElementById('inputKasAkhirTunai');
    if (inputKas) inputKas.value = '';

    const labelSelisih = document.getElementById('labelTutupSelisih');
    if (labelSelisih) {
        labelSelisih.innerText = 'Rp 0';
        labelSelisih.className = 'text-sm font-bold text-gray-500';
    }

    const boxSelisih = document.getElementById('boxTutupSelisih');
    if (boxSelisih) {
        boxSelisih.className = 'p-3 rounded-xl border border-gray-200 bg-gray-50 transition-all';
    }

    const descSelisih = document.getElementById('descTutupSelisih');
    if (descSelisih) {
        descSelisih.innerText = 'Masukkan nominal uang fisik tunai di atas untuk mengetahui selisih.';
        descSelisih.className = 'text-[11px] text-gray-500 mt-0.5';
    }

    currentExpectedCashEnding = 0;

    fetch(state.routes.kasStatus)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.kas) {
                const setEl = (id, val) => {
                    const el = document.getElementById(id);
                    if (el) el.innerText = val;
                };
                setEl('labelTutupSaldoAwal', formatRupiah(data.kas.saldo_awal));
                setEl('labelTutupPenjualanTunai', formatRupiah(data.kas.penjualan_tunai));
                setEl('labelTutupPenjualanNonTunai', formatRupiah(data.kas.penjualan_non_tunai));
                setEl('labelTutupMutasiKeluar', formatRupiah(data.kas.mutasi_keluar));
                setEl('labelTutupSaldoAkhir', formatRupiah(data.kas.saldo_akhir));

                currentExpectedCashEnding = Number(data.kas.saldo_akhir) || 0;
            }
        })
        .catch(err => console.error('Error load kas status:', err));

    const modal = document.getElementById('modalTutupKasir');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            const input = document.getElementById('inputKasAkhirTunai');
            if (input) input.focus();
        }, 120);
    }
}

// Tutup Modal Tutup Kasir
export function tutupModalTutupKasir() {
    const modal = document.getElementById('modalTutupKasir');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}

// Format input rupiah dan hitung selisih secara langsung
export function formatDanHitungSelisihTutupKasir(el) {
    if (!el) return;
    formatInputRupiah(el);
    const rawVal = el.value.replace(/\D/g, '');

    const labelSelisih = document.getElementById('labelTutupSelisih');
    const boxSelisih = document.getElementById('boxTutupSelisih');
    const descSelisih = document.getElementById('descTutupSelisih');

    if (rawVal === '') {
        if (labelSelisih) {
            labelSelisih.innerText = 'Rp 0';
            labelSelisih.className = 'text-sm font-bold text-gray-500';
        }
        if (boxSelisih) {
            boxSelisih.className = 'p-3 rounded-xl border border-gray-200 bg-gray-50 transition-all';
        }
        if (descSelisih) {
            descSelisih.innerText = 'Masukkan nominal uang fisik tunai di atas untuk mengetahui selisih.';
            descSelisih.className = 'text-[11px] text-gray-500 mt-0.5';
        }
        return;
    }

    const actualCash = parseFloat(rawVal) || 0;
    const diff = actualCash - currentExpectedCashEnding;

    if (diff === 0) {
        if (labelSelisih) {
            labelSelisih.innerText = 'Rp 0';
            labelSelisih.className = 'text-sm font-bold text-emerald-600';
        }
        if (boxSelisih) {
            boxSelisih.className = 'p-3 rounded-xl border border-emerald-300 bg-emerald-50 transition-all';
        }
        if (descSelisih) {
            descSelisih.innerText = '✓ Pas / Sesuai dengan pembukuan kasir sistem.';
            descSelisih.className = 'text-[11px] text-emerald-700 font-semibold mt-0.5';
        }
    } else if (diff > 0) {
        if (labelSelisih) {
            labelSelisih.innerText = formatRupiah(diff);
            labelSelisih.className = 'text-sm font-bold text-blue-600';
        }
        if (boxSelisih) {
            boxSelisih.className = 'p-3 rounded-xl border border-blue-300 bg-blue-50 transition-all';
        }
        if (descSelisih) {
            descSelisih.innerText = `Lebih (Surplus kas fisik lebih banyak ${formatRupiah(diff)} dibanding sistem).`;
            descSelisih.className = 'text-[11px] text-blue-700 font-semibold mt-0.5';
        }
    } else {
        const absDiff = Math.abs(diff);
        if (labelSelisih) {
            labelSelisih.innerText = formatRupiah(absDiff);
            labelSelisih.className = 'text-sm font-bold text-red-600';
        }
        if (boxSelisih) {
            boxSelisih.className = 'p-3 rounded-xl border border-red-300 bg-red-50 transition-all';
        }
        if (descSelisih) {
            descSelisih.innerText = `Kurang (Defisit kas fisik kurang ${formatRupiah(absDiff)} dibanding sistem).`;
            descSelisih.className = 'text-[11px] text-red-700 font-semibold mt-0.5';
        }
    }
}

// Clear input uang akhir fisik
export function clearInputKasAkhir() {
    const input = document.getElementById('inputKasAkhirTunai');
    if (input) {
        input.value = '';
        formatDanHitungSelisihTutupKasir(input);
        input.focus();
    }
}

// Proses Tutup Kasir via Backend
export function prosesTutupKasir() {
    const inputKas = document.getElementById('inputKasAkhirTunai');
    const rawVal = inputKas ? inputKas.value.replace(/\D/g, '') : '';

    if (!inputKas || rawVal === '') {
        alert('Silakan masukkan nominal akhir uang tunai fisik di kasir terlebih dahulu!');
        if (inputKas) inputKas.focus();
        return;
    }

    const actualCash = parseFloat(rawVal) || 0;
    const diff = actualCash - currentExpectedCashEnding;

    if (diff !== 0) {
        const pesanSelisih = diff < 0
            ? `Terdapat SELISIH KURANG kas fisik sebesar ${formatRupiah(Math.abs(diff))}.\n\nApakah Anda yakin ingin tetap menutup kasir?`
            : `Terdapat SELISIH LEBIH kas fisik sebesar ${formatRupiah(diff)}.\n\nApakah Anda yakin ingin tetap menutup kasir?`;

        if (!confirm(pesanSelisih)) {
            return;
        }
    }

    const btn = document.getElementById('btnEksekusiTutupKasir');
    if (btn) {
        btn.disabled = true;
        btn.innerText = 'Menutup...';
    }

    fetch(state.routes.tutupKasir, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': state.csrfToken
        },
        body: JSON.stringify({
            nominal_akhir_tunai: actualCash
        })
    })
    .then(res => res.json())
    .then(data => {
        if (btn) {
            btn.disabled = false;
            btn.innerText = 'Tutup';
        }

        if (data.success) {
            state.isKasirOpen = false;
            window.isKasirOpen = false;
            state.cartData = {};
            state.voucherAmount = 0;
            state.discountPercent = 0;
            state.currentGrandTotal = 0;

            // Tampilkan tampilan awal "Buka Kasir"
            const viewAwal = document.getElementById('viewBukaKasir');
            if (viewAwal) {
                viewAwal.classList.remove('hidden');
                viewAwal.classList.add('flex');
            }

            // Sembunyikan area kasir aktif
            const mainCenter = document.getElementById('mainCenterSection');
            if (mainCenter) {
                mainCenter.classList.add('hidden');
                mainCenter.classList.remove('flex');
            }

            const rightPanel = document.getElementById('rightOrderPanel');
            if (rightPanel) {
                rightPanel.classList.add('hidden');
                rightPanel.classList.remove('flex');
            }

            // Sembunyikan tombol Tutup Kasir di Header
            const btnHeader = document.getElementById('btnHeaderTutupKasir');
            if (btnHeader) {
                btnHeader.classList.add('hidden');
            }

            // Sembunyikan tombol Draft Transaksi di Header
            const btnHeaderDraft = document.getElementById('btnHeaderDraft');
            if (btnHeaderDraft) {
                btnHeaderDraft.classList.add('hidden');
                btnHeaderDraft.classList.remove('flex');
            }

            tutupModalTutupKasir();
            hapusDraft();

            // Jika penutupan kasir dipicu oleh niat Logout, langsung logout otomatis!
            if (window.__shouldLogoutAfterTutup) {
                const formLogout = document.getElementById('formSidebarLogout');
                if (formLogout) {
                    if (typeof window.showToast === 'function') {
                        window.showToast('Kasir berhasil ditutup. Mengalihkan keluar...', 'success');
                    }
                    setTimeout(() => {
                        formLogout.submit();
                    }, 500);
                    return;
                }
            }

            if (typeof window.showToast === 'function') {
                window.showToast('Kasir berhasil ditutup.', 'success');
            } else {
                alert('Kasir berhasil ditutup.');
            }
        } else {
            alert(data.message || 'Gagal menutup kasir!');
        }
    })
    .catch(err => {
        if (btn) {
            btn.disabled = false;
            btn.innerText = 'Tutup';
        }
        console.error('Error tutup kasir:', err);
        alert('Terjadi kesalahan saat menutup kasir.');
    });
}
