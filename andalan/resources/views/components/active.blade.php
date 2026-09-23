@props([
    'value' => null,
    ])

    @php
        $value = $attributes->get('value', $value ?? null);
        $class = $value == 1 ? 'badge badge-success' : 'badge badge-danger';

        $attributes = $attributes->class([$class])
        ->merge([
        ]);
    @endphp

    <div {{ $attributes }}>
        {{ $value == 1 ? 'Aktif' : 'Tidak Aktif' }}
    </div>
