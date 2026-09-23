@props(['price' => null, 'description' => null])
<p class="font-body-sm text-body-sm font-bold text-on-surface mt-1">Rp {{ number_format($price, 0, ',', '.') }}</p>
<p class="font-label-caps text-label-caps text-on-surface-variant">{{ $description }}</p>
