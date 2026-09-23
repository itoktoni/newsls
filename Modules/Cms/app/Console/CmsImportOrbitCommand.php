<?php

namespace Modules\Cms\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Cms\Models\Content;
use Modules\Cms\Models\Field;
use Modules\Cms\Models\Menu;
use Modules\Cms\Models\Section;
use Modules\Cms\Models\Type;

class CmsImportOrbitCommand extends Command
{
    protected $signature = 'cms:import-orbit';

    protected $description = 'Import CMS data from content/*.json (Orbit) into MariaDB (idempotent, deduplicated)';

    public function handle(): int
    {
        $this->info('Importing CMS Orbit data into MariaDB...');

        $types = $this->readDir('types');
        $sections = $this->readDir('sections');
        $fields = array_merge($this->readDir('fields'), $this->readDir('custom_fields'));
        $contents = $this->readDir('contents');

        // Canonical maps: source id -> kept id (lowest id per dedupe key).
        $typeMap = $this->dedupeMap($types, 'slug');
        $fieldMap = $this->dedupeMap($fields, 'name');
        [$canonicalSections, $sectionMap, $dupSectionIds] = $this->dedupeSections($sections, $typeMap);
        [$canonicalContents, $dupContentIds] = $this->dedupeContents($contents, $typeMap, $sectionMap);

        DB::transaction(function () use ($types, $typeMap, $fields, $fieldMap, $canonicalSections, $dupSectionIds, $canonicalContents, $dupContentIds) {
            $canonicalTypeIds = $this->canonicalIds($typeMap);
            foreach ($types as $data) {
                if (in_array($data['id'], $canonicalTypeIds, true)) {
                    $this->upsert(Type::class, $data, ['name', 'slug', 'type', 'description', 'supports', 'is_active', 'menu_position', 'menu_icon']);
                }
            }

            $canonicalFieldIds = $this->canonicalIds($fieldMap);
            foreach ($fields as $data) {
                if (in_array($data['id'], $canonicalFieldIds, true)) {
                    $this->upsert(Field::class, $data, ['name', 'label', 'type', 'config', 'rules', 'is_required', 'default_value', 'sort_order', 'parent_id', 'mode', 'min', 'max', 'collapsed', 'sortable', 'cloneable', 'layouts', 'type_id']);
                }
            }

            foreach ($canonicalSections as $data) {
                $data['content_type_id'] = $typeMap[$data['content_type_id']] ?? $data['content_type_id'];
                $data['field_ids'] = array_map(fn ($id) => $fieldMap[$id] ?? $id, $data['field_ids'] ?? []);
                $this->upsert(Section::class, $data, ['name', 'description', 'icon', 'content_type_id', 'field_ids', 'sort_order', 'is_active']);
            }

            foreach ($canonicalContents as $data) {
                $content = $this->upsert(Content::class, $data, ['content_type_id', 'title', 'slug', 'content', 'excerpt', 'status', 'published_at', 'author_id', 'featured_image', 'menu_order', 'meta', 'active_sections']);
                if (! empty($data['category_ids'])) {
                    $content->has_categories()->sync($data['category_ids']);
                }
                if (! empty($data['tag_ids'])) {
                    $content->has_tags()->sync($data['tag_ids']);
                }
            }

            // Remove leftovers from previous runs (never touches user-created rows).
            if ($dupContentIds !== []) {
                Content::whereIn('id', $dupContentIds)->delete();
            }
            if ($dupSectionIds !== []) {
                Section::whereIn('id', $dupSectionIds)->delete();
            }
            foreach ($this->dupIds($fieldMap) as $id) {
                Field::where('id', $id)->delete();
            }
            foreach ($this->dupIds($typeMap) as $id) {
                Type::where('id', $id)->delete();
            }
        });

        $this->seedMenus();

        $this->info(sprintf('  Types: %d canonical of %d files.', count($this->canonicalIds($typeMap)), count($types)));
        $this->info(sprintf('  Sections: %d canonical of %d files.', count($canonicalSections), count($sections)));
        $this->info(sprintf('  Fields: %d canonical of %d files.', count($this->canonicalIds($fieldMap)), count($fields)));
        $this->info(sprintf('  Contents: %d canonical of %d files.', count($canonicalContents), count($contents)));
        $this->info('CMS Orbit import finished.');

        return self::SUCCESS;
    }

    /**
     * Group source records by a field and map every source id to the lowest id
     * in its group (the canonical, kept record).
     */
    private function dedupeMap(array $items, string $keyField): array
    {
        $groups = [];
        foreach ($items as $item) {
            if (isset($item[$keyField])) {
                $groups[(string) $item[$keyField]][] = $item['id'];
            }
        }

        $map = [];
        foreach ($groups as $ids) {
            sort($ids);
            foreach ($ids as $id) {
                $map[$id] = $ids[0];
            }
        }

        return $map;
    }

    private function canonicalIds(array $map): array
    {
        return array_values(array_unique(array_values($map)));
    }

