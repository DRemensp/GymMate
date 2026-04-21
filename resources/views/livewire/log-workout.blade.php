<div>
    <div class="bg-white dark:bg-zinc-900/60 backdrop-blur-sm border border-zinc-300 dark:border-zinc-700 rounded-2xl shadow-sm p-5">
        <h2 class="text-zinc-900 dark:text-white font-semibold mb-4">Log Session</h2>

        <form wire:submit="save">

            {{-- Letzte Session --}}
            @if($lastSets)
            <div class="flex items-center justify-between mb-3 px-1">
                <span class="text-zinc-500 dark:text-zinc-400 text-xs">
                    Letzte: Ø {{ round(collect($lastSets)->avg('weight'), 2) }}kg &times; Ø {{ round(collect($lastSets)->avg('reps')) }} Reps
                </span>
            </div>
            @endif

            {{-- Set-Zeilen --}}
            <div class="space-y-2 mb-3">
                {{-- Header --}}
                @if($exercise->is_unilateral)
                <div class="grid grid-cols-[1.5rem_1fr_1fr_1fr_1.5rem] gap-1.5 px-1">
                    <span class="text-zinc-400 dark:text-zinc-600 text-xs text-center">#</span>
                    <span class="text-zinc-500 dark:text-zinc-400 text-xs">Gewicht (kg)</span>
                    <span class="text-zinc-500 dark:text-zinc-400 text-xs">Links</span>
                    <span class="text-zinc-500 dark:text-zinc-400 text-xs">Rechts</span>
                    <span></span>
                </div>
                @else
                <div class="grid grid-cols-[1.5rem_1fr_1fr_1.5rem] gap-1.5 px-1">
                    <span class="text-zinc-400 dark:text-zinc-600 text-xs text-center">#</span>
                    <span class="text-zinc-500 dark:text-zinc-400 text-xs">Gewicht (kg)</span>
                    <span class="text-zinc-500 dark:text-zinc-400 text-xs">Reps</span>
                    <span></span>
                </div>
                @endif

                @foreach($sets as $i => $set)
                    @if($lastSets && isset($lastSets[$i]))
                        @php
                            $lastWeight = $lastSets[$i]['weight'];
                            $rec = $progressionTip === 'increase' ? round($lastWeight * 1.10, 2) : $lastWeight;
                        @endphp
                        <div class="pl-8 -mb-0.5 flex items-center gap-1.5">
                            @if($progressionTip === 'increase')
                                <span class="text-xs text-orange-500 font-medium">↑ +10% → {{ $rec }}kg</span>
                            @else
                                <span class="text-xs text-zinc-400 dark:text-zinc-500">= {{ $rec }}kg halten</span>
                            @endif
                        </div>
                    @endif

                    @if($exercise->is_unilateral)
                    <div class="grid grid-cols-[1.5rem_1fr_1fr_1fr_1.5rem] gap-1.5 items-center min-w-0">
                        <span class="text-zinc-400 dark:text-zinc-500 text-sm text-center font-mono">{{ $i + 1 }}</span>

                        <input wire:model="sets.{{ $i }}.weight"
                            type="number" inputmode="decimal" step="0.01" min="0" placeholder="0"
                            class="bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-2 py-2 text-zinc-900 dark:text-white text-base placeholder-zinc-400 dark:placeholder-zinc-600 focus:outline-none focus:border-orange-500 transition-colors w-full min-w-0"/>

                        <input wire:model="sets.{{ $i }}.reps_left"
                            type="number" inputmode="numeric" min="1" placeholder="L"
                            class="bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-2 py-2 text-zinc-900 dark:text-white text-base placeholder-zinc-400 dark:placeholder-zinc-600 focus:outline-none focus:border-orange-500 transition-colors w-full min-w-0"/>

                        <input wire:model="sets.{{ $i }}.reps_right"
                            type="number" inputmode="numeric" min="1" placeholder="R"
                            class="bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-2 py-2 text-zinc-900 dark:text-white text-base placeholder-zinc-400 dark:placeholder-zinc-600 focus:outline-none focus:border-orange-500 transition-colors w-full min-w-0"/>

                        <button type="button" wire:click="removeSet({{ $i }})"
                            class="text-zinc-400 dark:text-zinc-600 hover:text-red-400 transition-colors disabled:opacity-30"
                            @if(count($sets) === 1) disabled @endif>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    @else
                    <div class="grid grid-cols-[1.5rem_1fr_1fr_1.5rem] gap-1.5 items-center min-w-0">
                        <span class="text-zinc-400 dark:text-zinc-500 text-sm text-center font-mono">{{ $i + 1 }}</span>

                        <input wire:model="sets.{{ $i }}.weight"
                            type="number" inputmode="decimal" step="0.01" min="0" placeholder="0"
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
                    @endif

                    @if($errors->has("sets.{$i}.weight") || $errors->has("sets.{$i}.reps") || $errors->has("sets.{$i}.reps_left") || $errors->has("sets.{$i}.reps_right"))
                        <p class="text-red-400 text-xs pl-8">
                            {{ $errors->first("sets.{$i}.weight") ?: ($errors->first("sets.{$i}.reps") ?: ($errors->first("sets.{$i}.reps_left") ?: $errors->first("sets.{$i}.reps_right"))) }}
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

            <div>
                <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Datum</label>
                <input wire:model="loggedAt" type="date"
                    max="{{ date('Y-m-d') }}"
                    class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-orange-500 transition-colors mb-3"/>
                @error('loggedAt') <p class="text-red-400 text-xs mb-2">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl transition-colors">
                Speichern
            </button>
        </form>
    </div>
</div>
