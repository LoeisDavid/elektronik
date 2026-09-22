{{-- Left Sidebar Navigation --}}
<aside class="w-48 flex-shrink-0 bg-primary-db text-secondary-bw flex flex-col h-screen select-none z-20 shadow-md">
    
    {{-- Hamburger Top Bar --}}
    <div class="h-12 flex items-center px-4 flex-shrink-0">
        <button type="button" class="text-secondary-bw hover:opacity-80 transition-opacity focus:outline-none cursor-pointer" aria-label="Toggle Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </div>

    {{-- Nav Links --}}
    <div class="flex-1 overflow-y-auto">
        
        {{-- ==================== POS SECTION ==================== --}}
        <div>
            {{-- POS Header Button --}}
            <div class="w-full flex items-center gap-2 px-4 py-2.5 font-bold text-secondary-bw border-secondary-bw border-b text-base tracking-wide">
                {{-- Shopping Cart Icon --}}
                <svg class="w-5 h-5 text-secondary-bw flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
                <span class="underline decoration-1 underline-offset-2">POS</span>
            </div>

            {{-- POS Sub-items Container --}}
            <div class="bg-primary-db py-1">
                @php
                    $posMenus = [
                        ['name' => 'Kasir', 'route' => 'POS.kasir'],
                        ['name' => 'Transaksi', 'route' => 'transaksi'],
                        ['name' => 'User', 'route' => 'user'],
                        ['name' => 'Sales', 'route' => 'sales'],
                        ['name' => 'Customer', 'route' => 'customer'],
                        ['name' => 'Laporan', 'route' => 'laporan'],
                    ];
                @endphp

                @foreach ($posMenus as $menu)
                    @php
                        $isActive = request()->routeIs($menu['route']);
                    @endphp
                    <a 
                        href="{{ Route::has($menu['route']) ? route($menu['route']) : '#' }}"
                        class="flex items-center w-full px-5 py-2 text-sm italic font-medium text-white transition-colors cursor-pointer {{ $isActive ? 'bg-primary-sdb font-semibold' : 'hover:bg-primary-db' }}"
                    >
                        {{-- Active Triangle Indicator --}}
                        @if ($isActive)
                            <span class="mr-2 text-[10px] leading-none select-none">►</span>
                        @else
                            <span class="w-[18px]"></span>
                        @endif
                        
                        {{-- Label --}}
                        <span class="{{ $isActive ? 'underline underline-offset-2 decoration-1' : '' }}">
                            {{ $menu['name'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ==================== INVENTORY SECTION ==================== --}}
        <div>
            {{-- Inventory Header Button --}}
            <div class="w-full flex items-center gap-2 px-4 py-2.5 font-bold text-secondary-bw border-secondary-bw border-b text-base tracking-wide">
                {{-- Box / Inventory Icon --}}
                <svg class="w-5 h-5 text-secondary-bw flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 2H4c-1 0-2 .9-2 2v3.01c0 .72.43 1.34 1 1.69V20c0 1.1 1.1 2 2 2h14c.9 0 2-.9 2-2V8.7c.57-.35 1-.97 1-1.69V4c0-1.1-1-2-2-2zm-1 18H5V9h14v11zm1-13H4V4h16v3z"/>
                </svg>
                <span>Inventory</span>
            </div>

            {{-- Inventory Sub-items Container --}}
            <div class="bg-primary-db py-1 border-secondary-bw border-t">
                @php
                    $invMenus = [
                        ['name' => 'Produk', 'route' => 'produk'],
                        ['name' => 'Supplier', 'route' => 'supplier'],
                        ['name' => 'Gudang', 'route' => 'gudang'],
                        ['name' => 'Cek stok', 'route' => 'cek-stok'],
                    ];
                @endphp

                @foreach ($invMenus as $menu)
                    @php
                        $isActive = request()->routeIs($menu['route']);
                    @endphp
                    <a 
                        href="{{ Route::has($menu['route']) ? route($menu['route']) : '#' }}"
                        class="flex items-center w-full px-5 py-2 text-sm italic font-medium text-white transition-colors cursor-pointer {{ $isActive ? 'bg-primary-sdb font-semibold' : 'hover:bg-primary-db' }}"
                    >
                        {{-- Active Triangle Indicator --}}
                        @if ($isActive)
                            <span class="mr-2 text-[10px] leading-none select-none">►</span>
                        @else
                            <span class="w-[18px]"></span>
                        @endif
                        
                        {{-- Label --}}
                        <span class="{{ $isActive ? 'underline underline-offset-2 decoration-1' : '' }}">
                            {{ $menu['name'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

    </div>
</aside>