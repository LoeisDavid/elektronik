<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NamaToko</title>

    <!-- Google Fonts: Inter & IBM Plex Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen w-full bg-secondary-bw font-sans flex flex-col items-center justify-center p-4 antialiased">
    
    {{-- Main Container Centered --}}
    <div class="w-full max-w-md flex flex-col items-center">
        
        {{-- Store Title --}}
        <h1 class="text-3xl md:text-4xl font-bold text-[#000000] tracking-tight mb-8">
            NamaToko
        </h1>

        {{-- Login Card (#dde5ee) --}}
        <div class="w-full bg-white p-8 md:p-10 shadow-[0_12px_30px_rgba(0,0,0,0.18)]">
            
            {{-- 
                BACKEND INTEGRATION NOTE:
                Change action to route('login.post') with method="POST" and add @csrf when hooking up backend auth.
            --}}
            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf

                @if ($errors->any())
                    <div class="p-3 bg-red-100/90 border-l-4 border-red-500 text-red-700 text-sm rounded-r">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- Username Input --}}
                <div class="space-y-2">
                    <label for="username" class="block text-sm font-medium text-black">
                        Username:
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        value="{{ old('username', old('USERNAME')) }}"
                        required
                        autocomplete="username"
                        class="w-full bg-transparent border-b-2 focus:border-black py-1 px-0.5 text-base text-black outline-none transition-colors duration-150"
                    />
                </div>

                {{-- Password Input --}}
                <div class="space-y-2 pt-2">
                    <label for="password" class="block text-sm font-medium text-black">
                        Password:
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        autocomplete="current-password"
                        class="w-full bg-transparent border-b-2 focus:border-black py-1 px-0.5 text-base text-black outline-none transition-colors duration-150"
                    />
                </div>

                {{-- Login Button --}}
                <div class="pt-6 flex justify-center">
                    <button 
                        type="submit" 
                        class="bg-primary-db text-white font-bold px-12 py-2 text-base tracking-wider shadow-sm hover:bg-primary-sdb transition-all cursor-pointer focus:outline-none w-full"
                    >
                        LOGIN
                    </button>
                </div>

            </form>
        </div>

    </div>

</body>
</html>
