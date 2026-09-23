@props(['paginator'])
<div class="flex items-center gap-2 justify-center py-3">
    <span class="font-label-caps text-label-caps text-on-surface-variant">Showing {{ $paginator->count() }} items</span>
    <div class="flex items-center gap-1">
        @if($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-surface-container text-on-surface-variant/40 cursor-not-allowed">
                <span class="material-symbols-outlined text-lg">chevron_left</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" wire:navigate class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined text-lg">chevron_left</span>
            </a>
        @endif

        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" wire:navigate class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined text-lg">chevron_right</span>
            </a>
        @else
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-surface-container text-on-surface-variant/40 cursor-not-allowed">
                <span class="material-symbols-outlined text-lg">chevron_right</span>
            </span>
        @endif
    </div>
</div>
