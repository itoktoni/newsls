@props(['type' => 'default'])
@php
    $class = match($type) {
        'success' => 'bg-green-100 text-green-700 border border-green-200',
        'error' => 'bg-error-container text-error border border-error/20',
        'warning' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'info' => 'bg-primary-fixed text-primary border border-primary/20',
        default => 'bg-surface-container text-on-surface-variant border border-outline-variant',
    };
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full font-label-caps text-label-caps $class"]) }}>{{ $slot }}</span>
