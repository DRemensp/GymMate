<div class="bg-white dark:bg-zinc-900/60 border border-zinc-300 dark:border-zinc-700 rounded-2xl p-5">
    <h2 class="text-zinc-900 dark:text-white font-semibold mb-1">Profildaten</h2>
    <p class="text-zinc-500 dark:text-zinc-400 text-sm mb-5">Name, E-Mail und Körperdaten anpassen.</p>

    {{-- Avatar Upload --}}
    <form method="post" action="{{ route('settings.avatar') }}" enctype="multipart/form-data" class="mb-5 pb-5 border-b border-zinc-200 dark:border-zinc-700/60">
        @csrf
        <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-2">Profilbild</label>
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full overflow-hidden bg-accent-500 flex items-center justify-center text-xl font-bold text-white shrink-0">
                @if($user->avatarUrl())
                    <img src="{{ $user->avatarUrl() }}" alt="" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <input id="avatar" name="avatar" type="file" accept="image/*" class="hidden"
                    onchange="this.form.submit()">
                <label for="avatar"
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg text-xs text-zinc-600 dark:text-zinc-300 cursor-pointer hover:border-accent-500 hover:text-accent-500 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                    Bild hochladen
                </label>
                <p class="text-[11px] text-zinc-400 mt-1">Max. 2 MB · JPG, PNG, WebP</p>
            </div>
        </div>
        @if(session('status') === 'avatar-updated')
            <p class="text-xs text-green-500 mt-2">Profilbild gespeichert.</p>
        @endif
        @error('avatar') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
    </form>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('settings.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Benutzername</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name"
                class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-accent-500 transition-colors" />
            @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">E-Mail</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-accent-500 transition-colors" />
            @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 flex items-center gap-3">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">E-Mail nicht verifiziert.</p>
                    <button form="send-verification" class="text-xs text-accent-500 hover:underline">
                        Verifizierungsmail senden
                    </button>
                </div>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 text-xs text-green-500">Verifizierungsmail wurde gesendet.</p>
                @endif
            @endif
        </div>

        <div class="border-t border-zinc-200 dark:border-zinc-700/60 pt-4">
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-3 font-medium uppercase tracking-wide">Körperdaten für kcal-Berechnung</p>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Gewicht (kg)</label>
                    <input id="weight_kg" name="weight_kg" type="number" step="0.1" min="30" max="300"
                        value="{{ old('weight_kg', $user->weight_kg) }}"
                        class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-accent-500 transition-colors" />
                    @error('weight_kg') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Größe (cm)</label>
                    <input id="height_cm" name="height_cm" type="number" min="100" max="250"
                        value="{{ old('height_cm', $user->height_cm) }}"
                        class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-accent-500 transition-colors" />
                    @error('height_cm') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-3">
                <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Geschlecht</label>
                <select id="gender" name="gender"
                    class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-accent-500 transition-colors">
                    <option value="">– Keine Angabe –</option>
                    <option value="männlich" {{ old('gender', $user->gender) === 'männlich' ? 'selected' : '' }}>Männlich</option>
                    <option value="weiblich" {{ old('gender', $user->gender) === 'weiblich' ? 'selected' : '' }}>Weiblich</option>
                    <option value="divers"   {{ old('gender', $user->gender) === 'divers'   ? 'selected' : '' }}>Divers</option>
                </select>
                @error('gender') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mt-3">
                <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Geburtstag</label>
                <input id="birthday" name="birthday" type="date"
                    value="{{ old('birthday', $user->birthday?->format('Y-m-d')) }}"
                    max="{{ now()->toDateString() }}"
                    class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-accent-500 transition-colors" />
                @error('birthday') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mt-3">
                <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Land</label>
                <input id="country" name="country" type="text" maxlength="100"
                    value="{{ old('country', $user->country) }}"
                    placeholder="z. B. Deutschland"
                    class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-zinc-900 dark:text-white text-sm focus:outline-none focus:border-accent-500 transition-colors" />
                @error('country') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-between pt-1">
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-500">Gespeichert.</p>
            @else
                <span></span>
            @endif
            <button type="submit"
                class="px-5 py-2 bg-accent-500 hover:bg-accent-600 text-white text-sm font-semibold rounded-xl transition-colors">
                Speichern
            </button>
        </div>
    </form>
</div>
