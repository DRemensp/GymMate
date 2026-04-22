<x-layouts.sidebar>
    <div class="max-w-2xl mx-auto px-4 py-8 space-y-8">

        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Daten exportieren & importieren</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Exportiere deine Daten als CSV oder importiere sie in einen neuen Account.</p>
        </div>

        {{-- Workouts ──────────────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-orange-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Workouts</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Alle Sätze, Gewichte und Reps aus deiner History</p>
                </div>
            </div>

            {{-- Export --}}
            <div class="flex items-center justify-between py-3 px-4 rounded-xl bg-zinc-50 dark:bg-zinc-800">
                <div>
                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Export</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">gymmate_workouts.csv herunterladen</p>
                </div>
                <a href="{{ route('data.export.workouts') }}"
                    class="px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Download
                </a>
            </div>

            {{-- Import --}}
            <div class="py-3 px-4 rounded-xl bg-zinc-50 dark:bg-zinc-800 space-y-3">
                <div>
                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Import</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Fehlende Standorte, Pläne und Übungen werden automatisch erstellt</p>
                </div>

                @if(session('success_workouts'))
                <div class="flex items-center gap-2 text-sm text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success_workouts') }}
                </div>
                @endif

                @error('file')
                <div class="text-sm text-red-500 bg-red-50 dark:bg-red-900/20 rounded-lg px-3 py-2">{{ $message }}</div>
                @enderror

                <form method="POST" action="{{ route('data.import.workouts') }}" enctype="multipart/form-data" class="flex items-center gap-3">
                    @csrf
                    <label class="flex-1 cursor-pointer">
                        <div class="border border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg px-3 py-2.5 text-sm text-zinc-500 dark:text-zinc-400 hover:border-orange-400 hover:text-orange-500 transition-colors text-center">
                            <span x-data x-ref="label">CSV-Datei wählen</span>
                        </div>
                        <input type="file" name="file" accept=".csv,.txt" class="hidden"
                            x-data
                            @change="$el.closest('form').querySelector('span').textContent = $el.files[0]?.name ?? 'CSV-Datei wählen'">
                    </label>
                    <button type="submit"
                        class="px-4 py-2.5 rounded-lg bg-zinc-700 hover:bg-zinc-600 dark:bg-zinc-600 dark:hover:bg-zinc-500 text-white text-sm font-semibold transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" style="transform: rotate(180deg); transform-origin: center;"/>
                        </svg>
                        Importieren
                    </button>
                </form>
            </div>
        </div>

        {{-- Cardio ────────────────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-red-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Cardio</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Alle Cardio-Sessions aus deiner History</p>
                </div>
            </div>

            {{-- Export --}}
            <div class="flex items-center justify-between py-3 px-4 rounded-xl bg-zinc-50 dark:bg-zinc-800">
                <div>
                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Export</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">gymmate_cardio.csv herunterladen</p>
                </div>
                <a href="{{ route('data.export.cardio') }}"
                    class="px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white text-sm font-semibold transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Download
                </a>
            </div>

            {{-- Import --}}
            <div class="py-3 px-4 rounded-xl bg-zinc-50 dark:bg-zinc-800 space-y-3">
                <div>
                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Import</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Cardio-Sessions werden direkt deinem Account hinzugefügt</p>
                </div>

                @if(session('success_cardio'))
                <div class="flex items-center gap-2 text-sm text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success_cardio') }}
                </div>
                @endif

                <form method="POST" action="{{ route('data.import.cardio') }}" enctype="multipart/form-data" class="flex items-center gap-3">
                    @csrf
                    <label class="flex-1 cursor-pointer">
                        <div class="border border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg px-3 py-2.5 text-sm text-zinc-500 dark:text-zinc-400 hover:border-red-400 hover:text-red-500 transition-colors text-center">
                            <span>CSV-Datei wählen</span>
                        </div>
                        <input type="file" name="file" accept=".csv,.txt" class="hidden"
                            x-data
                            @change="$el.closest('form').querySelector('span').textContent = $el.files[0]?.name ?? 'CSV-Datei wählen'">
                    </label>
                    <button type="submit"
                        class="px-4 py-2.5 rounded-lg bg-zinc-700 hover:bg-zinc-600 dark:bg-zinc-600 dark:hover:bg-zinc-500 text-white text-sm font-semibold transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" style="transform: rotate(180deg); transform-origin: center;"/>
                        </svg>
                        Importieren
                    </button>
                </form>
            </div>
        </div>

        {{-- Format Info ────────────────────────────────────────────────── --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-700 p-5 space-y-3">
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">CSV-Format</h3>
            <div class="space-y-2">
                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                    <span class="text-zinc-700 dark:text-zinc-300 font-semibold">Workouts:</span>
                    logged_at, location, training_plan, exercise, is_unilateral, set_number, weight, reps, reps_left, reps_right
                </p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                    <span class="text-zinc-700 dark:text-zinc-300 font-semibold">Cardio:</span>
                    logged_at, activity, duration_minutes, distance_km, intensity, hiit_rounds, hiit_work_seconds, hiit_rest_seconds, notes
                </p>
            </div>
        </div>

    </div>
</x-layouts.sidebar>
