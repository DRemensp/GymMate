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
            <div class="w-8 h-8 bg-accent-500 rounded-lg flex items-center justify-center">
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
        <a href="{{ route('following') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-zinc-500 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors {{ request()->routeIs('following*') ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
            </svg>
            Following
        </a>
        @endauth

    </nav>

    {{-- Footer --}}
    <div class="px-4 py-5 border-t border-zinc-200 dark:border-zinc-700" x-data="{ settingsOpen: false }">
        @auth
        <a href="{{ route('profile.public', Auth::user()->name) }}"
            class="flex items-center gap-3 px-3 py-2 mb-1 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors group {{ request()->routeIs('profile.public') ? 'bg-zinc-100 dark:bg-zinc-800' : '' }}">
            <div class="w-8 h-8 rounded-full overflow-hidden bg-accent-500 flex items-center justify-center text-sm font-bold text-white shrink-0">
                @if(Auth::user()->avatarUrl())
                    <img src="{{ Auth::user()->avatarUrl() }}" alt="" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">Profil bearbeiten</p>
            </div>
            <svg class="w-4 h-4 text-zinc-400 dark:text-zinc-600 group-hover:text-zinc-600 dark:group-hover:text-zinc-400 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

        {{-- Settings Button --}}
        <button type="button" @click="settingsOpen = true"
            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors text-left">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Einstellungen
        </button>

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

        {{-- Settings Modal --}}
        <div
            x-show="settingsOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[200] flex items-center justify-center p-4"
            style="display:none"
        >
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="settingsOpen = false"></div>
            <div
                x-show="settingsOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-sm p-6 border border-zinc-300 dark:border-zinc-700"
            >
                {{-- Header --}}
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Einstellungen</h2>
                    <button @click="settingsOpen = false" class="text-zinc-400 dark:text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Accent Color --}}
                <div class="mb-6">
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-3">Akzentfarbe</p>
                    <div class="grid grid-cols-6 gap-2">
                        @foreach([
                            ['name' => 'orange', 'hex' => '#f97316'],
                            ['name' => 'blue',   'hex' => '#3b82f6'],
                            ['name' => 'violet', 'hex' => '#8b5cf6'],
                            ['name' => 'green',  'hex' => '#22c55e'],
                            ['name' => 'red',    'hex' => '#ef4444'],
                            ['name' => 'pink',   'hex' => '#ec4899'],
                        ] as $color)
                        <button type="button"
                            onclick="window.__applyAccent('{{ $color['name'] }}')"
                            class="w-full aspect-square rounded-full border-2 border-transparent hover:scale-110 active:scale-95 transition-transform focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-zinc-900"
                            style="background-color: {{ $color['hex'] }}; --tw-ring-color: {{ $color['hex'] }}"
                            title="{{ ucfirst($color['name']) }}">
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Theme --}}
                <div class="mb-6">
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-3">Design</p>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Hell</span>
                        <button onclick="toggleTheme()"
                            class="relative w-11 h-6 rounded-full transition-colors duration-300 bg-zinc-200 dark:bg-accent-500 focus:outline-none"
                            aria-label="Theme umschalten">
                            <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-md transition-transform duration-300 dark:translate-x-5"></span>
                        </button>
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Dunkel</span>
                    </div>
                </div>

                {{-- Offline --}}
                <div class="mb-6">
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Offline</p>
                    <button type="button" id="prefetch-btn" onclick="prefetchAll()"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors text-left">
                        <svg id="prefetch-icon" class="w-5 h-5 shrink-0 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                        </svg>
                        <span id="prefetch-label" class="text-sm text-zinc-700 dark:text-zinc-300">Offline vorbereiten</span>
                    </button>
                </div>

                {{-- Tour --}}
                <div>
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Tour</p>
                    <form method="POST" action="{{ route('tour.reset') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors text-left">
                            <svg class="w-5 h-5 shrink-0 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
                            </svg>
                            <span class="text-sm text-zinc-700 dark:text-zinc-300">Tour wiederholen</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

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

    // ── Offline prefetch ───────────────────────────────────────
    async function prefetchAll() {
        const btn   = document.getElementById('prefetch-btn');
        const icon  = document.getElementById('prefetch-icon');
        const label = document.getElementById('prefetch-label');

        btn.disabled = true;

        // Spinner
        icon.innerHTML = `<circle cx="12" cy="12" r="9" stroke-width="2" stroke-dasharray="28 56" class="animate-spin origin-center" style="animation:spin 1s linear infinite"/>`;
        label.textContent = 'Lade URLs…';

        let urls;
        try {
            const res = await fetch('{{ route('prefetch.urls') }}', { credentials: 'include' });
            urls = await res.json();
        } catch {
            label.textContent = 'Fehler – bitte online';
            btn.disabled = false;
            return;
        }

        let done = 0;
        const total = urls.length;
        label.textContent = `0 / ${total}`;

        // 4 parallel fetches at a time so we don't flood the server
        const queue = [...urls];
        async function worker() {
            while (queue.length) {
                const url = queue.shift();
                try { await fetch(url, { credentials: 'include' }); } catch {}
                done++;
                label.textContent = `${done} / ${total}`;
            }
        }
        await Promise.all([worker(), worker(), worker(), worker()]);

        // Checkmark
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/>`;
        icon.classList.add('text-green-500');
        label.textContent = 'Bereit für Offline';

        setTimeout(() => {
            icon.classList.remove('text-green-500');
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>`;
            label.textContent = 'Offline vorbereiten';
            btn.disabled = false;
        }, 3000);
    }
</script>
