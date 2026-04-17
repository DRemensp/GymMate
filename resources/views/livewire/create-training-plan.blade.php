<div>
    <button wire:click="$set('open', true)"
        class="flex items-center justify-center w-full h-full min-h-48 rounded-2xl border-2 border-dashed border-zinc-300 dark:border-zinc-700 hover:border-orange-500 hover:bg-orange-500/5 transition-colors group">
        <svg class="w-10 h-10 text-zinc-400 dark:text-zinc-600 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
    </button>

    @if($open)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('open', false)"></div>

        <div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-md p-6 border border-zinc-300 dark:border-zinc-700">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-5">Neuer Trainingsplan</h2>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-sm text-zinc-500 dark:text-zinc-400 mb-1">Name</label>
                    <input wire:model="name" type="text" placeholder="z.B. Push / Pull / Legs"
                        class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-xl px-4 py-2.5 text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:border-orange-500 transition-colors"/>
                    @error('name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm text-zinc-500 dark:text-zinc-400 mb-1">Bild <span class="text-zinc-400 dark:text-zinc-600">(optional)</span></label>
                    <input wire:model="image" type="file" accept="image/*"
                        class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-xl px-4 py-2.5 text-zinc-500 dark:text-zinc-400 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-orange-500 file:text-white file:text-sm cursor-pointer"/>
                    @error('image') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror

                    @if($image)
                        <img src="{{ $image->temporaryUrl() }}" class="mt-2 h-24 w-full object-cover rounded-xl"/>
                    @endif
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('open', false)"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        Abbrechen
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-semibold transition-colors">
                        Erstellen
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
