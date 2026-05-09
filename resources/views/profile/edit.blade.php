<x-layouts.sidebar>
    <div class="min-h-screen px-6 pt-8 pb-10">
        <div class="max-w-2xl mx-auto">

            <div class="mb-8 sm:pl-14">
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Profil</h1>
                <p class="text-zinc-500 text-sm mt-1">Kontoeinstellungen & persönliche Daten.</p>
            </div>

            <div class="sm:pl-14 space-y-4">
                @include('profile.partials.update-profile-information-form')
                @include('profile.partials.update-password-form')
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
</x-layouts.sidebar>
