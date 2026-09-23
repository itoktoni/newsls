@props(['model' => null, 'id' => null])
<td class="w-24 whitespace-nowrap">
    <div class="flex gap-2">
        @can('update', $model ?? null)
        <a href="{{ moduleRoute('getUpdate', ['id' => $id]) }}" wire:navigate class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-colors">
            <span class="material-symbols-outlined text-lg">edit</span>
        </a>
        @endcan
        @can('delete', $model ?? null)
        <a onclick="return confirm('Are you sure you want to delete?')" href="{{ moduleRoute('getDelete', ['id' => $id]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-error/10 text-error hover:bg-error/20 transition-colors">
            <span class="material-symbols-outlined text-lg">delete</span>
        </a>
        @endcan
        {{ $slot }}
    </div>
</td>
