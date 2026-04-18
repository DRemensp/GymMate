<x-layouts.sidebar>

    <div class="min-h-screen px-6 pt-8 pb-10" x-data="{ editMode: false }">
        <div class="max-w-6xl mx-auto">

            <div class="mb-8 sm:pl-14 flex items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Meine Standorte</h1>
                    <p class="text-zinc-500 dark:text-zinc-500 text-sm mt-1">Wähle einen Standort oder erstelle einen neuen.</p>
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

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:pl-14"
                 @location-created.window="window.location.reload()"
                 @location-updated.window="window.location.reload()">

                @foreach($locations as $location)
                    <div x-data="{ open: false, confirmation: '' }"
                         class="group relative rounded-2xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 hover:border-orange-500 transition-colors min-h-48">

                        <a href="{{ route('locations.training-plans.index', $location) }}" class="absolute inset-0"
                           :class="editMode ? 'pointer-events-none' : ''">
                            @if($location->getFirstMediaUrl('image'))
                                <img src="{{ $location->getFirstMediaUrl('image') }}" alt="{{ $location->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"/>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-zinc-100 dark:bg-zinc-800/50">
                                    <svg class="w-10 h-10 text-zinc-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                    </svg>
                                </div>
                            @endif
                        </a>

                        <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center p-4 text-center pointer-events-none">
                            <p class="text-white font-semibold text-base leading-tight drop-shadow-lg">{{ $location->name }}</p>
                            <p class="text-zinc-300 text-xs mt-1 drop-shadow">{{ $location->trainingPlans()->count() }} Pläne</p>
                        </div>

                        <div x-show="editMode" x-transition class="absolute top-2 right-2 flex gap-1 z-10">
                            <button @click.prevent="$dispatch('edit-location', { id: {{ $location->id }} })"
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
                                    <h3 class="text-zinc-900 dark:text-white font-semibold mb-1">Standort löschen</h3>
                                    <p class="text-zinc-500 dark:text-zinc-400 text-sm mb-4">
                                        Tippe <span class="text-red-400 font-mono">löschen</span> um <span class="text-zinc-900 dark:text-white">„{{ $location->name }}"</span> und alle zugehörigen Pläne zu entfernen.
                                    </p>
                                    <input x-model="confirmation" type="text" placeholder="löschen"
                                        class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-xl px-4 py-2.5 text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-600 focus:outline-none focus:border-red-500 mb-4"/>
                                    <div class="flex gap-3">
                                        <button @click="open = false; confirmation = ''"
                                            class="flex-1 px-4 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                                            Abbrechen
                                        </button>
                                        <form method="POST" action="{{ route('locations.destroy', $location) }}" class="flex-1">
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

                <livewire:create-location />
            </div>

            <livewire:edit-location />

            {{-- Wochenübersicht --}}
            @php
                $dayFull = [1=>'Montag',2=>'Dienstag',3=>'Mittwoch',4=>'Donnerstag',5=>'Freitag',6=>'Samstag',7=>'Sonntag'];
                $orderedDows = collect(range(0, 6))->map(fn($i) => (($todayDow - 1 + $i) % 7) + 1);
            @endphp
            <div class="mt-8 sm:pl-14">
                <div class="flex gap-3 overflow-hidden">
                    @foreach($orderedDows as $i => $dow)
                        @php
                            $entry   = $schedule->get($dow);
                            $isToday = $i === 0;
                            $isRest  = $entry && $entry->is_rest;
                            $label   = $entry?->label;
                        @endphp
                        <div class="flex-shrink-0 w-36 flex flex-col rounded-2xl border p-3 h-28
                            {{ $isToday
                                ? 'bg-orange-500/10 border-orange-500/40'
                                : 'bg-white dark:bg-zinc-900 border-zinc-300 dark:border-zinc-700' }}">

                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold {{ $isToday ? 'text-orange-400' : 'text-zinc-500 dark:text-zinc-500' }}">
                                    {{ $dayFull[$dow] }}
                                </span>
                                @if($isToday && $label && !$isRest)
                                    @if($loggedToday)
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-400 flex-shrink-0"></span>
                                    @else
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse flex-shrink-0"></span>
                                    @endif
                                @endif
                            </div>

                            <div class="flex-1 flex items-start">
                                @if($isRest)
                                    <p class="text-zinc-400 dark:text-zinc-600 text-xs font-medium">Rest Day</p>
                                @elseif($label)
                                    <p class="text-xs font-semibold leading-snug line-clamp-4
                                        {{ $isToday ? 'text-zinc-900 dark:text-white' : 'text-zinc-600 dark:text-zinc-400' }}">
                                        {{ $label }}
                                    </p>
                                @else
                                    <p class="text-zinc-400 dark:text-zinc-700 text-xs">—</p>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

</x-layouts.sidebar>
