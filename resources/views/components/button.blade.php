@props(['type' => 'button', 'variant' => 'primary', 'size' => 'md', 'icon' => null])
@php
    $baseClass = 'inline-flex items-center justify-center gap-2 rounded-lg font-body-sm font-semibold transition-all active:scale-95';
    $sizeClass = match($size) {
        'xs' => 'h-8 px-3 text-xs',
        'sm' => 'h-9 px-4 text-sm',
        'md' => 'h-10 px-5 text-sm',
        'lg' => 'h-12 px-8 text-base',
        default => 'h-10 px-5 text-sm',
    };
    $variantClass = match($variant) {
        'primary' => 'bg-primary text-on-primary hover:bg-primary/90 shadow-sm',
        'secondary' => 'bg-secondary text-on-secondary hover:bg-secondary/90 shadow-sm',
        'outline' => 'border border-outline-variant text-on-surface-variant hover:bg-surface-container',
        'ghost' => 'text-on-surface-variant hover:bg-surface-container',
        'error' => 'bg-error text-on-error hover:bg-error/90 shadow-sm',
        'success' => 'bg-green-600 text-white hover:bg-green-700 shadow-sm',
        default => 'bg-primary text-on-primary hover:bg-primary/90 shadow-sm',
    };
@endphp
<button type="{{ $type }}" {{ $attributes->merge(['class' => "$baseClass $sizeClass $variantClass"]) }}>
    @if($icon)<span class="material-symbols-outlined text-xl">{{ $icon }}</span>@endif
    {{ $slot }}
</button>
