<!DOCTYPE html>
<html lang="de" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline – GymMate</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#f97316">
    <script>
        const saved = localStorage.getItem('gymmate-theme');
        if (saved === 'light') document.documentElement.classList.remove('dark');
        else document.documentElement.classList.add('dark');
    </script>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-zinc-950 text-white min-h-screen flex items-center justify-center p-6">
    <div class="text-center max-w-sm">
        <div class="text-6xl mb-6">📡</div>
        <h1 class="text-2xl font-bold mb-3">Du bist offline</h1>
        <p class="text-zinc-400 text-sm leading-relaxed mb-6">
            Diese Seite ist noch nicht im Cache. Sobald du wieder online bist, kannst du alle Funktionen nutzen.
            Workouts und Cardio die du offline geloggt hast werden automatisch synchronisiert.
        </p>
        <button onclick="window.history.back()"
            class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl transition-colors text-sm">
            Zurück
        </button>
    </div>
</body>
</html>
