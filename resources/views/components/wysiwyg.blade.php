@props(['name', 'label' => null, 'col' => '12', 'model' => null, 'helper' => null, 'placeholder' => null])
@php
    global $activeBladeModel;
    $label = $label ?? formatLabel($name);
    $m = $model ?? $activeBladeModel ?? null;
    $selected = old($name, $m ? data_get($m, $name, '') : '');
    $hasError = $errors->has($name);
@endphp
<div class="col-span-12 md:col-span-{{ $col }}">
    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">{{ $label }}</label>
    <textarea name="{{ $name }}" rows="8" id="wysiwyg-{{ $name }}" class="w-full cms-wysiwyg" data-wysiwyg="1" @if($placeholder) placeholder="{{ $placeholder }}" @endif>{{ $selected }}</textarea>
    @if($helper && !$hasError)<span class="font-label-caps text-label-caps text-on-surface-variant mt-1 block">{{ $helper }}</span>@endif
    @if($hasError)<span class="font-label-caps text-label-caps text-error mt-1 block">{{ $errors->first($name) }}</span>@endif
</div>
