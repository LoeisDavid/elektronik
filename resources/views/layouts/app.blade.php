<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- WAJIB: CSRF Token untuk keamanan Form/AJAX Laravel -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Point Of Sales') - {{ config('perusahaan.nama_perusahaan', 'Elektronik') }}</title>

    <script>
        window.PERUSAHAAN_CONFIG = @json(config('perusahaan'));
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- jQuery & Select2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('css')
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

    {{-- Flash Toast Notification Session Trigger --}}
    @if (session('success') || session('warning') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if (session('success'))
                    if (window.showToast) window.showToast("{{ session('success') }}", 'success');
                @endif
                @if (session('warning'))
                    if (window.showToast) window.showToast("{{ session('warning') }}", 'warning');
                @endif
                @if (session('error'))
                    if (window.showToast) window.showToast("{{ session('error') }}", 'error');
                @endif
            });
        </script>
    @endif

    {{-- Script Khusus Tiap Halaman --}}
    @yield('js')
    @stack('scripts')
</body>
</html>