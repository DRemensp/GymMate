<x-layouts.sidebar>
    <div class="min-h-screen px-4 pt-6 pb-12">
        <div class="max-w-lg mx-auto">

            <div class="mb-6 sm:pl-14">
                <h1 class="text-[19px] font-semibold text-zinc-900 dark:text-white tracking-tight">Following</h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-0.5">Finde und folge anderen Nutzern.</p>
            </div>

            <div class="sm:pl-14 space-y-4">

                {{-- Live-Suche --}}
                <livewire:search-users />

                {{-- Wem du folgst --}}
                @if($following->isNotEmpty())
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[22px] overflow-hidden">
                    <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800">
                        <span class="font-mono text-[10px] text-zinc-400 uppercase tracking-[0.08em]">Du folgst · {{ $following->count() }}</span>
                    </div>
                    @foreach($following as $followed)
                    <div class="flex items-center gap-3 px-4 py-3.5 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
                        <a href="{{ route('profile.public', $followed->name) }}" class="w-10 h-10 rounded-full overflow-hidden bg-accent-500 flex items-center justify-center text-sm font-bold text-white shrink-0">
                            @if($followed->avatarUrl())
                                <img src="{{ $followed->avatarUrl() }}" alt="" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($followed->name, 0, 1)) }}
                            @endif
                        </a>
                        <a href="{{ route('profile.public', $followed->name) }}" class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white truncate">{{ $followed->name }}</p>
                            @if($followed->country)
                            <p class="text-xs text-zinc-400 truncate">{{ $followed->country }}</p>
                            @endif
                        </a>
                        <form method="POST" action="{{ route('following.destroy', $followed->id) }}">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-zinc-300 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 rounded-xl hover:border-red-400 hover:text-red-400 transition-colors">
                                Gefolgt
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[22px] px-5 py-10 text-center">
                    <p class="text-zinc-400 text-sm">Du folgst noch niemandem.</p>
                    <p class="text-zinc-300 dark:text-zinc-600 text-xs mt-1">Suche oben nach Benutzernamen.</p>
                </div>
                @endif

            </div>
        </div>
    </div>
</x-layouts.sidebar>
