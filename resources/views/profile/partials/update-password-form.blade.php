<div class="bg-white dark:bg-zinc-900/60 border border-zinc-300 dark:border-zinc-700 rounded-2xl p-5">
    <h2 class="text-zinc-900 dark:text-white font-semibold mb-1">Passwort ändern</h2>
    <p class="text-zinc-500 dark:text-zinc-400 text-sm mb-5">Nutze ein langes, zufälliges Passwort für mehr Sicherheit.</p>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <div>
            <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Aktuelles Passwort</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-orange-500 transition-colors" />
            @if($errors->updatePassword->get('current_password'))
                <p class="text-red-400 text-xs mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Neues Passwort</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-orange-500 transition-colors" />
            @if($errors->updatePassword->get('password'))
                <p class="text-red-400 text-xs mt-1">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Passwort bestätigen</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-orange-500 transition-colors" />
            @if($errors->updatePassword->get('password_confirmation'))
                <p class="text-red-400 text-xs mt-1">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex items-center justify-between pt-1">
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-500">Gespeichert.</p>
            @else
                <span></span>
            @endif
            <button type="submit"
                class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl transition-colors">
                Speichern
            </button>
        </div>
    </form>
</div>
