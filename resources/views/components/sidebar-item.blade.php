<a href="{{ $route }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs($route) || request()->is($route) ? 'bg-primary-fixed text-primary font-semibold' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}" wire:navigate>
    <span class="material-symbols-outlined {{ request()->routeIs($route) || request()->is($route) ? 'text-primary' : 'text-on-surface-variant group-hover:text-on-surface' }}">{{ $icon }}</span>
    <span class="font-body-sm">{{ $label }}</span>
</a>
