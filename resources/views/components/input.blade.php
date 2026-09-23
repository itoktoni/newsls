@props(['name', 'label' => null, 'type' => 'text', 'col' => '12', 'value' => null, 'placeholder' => '', 'model' => null, 'helper' => null])
@php
    global $activeBladeModel;
    $label = $label ?? formatLabel($name);
    $m = $model ?? $activeBladeModel ?? null;
    $bound = $value ?? ($m ? old($name, data_get($m, $name, '')) : old($name, ''));
    if ($bound instanceof \BackedEnum) {
        $bound = $bound->value;
    }
    if ($type === 'date' && $bound) {
        $bound = \Illuminate\Support\Carbon::parse($bound)->format('Y-m-d');
    }
    $hasError = $errors->has($name);
@endphp
<div class="col-span-12 md:col-span-{{ $col }}">
    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" value="{{ $bound }}" class="w-full h-12 px-4 bg-white border {{ $hasError ? 'border-error' : 'border-outline-variant' }} rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" placeholder="{{ $placeholder ?: $label }}" {{ $attributes }}>
    @if($helper && !$hasError)<span class="font-label-caps text-label-caps text-on-surface-variant mt-1 block">{{ $helper }}</span>@endif
    @if($hasError)<span class="font-label-caps text-label-caps text-error mt-1 block">{{ $errors->first($name) }}</span>@endif
</div>
