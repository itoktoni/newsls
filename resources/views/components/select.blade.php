@props(['name', 'label' => null, 'col' => '12', 'options' => [], 'default' => null, 'multiple' => false, 'placeholder' => '', 'model' => null, 'helper' => null])
@php
    global $activeBladeModel;
    $label = $label ?? formatLabel($name);
    $m = $model ?? $activeBladeModel ?? null;
    $selected = old($name, $default ?? ($m ? data_get($m, $name, '') : ''));
    if ($selected instanceof \BackedEnum) {
        $selected = $selected->value;
    }
    if (! is_array($selected)) {
        $selected = (string) $selected;
    }
    $hasError = $errors->has($name);
    $isTomSelect = $attributes->get('class') && str_contains($attributes->get('class'), 'search');
    $extraClass = $attributes->get('class') ? $attributes->get('class') : '';
    $selectClass = $isTomSelect
        ? 'w-full h-12 bg-transparent font-body-sm ' . $extraClass
        : 'w-full h-12 pl-4 pr-10 bg-white border ' . ($hasError ? 'border-error' : 'border-outline-variant') . ' rounded-lg font-body-sm appearance-none cursor-pointer focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all ' . $extraClass;
@endphp
<div class="col-span-12 md:col-span-{{ $col }}">
    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">{{ $label }}</label>
    <div class="relative">
        <select name="{{ $name }}" {{ $multiple ? 'multiple' : '' }} id="select-{{ $name }}" class="{{ $selectClass }}" {{ $attributes->except('class') }} @if($hasError) data-has-error @endif>
            @if(!$multiple && $placeholder !== false)
            <option value="">{{ $placeholder ?: '-- Silahkan Pilih --' }}</option>
            @endif
            @foreach($options as $key => $text)
            <option value="{{ $key }}" @if($multiple) {{ is_array($selected) && in_array($key, $selected) ? 'selected' : '' }} @else {{ (string) $selected === (string) $key ? 'selected' : '' }} @endif>{{ $text }}</option>
            @endforeach
        </select>
        @if(!$isTomSelect)
        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-on-surface-variant text-xl">expand_more</span>
        @endif
    </div>
    @if($helper && !$hasError)<span class="font-label-caps text-label-caps text-on-surface-variant mt-1 block">{{ $helper }}</span>@endif
    @if($hasError)<span class="font-label-caps text-label-caps text-error mt-1 block">{{ $errors->first($name) }}</span>@endif
</div>

