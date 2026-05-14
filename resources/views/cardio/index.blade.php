<x-layouts.sidebar>
    <div class="min-h-screen px-6 pt-8 pb-10"
         x-data
         @cardio-saved.window="window.location.reload()">
        <div class="max-w-2xl mx-auto">

            <div class="mb-8 sm:pl-14">
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Cardio</h1>
                <p class="text-zinc-500 text-sm mt-1">Ausdauertraining tracken & analysieren.</p>
            </div>

            <div class="sm:pl-14 space-y-6">

                {{-- Stats --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-white dark:bg-zinc-900/60 border border-zinc-300 dark:border-zinc-700 rounded-2xl p-4 text-center">
                        <p class="text-2xl font-bold text-orange-500">{{ $weeklyMinutes }}</p>
                        <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-1">Min diese Woche</p>
                    </div>
                    <div class="bg-white dark:bg-zinc-900/60 border border-zinc-300 dark:border-zinc-700 rounded-2xl p-4 text-center">
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $weeklyCount }}</p>
                        <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-1">Sessions</p>
                    </div>
                    <div class="bg-white dark:bg-zinc-900/60 border border-zinc-300 dark:border-zinc-700 rounded-2xl p-4 text-center">
                        <p class="text-sm font-bold text-zinc-900 dark:text-white leading-tight mt-1">
                            {{ $favoriteActivity ? (\App\Models\CardioSession::ACTIVITIES[$favoriteActivity]['label'] ?? '–') : '–' }}
                        </p>
                        <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-1">Beliebteste</p>
                    </div>
                    <div class="bg-white dark:bg-zinc-900/60 border border-zinc-300 dark:border-zinc-700 rounded-2xl p-4 text-center">
                        @if($hasWeightData)
                            <p class="text-2xl font-bold text-orange-500">{{ number_format($weeklyCalories) }}</p>
                            <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-1">kcal diese Woche</p>
                        @else
                            <a href="{{ route('settings.edit') }}" class="text-sm font-medium text-orange-500 hover:underline leading-tight block mt-1">Gewicht eintragen</a>
                            <p class="text-zinc-500 dark:text-zinc-400 text-xs mt-1">für kcal-Berechnung</p>
                        @endif
                    </div>
                </div>

                {{-- Log Form --}}
                <livewire:log-cardio />

                {{-- Chart: Minuten / Woche --}}
                @if($sessions->isNotEmpty())
                <div class="bg-white dark:bg-zinc-900/60 border border-zinc-300 dark:border-zinc-700 rounded-2xl p-5">
                    <h2 class="text-zinc-900 dark:text-white font-semibold mb-4">Minuten / Woche</h2>
                    <div class="h-40">
                        <canvas id="cardioChart"></canvas>
                    </div>
                </div>

                {{-- Aktivitätsverteilung --}}
                @if($activityBreakdown->isNotEmpty())
                <div class="bg-white dark:bg-zinc-900/60 border border-zinc-300 dark:border-zinc-700 rounded-2xl p-5">
                    <h2 class="text-zinc-900 dark:text-white font-semibold mb-3">Letzte 30 Tage</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($activityBreakdown as $item)
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full">
                                <span class="text-zinc-900 dark:text-white text-sm font-medium">{{ $item['label'] }}</span>
                                <span class="text-zinc-500 dark:text-zinc-400 text-xs">{{ $item['count'] }}× · {{ $item['minutes'] }} Min</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- History --}}
                <div class="bg-white dark:bg-zinc-900/60 border border-zinc-300 dark:border-zinc-700 rounded-2xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-zinc-300 dark:border-zinc-700">
                        <h2 class="text-zinc-900 dark:text-white font-semibold">History</h2>
                    </div>
                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                        @foreach($sessions as $session)
                            <div class="flex items-center gap-3 px-5 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                {{-- Linke Seite: Datum + Aktivität + Badge --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-zinc-900 dark:text-white font-medium text-sm">{{ $session->activityLabel() }}</span>
                                        <span class="text-xs px-1.5 py-0.5 rounded-full
                                            {{ $session->intensity === 'leicht' ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : ($session->intensity === 'intensiv' ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400') }}">
                                            {{ ucfirst($session->intensity) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                        <span class="text-zinc-400 dark:text-zinc-500 text-xs">{{ $session->logged_at->format('d.m.Y') }}</span>
                                        <span class="text-zinc-300 dark:text-zinc-700 text-xs">·</span>
                                        <span class="text-zinc-500 dark:text-zinc-400 text-xs font-medium">{{ $session->duration_minutes }} Min</span>
                                        @if($session->calories_burned)
                                            <span class="text-zinc-300 dark:text-zinc-700 text-xs">·</span>
                                            <span class="text-zinc-500 dark:text-zinc-400 text-xs">{{ number_format($session->calories_burned) }} kcal</span>
                                        @endif
                                        @if($session->distance_km)
                                            <span class="text-zinc-300 dark:text-zinc-700 text-xs">·</span>
                                            <span class="text-zinc-500 dark:text-zinc-400 text-xs">{{ $session->distance_km }} km</span>
                                        @elseif($session->hiit_rounds)
                                            <span class="text-zinc-300 dark:text-zinc-700 text-xs">·</span>
                                            <span class="text-zinc-500 dark:text-zinc-400 text-xs">{{ $session->hiit_rounds }}× {{ $session->hiit_work_seconds }}s/{{ $session->hiit_rest_seconds }}s</span>
                                        @elseif($session->notes)
                                            <span class="text-zinc-300 dark:text-zinc-700 text-xs">·</span>
                                            <span class="text-zinc-500 dark:text-zinc-400 text-xs truncate max-w-[120px]">{{ $session->notes }}</span>
                                        @endif
                                    </div>
                                </div>
                                {{-- Rechts: Löschen --}}
                                <form method="POST" action="{{ route('cardio.destroy', $session) }}" class="shrink-0" x-data>
                                    @csrf @method('DELETE')
                                    <button type="button"
                                        class="p-2 text-zinc-400 dark:text-zinc-600 hover:text-red-400 transition-colors"
                                        @click="
                                            if (!navigator.onLine) {
                                                window.OfflineQueue.enqueue('delete_cardio', { cardio_id: {{ $session->id }} });
                                                $el.closest('.flex').remove();
                                            } else {
                                                $el.closest('form').submit();
                                            }
                                        ">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
                @else
                    <p class="text-zinc-500 text-sm text-center py-4">Noch keine Cardio-Sessions gespeichert.</p>
                @endif

            </div>
        </div>
    </div>

    @push('scripts')
    @if($sessions->isNotEmpty())
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const isDark = document.documentElement.classList.contains('dark');
        Chart.defaults.color       = isDark ? '#71717a' : '#52525b';
        Chart.defaults.borderColor = isDark ? '#27272a' : '#e4e4e7';

        new Chart(document.getElementById('cardioChart'), {
            type: 'bar',
            data: {
                labels:   @json($weeks->pluck('label')),
                datasets: [{
                    label: 'Minuten',
                    data:  @json($weeks->pluck('minutes')),
                    backgroundColor: 'rgba(249,115,22,0.7)',
                    borderColor:     'rgba(249,115,22,1)',
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 10 } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
    @endif
    @endpush

    @auth
    @php $tour = auth()->user()->getOrCreateTour(); @endphp
    <x-tour-popup step="cardio" :show="!$tour->cardio" icon="🏃" title="Cardio tracken">
        Trage hier deine Cardio-Einheiten ein: Aktivität, Dauer und optional verbrannte Kalorien. Dein Verlauf wird als Chart dargestellt.
    </x-tour-popup>
    @endauth

</x-layouts.sidebar>
