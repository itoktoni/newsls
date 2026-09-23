<?php /** @var Modules\Cms\Models\Field $model */ ?>
<?php
$typeOptions = \Modules\Cms\Models\Field::getTypeOptions();
$isEdit = isset($model) && $model->exists;
?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => ucfirst(modules())], ['url' => '', 'label' => $isEdit ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="ucfirst(modules())">
            @bind($model ?? null)
                <x-input col="6" name="label" />
                <x-input col="6" name="name" />
                <x-select col="6" name="type" :options="$typeOptions"/>
                <x-input col="6" name="default_value" />
                <x-select col="6" name="is_required" :options="['1' => 'Yes', '0' => 'No']"/>
                <x-input col="6" name="sort_order" type="number" />
            @endbind
        </x-card>

        {{-- Container Child Fields (shown when type = container) --}}
        <x-card label="Container Child Fields" class="mt-5" id="container-settings" style="display: {{ ($model->type ?? '') === 'container' ? 'block' : 'none' }}">
            <div class="col-span-12" id="root-child-fields">
                <div id="children-root" class="space-y-2"></div>
                <div id="children-empty-root" class="text-center py-6 text-gray-400 text-sm">
                    <i class="icon-[tabler--inbox] text-2xl mb-2 block opacity-30"></i>
                    No child fields yet. Click "Add Field" to add fields inside this container.
                </div>
                <div class="flex justify-end mt-3">
                    <button type="button" onclick="addChildField('root')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors border border-blue-200">
                        <i class="icon-[tabler--plus] text-[10px]"></i> Add Field
                    </button>
                </div>
            </div>
        </x-card>

        {{-- Config Options (for select, radio, checkbox types) --}}
        <x-card label="Options Config" id="options-config" style="display: {{ in_array($model->type ?? '', ['select','radio','checkbox','button_group']) ? 'block' : 'none' }}">
            <div class="col-span-12">
                <label class="block text-sm font-medium text-gray-700 mb-1">Options (one per line, format: <code>value : Label</code>)</label>
                <textarea name="config_options" rows="5" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono" placeholder="option1 : Option 1&#10;option2 : Option 2&#10;option3 : Option 3">{{ is_array($model->config ?? null) ? ($model->config['options'] ?? '') : '' }}</textarea>
            </div>
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>

    <script>
        var typeOptions = @json($typeOptions);
        var fieldCounters = {};

        function getPath(parentId) {
            if (parentId === 'root') return 'children';
            var indices = [];
            var el = document.getElementById('field-item-' + parentId);
            while (el) {
                indices.unshift(parseInt(el.dataset.index));
                var pid = el.dataset.parent;
                if (pid === 'root') break;
                el = document.getElementById('field-item-' + pid);
            }
            var path = 'children[' + indices[0] + ']';
            for (var i = 1; i < indices.length; i++) {
                path += '[children][' + indices[i] + ']';
            }
            return path + '[children]';
        }

        function escapeHtml(str) {
            if (!str) return '';
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(String(str)));
            return div.innerHTML;
        }

        function buildFieldHtml(parentId, idx, uid, child) {
            var basePath = getPath(parentId);
            var fieldName = basePath + '[' + idx + ']';

            var opts = '';
            for (var val in typeOptions) {
                var sel = (child && child.type === val) ? ' selected' : '';
                opts += '<option value="' + val + '"' + sel + '>' + typeOptions[val] + '</option>';
            }

            var label = child ? escapeHtml(child.label) : '';
            var name = child ? escapeHtml(child.name) : '';
            var reqChecked = (child && child.is_required) ? ' checked' : '';
            var sortOrder = child ? (child.sort_order || idx) : idx;
            var isContainer = child && child.type === 'container';
            var nestedClass = isContainer ? '' : ' hidden';
            var idField = child ? '<input type="hidden" name="' + fieldName + '[id]" value="' + child.id + '" />' : '';

            return '<div id="field-item-' + uid + '" class="child-field-item bg-white border rounded-lg" data-index="' + idx + '" data-parent="' + parentId + '">'
                + idField
                + '<div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b rounded-t-lg">'
                + '<div class="flex items-center gap-2">'
                + '<i class="icon-[tabler--grip-vertical] text-gray-300 cursor-move"></i>'
                + '<span class="text-xs font-medium text-gray-500">Field #' + (idx + 1) + '</span>'
                + '</div>'
                + '<button type="button" onclick="removeChildField(\'' + uid + '\', \'' + parentId + '\')" class="text-gray-300 hover:text-red-500 transition-colors p-1">'
                + '<i class="icon-[tabler--x] text-xs"></i></button>'
                + '</div>'
                + '<div class="p-4 grid grid-cols-12 gap-4">'
                + '<div class="col-span-6">'
                + '<label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Label</label>'
                + '<input type="text" name="' + fieldName + '[label]" value="' + label + '" placeholder="Label" class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" />'
                + '</div>'
                + '<div class="col-span-6">'
                + '<label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Name</label>'
                + '<input type="text" name="' + fieldName + '[name]" value="' + name + '" placeholder="name_field" class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" />'
                + '</div>'
                + '<div class="col-span-12">'
                + '<label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Type</label>'
                + '<select name="' + fieldName + '[type]" onchange="onChildTypeChange(\'' + uid + '\', this.value)" class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm">' + opts + '</select>'
                + '</div>'
                + '<input type="hidden" name="' + fieldName + '[sort_order]" value="' + sortOrder + '" />'
                + '<div class="col-span-12">'
                + '<label class="inline-flex items-center gap-2 cursor-pointer">'
                + '<input type="checkbox" name="' + fieldName + '[is_required]" value="1"' + reqChecked + ' class="rounded border-gray-300 text-blue-600">'
                + '<span class="font-body-sm text-body-sm font-bold text-on-surface-variant">Required</span></label>'
                + '</div>'
                + '</div>'
                + '<div id="nested-' + uid + '" class="' + nestedClass + ' border-t border-gray-100 bg-gray-50 px-4 py-3">'
                + '<div id="children-' + uid + '" class="space-y-2"></div>'
                + '<div id="children-empty-' + uid + '" class="text-center py-3 text-gray-400 text-xs">No nested fields yet.</div>'
                + '<div class="flex justify-end mt-2">'
                + '<button type="button" onclick="addChildField(\'' + uid + '\')" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded hover:bg-blue-100 border border-blue-200">'
                + '<i class="icon-[tabler--plus] text-[10px]"></i> Add Field</button>'
                + '</div>'
                + '</div>'
                + '</div>';
        }

        function addChildField(parentId) {
            var container = document.getElementById('children-' + parentId);
            var empty = document.getElementById('children-empty-' + parentId);
            if (empty) empty.style.display = 'none';

            if (!fieldCounters[parentId]) fieldCounters[parentId] = 0;
            var idx = fieldCounters[parentId]++;
            var uid = parentId + '_' + idx;

            var html = buildFieldHtml(parentId, idx, uid, null);
            container.insertAdjacentHTML('beforeend', html);

            var newItem = container.lastElementChild;
            var labelInput = newItem.querySelector('input[name$="[label]"]');
            var nameInput = newItem.querySelector('input[name$="[name]"]');
            labelInput.addEventListener('input', function() {
                nameInput.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
            });

            newItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function removeChildField(uid, parentId) {
            var el = document.getElementById('field-item-' + uid);
            if (el) el.remove();
            checkChildEmpty(parentId);
        }

        function checkChildEmpty(parentId) {
            var container = document.getElementById('children-' + parentId);
            var empty = document.getElementById('children-empty-' + parentId);
            if (empty && container) {
                empty.style.display = container.children.length === 0 ? 'block' : 'none';
            }
        }

        function onChildTypeChange(uid, type) {
            var nestedArea = document.getElementById('nested-' + uid);
            if (nestedArea) {
                if (type === 'container') {
                    nestedArea.classList.remove('hidden');
                } else {
                    nestedArea.classList.add('hidden');
                }
            }
        }

        function loadExistingChildren(parentId, children) {
            if (!children || children.length === 0) return;

            var container = document.getElementById('children-' + parentId);
            if (!container) return;

            var empty = document.getElementById('children-empty-' + parentId);
            if (empty) empty.style.display = 'none';

            if (!fieldCounters[parentId]) fieldCounters[parentId] = 0;

            children.forEach(function(child) {
                var idx = fieldCounters[parentId]++;
                var uid = parentId + '_' + idx;

                var html = buildFieldHtml(parentId, idx, uid, child);
                container.insertAdjacentHTML('beforeend', html);

                var newItem = container.lastElementChild;
                var labelInput = newItem.querySelector('input[name$="[label]"]');
                var nameInput = newItem.querySelector('input[name$="[name]"]');
                labelInput.addEventListener('input', function() {
                    nameInput.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
                });

                if (child.children && child.children.length > 0) {
                    loadExistingChildren(uid, child.children);
                }
            });
        }

        function initFieldForm() {
            var typeSelect = document.querySelector('select[name="type"]');
            if (!typeSelect) return;

            var containerSettings = document.getElementById('container-settings');
            var optionsConfig = document.getElementById('options-config');

            function updateVisibility() {
                var val = typeSelect.value;
                containerSettings.style.display = val === 'container' ? 'block' : 'none';
                optionsConfig.style.display = ['select', 'radio', 'checkbox', 'button_group'].includes(val) ? 'block' : 'none';
            }

            typeSelect.addEventListener('change', updateVisibility);
            updateVisibility();

            @if($isEdit && ($model->type ?? '') === 'container')
                var existingChildren = {!! $existingChildrenJson ?? '[]' !!};
                console.log('Loading existing children:', existingChildren);
                if (existingChildren && existingChildren.length > 0) {
                    loadExistingChildren('root', existingChildren);
                }
            @endif
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFieldForm);
        } else {
            initFieldForm();
        }
    </script>
</x-layouts::app>
