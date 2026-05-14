@props(['step', 'icon' => '🏋️', 'title', 'show'])

@if($show)
<div x-data="{ open: true }"
     x-show="open"
     class="fixed inset-0 z-50 bg-black/40 flex items-end sm:items-center justify-center px-4 pb-6 sm:pb-0">
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="bg-white dark:bg-zinc-900 rounded-[22px] p-6 w-full max-w-sm shadow-2xl border border-zinc-200 dark:border-zinc-800">
        <div class="text-3xl">{{ $icon }}</div>
        <h3 class="text-[17px] font-semibold text-zinc-900 dark:text-white mt-3 mb-2">{{ $title }}</h3>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">{{ $slot }}</p>
        <button
            @click="fetch('/tour/{{ $step }}',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}}).then(()=>open=false)"
            class="w-full mt-5 px-5 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl transition-colors">
            Verstanden
        </button>
        <button
            @click="fetch('/tour/skip',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}}).then(()=>open=false)"
            class="w-full mt-3 text-sm text-zinc-400 hover:text-zinc-600 transition-colors text-center">
            Tour überspringen
        </button>
    </div>
</div>
@endif
