<?php
$containerData = $value ?? [];
if (is_string($containerData)) {
    $containerData = json_decode($containerData, true) ?? [];
}
if (!is_array($containerData)) {
    $containerData = [];
}

$namePrefix = $namePrefix ?? 'meta';
$fieldName = $namePrefix . '[' . $field->name . ']';

$mode = $field->mode ?? 'single';
$layouts = method_exists($field, 'getLayouts') ? $field->getLayouts() : (is_array($field->layouts ?? null) ? $field->layouts : []);
$children = $field->has_children ?? collect();
$isFlexible = $mode === 'flexible';
$isMultiple = $mode === 'multiple';
$isSingle = $mode === 'single';

$layoutIcons = [
    'hero' => 'icon-[tabler--photo]',
    'slider' => 'icon-[tabler--photo]',
    'cta' => 'icon-[tabler--speakerphone]',
    'image_left_right' => 'icon-[tabler--columns]',
    'text_block' => 'icon-[tabler--align-left]',
    'gallery' => 'icon-[tabler--photo]',
    'faq' => 'icon-[tabler--help-circle]',
    'pricing' => 'icon-[tabler--tag]',
    'footer' => 'icon-[tabler--shoe]',
];

$layoutColors = [
    'hero' => 'bg-purple-100 text-purple-700 border-purple-200',
    'slider' => 'bg-blue-100 text-blue-700 border-blue-200',
    'cta' => 'bg-green-100 text-green-700 border-green-200',
    'image_left_right' => 'bg-orange-100 text-orange-700 border-orange-200',
    'text_block' => 'bg-gray-100 text-gray-700 border-gray-200',
    'gallery' => 'bg-pink-100 text-pink-700 border-pink-200',
    'faq' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
    'pricing' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
    'footer' => 'bg-slate-100 text-slate-700 border-slate-200',
];
?>

