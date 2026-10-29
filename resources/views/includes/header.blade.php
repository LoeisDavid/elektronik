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
    <div class="flex items-center gap-3">

        {{-- Tombol Draft Transaksi (Hanya di halaman Kasir saat kasir dibuka) --}}
        @if(request()->routeIs('POS.kasir'))
            <button
                type="button"
                id="btnHeaderDraft"
                onclick="modalDraftTransaksi()"
                class="{{ !empty($isKasirOpen) ? 'flex' : 'hidden' }} items-center gap-2 bg-white text-black text-xs font-semibold px-3.5 py-1.5 rounded-lg border border-black transition-all cursor-pointer hover:border-primary-db hover:text-primary-db hover:bg-[#EEF2FF] hover:shadow-sm active:scale-[0.97] focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-db/40">
                Draft Transaksi
                <span id="DraftCountBadgeHeader" class="hidden min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[10px] font-bold items-center justify-center">0</span>
            </button>
        @endif

        {{-- User Info --}}
        <div class="flex items-center gap-2.5 py-1 px-1.5 rounded-lg" title="User: {{ Auth::user()->USERNAME ?? 'User' }}">
            <span class="text-base font-medium italic text-primary-db tracking-tight">
                {{ Auth::user()->USERNAME ?? 'User' }}
            </span>
        </div>

    </div>
</header>