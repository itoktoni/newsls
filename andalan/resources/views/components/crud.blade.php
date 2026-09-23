@props([
    'action' => ['update', 'delete'],
    'model' => null,
])

<div {{ $attributes }}>
    @foreach ($action as $act)
    @switch($act)
        @case('update')
            @can(ACTION_UPDATE)
            <x-button module="getUpdate" key="{{ $model->field_primary }}" color="primary"
                icon="document-edit" />
            @endcan
            @break
        @case('delete')
            @can(ACTION_DELETE)
            @if(env('APP_SPA'))
            <x-button module="getDelete" key="{{ $model->field_primary }}" color="danger"
                icon="trash"  hx-confirm="Apakah anda yakin ingin menghapus ?" class="button-delete" />
            @else
            <x-button module="getDelete" key="{{ $model->field_primary }}" color="danger"
                icon="trash" onclick="return confirm('Apakah anda yakin ingin menghapus ?')" class="button-delete" />
            @endif
            @endcan
            @break
        @default
    @endswitch
    @endforeach
{{ $slot }}
</div>