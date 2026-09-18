<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NamaToko</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f0f4f8] min-h-screen flex flex-col items-center justify-center font-sans text-slate-800">
    <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full text-center space-y-4">
        <h1 class="text-2xl font-bold text-slate-900">Halaman Dashboard</h1>
        <p class="text-slate-600">Berhasil masuk sebagai: <span class="font-semibold text-slate-900">{{ Auth::user()->USERNAME ?? 'User' }}</span></p>
        
        <div class="pt-4 border-t border-slate-200">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded text-sm hover:bg-slate-700 transition">
                    Logout
                </button>
            </form>
        </div>
    </div>
</body>
</html>
