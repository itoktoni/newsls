@props(['field', 'value' => null])

@php
    $name = $attributes->get('name');
    $type = $field['type'] ?? 'text';
    $label = $field['label'] ?? '';
    $required = $field['required'] ?? false;
@endphp

<div class="form-group">
    @if($label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    
    @switch($type)
        @case('text')
        @case('email')
        @case('url')
        @case('number')
            <input type="{{ $type }}" 
                   name="{{ $name }}" 
                   id="{{ $name }}"
                   value="{{ $value ?? '' }}"
                   @if($required) required @endif
                   {{ $attributes->except(['field', 'value']) }}>
            @break
            
        @case('textarea')
            <textarea name="{{ $name }}" 
                      id="{{ $name }}"
                      @if($required) required @endif
                      {{ $attributes->except(['field', 'value']) }}>{{ $value ?? '' }}</textarea>
            @break
            
        @case('select')
            <select name="{{ $name }}" 
                    id="{{ $name }}"
                    @if($required) required @endif
                    {{ $attributes->except(['field', 'value']) }}>
                @foreach($field['options'] ?? [] as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" 
                            @if($value == $optionValue) selected @endif>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>
            @break
            
        @case('boolean')
            <input type="checkbox" 
                   name="{{ $name }}" 
                   id="{{ $name }}"
                   value="1"
                   @if($value) checked @endif
                   {{ $attributes->except(['field', 'value']) }}>
            @break
            
        @case('image')
            <input type="file" 
                   name="{{ $name }}" 
                   id="{{ $name }}"
                   accept="image/*"
                   {{ $attributes->except(['field', 'value']) }}>
            @if($value)
                <img src="{{ $value }}" alt="" class="mt-2 max-w-xs">
            @endif
            @break
            
        @default
            <input type="text" 
                   name="{{ $name }}" 
                   id="{{ $name }}"
                   value="{{ $value ?? '' }}"
                   @if($required) required @endif
                   {{ $attributes->except(['field', 'value']) }}>
    @endswitch
</div>
