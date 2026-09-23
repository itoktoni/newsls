@props(['label' => null])
<div class="flex items-center justify-between mt-2 pt-2 border-t border-outline-variant/50">
    <x-badge>{{ $label }}</x-badge>
    <div class="flex gap-1" onclick="event.stopPropagation()">
        {{ $slot }}
    </div>
</div>
