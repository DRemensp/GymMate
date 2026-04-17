<x-layouts.sidebar>

    <div class="min-h-screen px-6 pt-8 pb-10">
        <div class="max-w-2xl mx-auto">

            <div class="mb-8 sm:pl-14 flex items-center gap-3">
                <a href="{{ route('training-plans.exercises.index', $exercise->trainingPlan) }}"
                    class="text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $exercise->name }}</h1>
                    @if($exercise->description)
                        <p class="text-zinc-500 text-sm mt-0.5">{{ $exercise->description }}</p>
                    @endif
                </div>
            </div>

            <div class="sm:pl-14 space-y-6"
                 x-data
                 x-on:session-saved.window="window.location.reload()">

                <livewire:log-workout :exercise="$exercise" />
                <livewire:edit-workout-session />

                @if($sessions->isNotEmpty())
                <div class="bg-white dark:bg-zinc-900/60 backdrop-blur-sm border border-zinc-300 dark:border-zinc-700 rounded-2xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-zinc-300 dark:border-zinc-700">
                        <h2 class="text-zinc-900 dark:text-white font-semibold">History</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-zinc-300 dark:border-zinc-700">
                                    <th class="text-left text-zinc-500 font-medium px-5 py-3 w-36">Date</th>
                                    <th class="text-left text-zinc-500 font-medium px-3 py-3">Set</th>
                                    <th class="text-left text-zinc-500 font-medium px-3 py-3">Weight</th>
                                    <th class="text-left text-zinc-500 font-medium px-3 py-3">Reps</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                                @foreach($sessions as $session)
                                    @foreach($session->sets as $set)
                                        <tr x-on:click="Livewire.dispatchTo('edit-workout-session', 'load-session', { id: {{ $session->id }} })"
                                            class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors cursor-pointer group">
                                            <td class="px-5 py-2.5 text-zinc-500 dark:text-zinc-400">
                                                @if($loop->first)
                                                    <span class="group-hover:text-orange-500 transition-colors">{{ $session->logged_at->format('d.m.Y') }}</span>
                                                    <span class="block text-zinc-400 dark:text-zinc-600 text-xs">{{ $session->logged_at->format('H:i') }}</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2.5 text-zinc-400 dark:text-zinc-500 font-mono text-xs">{{ $set->set_number }}</td>
                                            <td class="px-3 py-2.5 text-zinc-900 dark:text-white font-semibold">{{ $set->weight }} kg</td>
                                            <td class="px-3 py-2.5 text-zinc-600 dark:text-zinc-300">{{ $set->reps }}x</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                    <p class="text-zinc-500 text-sm text-center py-4">No sessions logged yet.</p>
                @endif

            </div>
        </div>
    </div>

</x-layouts.sidebar>
