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
                <div class="grid grid-cols-3 gap-3">
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
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-zinc-300 dark:border-zinc-700">
                                    <th class="text-left text-zinc-500 font-medium px-5 py-3">Datum</th>
                                    <th class="text-left text-zinc-500 font-medium px-3 py-3">Aktivität</th>
                                    <th class="text-left text-zinc-500 font-medium px-3 py-3">Dauer</th>
                                    <th class="text-left text-zinc-500 font-medium px-3 py-3">Details</th>
                                    <th class="px-3 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                                @foreach($sessions as $session)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                        <td class="px-5 py-2.5">
                                            <span class="text-zinc-500 dark:text-zinc-400">{{ $session->logged_at->format('d.m.Y') }}</span>
                                        </td>
                                        <td class="px-3 py-2.5">
                                            <span class="text-zinc-900 dark:text-white font-medium">{{ $session->activityLabel() }}</span>
                                            <span class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full
                                                {{ $session->intensity === 'leicht' ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : ($session->intensity === 'intensiv' ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400') }}">
                                                {{ ucfirst($session->intensity) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 text-zinc-900 dark:text-white font-semibold">
                                            {{ $session->duration_minutes }} Min
                                        </td>
                                        <td class="px-3 py-2.5 text-zinc-500 dark:text-zinc-400 text-xs">
                                            @if($session->distance_km)
                                                {{ $session->distance_km }} km
                                            @elseif($session->hiit_rounds)
                                                {{ $session->hiit_rounds }}× {{ $session->hiit_work_seconds }}s/{{ $session->hiit_rest_seconds }}s
                                            @elseif($session->notes)
                                                {{ $session->notes }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5">
                                            <form method="POST" action="{{ route('cardio.destroy', $session) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="text-zinc-400 dark:text-zinc-600 hover:text-red-400 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
                labels:   {!! json_encode($weeks->pluck('label')) !!},
                datasets: [{
                    label: 'Minuten',
                    data:  {!! json_encode($weeks->pluck('minutes')) !!},
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
</x-layouts.sidebar>
