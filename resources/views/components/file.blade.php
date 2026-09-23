@props([
    'name',
    'label' => null,
    'col' => '12',
    'multiple' => false,
    'accept' => 'image/*',
    'capture' => null,
    'preview' => false,
    'value' => null,
    'helper' => null,
])
@php
    $label = $label ?? formatLabel($name);
    $uid = 'file_' . uniqid();
    $previewUid = $uid . '_preview';
    $hasError = $errors->has($name);
@endphp
<div class="col-span-12 md:col-span-{{ $col }}">
    @if($label)
        <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">{{ $label }}</label>
    @endif

    @if($preview)
    <div class="flex items-center gap-4 mb-3">
        <div id="{{ $previewUid }}" class="shrink-0 w-20 h-20 rounded-full border {{ $hasError ? 'border-error' : 'border-outline-variant' }} bg-surface flex items-center justify-center overflow-hidden">
            @if($value)
                <img src="{{ $value }}" alt="{{ $label }}" class="w-full h-full object-cover">
            @else
                <span class="material-symbols-outlined text-3xl text-on-surface-variant/40">person</span>
            @endif
        </div>
        <button type="button" onclick="document.getElementById('{{ $uid }}').click()"
            class="px-4 py-2 rounded-lg text-sm font-semibold bg-primary/10 text-primary hover:bg-primary/20">
            Pilih / Ambil Foto
        </button>
    </div>
    @if($value)
    <label class="inline-flex items-center gap-1.5 mb-3 text-xs text-on-surface-variant cursor-pointer">
        <input type="checkbox" name="remove_{{ $name }}" value="1" class="rounded border-outline-variant text-error focus:ring-error"> Hapus foto saat ini
    </label>
    @endif
    @endif

    <div class="border-2 border-dashed {{ $hasError ? 'border-error' : 'border-outline-variant' }} rounded-xl p-6 text-center cursor-pointer hover:border-primary transition-colors"
        onclick="document.getElementById('{{ $uid }}').click()"
        ondragover="event.preventDefault();this.classList.add('border-primary','bg-primary/5')"
        ondragleave="this.classList.remove('border-primary','bg-primary/5')"
        ondrop="event.preventDefault();this.classList.remove('border-primary','bg-primary/5');document.getElementById('{{ $uid }}').files=event.dataTransfer.files">
        <span class="material-symbols-outlined text-3xl text-on-surface-variant mb-2 block">{{ $preview ? 'add_a_photo' : 'upload_file' }}</span>
        <p class="font-body-sm text-body-sm text-on-surface-variant mb-1">{{ $preview ? 'Drag & drop atau klik untuk pilih foto baru' : 'Drag & drop files here' }}</p>
        <p class="font-label-caps text-label-caps text-outline">JPG, PNG, WEBP — Maks 2MB</p>
        <input type="file" id="{{ $uid }}" name="{{ $name }}" class="hidden"
            {{ $multiple ? 'multiple' : '' }}
            accept="{{ $accept }}"
            @if($capture) capture="{{ $capture }}" @endif
            @if($preview) onchange="previewAvatar(this,'{{ $previewUid }}')" @endif
            {{ $attributes }}>
    </div>

    @if($helper && !$hasError)
        <span class="font-label-caps text-label-caps text-on-surface-variant mt-1 block">{{ $helper }}</span>
    @endif
    @if($hasError)
        <span class="font-label-caps text-label-caps text-error mt-1 block">{{ $errors->first($name) }}</span>
    @endif
</div>

@if($preview)
@once
<script>function previewAvatar(i,p){var r=document.getElementById(p);if(i.files&&i.files[0]){var d=new FileReader();d.onload=function(e){r.innerHTML='<img src=\"'+e.target.result+'\" alt=\"Preview\" class=\"w-full h-full object-cover\">'};d.readAsDataURL(i.files[0])}}</script>
@endonce
@endif
