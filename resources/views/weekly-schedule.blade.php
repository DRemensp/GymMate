<x-layouts.sidebar>

    <div class="min-h-screen px-6 pt-8 pb-10">
        <div class="max-w-xl mx-auto">

            <div class="mb-8 sm:pl-14">
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Trainingsplan</h1>
                <p class="text-zinc-500 text-sm mt-1">Wähle welche Übungen an welchem Tag dran sind.</p>
            </div>

            <div class="sm:pl-14">
                @if(session('saved'))
                <div class="mb-4 px-4 py-3 rounded-xl bg-green-500/10 border border-green-500/30 text-green-600 dark:text-green-400 text-sm">
                    Gespeichert.
                </div>
                @endif

                <form method="POST" action="{{ route('weekly-schedule.update') }}" class="space-y-3">
                    @csrf

                    @php
                        $dayNames = [1 => 'Montag', 2 => 'Dienstag', 3 => 'Mittwoch', 4 => 'Donnerstag', 5 => 'Freitag', 6 => 'Samstag', 7 => 'Sonntag'];
                        $today = \Carbon\Carbon::today()->isoWeekday();
                        $hasExercises = $exerciseGroups->some(fn($loc) => $loc->trainingPlans->some(fn($p) => $p->exercises->isNotEmpty()));
                    @endphp

                    @foreach($days as $dow => $entry)
                    @php $selectedIds = $entry->exercises->pluck('id'); @endphp
                    <div
                        x-data="{ rest: {{ $entry->is_rest ? 'true' : 'false' }} }"
                        class="bg-white dark:bg-zinc-900 border {{ $dow === $today ? 'border-orange-500/50' : 'border-zinc-300 dark:border-zinc-700' }} rounded-2xl px-4 py-3">

                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-semibold {{ $dow === $today ? 'text-orange-400' : 'text-zinc-600 dark:text-zinc-400' }}">
                                {{ $dayNames[$dow] }}
                                @if($dow === $today)
                                    <span class="ml-1.5 text-xs text-orange-500/60 font-normal">heute</span>
                                @endif
                            </span>
                            <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                <input type="checkbox" name="days[{{ $dow }}][is_rest]" value="1"
                                    x-model="rest"
                                    class="w-4 h-4 rounded border-zinc-300 dark:border-zinc-600 bg-zinc-100 dark:bg-zinc-800 text-orange-500 focus:ring-0 focus:ring-offset-0">
                                <span class="text-zinc-500 text-xs">Rest Day</span>
                            </label>
                        </div>

                        {{-- Exercise selection --}}
                        <div x-show="!rest" x-cloak>
                            @if(!$hasExercises)
                                <p class="text-zinc-400 dark:text-zinc-600 text-xs">
                                    Noch keine Übungen vorhanden. Erstelle erst einen Trainingsplan.
                                </p>
                            @else
                                <div class="space-y-3">
                                    @foreach($exerciseGroups as $location)
                                        @foreach($location->trainingPlans as $plan)
                                            @if($plan->exercises->isNotEmpty())
                                            <div>
                                                <p class="text-zinc-400 dark:text-zinc-500 text-[11px] font-medium uppercase tracking-wide mb-1.5">
                                                    {{ $plan->name }}
                                                    @if($exerciseGroups->count() > 1)
                                                        <span class="normal-case tracking-normal font-normal">· {{ $location->name }}</span>
                                                    @endif
                                                </p>
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach($plan->exercises as $exercise)
                                                    <label class="inline-flex cursor-pointer">
                                                        <input type="checkbox"
                                                            name="days[{{ $dow }}][exercises][]"
                                                            value="{{ $exercise->id }}"
                                                            {{ $selectedIds->contains($exercise->id) ? 'checked' : '' }}
                                                            class="sr-only peer">
                                                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium border transition-colors select-none
                                                            peer-checked:bg-orange-500 peer-checked:border-orange-500 peer-checked:text-white
                                                            bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400
                                                            hover:border-orange-400 hover:text-orange-500 dark:hover:border-orange-500 dark:hover:text-orange-400">
                                                            {{ $exercise->name }}
                                                        </span>
                                                    </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Rest day label --}}
                        <p x-show="rest" x-cloak class="text-zinc-400 dark:text-zinc-600 text-xs font-medium">Rest Day</p>

                    </div>
                    @endforeach

                    {{-- Ziel-Reps --}}
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-2xl shadow-sm px-4 py-3">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Ziel-Reps</p>
                                <p class="text-zinc-500 text-xs mt-0.5">Angestrebte Wiederholungen pro Set</p>
                            </div>
                            <input type="number" inputmode="numeric"
                                name="target_reps"
                                value="{{ Auth::user()->target_reps }}"
                                min="1" max="100"
                                class="w-16 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-2 py-1.5 text-zinc-900 dark:text-white text-sm text-center focus:outline-none focus:border-orange-500 transition-colors"/>
                        </div>
                    </div>

                    <div x-data>
                        <button type="button"
                            @click="
                                if (!navigator.onLine) {
                                    const form = $el.closest('form');
                                    const fd = new FormData(form);
                                    const days = {};
                                    for (const [key, val] of fd.entries()) {
                                        const m = key.match(/^days\[(\d+)\]\[(\w+)\](?:\[\])?$/);
                                        if (!m) continue;
                                        const [, dow, field] = m;
                                        if (!days[dow]) days[dow] = { is_rest: false, exercises: [] };
                                        if (field === 'is_rest') days[dow].is_rest = true;
                                        if (field === 'exercises') days[dow].exercises.push(parseInt(val));
                                    }
                                    const targetReps = fd.get('target_reps');
                                    window.OfflineQueue.enqueue('update_schedule', { days, target_reps: targetReps ? parseInt(targetReps) : null });
                                } else {
                                    $el.closest('form').submit();
                                }
                            "
                            class="w-full py-2.5 mt-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl transition-colors">
                            Speichern
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    @auth
    @php $tour = auth()->user()->getOrCreateTour(); @endphp
    <x-tour-popup step="weekly" :show="!$tour->weekly" icon="📅" title="Deine Trainingswoche">
        Weise jedem Wochentag einen Trainingsplan zu oder markiere ihn als Ruhetag. Am besten erst den Wochenplan festlegen, wenn du alle Übungen eingetragen hast.
    </x-tour-popup>
    @endauth

</x-layouts.sidebar>
