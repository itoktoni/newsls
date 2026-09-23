@php
$isActive = request()->routeIs($route . '*') || request()->is($route . '*') || request()->url() == $route || request()->url() == rtrim($route, '/');
@endphp
<a href="{{ $route }}" class="flex flex-col items-center gap-0.5 {{ $isActive ? 'text-primary' : 'text-on-surface-variant' }}" wire:navigate>
    <span class="material-symbols-outlined" {{ $isActive ? "style=\"font-variation-settings: 'FILL' 1;\"" : '' }}>{{ $icon }}</span>
    <span class="text-[10px] font-semibold uppercase">{{ $label }}</span>
</a>
