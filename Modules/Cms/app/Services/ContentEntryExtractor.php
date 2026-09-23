<?php

namespace Modules\Cms\Services;

use Modules\Cms\Models\Content;

class ContentEntryExtractor
{
    public static function extract(Content $entry): array
    {
        $entry->loadMissing(['has_type.has_sections']);

        $blueprint = $entry->getBlueprintSchema();
        $meta = $entry->meta ?? [];
        $sections = [];

        foreach ($blueprint['sections'] as $sectionName => $sectionSchema) {
            $sectionData = self::collectSectionData($meta, $sectionSchema['fields']);
            if ($sectionData !== []) {
                $sections[$sectionName] = $sectionData;
            }
        }

        return [
            'id' => $entry->id,
            'title' => $entry->title,
            'slug' => $entry->slug,
            'status' => $entry->status,
            'content_type' => $blueprint['content_type'],
            'excerpt' => $entry->excerpt,
            'content' => $entry->content,
            'featured_image' => $entry->featured_image,
            'published_at' => $entry->published_at,
            'meta' => $meta,
            'categories' => self::extractCategories($entry),
            'tags' => self::extractTags($entry),
            'sections' => $sections,
        ];
    }

    public static function formSchema(Content $entry): array
    {
        $entry->loadMissing(['has_type.has_sections']);

        $blueprint = $entry->getBlueprintSchema();
        $meta = $entry->meta ?? [];

        $sections = [];
        foreach ($blueprint['sections'] as $sectionName => $sectionSchema) {
            $sections[$sectionName] = [
                'name' => $sectionName,
                'label' => $sectionSchema['label'],
                'description' => $sectionSchema['description'],
                'icon' => $sectionSchema['icon'],
                'fields' => self::buildFormFields(
                    $sectionSchema['fields'],
                    self::collectSectionData($meta, $sectionSchema['fields'])
                ),
            ];
        }

        return [
            'content_type' => $blueprint['content_type'],
            'supports' => $blueprint['supports'],
            'sections' => $sections,
            'values' => $meta,
        ];
    }

    /**
     * Collect field values for a section from meta, keyed by field name.
     */
    private static function collectSectionData(array $meta, array $fields): array
    {
        $result = [];

        foreach ($fields as $fieldSchema) {
            $fieldName = $fieldSchema['name'];

            if (! isset($meta[$fieldName])) {
                continue;
            }

            $value = $meta[$fieldName];

            if ($fieldSchema['type'] === 'container' && is_array($value)) {
                $result[$fieldName] = array_map(function ($item) {
                    return is_array($item) ? $item : $item;
                }, $value);
            } else {
                $result[$fieldName] = $value;
            }
        }

        return $result;
    }

    private static function extractSectionData(array $sectionData, array $fields): array
    {
        $result = [];

        // Handle case where meta is a numerically-indexed array (e.g., hero slides)
        // and the first field is a container — treat entire meta as that container's items
        if (isset($fields[0]) && $fields[0]['type'] === 'container' && isset($sectionData[0])) {
            $fieldName = $fields[0]['name'];
            $children = $fields[0]['fields'] ?? [];
            $result[$fieldName] = array_map(function ($item) use ($children) {
                return self::extractContainerItem($item, $children);
            }, $sectionData);

            return $result;
        }

        // Handle case where meta is an object and first field is a container
        // (e.g., verification, cta, news with named sub-fields)
        if (isset($fields[0]) && $fields[0]['type'] === 'container' && ! isset($sectionData[0]) && is_array($sectionData)) {
            $fieldName = $fields[0]['name'];
            $children = $fields[0]['fields'] ?? [];
            $result[$fieldName] = self::extractContainerItem($sectionData, $children);

            return $result;
        }

        foreach ($fields as $fieldSchema) {
            $fieldName = $fieldSchema['name'];
            $fieldType = $fieldSchema['type'];

            if (isset($sectionData[$fieldName])) {
                $value = $sectionData[$fieldName];

                if ($fieldType === 'container' && is_array($value)) {
                    $result[$fieldName] = array_map(function ($item) use ($fieldSchema) {
                        return self::extractContainerItem($item, $fieldSchema['fields'] ?? []);
                    }, $value);
                } else {
                    $result[$fieldName] = $value;
                }
            }
        }

        return $result;
    }

    private static function extractContainerItem(array $item, array $children): array
    {
        $result = [];

        foreach ($children as $childSchema) {
            $childName = $childSchema['name'];
            $result[$childName] = $item[$childName] ?? null;
        }

        return $result;
    }

    private static function buildFormFields(array $fields, array $values): array
    {
        $result = [];

        foreach ($fields as $fieldSchema) {
            $fieldName = $fieldSchema['name'];
            $fieldType = $fieldSchema['type'];

            $field = [
                'name' => $fieldName,
                'label' => $fieldSchema['label'],
                'type' => $fieldType,
                'required' => $fieldSchema['required'] ?? false,
                'value' => $values[$fieldName] ?? null,
            ];

            if ($fieldType === 'container') {
                $field['mode'] = $fieldSchema['mode'] ?? 'multiple';
                $field['min'] = $fieldSchema['min'] ?? 0;
                $field['max'] = $fieldSchema['max'] ?? 100;
                $field['children'] = $fieldSchema['fields'] ?? [];
                $field['value'] = $values[$fieldName] ?? [];
            }

            if ($fieldType === 'select') {
                $field['options'] = $fieldSchema['options'] ?? [];
            }

            $result[] = $field;
        }

        return $result;
    }

    private static function extractCategories(Content $entry): array
    {
        return $entry->has_categories()->get()->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'slug' => $c->slug,
        ])->values()->toArray();
    }

    private static function extractTags(Content $entry): array
    {
        return $entry->has_tags()->get()->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'slug' => $t->slug,
        ])->values()->toArray();
    }
}
