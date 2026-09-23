<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Requests\GeneralRequest;
use Modules\Cms\Models\Field;

class FieldController extends Controller
{
    public function __construct()
    {
        $this->model = new Field;
    }

    protected function share($data = [])
    {
        $default = [
            'model' => $this->model,
            'typeOptions' => Field::getTypeOptions(),
            'modeOptions' => ['single' => 'Single', 'multiple' => 'Multiple (Repeater)', 'flexible' => 'Flexible Content'],
            'types' => [],
        ];

        return array_merge($default, $data);
    }

    protected function getData()
    {
        return $this->model->whereNull('parent_id')->filter()->sort();
    }

    public function getCreate(GeneralRequest $request)
    {
        return $this->views($this->template(), ['model' => $this->model, 'existingChildrenJson' => '[]']);
    }

    public function postCreate(GeneralRequest $request)
    {
        $data = $request->validate((new Field)->rules());

        try {
            $field = $this->saveField($data);

            return $this->response(['status' => true, 'message' => 'Field created successfully', 'data' => $field]);
        } catch (\Throwable $th) {
            return $this->response(['status' => false, 'message' => 'Failed', 'data' => $th->getMessage()]);
        }
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $data = $request->validate((new Field)->rules());

        try {
            $field = Field::findOrFail($id);
            $this->saveField($data, $field);

            return $this->response(['status' => true, 'message' => 'Field updated successfully', 'data' => $field]);
        } catch (\Throwable $th) {
            return $this->response(['status' => false, 'message' => 'Failed', 'data' => $th->getMessage()]);
        }
    }

    private function saveField(array $data, ?Field $field = null): Field
    {
        $fieldData = collect($data)->except(['children', 'config_options'])->toArray();
        $fieldData['is_required'] = ! empty($fieldData['is_required']);

        if (! empty($data['config_options'])) {
            $options = [];
            foreach (explode("\n", $data['config_options']) as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }
                if (str_contains($line, ':')) {
                    [$val, $label] = array_map('trim', explode(':', $line, 2));
                    $options[$val] = $label;
                } else {
                    $options[$line] = $line;
                }
            }
            $fieldData['config'] = ['options' => $options];
        }

        if ($field) {
            $field->update($fieldData);
        } else {
            $field = Field::create($fieldData);
        }

        if (! empty($data['children'])) {
            $this->syncChildren($field, $data['children']);
        } else {
            $field->has_children()->each(function ($c) {
                $c->has_children()->each(fn ($gc) => $gc->delete());
                $c->delete();
            });
        }

        return $field;
    }

    private function syncChildren(Field $parent, array $children): void
    {
        $existingIds = $parent->has_children()->pluck('id')->toArray();
        $keepIds = [];

        foreach ($children as $index => $childData) {
            $attrs = [
                'label' => $childData['label'] ?? '',
                'name' => $childData['name'] ?? '',
                'type' => $childData['type'] ?? 'text',
                'is_required' => ! empty($childData['is_required']),
                'sort_order' => $childData['sort_order'] ?? $index,
                'parent_id' => $parent->id,
            ];

            if (! empty($childData['id']) && in_array($childData['id'], $existingIds)) {
                $child = Field::find($childData['id']);
                $child->update($attrs);
                $keepIds[] = $child->id;
            } else {
                $child = Field::create($attrs);
                $keepIds[] = $child->id;
            }

            if (! empty($childData['children'])) {
                $this->syncChildren($child, $childData['children']);
            }
        }

        foreach (array_diff($existingIds, $keepIds) as $removeId) {
            $remove = Field::find($removeId);
            if ($remove) {
                $remove->has_children()->each(fn ($c) => $c->delete());
                $remove->delete();
            }
        }
    }

    public function getUpdate(GeneralRequest $request, $id)
    {
        $model = Field::findOrFail($id);

        $existingChildren = $model->has_children()
            ->orderBy('sort_order')
            ->get()
            ->map(function ($child) {
                return [
                    'id' => $child->id,
                    'label' => $child->label ?? '',
                    'name' => $child->name ?? '',
                    'type' => $child->type ?? '',
                    'is_required' => (bool) ($child->is_required ?? false),
                    'sort_order' => $child->sort_order ?? 0,
                    'children' => $this->buildChildTree($child->id),
                ];
            })
            ->toArray();

        return $this->views($this->template(), [
            'model' => $model,
            'existingChildrenJson' => json_encode($existingChildren),
        ]);
    }

    private function buildChildTree(int $parentId): array
    {
        return Field::where('parent_id', $parentId)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($child) {
                return [
                    'id' => $child->id,
                    'label' => $child->label ?? '',
                    'name' => $child->name ?? '',
                    'type' => $child->type ?? '',
                    'is_required' => (bool) ($child->is_required ?? false),
                    'sort_order' => $child->sort_order ?? 0,
                    'children' => $this->buildChildTree($child->id),
                ];
            })
            ->toArray();
    }
}
