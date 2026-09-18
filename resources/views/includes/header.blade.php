{{-- Top Application Header --}}
<header class="h-12 bg-white border-b border-[#E2E8F0] px-6 flex items-center justify-between flex-shrink-0 z-10">
    {{-- Left: Page Title --}}
    <h1 class="text-base font-semibold text-[#2D3748] tracking-tight">
        @yield('header_title', 'Point Of Sales')
    </h1>

    {{-- Right: User Info with Avatar (Bigger & Bolder, Click to route back to login) --}}
    <a href="{{ route('login') }}" class="flex items-center gap-3 hover:opacity-85 transition-all cursor-pointer py-1 px-2 rounded-lg hover:bg-gray-100" title="Keluar / Ke Login">
        <span class="text-base font-bold text-[#1A1F36] tracking-tight">
            User 2
        </span>
        
        {{-- Circular Avatar (Bigger) --}}
        <div class="w-9 h-9 rounded-full overflow-hidden border-2 border-[#E2E8F0] bg-gray-100 flex-shrink-0 flex items-center justify-center shadow-xs">
            <img 
                src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=80&auto=format&fit=crop&q=80" 
                alt="User Avatar"
                class="w-full h-full object-cover"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            />
            <div class="w-full h-full bg-[#DBEAFE] text-[#2D8CFF] font-bold text-sm hidden items-center justify-center">
                U2
            </div>
        </div>
    </a>
</header>
