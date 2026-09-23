@props(['text' => null])
<label {{ $attributes->merge(['class' => 'font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1']) }}>{{ $text ?? $slot }}</label>
