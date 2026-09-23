<?php

namespace App\Helpers;

use Modules\Cms\Models\Field;

class CmsHelper
{
    /**
     * Get a nested field value from content meta.
     *
     * @param  array  $meta  The meta array
     * @param  string  $path  Dot notation path (e.g., "hero.title" or "slider.0.image")
     * @param  mixed  $default  Default value if not found
     * @return mixed
     */
    public static function getField(array $meta, string $path, $default = null)
    {
        $keys = explode('.', $path);

        foreach ($keys as $key) {
            if (is_array($meta) && isset($meta[$key])) {
                $meta = $meta[$key];
            } else {
                return $default;
            }
        }

        return $meta;
    }

    /**
     * Get all container fields from meta.
     */
    public static function getSections(array $meta): array
    {
        $sections = [];

        foreach ($meta as $key => $value) {
            // Skip internal keys
            if (str_starts_with($key, '_')) {
                continue;
            }

            // Skip top-level fields that are not containers
            $field = CustomField::where('name', $key)->first();
            if (! $field || ! $field->isContainerType()) {
                continue;
            }

            $sections[$key] = $value;
        }

        return $sections;
    }
}
