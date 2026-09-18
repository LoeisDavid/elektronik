<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NamaToko - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f3f5f9] min-h-screen flex flex-col items-center justify-center p-4 font-sans text-slate-900 select-none">

    <!-- Store Title -->
    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-black mb-10">
        NamaToko
    </h1>

    <!-- Login Card -->
    <div class="w-full max-w-[480px] bg-[#dde5ee] px-10 py-12 shadow-[0_20px_45px_rgba(0,0,0,0.18)] rounded-sm">
        
        @if ($errors->any())
            <div class="mb-6 p-3 bg-red-100/80 border-l-4 border-red-500 text-red-700 text-sm rounded-r">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-7">
            @csrf

            <!-- Username Field -->
            <div>
                <label for="USERNAME" class="block text-slate-900 text-sm font-semibold mb-2">
                    Username:
                </label>
                <input 
                    type="text" 
                    id="USERNAME" 
                    name="USERNAME" 
                    value="{{ old('USERNAME', 'admin') }}" 
                    required 
                    autofocus
                    autocomplete="username"
                    class="w-full bg-transparent border-b-2 border-slate-700 py-1.5 px-0.5 text-slate-900 focus:outline-none focus:border-black text-base transition-colors"
                >
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-slate-900 text-sm font-semibold mb-2">
                    Password:
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    value="admin123"
                    required 
                    autocomplete="current-password"
                    class="w-full bg-transparent border-b-2 border-slate-700 py-1.5 px-0.5 text-slate-900 focus:outline-none focus:border-black text-base transition-colors"
                >
            </div>

            <!-- Login Button -->
            <div class="pt-4 flex justify-center">
                <button 
                    type="submit" 
                    class="bg-white text-black font-extrabold text-sm tracking-wider px-14 py-2.5 shadow-sm hover:bg-slate-100 active:scale-95 transition-all cursor-pointer"
                >
                    LOGIN
                </button>
            </div>
        </form>

        <!-- Dummy Info Badge -->
        <div class="mt-8 text-center text-xs text-slate-500">
            Akun dummy terisi otomatis: <span class="font-semibold text-slate-700">admin</span> / <span class="font-semibold text-slate-700">admin123</span>
        </div>
    </div>

</body>
</html>
