<div class="bg-white dark:bg-zinc-900/60 backdrop-blur-sm border border-zinc-300 dark:border-zinc-700 rounded-2xl p-5">
    <h2 class="text-zinc-900 dark:text-white font-semibold mb-4">Cardio loggen</h2>

    <form wire:submit="save" class="space-y-4">

        {{-- Aktivität wählen --}}
        <div class="grid grid-cols-4 gap-2">
            @foreach($activities as $key => $meta)
                <button type="button" wire:click="$set('activity', '{{ $key }}')"
                    class="py-2 px-1 rounded-xl border text-xs font-medium transition-colors text-center
                        {{ $activity === $key
                            ? 'bg-orange-500 border-orange-500 text-white'
                            : 'bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:border-orange-500' }}">
                    {{ $meta['label'] }}
                </button>
            @endforeach
        </div>

        {{-- HIIT-Felder --}}
        @if($activity === 'hiit')
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Runden</label>
                    <input wire:model="hiitRounds" type="number" inputmode="numeric" min="1" placeholder="10"
                        class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-orange-500 transition-colors"/>
                    @error('hiitRounds') <p class="text-red-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Arbeit (s)</label>
                    <input wire:model="hiitWork" type="number" inputmode="numeric" min="5" placeholder="40"
                        class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-orange-500 transition-colors"/>
                    @error('hiitWork') <p class="text-red-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Pause (s)</label>
                    <input wire:model="hiitRest" type="number" inputmode="numeric" min="5" placeholder="20"
                        class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-orange-500 transition-colors"/>
                    @error('hiitRest') <p class="text-red-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>
            </div>
            <p class="text-zinc-400 dark:text-zinc-500 text-xs -mt-2">
                @if($hiitRounds && $hiitWork && $hiitRest)
                    ≈ {{ round(($hiitRounds * ((int)$hiitWork + (int)$hiitRest)) / 60) }} Min gesamt
                @endif
            </p>
        @endif

        {{-- Dauer + Distanz --}}
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">
                    Dauer (Min){{ $activity === 'hiit' ? ' (optional)' : '' }}
                </label>
                <input wire:model="duration" type="number" inputmode="numeric" min="1" placeholder="30"
                    class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-orange-500 transition-colors"/>
                @error('duration') <p class="text-red-400 text-xs mt-0.5">{{ $message }}</p> @enderror
            </div>
            @if(in_array($activity, ['laufband', 'fahrrad', 'rudergeraet']))
                <div>
                    <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Distanz (km, optional)</label>
                    <input wire:model="distance" type="number" inputmode="decimal" step="0.01" min="0" placeholder="5.00"
                        class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-orange-500 transition-colors"/>
                    @error('distance') <p class="text-red-400 text-xs mt-0.5">{{ $message }}</p> @enderror
                </div>
            @endif
        </div>

        {{-- Intensität --}}
        <div>
            <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1.5">Intensität</label>
            <div class="flex gap-2">
                @foreach(['leicht' => 'Leicht', 'mittel' => 'Mittel', 'intensiv' => 'Intensiv'] as $val => $lbl)
                    <button type="button" wire:click="$set('intensity', '{{ $val }}')"
                        class="flex-1 py-2 rounded-xl border text-xs font-semibold transition-colors
                            {{ $intensity === $val
                                ? ($val === 'leicht' ? 'bg-green-500 border-green-500 text-white' : ($val === 'mittel' ? 'bg-orange-500 border-orange-500 text-white' : 'bg-red-500 border-red-500 text-white'))
                                : 'bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:border-orange-500' }}">
                        {{ $lbl }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Notizen --}}
        <div>
            <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Notizen <span class="text-zinc-400 dark:text-zinc-600">(optional)</span></label>
            <input wire:model="notes" type="text" placeholder="z.B. Intervalle auf Stufe 12"
                class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-orange-500 transition-colors"/>
        </div>

        {{-- Datum --}}
        <div>
            <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Datum</label>
            <input wire:model="loggedAt" type="date" max="{{ date('Y-m-d') }}"
                class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-orange-500 transition-colors"/>
            @error('loggedAt') <p class="text-red-400 text-xs mt-0.5">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
            class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl transition-colors">
            Speichern
        </button>
    </form>
</div>
