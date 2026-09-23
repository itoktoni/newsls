@props([
'col' => null,
'label' => null
])

@php

$col = $attributes->get('col', $col ? 'col-md-'.$col : '');
$label = $attributes->get('label', moduleName($label));

$attributes = $attributes->class([
'card',
])->merge([

]);
@endphp

<div {{ $attributes }}>
    <div class="card-header">
        <h4 class="card-title">{{ $label }}</h4>
      </div>
    <div class="card-body">
        {{-- <div class="row"> --}}
            {{ $slot }}
        {{-- </div> --}}
    </div>
</div>
