@props(['model', 'action', 'method' => 'POST'])

@php
$isEdit = isset($model) && $model->exists;
if(empty($action))
{
    $action = $isEdit ? moduleRoute('getUpdate', ['id' => $model->field_primary]) : moduleRoute('getCreate');
}
@endphp

<form class="mt-4 lg:mt-0" action="{{ $action }}" method="{{ in_array(strtoupper($method), ['GET','POST']) ? strtoupper($method) : 'POST' }}" {{ $attributes }}>
    @csrf
    @if(!in_array(strtoupper($method), ['GET','POST']))
        @method($method)
    @endif
    {{ $slot }}
</form>
