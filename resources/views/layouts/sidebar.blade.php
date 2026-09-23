{{-- Desktop Sidebar --}}
@php
    $totalMenuItems = collect(config('menu.sidebar'))->sum(fn($section) => count($section['items']));
@endphp
<aside class="hidden md:flex flex-col fixed top-16 left-0 h-[calc(100vh-4rem)] w-72 z-40 transition-transform duration-300 px-3 pt-4 border-r border-outline-variant/50 shadow-sm" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <nav style="flex:1 1 0; min-height:0; overflow-y:scroll; -webkit-overflow-scrolling:touch; touch-action:pan-y; padding-bottom:1rem" class="space-y-2 sidebar-scroll {{ $totalMenuItems > 15 ? 'pr-3' : '' }}">
        <x-menu-items />
    </nav>
</aside>