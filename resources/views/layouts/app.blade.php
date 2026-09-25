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
<body class="h-screen w-screen bg-light-bg font-montserrat text-heading-dark antialiased select-none">
    
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

    <!-- Import Script Generic CRUD -->
    <script src="{{ asset('js/crud.js') }}"></script>
    
    @stack('scripts')
</body>
</html>