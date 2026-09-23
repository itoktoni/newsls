<div class="section-card border border-gray-200 rounded-lg bg-white shadow-sm" data-group-id="{{ $group->id }}">
    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-t-lg section-handle select-none cursor-pointer" onclick="toggleSection(this)">
        <div class="flex items-center gap-3">
            <i class="icon-[tabler--grip-vertical] text-gray-300"></i>
            <span class="section-order text-xs font-medium text-gray-500 w-6"></span>
            <span class="text-sm font-semibold text-gray-800">{{ $group->name }}</span>
        </div>
        <div class="flex items-center gap-1">
            <button type="button" onclick="event.stopPropagation(); toggleSection(this)" class="p-1.5 text-gray-400 hover:text-gray-600 rounded hover:bg-gray-200 transition-colors" title="Collapse/Expand">
                <i class="icon-[tabler--chevron-up] text-xs transition-transform"></i>
            </button>
            <button type="button" onclick="event.stopPropagation(); removeGroupSection(this)" class="p-1.5 text-gray-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors" title="Remove Section">
                <i class="icon-[tabler--trash] text-xs"></i>
            </button>
        </div>
    </div>
    <div class="p-4 space-y-4 section-fields">
        @foreach($group->fields as $field)
            @php
                $fieldValue = ($isNewSection ?? false) ? null : $field->default_value;
                if (is_string($fieldValue)) {
                    $decoded = json_decode($fieldValue, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $fieldValue = $decoded;
                    }
                }
            @endphp
            @if($field->type === 'container')
                @include('cms::pages.content.partials.container-field', ['field' => $field, 'value' => $fieldValue])
            @else
                @include('cms::pages.content.partials.basic-field', ['field' => $field, 'value' => $fieldValue])
            @endif
        @endforeach
    </div>
</div>