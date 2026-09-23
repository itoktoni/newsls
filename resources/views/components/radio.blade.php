@props(['name', 'label' => null, 'col' => '12', 'value' => '', 'checked' => null])
@php
    $isChecked = $checked ?? (isset($model) ? old($name, $model->{$name} ?? '') == $value : old($name) == $value);
@endphp
<div class="col-span-12 md:col-span-{{ $col }}">
    <label class="flex items-center gap-3 cursor-pointer">
        <input type="radio" name="{{ $name }}" value="{{ $value }}" class="w-4 h-4 border-outline-variant text-primary focus:ring-primary-container" {{ $isChecked ? 'checked' : '' }} {{ $attributes }}>
        <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $label }}</span>
    </label>
</div>
