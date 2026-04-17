<div>
    <div class="bg-white dark:bg-zinc-900/60 backdrop-blur-sm border border-zinc-300 dark:border-zinc-700 rounded-2xl shadow-sm p-5">
        <h2 class="text-zinc-900 dark:text-white font-semibold mb-4">Log Session</h2>

        <form wire:submit="save">

            {{-- Letzte Session / Empfehlung --}}
            @if($lastSets)
            <div class="flex items-center justify-between mb-3 px-1">
                <span class="text-zinc-500 dark:text-zinc-400 text-xs">
                    Letzte: {{ collect($lastSets)->avg('weight') }}kg &times; Ø {{ round(collect($lastSets)->avg('reps')) }} Reps
                </span>
                @if($progressionTip === 'increase')
                    <span class="text-xs font-semibold text-orange-500 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18"/>
                        </svg>
                        +5% empfohlen
                    </span>
                @else
                    <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500">
                        = Gewicht halten
                    </span>
                @endif
            </div>
            @endif

            {{-- Set-Zeilen --}}
            <div class="space-y-2 mb-3">
                {{-- Header --}}
                <div class="grid grid-cols-[1.5rem_1fr_1fr_1.5rem] gap-1.5 px-1">
                    <span class="text-zinc-400 dark:text-zinc-600 text-xs text-center">#</span>
                    <span class="text-zinc-500 dark:text-zinc-400 text-xs">Weight (kg)</span>
                    <span class="text-zinc-500 dark:text-zinc-400 text-xs">Reps</span>
                    <span></span>
                </div>

                @foreach($sets as $i => $set)
                    <div class="grid grid-cols-[1.5rem_1fr_1fr_1.5rem] gap-1.5 items-center min-w-0">
                        <span class="text-zinc-400 dark:text-zinc-500 text-sm text-center font-mono">{{ $i + 1 }}</span>

                        <input wire:model="sets.{{ $i }}.weight"
                            type="number" inputmode="decimal" step="0.5" min="0" placeholder="0"
                            class="bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-2 py-2 text-zinc-900 dark:text-white text-base placeholder-zinc-400 dark:placeholder-zinc-600 focus:outline-none focus:border-orange-500 transition-colors w-full min-w-0"/>

                        <input wire:model="sets.{{ $i }}.reps"
                            type="number" inputmode="numeric" min="1" placeholder="0"
                            class="bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-2 py-2 text-zinc-900 dark:text-white text-base placeholder-zinc-400 dark:placeholder-zinc-600 focus:outline-none focus:border-orange-500 transition-colors w-full min-w-0"/>

                        <button type="button" wire:click="removeSet({{ $i }})"
                            class="text-zinc-400 dark:text-zinc-600 hover:text-red-400 transition-colors disabled:opacity-30"
                            @if(count($sets) === 1) disabled @endif>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    @if($errors->has("sets.{$i}.weight") || $errors->has("sets.{$i}.reps"))
                        <p class="text-red-400 text-xs pl-8">
                            {{ $errors->first("sets.{$i}.weight") ?: $errors->first("sets.{$i}.reps") }}
                        </p>
                    @endif
                @endforeach
            </div>

            {{-- Set hinzufügen --}}
            <button type="button" wire:click="addSet"
                class="w-full py-2 rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700 hover:border-orange-500 text-zinc-500 hover:text-orange-500 text-sm transition-colors mb-4 flex items-center justify-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Add Set
            </button>

            <button type="submit"
                class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl transition-colors">
                Save
            </button>
        </form>
    </div>
</div>
