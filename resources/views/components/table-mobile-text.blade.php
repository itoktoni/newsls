@props(['text' => null, 'size' => 'sm', 'color' => null])
@php
$sizeClass = match($size) {
    'xs' => 'font-label-caps text-label-caps',
    'sm' => 'font-body-sm text-body-sm',
    'lg' => 'font-headline-md text-headline-md',
    'xl' => 'font-headline-lg text-headline-lg',
    default => 'font-body-sm text-body-sm'
};
$colorClass = match($color) {
    'primary' => 'text-primary',
    'secondary' => 'text-secondary',
    'success' => 'text-green-600',
    'warning' => 'text-amber-600',
    'error' => 'text-error',
    'muted' => 'text-on-surface-variant',
    default => 'text-on-surface'
};
$weightClass = $size === 'lg' || $size === 'xl' ? 'font-bold' : '';
@endphp
<p class="{{ $sizeClass }} {{ $colorClass }} {{ $weightClass }} {{ $attributes->get('class') }}">{{ $text }}</p>
