@props([
    'icon' => null,
    'label' => null,
    'color' => 'primary',
    'size' => null,
    'type' => 'button',
    'route' => null,
    'module' => null,
    'key' => null,
    'url' => null,
    'href' => null,
    'dismiss' => null,
    'toggle' => null,
    'click' => null,
    'confirm' => false,
    ])

    @php
        if ($module) $href = moduleRoute($module, ['code' => $key]);
        else if ($url) $href = route($route);
        else if ($url) $href = url($url);

        $icon = $attributes->get('icon', $icon ?? null);
        $class = $icon ? 'btn btn-sm btn-'.$color : 'btn btn-'.$color;

        $attributes = $attributes->class([$class])
        ->merge([
        'type' => !$href ? $type : null,
        'href' => $href,
        'data-bs-dismiss' => $dismiss,
        'data-bs-toggle' => $toggle,
        'wire:click' => $click,
        'onclick' => $confirm ? 'confirm(\'' . __('Are you sure?') . '\') || event.stopImmediatePropagation()' : null,
        ]);
    @endphp

    <{{ $href ? 'a' : 'button' }} {{ $attributes }}>
        @if($label)
            {{ $label ? __($label) : $slot }}
        @else
            <i class="dripicons-{{ $icon }}"></i>
        @endif
    </{{ $href ? 'a' : 'button' }}>
