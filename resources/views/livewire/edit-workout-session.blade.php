<div>
    {{-- Edit Modal --}}
    @if($open)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('open', false)"></div>

        <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-md p-6 border border-zinc-300 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Edit Session</h2>
                    <p class="text-zinc-500 text-xs mt-0.5">{{ $sessionDate }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="delete"
                        wire:confirm
                        class="p-1.5 rounded-lg text-zinc-400 dark:text-zinc-600 hover:text-red-400 hover:bg-red-500/10 transition-colors"
                        title="Einheit löschen">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                    <button wire:click="$set('open', false)" class="text-zinc-400 dark:text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form wire:submit="save" class="space-y-3">
                @if($isUnilateral)
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
                    @if($isUnilateral)
                    <div class="grid grid-cols-[1.5rem_1fr_1fr_1fr_1.5rem] gap-1.5 items-center min-w-0">
                        <span class="text-zinc-400 dark:text-zinc-500 text-sm text-center font-mono">{{ $i + 1 }}</span>
                        <input wire:model="sets.{{ $i }}.weight" type="number" inputmode="decimal" step="0.01" min="0"
                            class="bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-2 py-2 text-zinc-900 dark:text-white text-base focus:outline-none focus:border-orange-500 transition-colors w-full min-w-0"/>
                        <input wire:model="sets.{{ $i }}.reps_left" type="number" inputmode="numeric" min="1" placeholder="L"
                            class="bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-2 py-2 text-zinc-900 dark:text-white text-base focus:outline-none focus:border-orange-500 transition-colors w-full min-w-0"/>
                        <input wire:model="sets.{{ $i }}.reps_right" type="number" inputmode="numeric" min="1" placeholder="R"
                            class="bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-2 py-2 text-zinc-900 dark:text-white text-base focus:outline-none focus:border-orange-500 transition-colors w-full min-w-0"/>
                        <button type="button" wire:click="removeSet({{ $i }})"
                            class="text-zinc-400 dark:text-zinc-600 hover:text-red-400 transition-colors"
                            @if(count($sets) === 1) disabled @endif>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    @else
                    <div class="grid grid-cols-[1.5rem_1fr_1fr_1.5rem] gap-1.5 items-center min-w-0">
                        <span class="text-zinc-400 dark:text-zinc-500 text-sm text-center font-mono">{{ $i + 1 }}</span>
                        <input wire:model="sets.{{ $i }}.weight" type="number" inputmode="decimal" step="0.01" min="0"
                            class="bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-2 py-2 text-zinc-900 dark:text-white text-base focus:outline-none focus:border-orange-500 transition-colors w-full min-w-0"/>
                        <input wire:model="sets.{{ $i }}.reps" type="number" inputmode="numeric" min="1"
                            class="bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-2 py-2 text-zinc-900 dark:text-white text-base focus:outline-none focus:border-orange-500 transition-colors w-full min-w-0"/>
                        <button type="button" wire:click="removeSet({{ $i }})"
                            class="text-zinc-400 dark:text-zinc-600 hover:text-red-400 transition-colors"
                            @if(count($sets) === 1) disabled @endif>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    @endif
                @endforeach

                <button type="button" wire:click="addSet"
                    class="w-full py-2 rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700 hover:border-orange-500 text-zinc-500 hover:text-orange-500 text-sm transition-colors flex items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Add Set
                </button>

                <div class="flex gap-3 pt-1">
                    <button type="button" wire:click="$set('open', false)"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold transition-colors">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Undo Toast --}}
    @if($showUndo)
    <div
        x-data="{
            seconds: 15,
            timer: null,
            init() {
                this.timer = setInterval(() => {
                    this.seconds--;
                    if (this.seconds <= 0) {
                        clearInterval(this.timer);
                        $wire.permanentlyDelete();
                    }
                }, 1000);
            },
            undo() {
                clearInterval(this.timer);
                $wire.restore();
            }
        }"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-4 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-3.5 shadow-2xl"
    >
        <div class="relative w-8 h-8 flex-shrink-0">
            <svg class="w-8 h-8 -rotate-90" viewBox="0 0 32 32">
                <circle cx="16" cy="16" r="13" fill="none" stroke="#e4e4e7" class="dark:stroke-zinc-700" stroke-width="3"/>
                <circle cx="16" cy="16" r="13" fill="none" stroke="#f97316" stroke-width="3"
                    stroke-dasharray="81.68"
                    :stroke-dashoffset="81.68 - (81.68 * seconds / 15)"
                    style="transition: stroke-dashoffset 1s linear"/>
            </svg>
            <span class="absolute inset-0 flex items-center justify-center text-zinc-900 dark:text-white text-xs font-mono" x-text="seconds"></span>
        </div>

        <span class="text-zinc-600 dark:text-zinc-300 text-sm">Session deleted</span>

        <button @click="undo()"
            class="px-3 py-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold transition-colors">
            Undo
        </button>
    </div>
    @endif
</div>
