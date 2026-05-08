<x-layouts.sidebar>

    <div class="min-h-screen px-6 pt-8 pb-10" x-data="{ editMode: false }">
        <div class="max-w-6xl mx-auto">

            <div class="mb-8 sm:pl-14 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}"
                        class="text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $location->name }}</h1>
                        <p class="text-zinc-500 text-sm mt-0.5">Trainingspläne</p>
                    </div>
                </div>

                <button @click="editMode = !editMode"
                    :class="editMode
                        ? 'bg-orange-500 text-white border-orange-500'
                        : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 border-zinc-300 dark:border-zinc-700 hover:border-orange-500 hover:text-orange-500'"
                    class="flex items-center gap-2 px-3.5 py-2 rounded-xl border text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                    </svg>
                    <span x-text="editMode ? 'Fertig' : 'Bearbeiten'"></span>
                </button>
            </div>

            {{-- Wochenübersicht --}}
            @php
                $dayShort    = [1=>'Mo',2=>'Di',3=>'Mi',4=>'Do',5=>'Fr',6=>'Sa',7=>'So'];
                $dayFull     = [1=>'Montag',2=>'Dienstag',3=>'Mittwoch',4=>'Donnerstag',5=>'Freitag',6=>'Samstag',7=>'Sonntag'];
                $orderedDows = collect(range(0, 6))->map(fn($i) => (($todayDow - 1 + $i) % 7) + 1);
            @endphp
            <div class="mb-8 sm:pl-14">
                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
                    @foreach($orderedDows as $i => $dow)
                        @php
                            $entry     = $schedule->get($dow);
                            $isToday   = $i === 0;
                            $isRest    = $entry && $entry->is_rest;
                            $exercises = $entry?->exercises ?? collect();
                            $date      = $weekDates[$dow];
                            $allDone   = $exercises->isNotEmpty() && $exercises->every(fn($ex) => isset($loggedSet[$ex->id . '|' . $date]));
                            $anyDone   = $exercises->some(fn($ex) => isset($loggedSet[$ex->id . '|' . $date]));
                        @endphp
                        <div class="flex-shrink-0 w-40 flex flex-col rounded-2xl border
                            {{ $isToday ? 'bg-orange-500/10 border-orange-500/40' : 'bg-white dark:bg-zinc-900 border-zinc-300 dark:border-zinc-700' }}
                            overflow-hidden">

                            {{-- Day header --}}
                            <div class="flex items-center justify-between px-3 pt-2.5 pb-1.5
                                {{ $isToday ? 'border-b border-orange-500/20' : 'border-b border-zinc-100 dark:border-zinc-800' }}">
                                <span class="text-xs font-bold {{ $isToday ? 'text-orange-400' : 'text-zinc-500 dark:text-zinc-500' }}">
                                    {{ $dayFull[$dow] }}
                                    @if($isToday)
                                        <span class="ml-1 text-[10px] text-orange-400/60 font-normal">heute</span>
                                    @endif
                                </span>
                                @if($allDone && !$isRest)
                                    <svg class="w-3.5 h-3.5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.5 12.75l6 6 9-13.5"/>
                                    </svg>
                                @elseif($anyDone && !$isRest)
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-400 flex-shrink-0"></span>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="px-3 py-2 space-y-1 min-h-[4rem]">
                                @if($isRest)
                                    <p class="text-zinc-400 dark:text-zinc-600 text-xs font-medium">Rest Day</p>
                                @elseif($exercises->isEmpty())
                                    <p class="text-zinc-300 dark:text-zinc-700 text-xs">—</p>
                                @else
                                    @foreach($exercises as $ex)
                                        @php $done = isset($loggedSet[$ex->id . '|' . $date]); @endphp
                                        <a href="{{ route('exercises.show', $ex) }}"
                                            class="flex items-center gap-1.5 group/ex">
                                            @if($done)
                                                <svg class="w-3 h-3 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.5 12.75l6 6 9-13.5"/>
                                                </svg>
                                            @else
                                                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0
                                                    {{ $isToday ? 'bg-orange-400/60' : 'bg-zinc-300 dark:bg-zinc-600' }}"></span>
                                            @endif
                                            <span class="text-[11px] leading-tight truncate font-medium
                                                {{ $done ? 'line-through text-zinc-400 dark:text-zinc-600' : ($isToday ? 'text-zinc-800 dark:text-zinc-200 group-hover/ex:text-orange-500' : 'text-zinc-500 dark:text-zinc-400 group-hover/ex:text-orange-500') }}
                                                transition-colors">
                                                {{ $ex->name }}
                                            </span>
                                        </a>
                                    @endforeach
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Trainingspläne --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:pl-14"
                 @plan-created.window="window.location.reload()"
                 @plan-updated.window="window.location.reload()">

                @foreach($plans as $plan)
                    <div x-data="{ open: false, confirmation: '' }"
                         class="group relative rounded-2xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 hover:border-orange-500 transition-colors min-h-48">

                        <a href="{{ route('training-plans.exercises.index', $plan) }}" class="absolute inset-0"
                           :class="editMode ? 'pointer-events-none' : ''">
                            @if($plan->getFirstMediaUrl('image'))
                                <img src="{{ $plan->getFirstMediaUrl('image') }}"
                                    alt="{{ $plan->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"/>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-zinc-100 dark:bg-zinc-800/50">
                                    <svg class="w-10 h-10 text-zinc-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
                                    </svg>
                                </div>
                            @endif
                        </a>

                        <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center p-4 text-center pointer-events-none">
                            <p class="text-white font-semibold text-base leading-tight drop-shadow-lg">{{ $plan->name }}</p>
                            <p class="text-zinc-300 text-xs mt-1 drop-shadow">{{ $plan->exercises()->count() }} Übungen</p>
                        </div>

                        <div x-show="editMode" x-transition class="absolute top-2 right-2 flex gap-1 z-10">
                            <button @click.prevent="$dispatch('edit-training-plan', { id: {{ $plan->id }} })"
                                class="p-1.5 rounded-lg text-white/70 hover:text-orange-400 hover:bg-orange-500/20 bg-black/30 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                                </svg>
                            </button>
                            <button @click.prevent="open = true"
                                class="p-1.5 rounded-lg text-white/70 hover:text-red-400 hover:bg-red-500/20 bg-black/30 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>

                        <template x-teleport="body">
                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="open = false; confirmation = ''"></div>
                                <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-sm p-6 border border-zinc-300 dark:border-zinc-700">
                                    <h3 class="text-zinc-900 dark:text-white font-semibold mb-1">Trainingsplan löschen</h3>
                                    <p class="text-zinc-500 dark:text-zinc-400 text-sm mb-4">
                                        Tippe <span class="text-red-400 font-mono">löschen</span> um <span class="text-zinc-900 dark:text-white">„{{ $plan->name }}"</span> und alle zugehörigen Übungen zu entfernen.
                                    </p>
                                    <input x-model="confirmation" type="text" placeholder="löschen"
                                        class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-xl px-4 py-2.5 text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-600 focus:outline-none focus:border-red-500 mb-4"/>
                                    <div class="flex gap-3">
                                        <button @click="open = false; confirmation = ''"
                                            class="flex-1 px-4 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                                            Abbrechen
                                        </button>
                                        <form method="POST" action="{{ route('training-plans.destroy', $plan) }}" class="flex-1">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                :disabled="confirmation !== 'löschen'"
                                                :class="confirmation === 'löschen' ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-600 cursor-not-allowed'"
                                                class="w-full px-4 py-2.5 rounded-xl font-semibold transition-colors">
                                                Löschen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>
                @endforeach

                <livewire:create-training-plan :location="$location" />
            </div>

            <livewire:edit-training-plan />

        </div>
    </div>

</x-layouts.sidebar>
