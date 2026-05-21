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

        {{-- iOS home screen icon (180px for modern iPhones) --}}
        <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-180.png">
        <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192.png">

        {{-- iOS Splash Screens --}}
        {{-- iPhone 16 Pro Max --}}
        <link rel="apple-touch-startup-image" media="(device-width: 440px) and (device-height: 956px) and (-webkit-device-pixel-ratio: 3)" href="/icons/splash/splash-1320x2868.png">
        {{-- iPhone 16 Pro --}}
        <link rel="apple-touch-startup-image" media="(device-width: 402px) and (device-height: 874px) and (-webkit-device-pixel-ratio: 3)" href="/icons/splash/splash-1206x2622.png">
        {{-- iPhone 16 Plus / 15 Pro Max / 15 Plus / 14 Pro Max --}}
        <link rel="apple-touch-startup-image" media="(device-width: 430px) and (device-height: 932px) and (-webkit-device-pixel-ratio: 3)" href="/icons/splash/splash-1290x2796.png">
        {{-- iPhone 16 / 15 Pro / 15 / 14 Pro --}}
        <link rel="apple-touch-startup-image" media="(device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3)" href="/icons/splash/splash-1179x2556.png">
        {{-- iPhone 14 Plus / 13 Pro Max / 12 Pro Max --}}
        <link rel="apple-touch-startup-image" media="(device-width: 428px) and (device-height: 926px) and (-webkit-device-pixel-ratio: 3)" href="/icons/splash/splash-1284x2778.png">
        {{-- iPhone 13 / 13 Pro / 12 / 12 Pro --}}
        <link rel="apple-touch-startup-image" media="(device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3)" href="/icons/splash/splash-1170x2532.png">
        {{-- iPhone 13 mini / 12 mini / SE 3rd / X / XS / 11 Pro --}}
        <link rel="apple-touch-startup-image" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)" href="/icons/splash/splash-1125x2436.png">
        {{-- iPhone 11 Pro Max / XS Max --}}
        <link rel="apple-touch-startup-image" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)" href="/icons/splash/splash-1242x2688.png">
        {{-- iPhone 11 / XR --}}
        <link rel="apple-touch-startup-image" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)" href="/icons/splash/splash-828x1792.png">
        {{-- iPhone 8 Plus / 7 Plus --}}
        <link rel="apple-touch-startup-image" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3)" href="/icons/splash/splash-1242x2208.png">
        {{-- iPhone SE 2nd / 8 / 7 / 6s --}}
        <link rel="apple-touch-startup-image" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)" href="/icons/splash/splash-750x1334.png">
        {{-- iPhone SE 1st gen --}}
        <link rel="apple-touch-startup-image" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" href="/icons/splash/splash-640x1136.png">
        {{-- iPad mini / Air / 9.7" / 10.2" --}}
        <link rel="apple-touch-startup-image" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)" href="/icons/splash/splash-1536x2048.png">
        {{-- iPad Pro 10.5" / Air 10.9" --}}
        <link rel="apple-touch-startup-image" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)" href="/icons/splash/splash-1668x2224.png">
        {{-- iPad Pro 11" --}}
        <link rel="apple-touch-startup-image" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)" href="/icons/splash/splash-1668x2388.png">
        {{-- iPad Pro 12.9" --}}
        <link rel="apple-touch-startup-image" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)" href="/icons/splash/splash-2048x2732.png">

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
                    var tm = document.querySelector('meta[name="theme-color"]');
                    if (tm) tm.setAttribute('content', c[4]);
                };
                window.__applyAccent(localStorage.getItem('gymmate-accent') || 'orange');
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        {{-- Desktop ohne iframe → zurück zum Phone-View --}}
        <script>
            (function () {
                var inFrame  = window.self !== window.top;
                var isTouch  = 'ontouchstart' in window;
                var isNarrow = window.innerWidth < 1024;
                if (!inFrame && !isTouch && !isNarrow) {
                    window.location.replace('/');
                }
            })();
        </script>
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
