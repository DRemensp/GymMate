<div class="bg-white dark:bg-zinc-900/60 border border-zinc-300 dark:border-zinc-700 rounded-2xl p-5"
    x-data="{ open: false }">
    <h2 class="text-zinc-900 dark:text-white font-semibold mb-1">Konto löschen</h2>
    <p class="text-zinc-500 dark:text-zinc-400 text-sm mb-5">
        Nach dem Löschen werden alle Daten dauerhaft entfernt. Exportiere vorher alles Wichtige.
    </p>

    <button type="button" x-on:click="open = true"
        class="px-5 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">
        Konto löschen
    </button>

    {{-- Bestätigungs-Dialog --}}
    <div x-show="open" x-transition.opacity style="display:none"
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        x-on:keydown.escape.window="open = false">

        <div class="absolute inset-0 bg-black/50" x-on:click="open = false"></div>

        <div class="relative bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-2xl p-6 w-full max-w-md shadow-xl">
            <h3 class="text-zinc-900 dark:text-white font-semibold text-base mb-2">Wirklich löschen?</h3>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mb-5">
                Diese Aktion kann nicht rückgängig gemacht werden. Bitte gib dein Passwort ein, um zu bestätigen.
            </p>

            <form method="post" action="{{ route('settings.destroy') }}" class="space-y-4">
                @csrf
                @method('delete')

                <div>
                    <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Passwort</label>
                    <input id="password" name="password" type="password" placeholder="Dein Passwort"
                        class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-red-500 transition-colors" />
                    @if($errors->userDeletion->get('password'))
                        <p class="text-red-400 text-xs mt-1">{{ $errors->userDeletion->first('password') }}</p>
                    @endif
                </div>

                <div class="flex gap-3 justify-end pt-1">
                    <button type="button" x-on:click="open = false"
                        class="px-4 py-2 text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">
                        Abbrechen
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">
                        Endgültig löschen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
