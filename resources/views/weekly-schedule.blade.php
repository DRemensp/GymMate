<x-layouts.sidebar>

    <div class="min-h-screen px-6 pt-8 pb-10">
        <div class="max-w-xl mx-auto">

            <div class="mb-8 sm:pl-14">
                <h1 class="text-2xl font-bold text-white">Trainingsplan</h1>
                <p class="text-zinc-500 text-sm mt-1">Lege fest was an welchem Wochentag ansteht.</p>
            </div>

            <div class="sm:pl-14">
                @if(session('saved'))
                <div class="mb-4 px-4 py-3 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400 text-sm">
                    Gespeichert.
                </div>
                @endif

                <form method="POST" action="{{ route('weekly-schedule.update') }}" class="space-y-3">
                    @csrf

                    @php
                        $dayNames = [1 => 'Montag', 2 => 'Dienstag', 3 => 'Mittwoch', 4 => 'Donnerstag', 5 => 'Freitag', 6 => 'Samstag', 7 => 'Sonntag'];
                        $today = \Carbon\Carbon::today()->isoWeekday();
                    @endphp

                    @foreach($days as $dow => $entry)
                    <div class="bg-zinc-900 border {{ $dow === $today ? 'border-orange-500/50' : 'border-zinc-800' }} rounded-2xl px-4 py-3">

                        {{-- Kopfzeile: Tag + Rest-Checkbox --}}
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold {{ $dow === $today ? 'text-orange-400' : 'text-zinc-400' }}">
                                {{ $dayNames[$dow] }}
                                @if($dow === $today)
                                    <span class="ml-1.5 text-xs text-orange-500/60 font-normal">heute</span>
                                @endif
                            </span>
                            <label class="flex items-center gap-1.5 cursor-pointer select-none">
                                <input type="checkbox" name="days[{{ $dow }}][is_rest]" value="1"
                                    {{ $entry->is_rest ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-zinc-600 bg-zinc-800 text-orange-500 focus:ring-0 focus:ring-offset-0"
                                    onchange="const inp = this.closest('.bg-zinc-900').querySelector('input[type=text]'); inp.disabled = this.checked; if(this.checked) inp.value = '';">
                                <span class="text-zinc-500 text-xs">Rest Day</span>
                            </label>
                        </div>

                        {{-- Input --}}
                        <input type="text"
                            name="days[{{ $dow }}][label]"
                            value="{{ $entry->label }}"
                            placeholder="z.B. Push Pull (Brust / Rücken)"
                            {{ $entry->is_rest ? 'disabled' : '' }}
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-3 py-2 text-white text-sm placeholder-zinc-600 focus:outline-none focus:border-orange-500 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"/>
                    </div>
                    @endforeach

                    <button type="submit"
                        class="w-full py-2.5 mt-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl transition-colors">
                        Speichern
                    </button>
                </form>
            </div>

        </div>
    </div>

</x-layouts.sidebar>
