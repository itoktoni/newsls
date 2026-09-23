@props(['name', 'label' => null, 'col' => '12', 'value' => 1, 'checked' => null])
@php
    $label = $label ?? formatLabel($name);
    $isChecked = $checked ?? (isset($model) ? old($name, $model->{$name} ?? false) : old($name, false));
@endphp
<div class="col-span-12 md:col-span-{{ $col }}">
    <label class="flex items-center gap-3 cursor-pointer">
        <input type="checkbox" name="{{ $name }}" value="{{ $value }}" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary-container" {{ $isChecked ? 'checked' : '' }} {{ $attributes }}>
        <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $label }}</span>
    </label>
</div>
