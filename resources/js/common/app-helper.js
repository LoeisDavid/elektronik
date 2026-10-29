/**
 * =============================================================================
 * App Helper - Reusable Core Utilities
 * Menangani Toast, Format Rupiah, Select2 Dropdown, Action Menu, & Modal
 * =============================================================================
 */

// 1. Toast Notification
export function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');

    const types = {
        success: {
            bg: 'bg-[#d1fadf]',
            border: 'border-[#22c55e]',
            text: 'text-[#15803d]',
            icon: `<svg class="w-7 h-7 text-[#15803d] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`
        },
        warning: {
            bg: 'bg-[#fef9c3]',
            border: 'border-[#eab308]',
            text: 'text-[#a16207]',
            icon: `<svg class="w-7 h-7 text-[#a16207] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>`
        },
        error: {
            bg: 'bg-[#ffe4e6]',
            border: 'border-[#f43f5e]',
            text: 'text-[#be123c]',
            icon: `<svg class="w-7 h-7 text-[#be123c] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>`
        }
    };

    const config = types[type] || types.success;

    toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3.5 rounded-xl border-2 ${config.bg} ${config.border} ${config.text} shadow-lg transition-all duration-300 transform -translate-y-10 opacity-0 cursor-pointer`;

    toast.innerHTML = `
        ${config.icon}
        <span class="text-sm font-bold leading-tight">${message}</span>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.remove('-translate-y-10', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    }, 10);

    const removeToast = () => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('-translate-y-10', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    };

    toast.addEventListener('click', removeToast);
    setTimeout(removeToast, 3500);
}

// 2. Format Rupiah
export function formatRupiah(number) {
    if (number === null || number === undefined || isNaN(number) || number === '') return 'Rp 0';
    return 'Rp ' + Math.round(Number(number)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

export function parseRupiah(str) {
    if (!str) return 0;
    const clean = str.toString().replace(/\D/g, '');
    return clean ? parseFloat(clean) : 0;
}

export function formatInputRupiah(input) {
    if (!input) return;
    let rawVal = input.value.replace(/\D/g, '');
    if (!rawVal) {
        input.value = '';
        return;
    }
    input.value = formatRupiah(parseFloat(rawVal) || 0);
}

export function initRupiahInputs() {
    document.querySelectorAll('.input-rupiah, input[data-type="rupiah"]').forEach(function(input) {
        if (!input.dataset.rupiahBound) {
            input.dataset.rupiahBound = 'true';
            input.addEventListener('input', function() {
                formatInputRupiah(this);
            });
            if (input.value && !input.value.toString().trim().startsWith('Rp')) {
                let num = parseFloat(input.value.toString().replace(/\D/g, ''));
                if (!isNaN(num) && num > 0) {
                    input.value = formatRupiah(num);
                }
            }
        }
    });
}

// 3. Searchable Selects (jQuery Select2)
export function initSearchableSelects(context = document) {
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        jQuery(context).find('select.searchable-select, select.select2, select[data-searchable="true"]').each(function() {
            const $select = jQuery(this);
            if (!$select.hasClass("select2-hidden-accessible")) {
                const parentModal = $select.closest('.fixed, .modal, [id*="Modal"]');
                $select.select2({
                    width: '100%',
                    placeholder: $select.find('option[disabled]').text() || $select.data('placeholder') || 'Pilih...',
                    allowClear: Boolean($select.data('allow-clear')),
                    dropdownParent: parentModal.length ? parentModal : jQuery(document.body)
                });

                $select.on('select2:select', function () {
                    this.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }
        });
    }
}

// 4. Modal Helpers
export function openModal(modalId) {
    const modal = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    initSearchableSelects(modal);
}

export function closeModal(modalId) {
    const modal = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
    if (!modal) return;
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

// 5. Action Menu (3-dots) Dropdown
export function toggleActionMenu(button) {
    const menu = button.parentElement.querySelector('.action-menu');
    if (!menu) return;

    document.querySelectorAll('.action-menu').forEach(function (item) {
        if (item !== menu) item.classList.add('hidden');
    });

    menu.classList.toggle('hidden');
}

// 6. Global Click Outside
export function initGlobalClickOutside() {
    document.addEventListener('click', function (event) {
        // Tutup semua action menu jika klik di luar
        if (!event.target.closest('.action-menu') && !event.target.closest('[onclick*="toggleActionMenu"]') && !event.target.closest('.btn-action-menu')) {
            document.querySelectorAll('.action-menu').forEach(function (menu) {
                menu.classList.add('hidden');
            });
        }
    });
}

// 7. Double Submit & Clean Rupiah on Form Submit
export function initDoubleSubmitProtection() {
    document.addEventListener('submit', function (e) {
        if (e.defaultPrevented) return;
        const form = e.target;
        if (!form.matches('form')) return;

        // Jangan kunci form yang membuka tab baru (seperti Print Laporan / Struk PDF dengan target="_blank")
        if ((form.getAttribute('target') || '').toLowerCase() === '_blank' || form.hasAttribute('data-no-lock')) {
            return;
        }

        if (form.dataset.submitting === 'true') {
            e.preventDefault();
            return;
        }

        // Bersihkan format Rupiah sebelum dikirim ke backend Laravel controller
        form.querySelectorAll('.input-rupiah, input[data-type="rupiah"]').forEach(input => {
            if (input.value) {
                input.value = input.value.replace(/\D/g, '');
            }
        });

        form.dataset.submitting = 'true';

        const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        buttons.forEach(button => {
            button.disabled = true;
            button.classList.add('opacity-75', 'cursor-not-allowed');
        });
    });
}

// Expose to window for backward compatibility with inline calls
if (typeof window !== 'undefined') {
    window.showToast = showToast;
    window.formatRupiah = formatRupiah;
    window.parseRupiah = parseRupiah;
    window.formatInputRupiah = formatInputRupiah;
    window.initRupiahInputs = initRupiahInputs;
    window.initSearchableSelects = initSearchableSelects;
    window.openModal = openModal;
    window.closeModal = closeModal;
    window.toggleActionMenu = toggleActionMenu;
}

// Auto init on DOM ready
document.addEventListener('DOMContentLoaded', function () {
    initRupiahInputs();
    initSearchableSelects();
    initGlobalClickOutside();
    initDoubleSubmitProtection();
});
