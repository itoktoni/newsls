@props(['model' => null, 'onchange' => null])
<th class="w-12">
    @can('delete', $model ?? null)
    <input type="checkbox" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary-container" {{ $onchange ? 'onchange=' . $onchange : '' }}>
    @endcan
</th>
