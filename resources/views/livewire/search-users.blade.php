<div class="space-y-4">

    {{-- Suchfeld --}}
    <div class="relative">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
        </svg>
        <input
            wire:model.live.debounce.300ms="query"
            type="text"
            placeholder="Benutzername suchen…"
            autofocus
            autocomplete="off"
            class="w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl pl-10 pr-4 py-3 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:border-orange-500 transition-colors"/>
    </div>

    {{-- Suchergebnisse --}}
    @if($results->isNotEmpty())
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[22px] overflow-hidden">
        <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800">
            <span class="font-mono text-[10px] text-zinc-400 uppercase tracking-[0.08em]">{{ $results->count() }} Ergebnis{{ $results->count() !== 1 ? 'se' : '' }}</span>
        </div>
        @foreach($results as $result)
        @php $isFollowing = $followingIds->contains($result->id); @endphp
        <div class="flex items-center gap-3 px-4 py-3.5 border-b border-zinc-100 dark:border-zinc-800 last:border-0">
            <a href="{{ route('profile.public', $result->name) }}" class="w-10 h-10 rounded-full overflow-hidden bg-orange-500 flex items-center justify-center text-sm font-bold text-white shrink-0">
                @if($result->avatarUrl())
                    <img src="{{ $result->avatarUrl() }}" alt="" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($result->name, 0, 1)) }}
                @endif
            </a>
            <a href="{{ route('profile.public', $result->name) }}" class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-zinc-900 dark:text-white truncate">{{ $result->name }}</p>
                @if($result->country)
                <p class="text-xs text-zinc-400 truncate">{{ $result->country }}</p>
                @endif
            </a>
            @if($isFollowing)
            <button wire:click="unfollow({{ $result->id }})"
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-zinc-300 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 rounded-xl hover:border-red-400 hover:text-red-400 transition-colors">
                Gefolgt
            </button>
            @else
            <button wire:click="follow({{ $result->id }})"
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-orange-500 hover:bg-orange-600 text-white rounded-xl transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Folgen
            </button>
            @endif
        </div>
        @endforeach
    </div>
    @elseif(strlen($query) >= 1)
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[22px] px-5 py-8 text-center">
        <p class="text-zinc-400 text-sm">Kein Nutzer gefunden für „{{ $query }}"</p>
    </div>
    @endif

</div>
