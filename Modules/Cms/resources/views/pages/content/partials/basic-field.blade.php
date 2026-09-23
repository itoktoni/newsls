<?php
$namePrefix = $namePrefix ?? 'meta';
$fieldName = $namePrefix . '[' . $field->name . ']';
$required = $field->is_required ? 'required' : '';
$oldValue = old(str_replace(['[', ']'], ['.', ''], $fieldName), $value);
$hasError = $errors->has(str_replace(['[', ']'], ['.', ''], $fieldName));
$inputClass = 'w-full h-12 px-4 bg-white border ' . ($hasError ? 'border-error' : 'border-outline-variant') . ' rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm';
$labelClass = 'font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1';
$errorClass = 'font-label-caps text-label-caps text-error mt-1 block';
?>
<label class="{{ $labelClass }}">
    {{ $field->label }}
    @if($field->is_required)<span class="text-red-500">*</span>@endif
</label>

@if(in_array($field->type, ['text', 'url', 'email', 'color', 'slug']))
    <input type="{{ $field->type === 'slug' ? 'text' : $field->type }}" name="{{ $fieldName }}" value="{{ $oldValue }}" class="{{ $inputClass }}" {{ $required }}>
@elseif($field->type === 'textarea' || $field->type === 'wysiwyg')
    <textarea name="{{ $fieldName }}" rows="{{ $field->type === 'wysiwyg' ? 8 : 3 }}" @if($field->type === 'wysiwyg') class="w-full cms-wysiwyg" data-wysiwyg="1" @else class="w-full px-4 py-3 bg-white border {{ $hasError ? 'border-error' : 'border-outline-variant' }} rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" @endif {{ $required }}>{{ $oldValue }}</textarea>
@elseif($field->type === 'number' || $field->type === 'integer' || $field->type === 'float')
    <input type="number" name="{{ $fieldName }}" value="{{ $oldValue }}" class="{{ $inputClass }}" {{ $required }}>
@elseif($field->type === 'select')
    <select name="{{ $fieldName }}" class="{{ $inputClass }}" {{ $required }}>
        <option value="">-- Select --</option>
        @foreach(($field->config['options'] ?? $field->config['choices'] ?? []) as $optVal => $optLabel)
            <option value="{{ $optVal }}" {{ $oldValue == $optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
        @endforeach
    </select>
@elseif($field->type === 'date')
    <input type="date" name="{{ $fieldName }}" value="{{ $oldValue }}" class="{{ $inputClass }}" {{ $required }}>
@elseif($field->type === 'image')
    @php $pickerId = 'picker_' . md5($fieldName); @endphp
    <div id="{{ $pickerId }}" class="image-picker-wrapper">
        {{-- Drag & Drop Zone --}}
        <div id="{{ $pickerId }}_dropzone" class="relative border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 hover:bg-blue-50/30 transition-colors cursor-pointer"
             ondragover="event.preventDefault(); this.classList.add('border-blue-500','bg-blue-50')"
             ondragleave="this.classList.remove('border-blue-500','bg-blue-50')"
             ondrop="handleImageDrop(event, '{{ $pickerId }}')">
            <div id="{{ $pickerId }}_dropzone_content" @if($oldValue) style="display:none" @endif>
                <i class="icon-[tabler--cloud-upload] text-3xl text-gray-400 mb-2"></i>
                <p class="text-sm text-gray-500">Drag & drop image here, or click to select</p>
                <input type="file" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                       onchange="handleImageFileSelect(event, '{{ $pickerId }}')">
            </div>
            {{-- Preview (shown when image is set) --}}
            <div id="{{ $pickerId }}_preview_wrap" class="flex items-center gap-3 justify-center" @if(!$oldValue) style="display:none" @endif>
                <img id="{{ $pickerId }}_preview" src="{{ $oldValue }}" class="h-24 w-auto object-cover rounded border border-gray-200" alt="Preview">
                <div class="flex flex-col gap-1">
                    <button type="button" onclick="event.stopPropagation(); openImageBrowser('{{ $pickerId }}')" class="text-blue-500 hover:text-blue-700 text-xs">
                        <i class="icon-[tabler--switch-horizontal]"></i> Change
                    </button>
                    <button type="button" onclick="event.stopPropagation(); imgPickerRemove('{{ $pickerId }}')" class="text-red-500 hover:text-red-700 text-xs">
                        <i class="icon-[tabler--trash]"></i> Remove
                    </button>
                </div>
            </div>
            {{-- Upload progress --}}
            <div id="{{ $pickerId }}_upload_progress" class="hidden mt-2">
                <div class="flex items-center gap-2 justify-center">
                    <i class="icon-[tabler--loader] animate-spin text-blue-500"></i>
                    <span class="text-sm text-blue-600">Uploading...</span>
                </div>
            </div>
        </div>
        {{-- Hidden input for the URL value --}}
        <input type="hidden" name="{{ $fieldName }}" id="{{ $pickerId }}_input" value="{{ $oldValue }}">
        {{-- Browse button --}}
        <div class="mt-2 flex gap-2">
            <button type="button" onclick="openImageBrowser('{{ $pickerId }}')" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2 text-sm">
                <i class="icon-[tabler--photo] text-xs"></i> Media Library
            </button>
            <label class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2 text-sm cursor-pointer">
                <i class="icon-[tabler--upload] text-xs"></i> Image
                <input type="file" accept="image/*" class="hidden" onchange="handleImageFileSelect(event, '{{ $pickerId }}')">
            </label>
        </div>
    </div>
@elseif($field->type === 'file')
    <input type="text" name="{{ $fieldName }}" value="{{ $oldValue }}" class="{{ $inputClass }}" placeholder="File URL">
    @if($oldValue)<a href="{{ $oldValue }}" target="_blank" class="text-blue-500 text-sm">View file</a>@endif
@elseif($field->type === 'boolean' || $field->type === 'toggle')
    <div class="flex items-center gap-2 h-12">
        <input type="hidden" name="{{ $fieldName }}" value="0">
        <input type="checkbox" name="{{ $fieldName }}" value="1" {{ $oldValue ? 'checked' : '' }} class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary-container">
        <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $field->label }}</span>
    </div>
@else
    <input type="text" name="{{ $fieldName }}" value="{{ $oldValue }}" class="{{ $inputClass }}">
@endif

@if($hasError)<p class="{{ $errorClass }}">{{ $errors->first(str_replace(['[', ']'], ['.', ''], $fieldName)) }}</p>@endif
