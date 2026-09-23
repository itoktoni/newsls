@props(['cancel' => url()->previous(), 'model' => null, 'action' => []])
@php
    $showAction = function($actionName) use ($action) {
        return empty($action) || in_array($actionName, $action);
    };
@endphp
<style>
    @media (min-width: 768px) { .action-bar { bottom: 0 !important; } }
</style>
<div class="action-bar fixed left-0 right-0 lg:left-72 bg-surface-container-lowest border-t border-outline-variant shadow-[0_-4px_12px_rgba(0,0,0,0.08)] px-3 md:px-6 py-2 md:py-3 z-[45]" style="bottom: 4rem">
    <div class="flex items-center justify-between max-w-full mx-auto gap-2 md:gap-3">
        <div class="flex items-center gap-1.5 md:gap-3 flex-nowrap overflow-x-auto">
            {{ $slot }}
        </div>
        <div class="flex items-center gap-1.5 md:gap-3 flex-nowrap">
            {{ $right ?? '' }}
            @if($showAction('create'))
                @can('create', $model)
                <a href="{{ moduleRoute('getCreate') }}" wire:navigate class="inline-flex items-center justify-center gap-1 h-8 md:h-10 px-2.5 md:px-4 text-xs md:text-sm font-semibold rounded-lg bg-primary text-on-primary hover:bg-primary/90 shadow-sm transition-all active:scale-95 shrink-0">
                    <span class="material-symbols-outlined text-base md:text-xl">add</span>
                    <span class="hidden sm:inline">Create</span>
                </a>
                @endcan
            @endif
            @if($showAction('save'))
                @can('save', $model)
                <button type="submit" class="inline-flex items-center justify-center gap-1 h-8 md:h-10 px-2.5 md:px-4 text-xs md:text-sm font-semibold rounded-lg bg-primary text-on-primary hover:bg-primary/90 shadow-sm transition-all active:scale-95 shrink-0">
                    <span class="material-symbols-outlined text-base md:text-xl">save</span>
                    <span class="hidden sm:inline">Save</span>
                </button>
                @endcan
            @endif
            @if($showAction('update'))
                @can('update', $model)
                <button type="submit" class="inline-flex items-center justify-center gap-1 h-8 md:h-10 px-2.5 md:px-4 text-xs md:text-sm font-semibold rounded-lg bg-primary text-on-primary hover:bg-primary/90 shadow-sm transition-all active:scale-95 shrink-0">
                    <span class="material-symbols-outlined text-base md:text-xl">save</span>
                    <span class="hidden sm:inline">Update</span>
                </button>
                @endcan
            @endif
            @if($showAction('delete'))
                @can('delete', $model)
                <button type="button" onclick="deleteSelected()" class="inline-flex items-center justify-center gap-1 h-8 md:h-10 px-2.5 md:px-4 text-xs md:text-sm font-semibold rounded-lg bg-error text-on-error hover:bg-error/90 shadow-sm transition-all active:scale-95 shrink-0">
                    <span class="material-symbols-outlined text-base md:text-xl">delete</span>
                    <span class="hidden sm:inline">Delete</span>
                </button>
                @endcan
            @endif
            <a href="{{ $cancel }}" wire:navigate class="inline-flex items-center justify-center gap-1 h-8 md:h-10 px-2.5 md:px-4 text-xs md:text-sm font-semibold rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container transition-all shrink-0">
                <span class="material-symbols-outlined text-base md:text-xl">close</span>
                <span class="hidden sm:inline">Cancel</span>
            </a>
        </div>
    </div>
</div>
