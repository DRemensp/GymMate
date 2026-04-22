{{-- Drawer Overlay --}}
<div
    id="sidebar-overlay"
    class="fixed inset-0 bg-black/50 z-40 hidden"
    onclick="closeSidebar()"
></div>

{{-- Sidebar Drawer --}}
<aside
    id="sidebar"
    class="fixed top-0 left-0 h-full w-72 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white z-50 flex flex-col shadow-2xl border-r border-zinc-300 dark:border-zinc-700"
>
    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-5 border-b border-zinc-200 dark:border-zinc-700">
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
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-zinc-500 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Standorte
        </a>
        <a href="{{ route('analytics') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-zinc-500 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors {{ request()->routeIs('analytics') ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 13.5l4.5-4.5 4 4 4.5-4.5 3 3M3 20h18"/>
            </svg>
            Analyse
        </a>
        <a href="{{ route('weekly-schedule') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-zinc-500 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors {{ request()->routeIs('weekly-schedule') ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
            </svg>
            Trainingsplan
        </a>
        <a href="{{ route('cardio') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-zinc-500 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors {{ request()->routeIs('cardio') ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
            </svg>
            Cardio
        </a>
        <a href="{{ route('data') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-zinc-500 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors {{ request()->routeIs('data*') ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Export / Import
        </a>
        @endauth

    </nav>

    {{-- Footer --}}
    <div class="px-4 py-5 border-t border-zinc-200 dark:border-zinc-700">
        @auth
        <div class="flex items-center gap-3 px-3 py-2 mb-2">
            <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-sm font-bold text-white">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
        {{-- Theme Toggle --}}
        <div class="flex items-center justify-between px-3 py-2 mb-1">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-400 dark:text-zinc-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                </svg>
                <span class="text-xs text-zinc-400 dark:text-zinc-600 transition-colors">Hell</span>
            </div>
            <button id="theme-toggle" onclick="toggleTheme()"
                class="relative w-11 h-6 rounded-full transition-colors duration-300 bg-zinc-200 dark:bg-orange-500 focus:outline-none"
                aria-label="Theme umschalten">
                <span id="theme-knob"
                    class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-md transition-transform duration-300 dark:translate-x-5">
                </span>
            </button>
            <div class="flex items-center gap-2">
                <span class="text-xs text-zinc-400 dark:text-zinc-500 transition-colors">Dunkel</span>
                <svg class="w-4 h-4 text-zinc-400 dark:text-orange-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                </svg>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-zinc-500 dark:text-zinc-300 hover:bg-red-50 dark:hover:bg-red-900/40 hover:text-red-500 dark:hover:text-red-400 transition-colors text-left">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Abmelden
            </button>
        </form>
        @else
        <a href="{{ route('login') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-zinc-500 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors">
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
    class="fixed left-0 top-1/2 -translate-y-1/2 z-[51] flex flex-col items-center justify-center gap-1 py-5 px-1.5 bg-white dark:bg-zinc-800 border border-l-0 border-zinc-200 dark:border-zinc-700 rounded-r-xl shadow-lg transition-transform duration-300 ease-in-out hover:bg-zinc-50 dark:hover:bg-zinc-700"
    aria-label="Menü"
>
    <span class="w-0.5 h-4 bg-zinc-400 dark:bg-zinc-500 rounded-full"></span>
    <span class="w-0.5 h-4 bg-zinc-400 dark:bg-zinc-500 rounded-full"></span>
</button>

<script>
    // ── Theme ──────────────────────────────────────────────────
    function toggleTheme() {
        const html = document.documentElement;
        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            localStorage.setItem('gymmate-theme', 'light');
        } else {
            html.classList.add('dark');
            localStorage.setItem('gymmate-theme', 'dark');
        }
    }

    // ── Sidebar ────────────────────────────────────────────────
    const _sidebar = document.getElementById('sidebar');
    const _overlay = document.getElementById('sidebar-overlay');
    const _tab     = document.getElementById('sidebar-tab');
    const W        = 288;
    let   _open    = false;

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
                _animate(false);
                _dragging = true;
                _overlay.classList.remove('hidden');
            }
        }
        if (!_dragging) return;
        const newX     = Math.min(0, Math.max(-W, _baseX + dx));
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
        if (_baseX === 0) {
            finalX > -W * 0.15 ? openSidebar() : closeSidebar();
        } else {
            finalX > -W * 0.85 ? openSidebar() : closeSidebar();
        }
    }, { passive: true });

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
</script>
