<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Point Of Sales') - Elektronik</title>

    <!-- Google Fonts: Inter (Main, Sub, Text) & IBM Plex Sans (Numbers) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Alpine.js Collapse Plugin & Core for lightweight UI reactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-screen w-screen overflow-hidden bg-light-bg font-sans text-heading-dark antialiased select-none">
    <div class="flex h-screen w-full overflow-hidden">
        {{-- Left Sidebar Navigation --}}
        @include('includes.sidebar')

        {{-- Main Application Area --}}
        <div class="flex flex-1 flex-col h-screen overflow-hidden min-w-0">
            {{-- Top Header Bar --}}
            @include('includes.header')

            {{-- Page Content --}}
            <main class="flex-1 overflow-hidden bg-[#CECECE]/60">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>