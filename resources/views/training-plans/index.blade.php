<x-layouts.sidebar>

    <div class="min-h-screen px-6 pt-8 pb-10">
        <div class="max-w-6xl mx-auto">

            {{-- Header --}}
            <div class="mb-8 sm:pl-14 flex items-center gap-3">
                <a href="{{ route('dashboard') }}"
                    class="text-zinc-500 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $location->name }}</h1>
                    <p class="text-zinc-500 text-sm mt-0.5">Trainingspläne</p>
                </div>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:pl-14"
                 x-data
                 @plan-created.window="window.location.reload()">

                @foreach($plans as $plan)
                    <div x-data="{ open: false, confirmation: '' }"
                         class="group relative rounded-2xl overflow-hidden bg-zinc-900 border border-zinc-800 hover:border-orange-500 transition-colors min-h-48">

                        <a href="{{ route('training-plans.exercises.index', $plan) }}" class="absolute inset-0">
                            @if($plan->getFirstMediaUrl('image'))
                                <img src="{{ $plan->getFirstMediaUrl('image') }}"
                                    alt="{{ $plan->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"/>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-zinc-800/50">
                                    <svg class="w-10 h-10 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                        <button @click.prevent="open = true"
                            class="absolute top-2 right-2 z-10 p-1.5 rounded-lg text-white/50 hover:text-red-400 hover:bg-red-500/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>

                        {{-- Bestätigungs-Modal --}}
                        <template x-teleport="body">
                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="open = false; confirmation = ''"></div>
                                <div class="relative bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-sm p-6 border border-zinc-800">
                                    <h3 class="text-white font-semibold mb-1">Trainingsplan löschen</h3>
                                    <p class="text-zinc-400 text-sm mb-4">
                                        Tippe <span class="text-red-400 font-mono">löschen</span> um <span class="text-white">„{{ $plan->name }}"</span> und alle zugehörigen Übungen zu entfernen.
                                    </p>
                                    <input x-model="confirmation" type="text" placeholder="löschen"
                                        class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-2.5 text-white placeholder-zinc-600 focus:outline-none focus:border-red-500 mb-4"/>
                                    <div class="flex gap-3">
                                        <button @click="open = false; confirmation = ''"
                                            class="flex-1 px-4 py-2.5 rounded-xl border border-zinc-700 text-zinc-300 hover:bg-zinc-800 transition-colors">
                                            Abbrechen
                                        </button>
                                        <form method="POST" action="{{ route('training-plans.destroy', $plan) }}" class="flex-1">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                :disabled="confirmation !== 'löschen'"
                                                :class="confirmation === 'löschen' ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-zinc-800 text-zinc-600 cursor-not-allowed'"
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

        </div>
    </div>

</x-layouts.sidebar>
