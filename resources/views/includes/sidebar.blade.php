{{-- Left Sidebar Navigation --}}
<aside 
    id="sidebar" 
    class="w-48 flex-shrink-0 bg-primary-db text-secondary-bw flex flex-col h-screen select-none z-20 shadow-md transition-all duration-300 ease-in-out overflow-hidden"
>
    
    {{-- Hamburger Top Bar --}}
    <div class="h-12 flex items-center justify-start px-4 flex-shrink-0">
        <button 
            type="button" 
            id="btnToggleSidebar"
            class="text-secondary-bw hover:opacity-80 transition-opacity focus:outline-none cursor-pointer flex items-center justify-center" 
            aria-label="Toggle Menu"
        >
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </div>

    {{-- Nav Links --}}
    <div class="flex-1 overflow-y-auto overflow-x-hidden">
        
        {{-- ==================== POS SECTION ==================== --}}
        <div>
            {{-- POS Header Button (accordion trigger) --}}
            <button
                type="button"
                class="sidebar-header sidebar-toggle w-full flex items-center gap-3 px-4 py-2.5 font-bold text-secondary-bw border-secondary-bw border-b text-base tracking-wide transition-all duration-300 focus:outline-none cursor-pointer"
                data-target="posMenu"
                aria-expanded="true"
            >
                {{-- Shopping Cart Icon --}}
                <svg class="w-5 h-5 text-secondary-bw flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
                <span class="sidebar-text underline decoration-1 underline-offset-2 whitespace-nowrap transition-opacity duration-200 flex-1 text-left">POS</span>
                {{-- Chevron --}}
                <svg class="sidebar-text sidebar-chevron w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- POS Sub-items Container --}}
            <div id="posMenu" class="sidebar-menu-container bg-primary-db py-1 transition-all duration-300">
                @php
                    $posMenus = [
                        ['name' => 'Kasir', 'route' => 'POS.kasir'],
                        ['name' => 'Transaksi', 'route' => 'transaksi'],
                        ['name' => 'User', 'route' => 'POS.user'],
                        ['name' => 'Sales', 'route' => 'POS.sales'],
                        ['name' => 'Customer', 'route' => 'POS.customer'],
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
                            <span class="mr-2 text-[10px] leading-none select-none flex-shrink-0">►</span>
                        @else
                            <span class="w-[18px] flex-shrink-0"></span>
                        @endif
                        
                        {{-- Label --}}
                        <span class="sidebar-text whitespace-nowrap transition-opacity duration-200 {{ $isActive ? 'underline underline-offset-2 decoration-1' : '' }}">
                            {{ $menu['name'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ==================== INVENTORY SECTION ==================== --}}
        <div>
            {{-- Inventory Header Button (accordion trigger) --}}
            <button
                type="button"
                class="sidebar-header sidebar-toggle w-full flex items-center gap-3 px-4 py-2.5 font-bold text-secondary-bw border-secondary-bw border-b text-base tracking-wide transition-all duration-300 focus:outline-none cursor-pointer"
                data-target="inventoryMenu"
                aria-expanded="true"
            >
                {{-- Box / Inventory Icon --}}
                <svg class="w-5 h-5 text-secondary-bw flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 2H4c-1 0-2 .9-2 2v3.01c0 .72.43 1.34 1 1.69V20c0 1.1 1.1 2 2 2h14c.9 0 2-.9 2-2V8.7c.57-.35 1-.97 1-1.69V4c0-1.1-1-2-2-2zm-1 18H5V9h14v11zm1-13H4V4h16v3z"/>
                </svg>
                <span class="sidebar-text whitespace-nowrap transition-opacity duration-200 flex-1 text-left">Inventory</span>
                {{-- Chevron --}}
                <svg class="sidebar-text sidebar-chevron w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Inventory Sub-items Container --}}
            <div id="inventoryMenu" class="sidebar-menu-container bg-primary-db py-1 transition-all duration-300">
                @php
                    $invMenus = [
                        ['name' => 'Produk', 'route' => 'inventory.item'],
                        ['name' => 'Supplier', 'route' => 'inventory.supplier'],
                        ['name' => 'Gudang', 'route' => 'inventory.gudang'],
                        ['name' => 'Cek stok', 'route' => 'inventory.opname-stok'],
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
                            <span class="mr-2 text-[10px] leading-none select-none flex-shrink-0">►</span>
                        @else
                            <span class="w-[18px] flex-shrink-0"></span>
                        @endif
                        
                        {{-- Label --}}
                        <span class="sidebar-text whitespace-nowrap transition-opacity duration-200 {{ $isActive ? 'underline underline-offset-2 decoration-1' : '' }}">
                            {{ $menu['name'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

    </div>
</aside>

<style>
    /* When the sidebar is collapsed to icon-only mode, drop the header divider
       lines and give each icon row extra breathing room, matching the
       spaced-out icon stack in icon-only view. */
    #sidebar.is-collapsed .sidebar-header {
        border-bottom-color: transparent;
        padding-top: 0.9rem;
        padding-bottom: 0.9rem;
        justify-content: center;
        gap: 0;
    }

    /* Rotate the chevron to point sideways when its section is collapsed */
    .sidebar-chevron.rotate-closed {
        transform: rotate(-90deg);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const btnToggle = document.getElementById('btnToggleSidebar');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');
        const menuContainers = document.querySelectorAll('.sidebar-menu-container');
        const sidebarToggles = document.querySelectorAll('.sidebar-toggle');

        // ---------- Sidebar collapse / expand (icon-only mode) ----------
        const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
        if (isCollapsed) {
            applyCollapse(true);
        }

        btnToggle.addEventListener('click', () => {
            const currentlyCollapsed = sidebar.classList.contains('is-collapsed');
            applyCollapse(!currentlyCollapsed);
        });

        function applyCollapse(collapse) {
            if (collapse) {
                sidebar.classList.remove('w-48');
                sidebar.classList.add('w-14', 'is-collapsed');

                sidebarTexts.forEach(el => {
                    el.classList.add('opacity-0', 'pointer-events-none', 'hidden');
                });

                menuContainers.forEach(el => {
                    el.classList.add('hidden');
                });

                localStorage.setItem('sidebar_collapsed', 'true');
            } else {
                sidebar.classList.remove('w-14', 'is-collapsed');
                sidebar.classList.add('w-48');

                sidebarTexts.forEach(el => {
                    el.classList.remove('hidden', 'opacity-0', 'pointer-events-none');
                });

                // Restore each accordion section to whatever open/closed
                // state it had before the sidebar was collapsed.
                sidebarToggles.forEach(toggle => {
                    const target = document.getElementById(toggle.dataset.target);
                    const expanded = toggle.getAttribute('aria-expanded') === 'true';
                    if (target) {
                        target.classList.toggle('hidden', !expanded);
                    }
                });

                localStorage.setItem('sidebar_collapsed', 'false');
            }
        }

        // ---------- POS / Inventory accordion toggles ----------
        sidebarToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                // Ignore clicks while the sidebar itself is icon-only;
                // expand the sidebar first instead of toggling the section.
                if (sidebar.classList.contains('is-collapsed')) {
                    applyCollapse(false);
                    return;
                }

                const target = document.getElementById(toggle.dataset.target);
                const chevron = toggle.querySelector('.sidebar-chevron');
                const isOpen = toggle.getAttribute('aria-expanded') === 'true';

                target.classList.toggle('hidden', isOpen);
                toggle.setAttribute('aria-expanded', String(!isOpen));
                chevron.classList.toggle('rotate-closed', isOpen);
            });
        });
    });
</script>