    private function dupIds(array $map): array
    {
        return array_keys(array_filter($map, fn ($canonical, $id) => $canonical !== $id, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Sections reference a content type. After types are deduplicated, sections
     * are re-pointed to the canonical type and deduplicated by (type, name).
     *
     * @return array{0: array, 1: array, 2: array} [canonical records, sourceId=>canonicalId, duplicate ids]
     */
    private function dedupeSections(array $sections, array $typeMap): array
    {
        $groups = [];
        foreach ($sections as $section) {
            $typeId = $typeMap[$section['content_type_id']] ?? $section['content_type_id'];
            $groups[$typeId.'|'.$section['name']][] = $section;
        }

        $canonical = [];
        $map = [];
        foreach ($groups as $group) {
            usort($group, fn ($a, $b) => $a['id'] <=> $b['id']);
            $canonical[$group[0]['id']] = $group[0];
            foreach ($group as $item) {
                $map[$item['id']] = $group[0]['id'];
            }
        }

        return [$canonical, $map, $this->dupIds($map)];
    }

    /**
     * Keep one content per slug (lowest id), re-point its content type and
     * remap section references inside active_sections / meta._active_field_groups.
     *
     * @return array{0: array, 1: array} [canonical records, duplicate ids]
     */
    private function dedupeContents(array $contents, array $typeMap, array $sectionMap): array
    {
        $groups = [];
        foreach ($contents as $content) {
            if (isset($content['slug'])) {
                $groups[(string) $content['slug']][] = $content;
            }
        }

        $canonical = [];
        $map = [];
        foreach ($groups as $group) {
            usort($group, fn ($a, $b) => $a['id'] <=> $b['id']);
            $record = $group[0];
            $record['content_type_id'] = $typeMap[$record['content_type_id']] ?? $record['content_type_id'];

            if (! empty($record['active_sections']) && is_array($record['active_sections'])) {
                $record['active_sections'] = array_map(fn ($id) => $sectionMap[$id] ?? $id, $record['active_sections']);
            }
            if (isset($record['meta']['_active_field_groups']) && is_array($record['meta']['_active_field_groups'])) {
                $record['meta']['_active_field_groups'] = array_map(fn ($id) => $sectionMap[$id] ?? $id, $record['meta']['_active_field_groups']);
            }

            // Published posts without a publish date fall back to their creation date.
            if (($record['status'] ?? null) === 'published' && empty($record['published_at'])) {
                $record['published_at'] = $record['created_at'] ?? now()->toDateTimeString();
            }

            $canonical[$record['id']] = $record;
            foreach ($group as $item) {
                $map[$item['id']] = $record['id'];
            }
        }

        return [$canonical, $this->dupIds($map)];
    }

    private function seedMenus(): void
    {
        $menus = [
            'main' => [
                'name' => 'Main Menu',
                'slug' => 'main-menu',
                'items' => [
                    ['label' => 'Home', 'url' => '/', 'order' => 0],
                    ['label' => 'Blog', 'url' => '/blog', 'order' => 1],
                    ['label' => 'Services', 'url' => '/services', 'order' => 2],
                    ['label' => 'Contact', 'url' => '/contact', 'order' => 3],
                ],
            ],
            'footer' => [
                'name' => 'Footer Menu',
                'slug' => 'footer-menu',
                'items' => [
                    ['label' => 'Home', 'url' => '/', 'order' => 0],
                    ['label' => 'Blog', 'url' => '/blog', 'order' => 1],
                    ['label' => 'Privacy', 'url' => '/privacy', 'order' => 2],
                ],
            ],
        ];

        foreach ($menus as $location => $data) {
            $menu = Menu::withTrashed()->where('location', $location)->first();
            if ($menu === null) {
                Menu::create($data + ['location' => $location, 'is_active' => true, 'sort_order' => 0]);
            } elseif ($menu->trashed()) {
                $menu->restore();
                $menu->update($data + ['location' => $location, 'is_active' => true, 'sort_order' => 0]);
            }
        }
    }

    /**
     * Idempotently create/update a record preserving its Orbit id.
     *
     * Eloquent's updateOrCreate() cannot set a primary key that is not fillable,
     * so the id is assigned directly on the model instead of via mass assignment.
     */
    private function upsert(string $class, array $data, array $keys): Model
    {
        $values = $this->only($data, $keys);

        $model = $class::find($data['id']);
        if ($model === null) {
            $model = new $class($values);
            $model->id = $data['id'];
        } else {
            $model->fill($values);
        }

        $model->save();

        return $model;
    }

    private function readDir(string $dir): array
    {
        $path = base_path('content/'.$dir);
        if (! is_dir($path)) {
            $this->warn("content/{$dir} not found, skipped.");

            return [];
        }

        $items = [];
        foreach (glob($path.'/*.json') as $file) {
            $data = json_decode((string) file_get_contents($file), true);
            if (is_array($data)) {
                $items[] = $data;
            }
        }

        return $items;
    }

    private function only(array $data, array $keys): array
    {
        $out = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && ! is_null($data[$key])) {
                $out[$key] = $data[$key];
            }
        }

        return $out;
    }
}
