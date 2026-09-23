<?php $field = $field ?? null; $value = $value ?? null; ?>
@if($field)
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        {{ $field->label }} @if($field->is_required)<span class="text-red-500">*</span>@endif
    </label>
    @if($field->type === 'text')
        <input type="text" name="meta[{{ $field->name }}]" value="{{ old('meta.'.$field->name, $value) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" {!! $field->is_required ? 'required' : '' !!}>
    @elseif($field->type === 'wysiwyg')
        <textarea name="meta[{{ $field->name }}]" rows="8" class="w-full cms-wysiwyg" data-wysiwyg="1" {!! $field->is_required ? 'required' : '' !!}>{{ old('meta.'.$field->name, $value) }}</textarea>
    @elseif($field->type === 'textarea')
        <textarea name="meta[{{ $field->name }}]" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" {!! $field->is_required ? 'required' : '' !!}>{{ old('meta.'.$field->name, $value) }}</textarea>
    @elseif($field->type === 'number')
        <input type="number" name="meta[{{ $field->name }}]" value="{{ old('meta.'.$field->name, $value) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" {!! $field->is_required ? 'required' : '' !!}>
    @elseif($field->type === 'image' || $field->type === 'file')
        <input type="file" name="meta[{{ $field->name }}]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" {!! $field->is_required ? 'required' : '' !!}>
    @endif
</div>
@endif
