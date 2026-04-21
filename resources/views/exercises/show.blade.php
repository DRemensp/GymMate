<x-layouts.sidebar>

    <div class="min-h-screen px-6 pt-8 pb-10">
        <div class="max-w-2xl mx-auto">

            <div class="mb-8 sm:pl-14 flex items-center gap-3">
                <a href="{{ route('training-plans.exercises.index', $exercise->trainingPlan) }}"
                    class="text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $exercise->name }}</h1>
                    @if($exercise->description)
                        <p class="text-zinc-500 text-sm mt-0.5">{{ $exercise->description }}</p>
                    @endif
                </div>
            </div>

            <div class="sm:pl-14 space-y-6"
                 x-data
                 x-on:session-saved.window="window.location.reload()">

                <livewire:log-workout :exercise="$exercise" />
                <livewire:edit-workout-session />

                @if($sessions->isNotEmpty())
                <div class="bg-white dark:bg-zinc-900/60 backdrop-blur-sm border border-zinc-300 dark:border-zinc-700 rounded-2xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-zinc-300 dark:border-zinc-700">
                        <h2 class="text-zinc-900 dark:text-white font-semibold">History</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-zinc-300 dark:border-zinc-700">
                                    <th class="text-left text-zinc-500 font-medium px-5 py-3 w-36">Date</th>
                                    <th class="text-left text-zinc-500 font-medium px-3 py-3">Set</th>
                                    <th class="text-left text-zinc-500 font-medium px-3 py-3">Weight</th>
                                    <th class="text-left text-zinc-500 font-medium px-3 py-3">Reps</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                                @foreach($sessions as $session)
                                    @foreach($session->sets as $set)
                                        <tr x-on:click="Livewire.dispatchTo('edit-workout-session', 'load-session', { id: {{ $session->id }} })"
                                            class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors cursor-pointer group">
                                            <td class="px-5 py-2.5 text-zinc-500 dark:text-zinc-400">
                                                @if($loop->first)
                                                    <span class="group-hover:text-orange-500 transition-colors">{{ $session->logged_at->format('d.m.Y') }}</span>
                                                    <span class="block text-zinc-400 dark:text-zinc-600 text-xs">{{ $session->logged_at->format('H:i') }}</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2.5 text-zinc-400 dark:text-zinc-500 font-mono text-xs">{{ $set->set_number }}</td>
                                            <td class="px-3 py-2.5 text-zinc-900 dark:text-white font-semibold">{{ $set->weight }} kg</td>
                                            <td class="px-3 py-2.5 text-zinc-600 dark:text-zinc-300">{{ $set->reps }}x</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                    <p class="text-zinc-500 text-sm text-center py-4">No sessions logged yet.</p>
                @endif

            </div>
        </div>
    </div>

    {{-- Pause Timer FAB --}}
    <div
        x-data="{
            open: false,
            duration: 120,
            remaining: 120,
            running: false,
            _interval: null,
            fmt(s) {
                let m = Math.floor(s / 60);
                let sec = s % 60;
                return String(m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
            },
            progress() {
                return this.duration > 0 ? (this.remaining / this.duration) : 1;
            },
            circumference: 2 * Math.PI * 38,
            dashOffset() {
                return this.circumference * (1 - this.progress());
            },
            preset(s) {
                this.stop();
                this.duration = s;
                this.remaining = s;
            },
            start() {
                if (this.remaining <= 0) this.remaining = this.duration;
                this.running = true;
                this._interval = setInterval(() => {
                    if (this.remaining > 0) {
                        this.remaining--;
                    } else {
                        this.stop();
                        this.beep();
                    }
                }, 1000);
            },
            stop() {
                clearInterval(this._interval);
                this.running = false;
            },
            reset() {
                this.stop();
                this.remaining = this.duration;
            },
            beep() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    [0, 0.15, 0.3].forEach(offset => {
                        const o = ctx.createOscillator();
                        const g = ctx.createGain();
                        o.connect(g); g.connect(ctx.destination);
                        o.frequency.value = 880;
                        g.gain.setValueAtTime(0.3, ctx.currentTime + offset);
                        g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + offset + 0.12);
                        o.start(ctx.currentTime + offset);
                        o.stop(ctx.currentTime + offset + 0.12);
                    });
                } catch(e) {}
            }
        }"
        class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-3"
    >
        {{-- Overlay Card --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-2xl shadow-2xl p-5 w-52"
        >
            {{-- Ring + Zeit --}}
            <div class="flex items-center justify-center mb-4">
                <div class="relative w-24 h-24">
                    <svg class="w-24 h-24 -rotate-90" viewBox="0 0 88 88">
                        <circle cx="44" cy="44" r="38" fill="none" stroke-width="6"
                            class="stroke-zinc-200 dark:stroke-zinc-700"/>
                        <circle cx="44" cy="44" r="38" fill="none" stroke-width="6"
                            :stroke="remaining === 0 ? '#22c55e' : '#f97316'"
                            stroke-linecap="round"
                            :stroke-dasharray="circumference"
                            :stroke-dashoffset="dashOffset()"
                            style="transition: stroke-dashoffset 0.9s linear, stroke 0.3s"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span
                            class="text-xl font-bold font-mono tabular-nums"
                            :class="remaining === 0 ? 'text-green-500' : 'text-zinc-900 dark:text-white'"
                            x-text="fmt(remaining)">
                        </span>
                    </div>
                </div>
            </div>

            {{-- Presets --}}
            <div class="flex gap-1.5 mb-3">
                <template x-for="s in [90, 120, 180]">
                    <button
                        @click="preset(s)"
                        :class="duration === s ? 'bg-orange-500 text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                        class="flex-1 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                        x-text="s + 's'">
                    </button>
                </template>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <button
                    @click="running ? stop() : start()"
                    :class="running ? 'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200' : 'bg-orange-500 hover:bg-orange-600 text-white'"
                    class="flex-1 py-2 rounded-xl text-sm font-semibold transition-colors"
                    x-text="running ? 'Pause' : (remaining < duration && remaining > 0 ? 'Weiter' : 'Start')">
                </button>
                <button
                    @click="reset()"
                    class="px-3 py-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- FAB Button --}}
        <button
            @click="open = !open"
            :class="running ? 'ring-4 ring-orange-500/40' : ''"
            class="w-14 h-14 bg-orange-500 hover:bg-orange-600 text-white rounded-full shadow-lg flex items-center justify-center transition-all"
        >
            <span x-show="!running || open" class="pointer-events-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
            <span
                x-show="running && !open"
                class="text-xs font-bold font-mono tabular-nums pointer-events-none"
                x-text="fmt(remaining)">
            </span>
        </button>
    </div>

</x-layouts.sidebar>
