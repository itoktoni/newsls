@props(['name', 'label' => null, 'col' => '12', 'value' => null, 'rows' => 3, 'placeholder' => '', 'model' => null, 'helper' => null])
@php
    global $activeBladeModel;
    $label = $label ?? formatLabel($name);
    $m = $model ?? $activeBladeModel ?? null;
    $bound = $value ?? ($m ? old($name, data_get($m, $name, '')) : old($name, ''));
    $hasError = $errors->has($name);
@endphp
<div class="col-span-12 md:col-span-{{ $col }}">
    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">{{ $label }}</label>
    <textarea name="{{ $name }}" class="w-full px-4 py-3 bg-white border {{ $hasError ? 'border-error' : 'border-outline-variant' }} rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm resize-none" rows="{{ $rows }}" placeholder="{{ $placeholder ?: $label }}" {{ $attributes }}>{{ $bound }}</textarea>
    @if($helper && !$hasError)<span class="font-label-caps text-label-caps text-on-surface-variant mt-1 block">{{ $helper }}</span>@endif
    @if($hasError)<span class="font-label-caps text-label-caps text-error mt-1 block">{{ $errors->first($name) }}</span>@endif
</div>
