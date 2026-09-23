@props(['name', 'label' => null, 'col' => '12', 'value' => null, 'placeholder' => 'Select date'])
@php
    $label = $label ?? formatLabel($name);
    $val = $value ?? (isset($model) ? old($name, $model->{$name} ?? '') : old($name, ''));
    $hasError = $errors->has($name);
@endphp
<div class="col-span-12 md:col-span-{{ $col }}">
    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">{{ $label }}</label>
    <input type="date" name="{{ $name }}" value="{{ $val }}" class="w-full h-12 px-4 bg-white border {{ $hasError ? 'border-error' : 'border-outline-variant' }} rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" placeholder="{{ $placeholder }}" {{ $attributes }}>
    @if($hasError)<span class="font-label-caps text-label-caps text-error mt-1 block">{{ $errors->first($name) }}</span>@endif
</div>
