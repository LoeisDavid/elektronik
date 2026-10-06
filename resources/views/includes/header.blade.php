{{-- Top Application Header --}}
<header class="h-12 bg-white border-b border-[#E2E8F0] px-6 flex items-center justify-between flex-shrink-0 z-10">
    
    {{-- Left Section: Judul Page & Tombol Tutup Kasir --}}
    <div class="flex items-center gap-3">
        <h1 class="text-2xl font-bold text-primary-db tracking-tight">
            @yield('header_title')
        </h1>

        {{-- Tombol Tutup Kasir (Hanya tampil jika kasir sudah dibuka) --}}
        <button
            type="button"
            id="btnHeaderTutupKasir"
            onclick="modalTutupKasir()"
            class="{{ !empty($isKasirOpen) ? '' : 'hidden ' }}bg-primary-db hover:bg-primary-sdb text-secondary-bw text-xs font-medium px-3 py-1 rounded-lg transition-colors cursor-pointer">
            Tutup kasir
        </button>
    </div>

    {{-- Right Section: Action Buttons & User Profile --}}
    <div class="flex items-center gap-4">

        {{-- User Info dengan Avatar --}}
        <a href="{{ route('login') }}" class="flex items-center gap-2.5 hover:opacity-85 transition-all cursor-pointer py-1 px-1.5 rounded-lg hover:bg-gray-100" title="Keluar / Ke Login">
            <span class="text-base font-medium italic text-primary-db tracking-tight">
                User 2
            </span>
            
            {{-- Circular Avatar --}}
            <div class="w-8 h-8 rounded-full overflow-hidden border border-[#E2E8F0] bg-gray-100 shrink-0 flex items-center justify-center shadow-xs">
                <img 
                    src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=80&auto=format&fit=crop&q=80" 
                    alt="User Avatar"
                    class="w-full h-full object-cover"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                />
                <div class="w-full h-full bg-[#DBEAFE] text-[#2D8CFF] font-bold text-xs hidden items-center justify-center">
                    U2
                </div>
            </div>
        </a>

    </div>
</header>