<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- WAJIB: CSRF Token untuk keamanan Form/AJAX Laravel -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Point Of Sales') - Elektronik</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- jQuery & Select2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Styling Custom Modern untuk Select2 -->
    <style>
        .select2-container {
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single {
            background-color: #ffffff;
            border: 1px solid #94a3b8;
            border-radius: 0.75rem; /* rounded-xl */
            height: 42px;
            padding: 5px 12px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            font-family: inherit;
            font-size: 0.875rem;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #101D36;
            box-shadow: 0 0 0 1px #101D36;
            outline: none;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #0f172a;
            line-height: normal;
            padding-left: 0;
            padding-right: 24px;
            font-weight: 500;
            width: 100%;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8;
            font-weight: 400;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            top: 0;
            right: 12px;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #64748b transparent transparent transparent;
            border-width: 5px 4px 0 4px;
        }
        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #64748b transparent;
            border-width: 0 4px 5px 4px;
        }
        .select2-dropdown {
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            font-family: inherit;
            font-size: 0.875rem;
            overflow: hidden;
            z-index: 999999 !important;
        }
        .select2-search--dropdown {
            padding: 8px 10px;
        }
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            padding: 6px 12px;
            font-family: inherit;
            font-size: 0.8125rem;
            outline: none;
            width: 100%;
        }
        .select2-search--dropdown .select2-search__field:focus {
            border-color: #101D36;
            box-shadow: 0 0 0 1px #101D36;
        }
        .select2-results__option {
            padding: 8px 14px;
            font-size: 0.875rem;
            color: #1e293b;
            cursor: pointer;
        }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #101D36;
            color: #ffffff;
        }
        .select2-container--default .select2-results__option--selected {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 600;
        }
        .select2-container--default .select2-selection--single .select2-selection__clear {
            margin-right: 18px;
            color: #94a3b8;
            font-size: 1.1rem;
            font-weight: bold;
        }
        .select2-container--default .select2-selection--single .select2-selection__clear:hover {
            color: #ef4444;
        }
    </style>

    @stack('styles')
</head>
<body class="h-screen w-screen bg-light-bg font-montserrat text-heading-dark antialiased relative">

    {{-- ========================================================= --}}
    {{-- TOAST CONTAINER (Melayang di Tengah Atas) --}}
    {{-- ========================================================= --}}
    <div id="toast-container" class="fixed top-5 left-1/2 -translate-x-1/2 z-9999 flex flex-col gap-3 pointer-events-none w-full max-w-sm px-4"></div>

    <!-- Wrapper Utama dengan Flexbox Horizontal -->
    <main class="flex h-screen w-screen overflow-hidden">
        
        <!-- Sidebar -->
        @include('includes.sidebar')

        <!-- Main Content (Header + Content Vertikal) -->
        <section class="flex-1 flex flex-col h-screen overflow-hidden min-w-0">
            <!-- Header -->
            @include('includes.header')
            
            <!-- Page Content -->
            <div class="flex-1 overflow-hidden flex flex-col">
                @yield('content')
            </div>
        </section>

    </main>

    {{-- ========================================================= --}}
    {{-- SCRIPT VANILLA JS TOAST NOTIFICATION --}}
    {{-- ========================================================= --}}
    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            // Template Ikon & Warna Sesuai Gambar
            const styles = {
                success: {
                    bg: 'bg-[#d1fadf]', // Hijau Muda
                    border: 'border-[#22c55e]', // Hijau
                    text: 'text-[#15803d]',
                    icon: `<svg class="w-7 h-7 text-[#15803d] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`
                },
                warning: {
                    bg: 'bg-[#fef9c3]', // Kuning/Oranye Muda
                    border: 'border-[#eab308]', // Kuning/Oranye
                    text: 'text-[#a16207]',
                    icon: `<svg class="w-7 h-7 text-[#a16207] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>`
                },
                error: {
                    bg: 'bg-[#ffe4e6]', // Merah Muda
                    border: 'border-[#f43f5e]', // Merah
                    text: 'text-[#be123c]',
                    icon: `<svg class="w-7 h-7 text-[#be123c] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>`
                }
            };

            const config = styles[type] || styles.success;

            // Buat Elemen Toast
            const toast = document.createElement('div');
            toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3.5 rounded-xl border-2 ${config.bg} ${config.border} ${config.text} shadow-lg transition-all duration-300 transform -translate-y-10 opacity-0`;
            
            toast.innerHTML = `
                ${config.icon}
                <span class="text-sm font-bold leading-tight">${message}</span>
            `;

            container.appendChild(toast);

            // Animasi Masuk (Slide Down)
            setTimeout(() => {
                toast.classList.remove('-translate-y-10', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            }, 10);

            // Hilang Otomatis setelah 3 detik
            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('-translate-y-10', 'opacity-0');
                
                // Hapus dari DOM setelah animasi selesai
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }

        // Trigger Otomatis Flash Session dari Laravel Controller
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif

            @if (session('warning'))
                showToast("{{ session('warning') }}", 'warning');
            @endif

            @if (session('error'))
                showToast("{{ session('error') }}", 'error');
            @endif
        });
    </script>
    <script>
        // =========================================================
        // HELPER GLOBAL FORMAT RUPIAH
        // =========================================================
        function formatRupiah(number) {
            if (number === null || number === undefined || isNaN(number) || number === '') return 'Rp 0';
            return 'Rp ' + Math.round(Number(number)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function formatInputRupiah(input) {
            if (!input) return;
            let rawVal = input.value.replace(/\D/g, '');
            if (!rawVal) {
                input.value = '';
                return;
            }
            let numVal = parseFloat(rawVal) || 0;
            input.value = formatRupiah(numVal);
        }

        // Inisialisasi otomatis semua field nominal / rupiah
        function initRupiahInputs() {
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

        // Inisialisasi otomatis semua selectbox dengan search menggunakan jQuery Select2
        function initSearchableSelects() {
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
                $('select.searchable-select, select.select2, select[data-searchable="true"]').each(function() {
                    let $select = $(this);
                    if (!$select.hasClass("select2-hidden-accessible")) {
                        let parentModal = $select.closest('.fixed, .modal, [id*="Modal"]');
                        $select.select2({
                            width: '100%',
                            placeholder: $select.find('option[disabled]').text() || $select.data('placeholder') || 'Pilih...',
                            allowClear: Boolean($select.data('allow-clear')),
                            dropdownParent: parentModal.length ? parentModal : $(document.body)
                        });

                        $select.on('select2:select', function () {
                            this.dispatchEvent(new Event('change', { bubbles: true }));
                        });
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            initRupiahInputs();
            initSearchableSelects();
        });

        document.addEventListener('submit', function (e) {
            const form = e.target;

            if (!form.matches('form')) {
                return;
            }

            // Cegah submit kedua
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

            // Disable tombol submit
            const buttons = form.querySelectorAll(
                'button[type="submit"], input[type="submit"]'
            );

            buttons.forEach(button => {
                button.disabled = true;
            });
        });
    </script>

    @stack('scripts')
</body>
</html>