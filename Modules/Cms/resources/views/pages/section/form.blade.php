<?php /** @var Modules\Cms\Models\Section $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => 'Sections'], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card label="Sections">
            @bind($model ?? null)
                <x-input col="6" name="name" />
                <x-textarea col="6" name="description" />
                <x-select col="6" name="content_type_id" :options="$contentTypes" label="Type"/>
                <x-input col="6" name="sort_order" type="number" />
                <x-select col="6" name="is_active" :options="['1' => 'Active', '0' => 'Inactive']"/>
            @endbind
        </x-card>

        <x-card label="Field Selection">
            @php
                $allFields = $allFields ?? collect();
                $selectedFields = array_map('strval', $model->field_ids ?? []);
            @endphp
            <div class="col-span-12">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select which custom fields belong to this group:</label>
                <p class="text-xs text-gray-400 mb-3">Drag the handle to reorder — the order here is how fields appear in the content editor.</p>
                <div id="field-selection" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach($allFields as $field)
                    @php $isSelected = in_array((string)$field->id, $selectedFields); @endphp
                    <div class="field-pick flex items-center gap-2 px-3 py-2.5 border rounded-lg transition-colors {{ $isSelected ? 'bg-blue-50 border-blue-200' : 'bg-white border-gray-200 hover:bg-gray-50' }}">
                        <span class="field-handle text-gray-300 hover:text-gray-500 cursor-grab active:cursor-grabbing shrink-0 material-symbols-outlined text-lg">drag_indicator</span>
                        <label class="flex items-center gap-3 cursor-pointer flex-1 min-w-0">
                            <input type="checkbox" name="field_ids[]" value="{{ $field->id }}"
                                {{ $isSelected ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600">
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-gray-800 truncate">{{ $field->label }}</div>
                                <div class="text-xs text-gray-400">{{ $field->name }} · {{ $field->type }}</div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        if (typeof Sortable !== 'undefined') {
            var fieldSel = document.getElementById('field-selection');
            if (fieldSel) {
                new Sortable(fieldSel, {
                    handle: '.field-handle',
                    animation: 150,
                    ghostClass: 'bg-blue-50',
                });
            }
        }
    </script>
</x-layouts::app>
