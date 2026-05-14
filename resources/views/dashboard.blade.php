<x-layouts.sidebar>

    <div class="min-h-screen pb-10" x-data="{ editMode: false }">
        <div class="max-w-xl mx-auto px-5 pt-6">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-6 sm:pl-14">
                <span class="text-[19px] font-semibold text-zinc-900 dark:text-white tracking-tight">Standorte</span>
                <button @click="editMode = !editMode"
                    :class="editMode
                        ? 'bg-orange-500 text-white'
                        : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 hover:text-orange-500 dark:hover:text-orange-400'"
                    class="w-[38px] h-[38px] rounded-xl flex items-center justify-center transition-colors flex-shrink-0">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                    </svg>
                </button>
            </div>

            {{-- Greeting --}}
            <div class="sm:pl-14 mb-6">
                <div class="text-[26px] font-medium text-zinc-900 dark:text-white tracking-tight leading-tight">
                    {{ $greeting }},
                </div>
                <div class="mt-0.5">
                    <span class="text-[26px] font-medium text-zinc-400 dark:text-zinc-500 tracking-tight leading-tight">
                        {{ $firstName }}
                    </span>
                </div>
            </div>

            {{-- Heute card --}}
            @if($todaySchedule && !$todaySchedule->is_rest && $todaySchedule->exercises->isNotEmpty())
            <div class="sm:pl-14 mb-5">
                <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 rounded-[18px] px-4 py-3.5 border border-zinc-200 dark:border-zinc-800">
                    <div class="w-10 h-10 rounded-xl bg-orange-500 flex items-center justify-center text-white flex-shrink-0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="5" width="18" height="16" rx="2.5"/>
                            <path d="M3 10h18M8 3v4M16 3v4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-mono text-[10px] text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em]">
                            HEUTE &middot; {{ strtoupper($todayDayName) }}
                        </div>
                        <div class="text-[15px] font-semibold text-zinc-900 dark:text-white mt-0.5">
                            {{ $todaySchedule->exercises->count() }} {{ $todaySchedule->exercises->count() === 1 ? 'Übung' : 'Übungen' }}
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Location cards --}}
            <div class="sm:pl-14 flex flex-col gap-3"
                 @location-created.window="window.location.reload()"
                 @location-updated.window="window.location.reload()">

                @php $todayDay = now()->day; $daysInMonth = now()->daysInMonth; @endphp
                @foreach($locations as $i => $location)

                <div x-data="{ open: false, confirmation: '' }"
                     class="relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[22px] p-[18px]">

                    {{-- Edit mode buttons --}}
                    <div x-show="editMode" x-transition
                         class="absolute top-3 right-3 flex gap-1 z-10">
                        <button @click.prevent="$dispatch('edit-location', { id: {{ $location->id }} })"
                            class="p-1.5 rounded-lg text-zinc-500 dark:text-zinc-400 hover:text-orange-500 hover:bg-orange-500/10 bg-zinc-100 dark:bg-zinc-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                            </svg>
                        </button>
                        <button @click.prevent="open = true"
                            class="p-1.5 rounded-lg text-zinc-500 dark:text-zinc-400 hover:text-red-400 hover:bg-red-500/10 bg-zinc-100 dark:bg-zinc-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Card body --}}
                    <a href="{{ route('locations.training-plans.index', $location) }}"
                       :class="editMode ? 'pointer-events-none' : ''"
                       class="block">

                        {{-- Meta row --}}
                        <div class="flex items-center gap-1.5 mb-1">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 dark:text-zinc-500 flex-shrink-0">
                                <path d="M12 21s7-6.4 7-12a7 7 0 0 0-14 0c0 5.6 7 12 7 12z"/>
                                <circle cx="12" cy="9" r="2.6"/>
                            </svg>
                            <span class="font-mono text-[11px] text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em]">
                                {{ $location->trainingPlans->count() }} {{ $location->trainingPlans->count() === 1 ? 'Plan' : 'Pläne' }}
                            </span>
                        </div>

                        {{-- Location name --}}
                        <div class="text-[22px] font-semibold text-zinc-900 dark:text-white tracking-tight leading-tight">
                            {{ $location->name }}
                        </div>

                        {{-- Dots + Session count --}}
                        <div class="flex items-center gap-4 mt-3">
                            {{-- Month dots (links, 10er-Grid) --}}
                            <div class="grid gap-[3px]" style="grid-template-columns: repeat(10, 7px)">
                                @foreach($location->monthDots as $dayIdx => $trained)
                                @php $day = $dayIdx + 1; $isToday = $day === $todayDay; $isFuture = $day > $todayDay; @endphp
                                <span class="w-[7px] h-[7px] rounded-full transition-colors
                                    @if($trained) bg-green-400
                                    @elseif($isToday) bg-orange-400/60
                                    @elseif($isFuture) bg-zinc-200 dark:bg-zinc-800
                                    @else bg-zinc-200 dark:bg-zinc-700
                                    @endif"
                                    @if($isToday && !$trained) style="box-shadow: 0 0 0 1.5px #f97316, 0 0 0 3px rgba(249,115,22,0.15)"
                                    @elseif($trained) style="box-shadow: 0 0 5px 1px rgba(74,222,128,0.5)"
                                    @endif>
                                </span>
                                @endforeach
                            </div>

                            {{-- Session count (rechts, zentriert) --}}
                            <div class="flex-1 flex flex-col items-center justify-center text-center">
                                <div class="font-mono text-[28px] font-medium text-zinc-900 dark:text-white tracking-tight leading-none">
                                    {{ $location->sessionCount }}
                                </div>
                                <div class="font-mono text-[11px] text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em] mt-1">
                                    Sessions
                                    @if($location->lastVisit)
                                        <br>{{ $location->lastVisit->diffForHumans() }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>

                    {{-- Delete modal --}}
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
                                        <button type="button"
                                            :disabled="confirmation !== 'löschen'"
                                            :class="confirmation === 'löschen' ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-600 cursor-not-allowed'"
                                            class="w-full px-4 py-2.5 rounded-xl font-semibold transition-colors"
                                            @click="
                                                if (confirmation !== 'löschen') return;
                                                if (!navigator.onLine) {
                                                    window.OfflineQueue.enqueue('delete_location', { location_id: {{ $location->id }} });
                                                    open = false; confirmation = '';
                                                } else {
                                                    $el.closest('form').submit();
                                                }
                                            ">
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

        </div>
    </div>

    @auth
    @php $tour = auth()->user()->getOrCreateTour(); @endphp
    <x-tour-popup step="locations" :show="!$tour->locations" icon="🏋️" title="Erstelle deinen ersten Standort">
        Tippe auf <strong class="text-zinc-700 dark:text-zinc-300">+ Standort</strong> und gib deinem Gym einen Namen. Für jeden Standort werden automatisch 5 Trainingspläne angelegt: Schulter, Arme, Rücken, Brust und Beine.
    </x-tour-popup>
    @endauth

</x-layouts.sidebar>
