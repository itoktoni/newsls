{{-- Mobile Drawer --}}
<div class="fixed top-0 left-0 h-full w-72 bg-surface-container-lowest z-50 md:hidden shadow-2xl flex flex-col transition-transform duration-300 -translate-x-full" :class="drawerOpen ? 'translate-x-0' : '-translate-x-full'">
    <div class="flex items-center justify-between px-5 h-16 border-b border-outline-variant shrink-0">
        <h2 class="font-headline-md text-headline-md font-bold text-primary">{{ config('website.name', 'CMS') }}</h2>
        <button class="p-2 hover:bg-surface-container rounded-full transition-colors" @click="drawerOpen = false">
            <span class="text-on-surface-variant">✕</span>
        </button>
    </div>
    <nav style="flex:1 1 0; min-height:0; padding:1rem 0.75rem 6rem 0.75rem; overflow-y:scroll; -webkit-overflow-scrolling:touch; touch-action:pan-y">
        <div class="space-y-1">
            <x-menu-items :mobile="true" />
        </div>
    </nav>
</div>