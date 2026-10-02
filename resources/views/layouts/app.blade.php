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

    @stack('scripts')
</body>
</html>