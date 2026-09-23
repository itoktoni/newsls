<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Requests\GeneralRequest;
use Illuminate\Http\Request;
use Modules\Cms\Models\Category;
use Modules\Cms\Models\Content;
use Modules\Cms\Models\Field;
use Modules\Cms\Models\Section;
use Modules\Cms\Models\Tag;
use Modules\Cms\Models\Type;

class ContentController extends Controller
{
    public function __construct()
    {
        $this->model = new Content;
    }

    protected function share($data = [])
    {
        $default = [
            'model' => $this->model,
            'contentTypes' => Type::pluck('name', 'id')->toArray(),
            'allTypes' => Type::all()->toArray(),
            'allSections' => Section::all()->toArray(),
            'allFields' => Field::all()->toArray(),
            'contentTypeId' => request()->input('content_type_id'),
            'categories' => Category::pluck('name', 'id'),
            'tags' => Tag::pluck('name', 'id'),
            'typeTabs' => $this->typeTabs(),
            'activeTypeSlug' => request('filters.has_type.slug.$eq', $this->defaultTypeSlug()),
        ];

        return array_merge($default, $data);
    }

    protected function defaultTypeSlug(): string
    {
        return 'homepage';
    }

    protected function typeTabs(): array
    {
        return Type::where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name', 'slug'])
            ->unique('slug')
            ->values()
            ->map(fn ($type) => ['id' => $type->id, 'name' => $type->name, 'slug' => $type->slug])
            ->all();
    }

    protected function getData()
    {
        $query = $this->model->filter()->sort();

        if (! request()->has('filters.has_type')) {
            $query->whereHas('has_type', fn ($q) => $q->where('slug', $this->defaultTypeSlug()));
        }

        return $query;
    }

    public function getCreate(GeneralRequest $request)
    {
        return $this->views($this->template(), ['model' => $this->model]);
    }

    public function postCreate(GeneralRequest $request)
    {
        $content = Content::create($request->only([
            'content_type_id',
            'title',
            'slug',
            'content',
            'excerpt',
            'status',
            'published_at',
            'author_id',
            'featured_image',
            'menu_order',
            'meta',
            'active_sections',
        ]));

        try {
            $this->syncRelations($content, $request);

            return $this->response(['status' => true, 'message' => TOAST_SUCCESS, 'data' => $content]);
        } catch (\Throwable $th) {
            return $this->response(['status' => false, 'message' => TOAST_FAILED, 'data' => $th->getMessage()]);
        }
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $content = Content::findOrFail($id);
        $content->update($request->only([
            'content_type_id',
            'title',
            'slug',
            'content',
            'excerpt',
            'status',
            'published_at',
            'author_id',
            'featured_image',
            'menu_order',
            'meta',
            'active_sections',
        ]));

        try {
            $this->syncRelations($content, $request);

            return $this->response(['status' => true, 'message' => TOAST_SUCCESS, 'data' => $content]);
        } catch (\Throwable $th) {
            return $this->response(['status' => false, 'message' => TOAST_FAILED, 'data' => $th->getMessage()]);
        }
    }

    private function syncRelations(Content $content, Request $request): void
    {
        $content->has_categories()->sync(array_map('intval', $request->input('category_ids', [])));
        $content->has_tags()->sync(array_map('intval', $request->input('tag_ids', [])));
    }

    public function getSectionHtml($id)
    {
        $section = Section::findOrFail($id);

        $group = $section;
        $group->fields = $section->fields;

        $html = view('cms::pages.content.partials.section-card', [
            'group' => $group,
            'isNewSection' => true,
        ])->render();

        return response()->json([
            'html' => $html,
            'section' => [
                'id' => $section->id,
                'name' => $section->name ?? '',
                'description' => $section->description ?? '',
            ],
        ]);
    }

    public function preview(Request $request, ?int $id = null)
    {
        $metaData = $request->input('meta', []);
        $activeSections = $request->input('active_sections', $request->input('active_field_groups', []));
        $type = null;

        if ($request->filled('content_type_id')) {
            $type = Type::find($request->input('content_type_id'));
        }

        $sections = [];
        if ($type && ! empty($activeSections)) {
            foreach ($activeSections as $sectionId) {
                $section = Section::find($sectionId);
                if (! $section) {
                    continue;
                }
                $fieldValues = [];
                $fieldIds = $section->field_ids ?? [];
                foreach ($fieldIds as $fid) {
                    $field = Field::find($fid);
                    if ($field) {
                        $fieldValues[$field->name ?? 'f_'.$fid] = $metaData[$field->name ?? ''] ?? null;
                    }
                }
                $sections[$section->name ?? 'Section '.$sectionId] = $fieldValues;
            }
        }

        return response()->json([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'content' => $request->input('content'),
            'excerpt' => $request->input('excerpt'),
            'status' => $request->input('status', 'draft'),
            'featured_image' => $request->input('featured_image'),
            'content_type' => $type->slug ?? null,
            'sections' => $sections,
        ]);
    }
}
