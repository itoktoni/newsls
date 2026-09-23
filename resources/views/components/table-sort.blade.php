@props(['field', 'label', 'sortField' => '', 'sortDir' => 'asc'])
<th class="cursor-pointer select-none hover:text-on-surface transition-colors" onclick="doSort('{{ $field }}')">
    <span class="inline-flex items-center gap-1.5">
        {{ $label }}
        <span class="material-symbols-outlined text-base">{{ $sortField===$field ? ($sortDir==='asc'?'arrow_upward':'arrow_downward') : 'unfold_more' }}</span>
    </span>
</th>
