{{-- Left Sidebar Navigation --}}
<aside 
    id="sidebar" 
    class="w-48 shrink-0 bg-primary-db text-secondary-bw flex flex-col h-screen z-20 shadow-md transition-all duration-300 ease-in-out overflow-hidden"
>
    
    {{-- Hamburger Top Bar --}}
    <div class="h-12 flex items-center justify-start px-4 shrink-0">
        <button 
            type="button" 
            id="btnToggleSidebar"
            class="text-secondary-bw hover:bg-white/10 hover:opacity-100 rounded-md p-1 transition-all duration-200 focus:outline-none cursor-pointer flex items-center justify-center" 
            aria-label="Toggle Menu"
        >
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </div>

    {{-- Nav Links --}}
    <div class="flex-1 overflow-y-auto overflow-x-hidden">
        
        @php
            $isPosActive = request()->is('pos*') || request()->is('POS*') || request()->routeIs('POS.*');
            $isInvActive = request()->is('inventory*') || request()->is('Inventory*') || request()->routeIs('inventory.*');
        @endphp

        {{-- ==================== POS SECTION ==================== --}}
        <div>
            {{-- POS Header Button (accordion trigger) --}}
            <button
                type="button"
                class="sidebar-header sidebar-toggle w-full flex items-center gap-3 px-4 py-2.5 font-bold text-secondary-bw border-secondary-bw border-b text-base tracking-wide transition-all duration-200 hover:bg-white/10 focus:outline-none cursor-pointer"
                data-target="posMenu"
                aria-expanded="true"
            >
                {{-- Shopping Cart Icon --}}
                <svg class="w-5 h-5 text-secondary-bw shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
                <span class="sidebar-text {{ $isPosActive ? 'underline decoration-1 underline-offset-2' : '' }} whitespace-nowrap transition-opacity duration-200 flex-1 text-left">POS</span>
                {{-- Chevron --}}
                <svg class="sidebar-text sidebar-chevron w-3.5 h-3.5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- POS Sub-items Container --}}
            <div id="posMenu" class="sidebar-menu-container bg-primary-db py-1 transition-all duration-300">
                @php
                    $posMenus = [
                        ['name' => 'Kasir', 'route' => 'POS.kasir'],
                        ['name' => 'Transaksi', 'route' => 'POS.transaksi'],
                        ['name' => 'User', 'route' => 'POS.user'],
                        ['name' => 'Sales', 'route' => 'POS.sales'],
                        ['name' => 'Customer', 'route' => 'POS.customer'],
                        ['name' => 'Laporan', 'route' => 'POS.laporan'],
                        ['name' => 'Desa', 'route' => 'POS.desa']
                    ];
                @endphp

                @foreach ($posMenus as $menu)
                    @php
                        $isActive = request()->routeIs($menu['route']) ||
                                    ($menu['route'] === 'POS.transaksi' && request()->routeIs('POS.transaksi.*')) ||
                                    ($menu['route'] === 'POS.desa' && request()->routeIs('POS.desa.*'));
                    @endphp
                    <a 
                        href="{{ Route::has($menu['route']) ? route($menu['route']) : '#' }}"
                        class="flex items-center w-full px-5 py-2 text-sm italic font-medium text-white transition-colors duration-200 cursor-pointer {{ $isActive ? 'bg-primary-sdb font-semibold hover:bg-white/15' : 'hover:bg-white/10' }}"
                    >
                        {{-- Active Triangle Indicator --}}
                        @if ($isActive)
                            <span class="mr-2 text-[10px] leading-none shrink-0">►</span>
                        @else
                            <span class="w-4.5 shrink-0"></span>
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
                class="sidebar-header sidebar-toggle w-full flex items-center gap-3 px-4 py-2.5 font-bold text-secondary-bw border-secondary-bw border-b text-base tracking-wide transition-all duration-200 hover:bg-white/10 focus:outline-none cursor-pointer"
                data-target="inventoryMenu"
                aria-expanded="true"
            >
                {{-- Box / Inventory Icon --}}
                <svg class="w-5 h-5 text-secondary-bw shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 2H4c-1 0-2 .9-2 2v3.01c0 .72.43 1.34 1 1.69V20c0 1.1 1.1 2 2 2h14c.9 0 2-.9 2-2V8.7c.57-.35 1-.97 1-1.69V4c0-1.1-1-2-2-2zm-1 18H5V9h14v11zm1-13H4V4h16v3z"/>
                </svg>
                <span class="sidebar-text {{ $isInvActive ? 'underline decoration-1 underline-offset-2' : '' }} whitespace-nowrap transition-opacity duration-200 flex-1 text-left">Inventory</span>
                {{-- Chevron --}}
                <svg class="sidebar-text sidebar-chevron w-3.5 h-3.5 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Inventory Sub-items Container --}}
            <div id="inventoryMenu" class="sidebar-menu-container bg-primary-db py-1 transition-all duration-300">
                @php
                    $invMenus = [
                        ['name' => 'Produk', 'route' => 'inventory.item'],
                        ['name' => 'Merk', 'route' => 'inventory.merk'],
                        ['name' => 'Kategori', 'route' => 'inventory.kategori'],
                        ['name' => 'Supplier', 'route' => 'inventory.supplier'],
                        ['name' => 'Gudang', 'route' => 'inventory.gudang'],
                        ['name' => 'Perpindahan', 'route' => 'inventory.perpindahan']
                    ];
                @endphp

                @foreach ($invMenus as $menu)
                    @php
                        $isActive = request()->routeIs($menu['route']) ||
                                    ($menu['route'] === 'inventory.item' && request()->routeIs('inventory.item.*')) ||
                                    ($menu['route'] === 'inventory.gudang' && request()->routeIs('inventory.gudang.*')) ||
                                    ($menu['route'] === 'inventory.perpindahan' && request()->routeIs('inventory.perpindahan.*')) ||
                                    ($menu['route'] === 'inventory.opname' && request()->routeIs('inventory.opname.*'));
                    @endphp
                    <a 
                        href="{{ Route::has($menu['route']) ? route($menu['route']) : '#' }}"
                        class="flex items-center w-full px-5 py-2 text-sm italic font-medium text-white transition-colors duration-200 cursor-pointer {{ $isActive ? 'bg-primary-sdb font-semibold hover:bg-white/15' : 'hover:bg-white/10' }}"
                    >
                        {{-- Active Triangle Indicator --}}
                        @if ($isActive)
                            <span class="mr-2 text-[10px] leading-none shrink-0">►</span>
                        @else
                            <span class="w-4.5 shrink-0"></span>
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

    {{-- Active Database Indicator --}}
    @if(session('selected_database'))
        <div class="border-t border-white/10 px-4 py-2 shrink-0 flex items-center gap-2.5 bg-black/15" title="Database Aktif: {{ session('selected_database') }}">
            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7M4 7c0-2 1.5-3 3.5-3h9c2 0 3.5 1 3.5 3M4 7c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3m0 5c0 2-1.5 3-3.5 3h-9C5.5 15 4 14 4 12" />
            </svg>
            <div class="sidebar-text flex flex-col min-w-0">
                <span class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold leading-tight">Database</span>
                <span class="text-xs font-semibold text-emerald-300 truncate leading-tight">{{ session('selected_database') }}</span>
            </div>
        </div>
    @endif

    {{-- Logout Section - full-bleed square red hover --}}
    <div class="border-t border-white/10 shrink-0">
        <form action="{{ route('logout', [], false) }}" method="POST" id="formSidebarLogout">
            @csrf
            <button 
                type="submit" 
                class="sidebar-logout-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-red-300 hover:text-white hover:bg-red-600 active:bg-red-700 transition-colors duration-200 cursor-pointer group"
                title="Keluar / Logout"
            >
                {{-- Logout Icon --}}
                <svg class="w-5 h-5 shrink-0 text-red-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                <span class="sidebar-text whitespace-nowrap transition-opacity duration-200">
                    Logout
                </span>
            </button>
        </form>
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

    /* Center logout button icon in collapsed mode */
    #sidebar.is-collapsed .sidebar-logout-btn {
        justify-content: center;
        padding-left: 0;
        padding-right: 0;
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
                // state it had based on user choice.
                sidebarToggles.forEach(toggle => {
                    const targetId = toggle.dataset.target;
                    const target = document.getElementById(targetId);
                    const isClosed = localStorage.getItem('sidebar_menu_' + targetId) === 'closed';
                    if (target) {
                        target.classList.toggle('hidden', isClosed);
                    }
                    toggle.setAttribute('aria-expanded', String(!isClosed));
                });

                localStorage.setItem('sidebar_collapsed', 'false');
            }
        }

        // ---------- POS / Inventory accordion toggles ----------
        sidebarToggles.forEach(toggle => {
            const targetId = toggle.dataset.target;
            const target = document.getElementById(targetId);
            const chevron = toggle.querySelector('.sidebar-chevron');
            const storageKey = 'sidebar_menu_' + targetId;

            // Inisialisasi: Jangan auto nutup ketika pindah halaman/judul!
            // Hanya ditutup jika user pernah sengaja mengklik untuk menutupnya.
            const savedState = localStorage.getItem(storageKey);
            if (savedState === 'closed') {
                target.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
                if (chevron) chevron.classList.add('rotate-closed');
            } else {
                target.classList.remove('hidden');
                toggle.setAttribute('aria-expanded', 'true');
                if (chevron) chevron.classList.remove('rotate-closed');
            }

            toggle.addEventListener('click', () => {
                // Ignore clicks while the sidebar itself is icon-only;
                // expand the sidebar first instead of toggling the section.
                if (sidebar.classList.contains('is-collapsed')) {
                    applyCollapse(false);
                    return;
                }

                const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                const nextOpen = !isOpen;

                target.classList.toggle('hidden', !nextOpen);
                toggle.setAttribute('aria-expanded', String(nextOpen));
                if (chevron) chevron.classList.toggle('rotate-closed', !nextOpen);

                // Simpan status per judul agar tetap bertahan saat berpindah halaman
                localStorage.setItem(storageKey, nextOpen ? 'open' : 'closed');
            });
        });

        // ---------- Validasi Tutup Kasir sebelum Logout ----------
        const formLogout = document.getElementById('formSidebarLogout');
        if (formLogout) {
            formLogout.addEventListener('submit', function (e) {
                // Cek apakah sedang berada di halaman POS Kasir (case-insensitive)
                const currentPath = (window.location.pathname || '').toLowerCase();
                const isPosPage = currentPath.includes('/pos/kasir');

                if (isPosPage) {
                    const btnHeaderTutup = document.getElementById('btnHeaderTutupKasir');
                    const isHeaderOpen = btnHeaderTutup && !btnHeaderTutup.classList.contains('hidden');
                    const isOpen = (typeof window.isKasirOpen !== 'undefined') ? window.isKasirOpen : isHeaderOpen;

                    if (isOpen) {
                        e.preventDefault();
                        window.__shouldLogoutAfterTutup = true;

                        const msg = 'Kasir belum ditutup! Silakan lakukan tutup kasir terlebih dahulu sebelum logout.';
                        if (typeof showToast === 'function') {
                            showToast(msg, 'error');
                        } else {
                            alert(msg);
                        }

                        if (typeof modalTutupKasir === 'function') {
                            modalTutupKasir();
                        }
                        return;
                    }

                    // Kasir sudah ditutup, biarkan form langsung tersubmit ke /logout
                    return;
                }

                // Jika di luar halaman POS Kasir, periksa status kasir aktif ke server secara dinamis
                e.preventDefault();
                fetch('{{ route('POS.kas-status', [], false) }}')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.kas) {
                            const msg = 'Kasir belum ditutup! Silakan lakukan tutup kasir terlebih dahulu sebelum logout.';
                            if (typeof showToast === 'function') {
                                showToast(msg, 'error');
                            } else {
                                alert(msg);
                            }

                            setTimeout(() => {
                                window.location.href = "{{ route('POS.kasir', [], false) }}?action=tutup_kasir";
                            }, 800);
                        } else {
                            // Kasir sudah tutup, lakukan submit logout
                            formLogout.submit();
                        }
                    })
                    .catch(() => {
                        // Jika fetch bermasalah, langsung submit dan biarkan middleware backend yang menangani
                        formLogout.submit();
                    });
            });
        }
    });
</script>