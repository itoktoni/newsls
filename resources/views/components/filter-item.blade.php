@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'operator' => '$eq',
    'operators' => [],
    'options' => [],
    'placeholder' => '...'
])

@php
if(str_contains($name, '_at')) {
    $type = 'date';
}
@endphp

<div>
    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">{{ $label }}</label>
    @if(count($options))
    <div class="relative">
        <select class="w-full h-12 pl-4 pr-10 bg-white border border-outline-variant rounded-lg font-body-sm appearance-none cursor-pointer focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all" data-field="{{ $name }}" data-op="{{ $operator }}">
            <option value="">-- All --</option>
            @foreach($options as $value => $text)
            <option value="{{ $value }}" {{ request('filters.' . $name . '.$eq') == (string)$value ? 'selected' : '' }}>{{ $text }}</option>
            @endforeach
        </select>
        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-on-surface-variant text-xl">expand_more</span>
    </div>
    @else
    <div class="grid" style="grid-template-columns: 30% 70%; gap: 0.5rem;">
        @if(count($operators))
        <div class="relative">
            <select class="w-full h-12 pl-4 pr-8 bg-white border border-outline-variant rounded-lg font-body-sm appearance-none cursor-pointer focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all" data-op="{{ $name }}">
                @foreach($operators as $op => $symbol)
                <option value="{{ $op }}" {{ $operator === $op ? 'selected' : '' }}>{{ $symbol }}</option>
                @endforeach
            </select>
            <span class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 material-symbols-outlined text-on-surface-variant text-lg">expand_more</span>
        </div>
        @else
        <div class="relative">
            <select class="w-full h-12 pl-4 pr-8 bg-white border border-outline-variant rounded-lg font-body-sm appearance-none cursor-pointer focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all" data-op="{{ $name }}" onchange="updateFilterOp('{{ $name }}')">
                @php
                $defaultOps = [
                    '$eq' => '=',
                    '$ne' => '!=',
                    '$contains' => '~',
                    '$notContains' => '!~',
                    '$gt' => '>',
                    '$gte' => '>=',
                    '$lt' => '<',
                    '$lte' => '<=',
                ];
                $filterOperator = request('filter_op.' . $name, $operator);
                @endphp
                @foreach($defaultOps as $op => $symbol)
                <option value="{{ $op }}" {{ $filterOperator === $op ? 'selected' : '' }}>{{ $symbol }}</option>
                @endforeach
            </select>
            <span class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 material-symbols-outlined text-on-surface-variant text-lg">expand_more</span>
        </div>
        @endif
        <input type="{{ $type }}" class="h-12 px-4 bg-white border border-outline-variant rounded-lg font-body-sm focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all" data-field="{{ $name }}" value="{{ request('filters.' . $name . '.' . $operator) }}" placeholder="{{ $placeholder }}">
    </div>
    @endif
</div>
