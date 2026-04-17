<x-layouts.sidebar>

    <div class="min-h-screen px-6 pt-8 pb-10">
        <div class="max-w-6xl mx-auto">

            <div class="mb-6 sm:pl-14">
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Analyse</h1>
                <p class="text-zinc-500 text-sm mt-1">Deine Trainingsstatistiken auf einen Blick.</p>
            </div>

            <div class="sm:pl-14 space-y-8">

                {{-- Location Tabs --}}
                @if($locations->count() > 1)
                <div class="flex gap-2 flex-wrap">
                    @foreach($locations as $loc)
                    <a href="{{ route('analytics', ['location' => $loc->id]) }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium transition-colors
                              {{ $active?->id === $loc->id
                                 ? 'bg-orange-500 text-white'
                                 : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                        {{ $loc->name }}
                    </a>
                    @endforeach
                </div>
                @endif

                {{-- Summary Cards --}}
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-2xl shadow-sm p-4">
                        <p class="text-zinc-500 text-xs mb-1">Total Sessions</p>
                        <p class="text-zinc-900 dark:text-white text-3xl font-bold">{{ $totalSessions }}</p>
                    </div>
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-2xl shadow-sm p-4">
                        <p class="text-zinc-500 text-xs mb-1">Total Volume</p>
                        <p class="text-zinc-900 dark:text-white text-3xl font-bold">{{ number_format($totalVolume / 1000, 1) }}<span class="text-lg text-zinc-400 ml-1">t</span></p>
                    </div>
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-2xl shadow-sm p-4">
                        <p class="text-zinc-500 text-xs mb-1">Total Sets</p>
                        <p class="text-zinc-900 dark:text-white text-3xl font-bold">{{ $totalSets }}</p>
                    </div>
                </div>

                {{-- Weekly Volume Chart --}}
                <div class="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-2xl shadow-sm p-6">
                    <h2 class="text-zinc-900 dark:text-white font-semibold mb-4">Weekly Volume <span class="text-zinc-500 text-sm font-normal">(last 16 weeks, kg)</span></h2>
                    <div class="relative h-52">
                        <canvas id="weeklyChart"></canvas>
                    </div>
                </div>

                {{-- Exercise Table --}}
                @if($exerciseStats->isNotEmpty())
                <div class="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-zinc-300 dark:border-zinc-700">
                        <h2 class="text-zinc-900 dark:text-white font-semibold">Exercises</h2>
                    </div>

                    {{-- Desktop Tabelle --}}
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-zinc-500 text-xs border-b border-zinc-300 dark:border-zinc-700">
                                    <th class="text-left px-6 py-3 font-medium">Exercise</th>
                                    <th class="text-right px-4 py-3 font-medium">Max Weight</th>
                                    <th class="text-right px-4 py-3 font-medium">Est. 1RM</th>
                                    <th class="text-right px-4 py-3 font-medium">Sessions</th>
                                    <th class="text-right px-4 py-3 font-medium">Volume</th>
                                    <th class="text-right px-4 py-3 font-medium">Last Trained</th>
                                    <th class="px-4 py-3">Trend</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                @foreach($exerciseStats as $stat)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td class="px-6 py-3 text-zinc-900 dark:text-white font-medium">{{ $stat['name'] }}</td>
                                    <td class="px-4 py-3 text-right text-zinc-600 dark:text-zinc-300">{{ $stat['max_weight'] }} <span class="text-zinc-400 dark:text-zinc-600">kg</span></td>
                                    <td class="px-4 py-3 text-right text-orange-500 font-semibold">{{ $stat['best_1rm'] }} <span class="text-zinc-400 dark:text-zinc-600 font-normal">kg</span></td>
                                    <td class="px-4 py-3 text-right text-zinc-600 dark:text-zinc-300">{{ $stat['sessions'] }}</td>
                                    <td class="px-4 py-3 text-right text-zinc-600 dark:text-zinc-300">{{ number_format($stat['total_volume']) }} <span class="text-zinc-400 dark:text-zinc-600">kg</span></td>
                                    <td class="px-4 py-3 text-right text-zinc-500">{{ $stat['last_trained'] }}</td>
                                    <td class="px-4 py-3">
                                        <canvas class="sparkline h-8 w-24" data-values="{{ json_encode($stat['sparkline']) }}"></canvas>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile: klickbare Zeilen mit Bottom-Sheet --}}
                    <div class="sm:hidden divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach($exerciseStats as $stat)
                        <div
                            x-data="{
                                open: false,
                                chart: null,
                                openSheet() {
                                    this.open = true;
                                    this.$nextTick(() => {
                                        const canvas = this.$refs.modalSparkline;
                                        if (canvas && !this.chart) {
                                            this.chart = new Chart(canvas, {
                                                type: 'line',
                                                data: {
                                                    labels: {{ json_encode(array_keys($stat['sparkline'])) }},
                                                    datasets: [{
                                                        data: {{ json_encode(array_values($stat['sparkline'])) }},
                                                        borderColor: '#f97316',
                                                        borderWidth: 2.5,
                                                        pointRadius: 3,
                                                        pointBackgroundColor: '#f97316',
                                                        tension: 0.4,
                                                        fill: false,
                                                    }]
                                                },
                                                options: {
                                                    responsive: true,
                                                    maintainAspectRatio: false,
                                                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                                                    scales: { x: { display: false }, y: { display: false } },
                                                }
                                            });
                                        }
                                    });
                                }
                            }"
                        >
                            {{-- Zeile --}}
                            <button @click="openSheet()" class="w-full flex items-center justify-between px-4 py-3.5 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors text-left gap-3">
                                <span class="text-zinc-900 dark:text-white font-medium text-sm flex-1 truncate">{{ $stat['name'] }}</span>
                                <div class="flex items-center gap-4 flex-shrink-0">
                                    <div class="text-center">
                                        <p class="text-zinc-500 text-xs">Max</p>
                                        <p class="text-zinc-700 dark:text-zinc-300 font-semibold text-sm">{{ $stat['max_weight'] }}<span class="text-zinc-400 text-xs font-normal">kg</span></p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-zinc-500 text-xs">1RM</p>
                                        <p class="text-orange-500 font-semibold text-sm">{{ $stat['best_1rm'] }}<span class="text-zinc-400 text-xs font-normal">kg</span></p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-zinc-500 text-xs">Sets</p>
                                        <p class="text-zinc-700 dark:text-zinc-300 font-semibold text-sm">{{ $stat['sessions'] }}</p>
                                    </div>
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </button>

                            {{-- Bottom Sheet --}}
                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end justify-center">
                                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>

                                <div class="relative w-full bg-white dark:bg-zinc-900 rounded-t-3xl border-t border-zinc-300 dark:border-zinc-700 shadow-2xl p-6 pb-10"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="translate-y-full"
                                     x-transition:enter-end="translate-y-0"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="translate-y-0"
                                     x-transition:leave-end="translate-y-full">

                                    {{-- Handle --}}
                                    <div class="w-10 h-1 bg-zinc-300 dark:bg-zinc-700 rounded-full mx-auto mb-5"></div>

                                    {{-- Titel --}}
                                    <div class="flex items-start justify-between mb-5">
                                        <h3 class="text-zinc-900 dark:text-white font-bold text-xl">{{ $stat['name'] }}</h3>
                                        <button @click="open = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-white transition-colors p-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Stats Grid --}}
                                    <div class="grid grid-cols-3 gap-3 mb-5">
                                        <div class="bg-zinc-100 dark:bg-zinc-800 rounded-2xl p-3 text-center">
                                            <p class="text-zinc-500 text-xs mb-1">Max Weight</p>
                                            <p class="text-zinc-900 dark:text-white font-bold text-lg">{{ $stat['max_weight'] }}<span class="text-zinc-400 text-xs font-normal ml-0.5">kg</span></p>
                                        </div>
                                        <div class="bg-orange-500/10 rounded-2xl p-3 text-center">
                                            <p class="text-orange-500/70 text-xs mb-1">Est. 1RM</p>
                                            <p class="text-orange-500 font-bold text-lg">{{ $stat['best_1rm'] }}<span class="text-orange-400/60 text-xs font-normal ml-0.5">kg</span></p>
                                        </div>
                                        <div class="bg-zinc-100 dark:bg-zinc-800 rounded-2xl p-3 text-center">
                                            <p class="text-zinc-500 text-xs mb-1">Sessions</p>
                                            <p class="text-zinc-900 dark:text-white font-bold text-lg">{{ $stat['sessions'] }}</p>
                                        </div>
                                        <div class="bg-zinc-100 dark:bg-zinc-800 rounded-2xl p-3 text-center">
                                            <p class="text-zinc-500 text-xs mb-1">Volume</p>
                                            <p class="text-zinc-900 dark:text-white font-bold text-base">{{ number_format($stat['total_volume']) }}<span class="text-zinc-400 text-xs font-normal ml-0.5">kg</span></p>
                                        </div>
                                        <div class="bg-zinc-100 dark:bg-zinc-800 rounded-2xl p-3 text-center col-span-2">
                                            <p class="text-zinc-500 text-xs mb-1">Zuletzt trainiert</p>
                                            <p class="text-zinc-900 dark:text-white font-bold text-base">{{ $stat['last_trained'] }}</p>
                                        </div>
                                    </div>

                                    {{-- Trend Sparkline --}}
                                    @if(count($stat['sparkline']) > 1)
                                    <div>
                                        <p class="text-zinc-500 text-xs mb-2">Volumen-Trend (letzte {{ count($stat['sparkline']) }} Sessions)</p>
                                        <div class="h-28 w-full">
                                            <canvas x-ref="modalSparkline"></canvas>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-2xl shadow-sm p-12 text-center">
                    <p class="text-zinc-500">No training data yet.</p>
                </div>
                @endif

            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const isDark = document.documentElement.classList.contains('dark');
        Chart.defaults.color      = isDark ? '#71717a' : '#52525b';
        Chart.defaults.borderColor = isDark ? '#27272a' : '#e4e4e7';

        new Chart(document.getElementById('weeklyChart'), {
            type: 'bar',
            data: {
                labels: @json($weeklyLabels),
                datasets: [{
                    data: @json($weeklyData),
                    backgroundColor: 'rgba(249,115,22,0.7)',
                    hoverBackgroundColor: 'rgba(249,115,22,1)',
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: {
                    callbacks: { label: ctx => ` ${ctx.parsed.y.toLocaleString()} kg` }
                }},
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { grid: { color: isDark ? '#27272a' : '#e4e4e7' }, ticks: { font: { size: 11 },
                        callback: v => v >= 1000 ? (v/1000).toFixed(1)+'t' : v+'kg' }
                    }
                }
            }
        });

        document.querySelectorAll('.sparkline').forEach(canvas => {
            const values = JSON.parse(canvas.dataset.values);
            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: values.map((_, i) => i),
                    datasets: [{
                        data: values,
                        borderColor: '#f97316',
                        borderWidth: 2,
                        pointRadius: 0,
                        tension: 0.4,
                        fill: false,
                    }]
                },
                options: {
                    responsive: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    scales: { x: { display: false }, y: { display: false } },
                    animation: false,
                }
            });
        });
    </script>
    @endpush

</x-layouts.sidebar>