<div class="container-builder" id="container-{{ $field->name }}">
    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $field->label }}</label>

    @if($isFlexible)
        {{-- Flexible mode: layout picker --}}
        <div class="sections-list space-y-3" data-field="{{ $field->name }}">
            @foreach($containerData as $secIdx => $section)
                @php
                    $layoutName = $section['_layout'] ?? '';
                    $layoutDef = collect($layouts)->firstWhere('name', $layoutName);
                    $icon = $layoutIcons[$layoutName] ?? 'icon-[tabler--puzzle]';
                    $colorClass = $layoutColors[$layoutName] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                @endphp
                <div class="section-item border border-gray-200 rounded-lg bg-white shadow-sm" data-index="{{ $secIdx }}">
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-t-lg cursor-move section-handle select-none">
                        <div class="flex items-center gap-3">
                            <i class="icon-[tabler--grip-vertical] text-gray-300"></i>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full border {{ $colorClass }}">
                                <i class="{{ $icon }} text-[10px]"></i>
                                {{ $layoutDef['label'] ?? $layoutName }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="toggleSection(this)" class="p-1.5 text-gray-400 hover:text-gray-600 rounded hover:bg-gray-200 transition-colors" title="Collapse/Expand">
                                <i class="icon-[tabler--chevron-up] text-xs transition-transform"></i>
                            </button>
                            <button type="button" onclick="removeSection(this)" class="p-1.5 text-gray-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors" title="Remove Section">
                                <i class="icon-[tabler--trash] text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="{{ $fieldName }}[{{ $secIdx }}][_layout]" value="{{ $layoutName }}" />

                    <div class="section-fields px-4 py-4 space-y-3 border-t border-gray-100">
                        @if($layoutName && $layoutDef)
                            @foreach($layoutDef['fields'] as $fieldDef)
                                @php
                                    $fName = $fieldDef['name'];
                                    $fType = $fieldDef['type'] ?? 'text';
                                    $fValue = $section[$fName] ?? ($fieldDef['default_value'] ?? '');
                                    $fLabel = $fieldDef['label'] ?? $fName;
                                    $childFieldName = $fieldName . '[' . $secIdx . '][' . $fName . ']';
                                @endphp
                                <div>
                                    @if($fType !== 'toggle')
                                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">{{ $fLabel }}</label>
                                    @endif
                                    {!! \renderFieldInput($fName, $fType, $fValue, $childFieldName, $fLabel, $fieldDef) !!}
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Add Section Button --}}
        <div class="mt-4 relative" id="add-section-wrapper-{{ $field->name }}">
            <button type="button" onclick="toggleAddMenu('{{ $field->name }}')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="icon-[tabler--plus] text-xs"></i> Add Section
            </button>

            <div id="add-menu-{{ $field->name }}" class="hidden absolute left-0 bottom-full mb-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-800">Choose Section Layout</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Select a layout to add to your page</p>
                </div>
                <div class="p-2 max-h-80 overflow-y-auto">
                    @foreach($layouts as $layout)
                        @php
                            $icon = $layoutIcons[$layout['name']] ?? 'icon-[tabler--puzzle]';
                            $colorClass = $layoutColors[$layout['name']] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <button type="button"
                            data-container="{{ $field->name }}"
                            data-layout="{{ $layout['name'] }}"
                            data-label="{{ $layout['label'] ?? $layout['name'] }}"
                            data-fields='@json($layout['fields'] ?? [])'
                            onclick="handleAddSection(this)"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-left group">
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-gray-800 group-hover:text-blue-600 transition-colors">{{ $layout['label'] ?? $layout['name'] }}</div>
                                <div class="text-xs text-gray-400 truncate">{{ count($layout['fields'] ?? []) }} fields</div>
                            </div>
                            <i class="icon-[tabler--plus] text-gray-300 group-hover:text-blue-500 text-xs"></i>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @elseif($isMultiple)
        {{-- Multiple mode: repeatable set of child fields (plain HTML, submits via parent form) --}}
        <div class="repeater-wrapper border border-gray-200 rounded-lg bg-gray-50 p-3" id="repeater-{{ $field->name }}">
            <div class="repeater-items space-y-2" id="repeater-items-{{ $field->name }}">
                @forelse($containerData as $itemIdx => $item)
                    <div class="repeater-item bg-white border border-gray-200 rounded-md p-3 space-y-3" data-index="{{ $itemIdx }}">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400 font-medium">#{{ $itemIdx + 1 }}</span>
                            <button type="button" onclick="this.closest('.repeater-item').remove(); checkRepeaterEmpty('{{ $field->name }}');" class="text-gray-300 hover:text-red-500 transition-colors">
                                <i class="icon-[tabler--x] text-xs"></i>
                            </button>
                        </div>
                        @foreach($children as $childField)
                            @php
                                $childValue = $item[$childField->name] ?? $childField->default_value ?? '';
                            @endphp
                            @if($childField->type === 'container')
                                @include('cms::pages.content.partials.container-field', ['field' => $childField, 'value' => $childValue, 'namePrefix' => $fieldName . '[' . $itemIdx . ']'])
                            @else
                                @include('cms::pages.content.partials.basic-field', ['field' => $childField, 'value' => $childValue, 'namePrefix' => $fieldName . '[' . $itemIdx . ']'])
                            @endif
                        @endforeach
                    </div>
                @empty
                    <div id="repeater-empty-{{ $field->name }}" class="text-center py-4 text-gray-400 text-xs">
                        <i class="icon-[tabler--inbox] mb-1 block text-lg opacity-30"></i>
                        No items yet. Click "Add" below.
                    </div>
                @endforelse
            </div>

            {{-- Hidden template used for cloning new empty items --}}
            <template id="repeater-template-{{ $field->name }}">
                <div class="repeater-item bg-white border border-gray-200 rounded-md p-3 space-y-3" data-index="__IDX__">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-medium">#__IDX_PLUS_1__</span>
                        <button type="button" onclick="this.closest('.repeater-item').remove(); checkRepeaterEmpty('{{ $field->name }}');" class="text-gray-300 hover:text-red-500 transition-colors">
                            <i class="icon-[tabler--x] text-xs"></i>
                        </button>
                    </div>
                    @foreach($children as $childField)
                        @if($childField->type === 'container')
                            @include('cms::pages.content.partials.container-field', ['field' => $childField, 'value' => $childField->default_value, 'namePrefix' => $fieldName . '[__IDX__]'])
                        @else
                            @include('cms::pages.content.partials.basic-field', ['field' => $childField, 'value' => $childField->default_value, 'namePrefix' => $fieldName . '[__IDX__]'])
                        @endif
                    @endforeach
                </div>
            </template>

            <div class="mt-3 text-right">
                <button type="button" onclick="addRepeaterItemFromServer('{{ $field->name }}')" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors border border-blue-200">
                    <i class="icon-[tabler--plus] text-[10px]"></i> Add {{ $field->label }}
                </button>
            </div>
        </div>
    @else
        {{-- Single mode: render children once --}}
        <div class="single-container border border-gray-200 rounded-lg bg-white p-4 space-y-3">
            @if($children->isEmpty())
                <div class="text-center py-6 text-gray-400">
                    <i class="icon-[tabler--puzzle] text-2xl mb-2 block opacity-30"></i>
                    <p class="text-sm font-medium">No fields defined for this container</p>
                    <p class="text-xs mt-1">
                        <a href="{{ route('field.getUpdate', $field->id) }}" class="text-blue-500 hover:text-blue-700 underline">
                            Add child fields
                        </a>
                        to this container in the Custom Fields settings.
                    </p>
                </div>
            @else
                @foreach($children as $childField)
                    @php
                        $childValue = $containerData[$childField->name] ?? $childField->default_value ?? '';
                    @endphp
                    @if($childField->type === 'container')
                        @include('cms::pages.content.partials.container-field', ['field' => $childField, 'value' => $childValue, 'namePrefix' => $fieldName])
                    @else
                        @include('cms::pages.content.partials.basic-field', ['field' => $childField, 'value' => $childValue, 'namePrefix' => $fieldName])
                    @endif
                @endforeach
            @endif
        </div>
    @endif

    @if($isFlexible && count($containerData) === 0)
        <div id="empty-state-{{ $field->name }}" class="text-center py-12 text-gray-400">
            <i class="icon-[tabler--stack] text-4xl mb-3 block opacity-30"></i>
            <p class="text-sm font-medium">No sections yet</p>
            <p class="text-xs mt-1">Click <strong>"Add Section"</strong> to start building your page layout</p>
        </div>
    @endif
</div>