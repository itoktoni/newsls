@props([
    'status',
])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-body-sm text-body-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif
