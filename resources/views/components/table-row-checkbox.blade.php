@props(['model' => null, 'value' => null])
<td class="w-12">
    @can('delete', $model ?? null)
    <input type="checkbox" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary-container" value="{{ $value }}">
    @endcan
</td>
