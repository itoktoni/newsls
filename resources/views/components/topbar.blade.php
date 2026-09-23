<div class="lg:hidden fixed top-0 left-0 right-0 h-11 bg-surface-container-lowest border-b border-outline-variant shadow-sm py-7 flex items-center px-3 z-50">
    <button class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-surface-container transition-colors" onclick="toggleSB()">
        <span class="material-symbols-outlined text-on-surface-variant">menu</span>
    </button>
    <span class="ml-2 font-headline-md text-headline-md text-on-surface">{{ $title ?? config('app.name') }}</span>
    <div class="flex-1"></div>
</div>
