{{-- Drawer Overlay --}}
<div
    id="sidebar-overlay"
    class="fixed inset-0 bg-black/50 z-40 hidden"
    onclick="closeSidebar()"
></div>

{{-- Sidebar Drawer --}}
<aside
    id="sidebar"
    class="fixed top-0 left-0 h-full w-72 bg-zinc-900 text-white z-50 flex flex-col shadow-2xl"
>
    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-5 border-b border-zinc-700">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
            </div>
            <span class="text-lg font-bold tracking-wide">GymMate</span>
        </div>
    </div>

    {{-- Nav Links --}}
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        @auth
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-zinc-300 hover:bg-zinc-800 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'bg-zinc-800 text-white' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Standorte
        </a>
        <a href="{{ route('analytics') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-zinc-300 hover:bg-zinc-800 hover:text-white transition-colors {{ request()->routeIs('analytics') ? 'bg-zinc-800 text-white' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 13.5l4.5-4.5 4 4 4.5-4.5 3 3M3 20h18"/>
            </svg>
            Analyse
        </a>
        <a href="{{ route('weekly-schedule') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-zinc-300 hover:bg-zinc-800 hover:text-white transition-colors {{ request()->routeIs('weekly-schedule') ? 'bg-zinc-800 text-white' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
            </svg>
            Trainingsplan
        </a>
        @endauth
    </nav>

    {{-- Footer --}}
    <div class="px-4 py-5 border-t border-zinc-700">
        @auth
        <div class="flex items-center gap-3 px-3 py-2 mb-2">
            <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-sm font-bold">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-zinc-400 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-zinc-300 hover:bg-red-900/40 hover:text-red-400 transition-colors text-left">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Abmelden
            </button>
        </form>
        @else
        <a href="{{ route('login') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-zinc-300 hover:bg-zinc-800 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Einloggen
        </a>
        @endauth
    </div>
</aside>

{{-- Pull-Tab --}}
<button
    id="sidebar-tab"
    onclick="toggleSidebar()"
    class="fixed left-0 top-1/2 -translate-y-1/2 z-[51] flex flex-col items-center justify-center gap-1 py-5 px-1.5 bg-zinc-800 border border-l-0 border-zinc-700 rounded-r-xl shadow-lg transition-transform duration-300 ease-in-out hover:bg-zinc-700"
    aria-label="Menü"
>
    <span class="w-0.5 h-4 bg-zinc-500 rounded-full"></span>
    <span class="w-0.5 h-4 bg-zinc-500 rounded-full"></span>
</button>

<script>
    const _sidebar = document.getElementById('sidebar');
    const _overlay = document.getElementById('sidebar-overlay');
    const _tab     = document.getElementById('sidebar-tab');
    const W        = 288;
    let   _open    = false;

    // Initialzustand via JS steuern
    _sidebar.style.transform = `translateX(-${W}px)`;
    _tab.style.transform     = 'translateY(-50%) translateX(0px)';

    function _animate(on) {
        const t = on ? 'transform 300ms ease-in-out' : 'none';
        _sidebar.style.transition = t;
        _tab.style.transition     = t;
    }

    function openSidebar() {
        _animate(true);
        _sidebar.style.transform = 'translateX(0px)';
        _tab.style.transform     = `translateY(-50%) translateX(${W}px)`;
        _overlay.classList.remove('hidden');
        _open = true;
    }
    function closeSidebar() {
        _animate(true);
        _sidebar.style.transform = `translateX(-${W}px)`;
        _tab.style.transform     = 'translateY(-50%) translateX(0px)';
        _overlay.classList.add('hidden');
        _open = false;
    }
    function toggleSidebar() { _open ? closeSidebar() : openSidebar(); }

    // Touch — Sidebar folgt dem Finger in Echtzeit
    let _tx = 0, _ty = 0, _intent = null, _dragging = false, _baseX = 0;

    document.addEventListener('touchstart', e => {
        _tx       = e.touches[0].clientX;
        _ty       = e.touches[0].clientY;
        _intent   = null;
        _dragging = false;
        _baseX    = _open ? 0 : -W;
    }, { passive: true });

    document.addEventListener('touchmove', e => {
        if (_intent === 'vertical') return;

        const dx = e.touches[0].clientX - _tx;
        const dy = e.touches[0].clientY - _ty;

        if (_intent === null) {
            if (Math.abs(dx) < 5 && Math.abs(dy) < 5) return;
            _intent = Math.abs(dx) > Math.abs(dy) ? 'horizontal' : 'vertical';
            if (_intent === 'horizontal') {
                _animate(false);          // Transition aus → direkte Reaktion
                _dragging = true;
                _overlay.classList.remove('hidden');
            }
        }

        if (!_dragging) return;

        const newX    = Math.min(0, Math.max(-W, _baseX + dx));
        const progress = (newX + W) / W;

        _sidebar.style.transform = `translateX(${newX}px)`;
        _tab.style.transform     = `translateY(-50%) translateX(${newX + W}px)`;
        _overlay.style.opacity   = `${progress * 0.6}`;
    }, { passive: true });

    document.addEventListener('touchend', e => {
        if (!_dragging) return;
        _dragging = false;
        _overlay.style.opacity = '';

        const finalX = _baseX + (e.changedTouches[0].clientX - _tx);
        // _baseX === 0 → war offen; _baseX === -W → war zu
        if (_baseX === 0) {
            finalX > -W * 0.15 ? openSidebar() : closeSidebar();
        } else {
            finalX > -W * 0.85 ? openSidebar() : closeSidebar();
        }
    }, { passive: true });

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
</script>
