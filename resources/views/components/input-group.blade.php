@props(['name', 'label' => null, 'col' => '12', 'type' => 'text', 'value' => null, 'placeholder' => '', 'prefix' => null, 'suffix' => null])
@php
    $label = $label ?? ($name ? formatLabel($name) : null);
    $val = $value ?? (isset($model) ? old($name, $model->{$name} ?? '') : old($name, ''));
    $hasError = $errors->has($name);
@endphp
<div class="col-span-12 md:col-span-{{ $col }}">
    @if($label)<label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">{{ $label }}</label>@endif
    <div class="flex">
        @if($prefix)<span class="inline-flex items-center px-3 h-12 bg-surface-container border border-outline-variant border-r-0 rounded-l-lg font-body-sm text-on-surface-variant">{{ $prefix }}</span>@endif
        <input type="{{ $type }}" name="{{ $name }}" value="{{ $val }}" class="flex-1 h-12 px-4 bg-white border {{ $hasError ? 'border-error' : 'border-outline-variant' }} {{ $prefix ? 'rounded-r-lg' : ($suffix ? 'rounded-l-lg' : 'rounded-lg') }} focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" placeholder="{{ $placeholder ?: $label }}" {{ $attributes }}>
        @if($suffix)<span class="inline-flex items-center px-3 h-12 bg-surface-container border border-outline-variant border-l-0 rounded-r-lg font-body-sm text-on-surface-variant">{{ $suffix }}</span>@endif
    </div>
    @if($hasError)<span class="font-label-caps text-label-caps text-error mt-1 block">{{ $errors->first($name) }}</span>@endif
</div>
