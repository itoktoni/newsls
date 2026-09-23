@props(['type' => 'info'])
@php
    $class = match($type) {
        'success' => 'bg-green-50 border-green-200 text-green-700',
        'error' => 'bg-error-container/50 border-error/20 text-error',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-700',
        'info' => 'bg-primary-fixed/50 border-primary/20 text-primary',
        default => 'bg-surface-container border-outline-variant text-on-surface-variant',
    };
    $icon = match($type) {
        'success' => 'check_circle',
        'error' => 'error',
        'warning' => 'warning',
        'info' => 'info',
        default => 'info',
    };
@endphp
<div {{ $attributes->merge(['class' => "flex items-center gap-3 px-4 py-3 rounded-lg border font-body-sm text-body-sm $class"]) }}>
    <span class="material-symbols-outlined text-lg">{{ $icon }}</span>
    <span>{{ $slot }}</span>
</div>
