<x-layouts.sidebar>
    <div class="min-h-screen px-4 pt-6 pb-12">
        <div class="max-w-lg mx-auto space-y-4">

            {{-- Hero Card --}}
            <div class="relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[22px] overflow-hidden">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-56 h-28 bg-accent-500/15 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative px-5 pt-7 pb-6">
                    @auth
                        @if(Auth::user()->name === $user->name)
                        <a href="{{ route('settings.edit') }}"
                            class="absolute top-4 right-4 flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 rounded-xl hover:text-accent-500 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                            </svg>
                            Bearbeiten
                        </a>
                        @endif
                    @endauth

                    <div class="flex flex-col items-center text-center">
                        <div class="w-[72px] h-[72px] rounded-full overflow-hidden bg-gradient-to-br from-accent-400 to-accent-600 flex items-center justify-center text-[28px] font-bold text-white shadow-lg shadow-accent-500/30 mb-3 shrink-0">
                            @if($user->avatarUrl())
                                <img src="{{ $user->avatarUrl() }}" alt="" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                        <h1 class="text-[20px] font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $user->name }}</h1>
                        @if($user->country)
                        <p class="text-sm text-zinc-400 dark:text-zinc-500 mt-0.5">{{ $user->country }}</p>
                        @endif

                        {{-- Inline stats --}}
                        @php $metaItems = array_filter([
                            $user->age() ? $user->age() . ' J.' : null,
                            $user->height_cm ? $user->height_cm . ' cm' : null,
                            $user->weight_kg ? number_format((float)$user->weight_kg, 1) . ' kg' : null,
                        ]); @endphp
                        @if(count($metaItems))
                        <div class="flex items-center gap-2 mt-2.5">
                            @foreach($metaItems as $item)
                                @if(!$loop->first)<span class="w-px h-3 bg-zinc-200 dark:bg-zinc-700"></span>@endif
                                <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $item }}</span>
                            @endforeach
                        </div>
                        @endif

                        <p class="text-[11px] text-zinc-300 dark:text-zinc-700 mt-2">
                            Dabei seit {{ $user->created_at->translatedFormat('M Y') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Stats Grid --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[18px] p-4">
                    <div class="font-mono text-[10px] text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em] mb-1">Sessions</div>
                    <div class="text-[28px] font-medium text-accent-500 tracking-tight leading-none">{{ number_format($totalSessions) }}</div>
                </div>

                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[18px] p-4">
                    <div class="font-mono text-[10px] text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em] mb-1">
                        Wochen Streak {{ $streak > 0 ? '🔥' : '' }}
                    </div>
                    <div class="flex items-baseline gap-1 leading-none">
                        <span class="text-[28px] font-medium text-zinc-900 dark:text-white tracking-tight">{{ $streak }}</span>
                        <span class="text-sm text-zinc-400">W</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[18px] p-4">
                    <div class="font-mono text-[10px] text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em] mb-1">Geliftet</div>
                    @if($totalVolume >= 1000)
                        <div class="flex items-baseline gap-0.5 leading-none">
                            <span class="text-[28px] font-medium text-zinc-900 dark:text-white tracking-tight">{{ number_format($totalVolume / 1000, 1) }}</span>
                            <span class="text-sm text-zinc-400">t</span>
                        </div>
                    @else
                        <div class="flex items-baseline gap-0.5 leading-none">
                            <span class="text-[28px] font-medium text-zinc-900 dark:text-white tracking-tight">{{ number_format($totalVolume) }}</span>
                            <span class="text-sm text-zinc-400">kg</span>
                        </div>
                    @endif
                </div>

                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[18px] p-4">
                    <div class="font-mono text-[10px] text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em] mb-1">Lieblingsort</div>
                    @if($favoriteLocation && $favoriteLocation['count'] > 0)
                        <div class="text-[15px] font-semibold text-zinc-900 dark:text-white leading-tight truncate">{{ $favoriteLocation['name'] }}</div>
                        <div class="text-[11px] text-zinc-400 mt-0.5">{{ $favoriteLocation['count'] }}× besucht</div>
                    @else
                        <div class="text-[22px] font-medium text-zinc-300 dark:text-zinc-700 leading-none">–</div>
                    @endif
                </div>

                @if($user->bmi())
                @php
                    $bmi = $user->bmi();
                    [$bmiLabel, $bmiColor] = match(true) {
                        $bmi < 18.5 => ['Untergewicht', 'text-blue-400'],
                        $bmi < 25   => ['Normalgewicht', 'text-green-400'],
                        $bmi < 30   => ['Übergewicht', 'text-yellow-400'],
                        default     => ['Adipositas', 'text-red-400'],
                    };
                @endphp
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[18px] p-4">
                    <div class="font-mono text-[10px] text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em] mb-1">BMI</div>
                    <div class="flex items-baseline gap-1 leading-none">
                        <span class="text-[28px] font-medium text-zinc-900 dark:text-white tracking-tight">{{ $bmi }}</span>
                    </div>
                    <div class="text-[11px] {{ $bmiColor }} mt-0.5">{{ $bmiLabel }}</div>
                </div>
                @endif

@if($user->prime_time)
                @php
                    $primeRanges = [
                        'Morgen'     => '05–09 Uhr',
                        'Vormittag'  => '10–13 Uhr',
                        'Nachmittag' => '14–17 Uhr',
                        'Abend'      => '18–21 Uhr',
                        'Nacht'      => '22–04 Uhr',
                    ];
                @endphp
                <div class="bg-accent-500/5 border border-accent-500/20 rounded-[18px] p-4 col-span-2">
                    <div class="font-mono text-[10px] text-accent-400/70 uppercase tracking-[0.08em] mb-1">PrimeTime</div>
                    <div class="text-[18px] font-semibold text-accent-500 leading-tight">{{ $user->prime_time }}</div>
                    <div class="text-[11px] text-accent-400/60 mt-0.5">{{ $primeRanges[$user->prime_time] ?? '' }}</div>
                </div>
                @endif
            </div>

            {{-- Aktivität + Letztes Training nebeneinander --}}
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[22px] p-[18px] flex items-start gap-4">

                {{-- Heatmap links --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[15px] font-semibold text-zinc-900 dark:text-white">Aktivität</span>
                        <span class="font-mono text-[10px] text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em]">
                            {{ now()->translatedFormat('M Y') }}
                        </span>
                    </div>

                    @php $todayDay = now()->day; @endphp
                    <div class="grid gap-[4px]" style="grid-template-columns: repeat(10, 9px)">
                        @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $trained  = isset($trainedDays[$day]);
                            $isToday  = $day === $todayDay;
                            $isFuture = $day > $todayDay;
                        @endphp
                        <span class="w-[9px] h-[9px] rounded-full transition-colors
                            @if($trained) bg-green-400
                            @elseif($isToday) bg-accent-400/60
                            @elseif($isFuture) bg-zinc-200 dark:bg-zinc-800
                            @else bg-zinc-200 dark:bg-zinc-700
                            @endif"
                            @if($isToday && !$trained) style="box-shadow: 0 0 0 1.5px var(--accent-hex), 0 0 0 3px color-mix(in srgb, var(--accent-hex) 15%, transparent)"
                            @elseif($trained) style="box-shadow: 0 0 5px 1px rgba(74,222,128,0.5)"
                            @endif>
                        </span>
                        @endfor
                    </div>
                </div>

                {{-- Divider --}}
                <div class="w-px self-stretch bg-zinc-100 dark:bg-zinc-800 shrink-0"></div>

                {{-- Letztes Training rechts --}}
                <div class="shrink-0 flex flex-col justify-center">
                    <div class="font-mono text-[10px] text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em] mb-2">Letztes Training</div>
                    @if($lastTraining)
                        <div class="text-[13px] font-semibold text-zinc-900 dark:text-white leading-tight">
                            {{ $lastTraining->diffForHumans() }}
                        </div>
                        <div class="text-[11px] text-zinc-400 mt-0.5">{{ $lastTraining->format('d.m.Y') }}</div>
                    @else
                        <div class="text-[22px] font-medium text-zinc-300 dark:text-zinc-700 leading-none">–</div>
                    @endif
                </div>

            </div>

            {{-- Wochenplan --}}
            @if($weeklySchedule->isNotEmpty())
            @php
                $dayShort = [1=>'Mo',2=>'Di',3=>'Mi',4=>'Do',5=>'Fr',6=>'Sa',7=>'So'];
                $dayFull  = [1=>'Montag',2=>'Dienstag',3=>'Mittwoch',4=>'Donnerstag',5=>'Freitag',6=>'Samstag',7=>'Sonntag'];
                $todayDow = now()->dayOfWeekIso;
            @endphp
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[22px] overflow-hidden"
                 x-data="{ selected: {{ $todayDow }} }">

                <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800">
                    <span class="text-[15px] font-semibold text-zinc-900 dark:text-white">Wochenplan</span>
                </div>

                {{-- Day pills --}}
                <div class="flex gap-2 overflow-x-auto px-4 py-3 scrollbar-none">
                    @foreach(range(1, 7) as $dow)
                    @php $entry = $weeklySchedule->get($dow); @endphp
                    <button
                        @click="selected = {{ $dow }}"
                        :class="selected === {{ $dow }}
                            ? '{{ $dow === $todayDow ? 'bg-accent-500 border-accent-500 text-white' : 'bg-zinc-800 dark:bg-white border-zinc-800 dark:border-white text-white dark:text-zinc-900' }}'
                            : 'border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400'"
                        class="flex-shrink-0 flex flex-col items-center gap-1 px-3.5 py-2.5 rounded-2xl border transition-all">
                        <span class="text-[12px] font-semibold">{{ $dayShort[$dow] }}</span>
                        <span class="w-1.5 h-1.5 rounded-full
                            {{ $entry?->is_rest ? 'bg-zinc-300 dark:bg-zinc-600' : ($entry?->exercises->isNotEmpty() ? 'bg-accent-400' : 'bg-transparent') }}"
                            :class="{ 'opacity-0': selected === {{ $dow }} }">
                        </span>
                    </button>
                    @endforeach
                </div>

                {{-- Day panels --}}
                @foreach(range(1, 7) as $dow)
                @php $entry = $weeklySchedule->get($dow); @endphp
                <div x-show="selected === {{ $dow }}" x-cloak
                     class="border-t border-zinc-100 dark:border-zinc-800 {{ $dow === $todayDow ? 'border-accent-500/20' : '' }}">
                    <div class="flex items-center gap-2 px-5 py-3 border-b border-zinc-100 dark:border-zinc-800 {{ $dow === $todayDow ? 'bg-accent-500/5' : '' }}">
                        <span class="text-sm font-semibold {{ $dow === $todayDow ? 'text-accent-400' : 'text-zinc-700 dark:text-zinc-300' }}">
                            {{ $dayFull[$dow] }}
                        </span>
                        @if($dow === $todayDow)
                        <span class="text-[10px] text-accent-400/70">heute</span>
                        @endif
                    </div>
                    <div class="px-5 py-3.5">
                        @if(!$entry || (!$entry->is_rest && $entry->exercises->isEmpty()))
                            <p class="text-sm text-zinc-400 dark:text-zinc-600">Kein Plan</p>
                        @elseif($entry->is_rest)
                            <p class="text-sm text-zinc-400 dark:text-zinc-600 font-medium">Rest Day</p>
                        @else
                            <div class="space-y-2.5">
                                @foreach($entry->exercises as $exercise)
                                <div class="flex items-center gap-3">
                                    <span class="w-1.5 h-1.5 rounded-full bg-accent-400 shrink-0"></span>
                                    <span class="text-sm text-zinc-800 dark:text-zinc-200">{{ $exercise->name }}</span>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach

            </div>
            @endif

        </div>
    </div>
</x-layouts.sidebar>
