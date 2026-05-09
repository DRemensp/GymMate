<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-lg font-semibold text-gray-900">Willkommen bei GymMate!</h2>
        <p class="mt-1 text-sm text-gray-600">Damit wir deinen Kalorienverbrauch berechnen können, brauchen wir ein paar Angaben.</p>
    </div>

    <form method="POST" action="{{ route('onboarding.store') }}">
        @csrf

        <div>
            <x-input-label for="weight_kg" value="Gewicht (kg)" />
            <x-text-input id="weight_kg" class="block mt-1 w-full" type="number" name="weight_kg"
                :value="old('weight_kg')" step="0.1" min="30" max="300" required />
            <x-input-error :messages="$errors->get('weight_kg')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="height_cm" value="Größe (cm)" />
            <x-text-input id="height_cm" class="block mt-1 w-full" type="number" name="height_cm"
                :value="old('height_cm')" min="100" max="250" required />
            <x-input-error :messages="$errors->get('height_cm')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="gender" value="Geschlecht" />
            <select id="gender" name="gender"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Bitte wählen</option>
                <option value="männlich" {{ old('gender') === 'männlich' ? 'selected' : '' }}>Männlich</option>
                <option value="weiblich" {{ old('gender') === 'weiblich' ? 'selected' : '' }}>Weiblich</option>
                <option value="divers" {{ old('gender') === 'divers' ? 'selected' : '' }}>Divers</option>
            </select>
            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('dashboard') }}"
                class="text-sm text-gray-500 hover:text-gray-700 underline">
                Überspringen
            </a>
            <x-primary-button>
                Weiter
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
