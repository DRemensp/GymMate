<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' – GymMate' : config('app.name', 'GymMate') }}</title>

        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#f97316">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="GymMate">
        <link rel="apple-touch-icon" href="/icons/icon-192.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>

        {{-- Theme + accent init before render to prevent flash --}}
        <script>
            (function() {
                // Theme
                const savedTheme = localStorage.getItem('gymmate-theme');
                if (savedTheme === 'light') document.documentElement.classList.remove('dark');
                else document.documentElement.classList.add('dark');

                // Accent color
                var accents = {
                    orange: ['255 237 213','251 146 60','249 115 22','234 88 12','#f97316'],
                    blue:   ['219 234 254','96 165 250','59 130 246','37 99 235','#3b82f6'],
                    violet: ['237 233 254','167 139 250','139 92 246','124 58 237','#8b5cf6'],
                    green:  ['220 252 231','74 222 128','34 197 94','22 163 74','#22c55e'],
                    red:    ['254 226 226','248 113 113','239 68 68','220 38 38','#ef4444'],
                    pink:   ['252 231 243','244 114 182','236 72 153','219 39 119','#ec4899'],
                };
                window.__accentColors = accents;
                window.__applyAccent = function(name) {
                    var c = accents[name]; if (!c) return;
                    var r = document.documentElement;
                    r.style.setProperty('--accent-100', c[0]);
                    r.style.setProperty('--accent-400', c[1]);
                    r.style.setProperty('--accent-500', c[2]);
                    r.style.setProperty('--accent-600', c[3]);
                    r.style.setProperty('--accent-hex', c[4]);
                    window.__accentHex  = c[4];
                    window.__accentName = name;
                    localStorage.setItem('gymmate-accent', name);
                };
                window.__applyAccent(localStorage.getItem('gymmate-accent') || 'orange');
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-white min-h-screen">

        <div class="fixed inset-0 bg-gradient-to-br from-zinc-50 via-white to-zinc-50 dark:from-zinc-950 dark:via-zinc-900 dark:to-zinc-950 -z-10"></div>
        <div class="fixed top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-accent-500/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

        <x-sidebar-nav />

        <main>
            {{ $slot }}
        </main>

        @livewireScripts
        @stack('scripts')
        <script>
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js');
            }
        </script>

        {{-- Offline-Banner + Toast --}}
        <div
            x-data="{
                online: navigator.onLine,
                toasts: [],
                pendingCount: 0,
                addToast(msg, type) {
                    const id = Date.now();
                    this.toasts.push({ id, msg, type });
                    setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 4000);
                },
                init() {
                    window.addEventListener('online',  () => { this.online = true; });
                    window.addEventListener('offline', () => { this.online = false; });
                    window.addEventListener('offline-toast', e => {
                        this.addToast(e.detail.message, e.detail.type);
                    });
                    window.addEventListener('offline-queue-count', e => {
                        this.pendingCount = e.detail.count;
                    });
                }
            }"
            x-init="init()"
            id="offline-system"
        >
            {{-- Offline-Banner --}}
            <div
                x-show="!online"
                x-transition
                class="fixed top-0 left-0 right-0 z-[100] bg-accent-500 text-white text-center text-xs py-2 px-4 font-medium"
                style="display:none"
            >
                Offline – Änderungen werden lokal gespeichert und automatisch synchronisiert
            </div>

            {{-- Pending Badge (global, kleine Pille oben rechts) --}}
            <div
                x-show="pendingCount > 0"
                x-transition
                class="fixed top-3 right-3 z-[99] bg-accent-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-lg"
                style="display:none"
                x-text="pendingCount + ' ausstehend'"
            ></div>

            {{-- Toast-Container --}}
            <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-[100] flex flex-col items-center gap-2 pointer-events-none" style="min-width:280px;max-width:90vw">
                <template x-for="toast in toasts" :key="toast.id">
                    <div
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        :class="{
                            'bg-green-600': toast.type === 'success',
                            'bg-accent-500': toast.type === 'offline',
                            'bg-yellow-500': toast.type === 'conflict'
                        }"
                        class="text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-xl text-center pointer-events-auto"
                        x-text="toast.msg"
                    ></div>
                </template>
            </div>
        </div>
    </body>
</html>
