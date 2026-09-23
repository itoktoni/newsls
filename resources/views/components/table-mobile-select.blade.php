@props(['model' => null, 'total' => null])
<div class="flex items-center gap-2 px-3 mb-2 py-2 bg-surface-container border-b border-outline-variant">
    <button class="inline-flex items-center gap-1 h-8 px-3 text-xs font-semibold rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high transition-colors" id="mToggleAll" onclick="mToggleAll()">Select All</button>
    @can('delete', $model ?? null)
    @endcan
    <span class="flex-1"></span>
    <span id="mSelCount" class="font-label-caps text-label-caps text-primary font-semibold"></span>
</div>
