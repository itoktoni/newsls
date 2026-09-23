@props(['id' => null])
<div class="mx-1 mb-3 border border-outline-variant rounded-xl p-3 cursor-pointer active:bg-surface-container transition-colors shadow-sm" data-id="{{ $id }}" onclick="mToggle(this)">
    {{ $slot }}
</div>
