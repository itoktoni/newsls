<?php

namespace Modules\Cms\Http\Controllers\Api;

use Modules\Cms\Http\Controllers\Controller;
use Modules\Cms\Models\Content;
use Modules\Cms\Models\Type;

class CmsController extends Controller
{
    public function show(Content $content)
    {
        return response()->json($content->getNormalizedData());
    }

    public function indexByType(string $slug)
    {
        $type = Type::where('slug', $slug)->firstOrFail();
        $entries = $type->has_contents()
            ->published()
            ->get()
            ->map(fn ($entry) => $entry->getNormalizedData());

        return response()->json($entries);
    }

    public function getBlueprintSchema(string $slug)
    {
        $type = Type::where('slug', $slug)->firstOrFail();
        $schema = [];
        $sections = $type->has_sections()->where('is_active', true)->get();

        foreach ($sections as $group) {
            $schema[$group->name] = $group->getJsonSchema();
        }

        return response()->json([
            'content_type' => $type->slug,
            'type' => $type->type,
            'supports' => $type->supports,
            'sections' => $schema,
        ]);
    }
}
