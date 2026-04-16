<x-layouts.sidebar>

    <div class="min-h-screen px-6 pt-8 pb-10">
        <div class="max-w-6xl mx-auto">

            <div class="mb-6 sm:pl-14">
                <h1 class="text-2xl font-bold text-white">Analyse</h1>
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
                                 : 'bg-zinc-800 text-zinc-400 hover:text-white hover:bg-zinc-700' }}">
                        {{ $loc->name }}
                    </a>
                    @endforeach
                </div>
                @endif

                {{-- Summary Cards --}}
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4">
                        <p class="text-zinc-500 text-xs mb-1">Total Sessions</p>
                        <p class="text-white text-3xl font-bold">{{ $totalSessions }}</p>
                    </div>
                    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4">
                        <p class="text-zinc-500 text-xs mb-1">Total Volume</p>
                        <p class="text-white text-3xl font-bold">{{ number_format($totalVolume / 1000, 1) }}<span class="text-lg text-zinc-400 ml-1">t</span></p>
                    </div>
                    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4">
                        <p class="text-zinc-500 text-xs mb-1">Total Sets</p>
                        <p class="text-white text-3xl font-bold">{{ $totalSets }}</p>
                    </div>
                </div>

                {{-- Weekly Volume Chart --}}
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
                    <h2 class="text-white font-semibold mb-4">Weekly Volume <span class="text-zinc-500 text-sm font-normal">(last 16 weeks, kg)</span></h2>
                    <div class="relative h-52">
                        <canvas id="weeklyChart"></canvas>
                    </div>
                </div>

                {{-- Exercise Table --}}
                @if($exerciseStats->isNotEmpty())
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-zinc-800">
                        <h2 class="text-white font-semibold">Exercises</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-zinc-500 text-xs border-b border-zinc-800">
                                    <th class="text-left px-6 py-3 font-medium">Exercise</th>
                                    <th class="text-right px-4 py-3 font-medium">Max Weight</th>
                                    <th class="text-right px-4 py-3 font-medium">Est. 1RM</th>
                                    <th class="text-right px-4 py-3 font-medium">Sessions</th>
                                    <th class="text-right px-4 py-3 font-medium hidden sm:table-cell">Volume</th>
                                    <th class="text-right px-4 py-3 font-medium hidden sm:table-cell">Last Trained</th>
                                    <th class="px-4 py-3 hidden md:table-cell">Trend</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-800">
                                @foreach($exerciseStats as $stat)
                                <tr class="hover:bg-zinc-800/50 transition-colors">
                                    <td class="px-6 py-3 text-white font-medium">{{ $stat['name'] }}</td>
                                    <td class="px-4 py-3 text-right text-zinc-300">{{ $stat['max_weight'] }} <span class="text-zinc-600">kg</span></td>
                                    <td class="px-4 py-3 text-right text-orange-400 font-semibold">{{ $stat['best_1rm'] }} <span class="text-zinc-600 font-normal">kg</span></td>
                                    <td class="px-4 py-3 text-right text-zinc-300">{{ $stat['sessions'] }}</td>
                                    <td class="px-4 py-3 text-right text-zinc-300 hidden sm:table-cell">{{ number_format($stat['total_volume']) }} <span class="text-zinc-600">kg</span></td>
                                    <td class="px-4 py-3 text-right text-zinc-500 hidden sm:table-cell">{{ $stat['last_trained'] }}</td>
                                    <td class="px-4 py-3 hidden md:table-cell">
                                        <canvas class="sparkline h-8 w-24" data-values="{{ json_encode($stat['sparkline']) }}"></canvas>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-12 text-center">
                    <p class="text-zinc-500">No training data yet.</p>
                </div>
                @endif

            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        Chart.defaults.color = '#71717a';
        Chart.defaults.borderColor = '#27272a';

        // Weekly volume bar chart
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
                    y: { grid: { color: '#27272a' }, ticks: { font: { size: 11 },
                        callback: v => v >= 1000 ? (v/1000).toFixed(1)+'t' : v+'kg' }
                    }
                }
            }
        });

        // Sparklines
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
