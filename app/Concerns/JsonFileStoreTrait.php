<?php

namespace App\Concerns;

use App\Http\Requests\GeneralRequest;
use Illuminate\Support\Collection;

trait JsonFileStoreTrait
{
    abstract protected function jsonFilePath(): string;

    // Borrowed from ControllerTrait to avoid property_exists(null) crash.
    protected function getFields(): array
    {
        return [];
    }

    // ponytail: decorates stdClass so views see exists, field_primary, field_name
    protected function enrichItem(object $item): object
    {
        $item->exists = true;
        $item->field_primary = $item->id ?? null;
        $item->field_name = $item->name ?? $item->title ?? '';

        return $item;
    }

    public function postCreate(GeneralRequest $request): mixed
    {
        $items = $this->readJson();
        $data = $request->except(['_token', '_method', 'meta', 'active_sections', 'active_field_groups', 'category_ids', 'tag_ids']);
        $data['id'] = $this->nextId($items);
        $data['created_at'] = now()->toIso8601String();
        $data['updated_at'] = $data['created_at'];
        $items[] = $data;
        $this->writeJson($items);

        return $this->response(['status' => true, 'message' => 'Created', 'data' => (object) $data]);
    }

    public function postUpdate(GeneralRequest $request, $id): mixed
    {
        $items = $this->readJson();
        $data = $request->except(['_token', '_method', 'meta', 'active_sections', 'active_field_groups', 'category_ids', 'tag_ids']);
        $found = false;
        foreach ($items as &$item) {
            if (($item['id'] ?? null) == $id) {
                foreach ($data as $k => $v) {
                    $item[$k] = $v;
                }
                $item['updated_at'] = now()->toIso8601String();
                $found = true;
                $data = $item;
                break;
            }
        }
        if (! $found) {
            return $this->response(['status' => false, 'message' => 'Not found']);
        }
        $this->writeJson($items);

        return $this->response(['status' => true, 'message' => 'Updated', 'data' => (object) $data]);
    }

    public function getDelete(GeneralRequest $request, $id): mixed
    {
        $items = $this->readJson();
        $items = array_values(array_filter($items, fn ($i) => ($i['id'] ?? null) != $id));
        $this->writeJson($items);

        return $this->response(['status' => true, 'message' => 'Deleted']);
    }

    public function postDelete(GeneralRequest $request): mixed
    {
        $ids = $request->input('ids', []);
        if (! is_array($ids)) {
            $ids = [$ids];
        }
        $items = $this->readJson();
        $items = array_values(array_filter($items, fn ($i) => ! in_array($i['id'] ?? null, $ids)));
        $this->writeJson($items);

        return $this->response(['status' => true, 'message' => 'Deleted']);
    }

    public function getTable(GeneralRequest $request)
    {
        $items = collect($this->readJson())->map(fn ($i) => $this->enrichItem((object) $i));
        $perPage = (int) $request->input('per_page', 25);
        $page = (int) $request->input('page', 1);
        $total = $items->count();
        $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return $this->views($this->template(), [
            'data' => new class($slice, $total, $perPage, $page)
            {
                public function __construct(
                    public Collection $items,
                    public int $total,
                    public int $perPage,
                    public int $currentPage,
                ) {}

                public function count(): int
                {
                    return $this->items->count();
                }

                public function items()
                {
                    return $this->items;
                }

                public function links()
                {
                    return null;
                }

                public function lastPage(): int
                {
                    return (int) ceil($this->total / $this->perPage);
                }

                public function hasMorePages(): bool
                {
                    return $this->currentPage < $this->lastPage();
                }

                public function nextPageUrl()
                {
                    return null;
                }

                public function previousPageUrl()
                {
                    return null;
                }

                public function onFirstPage(): bool
                {
                    return $this->currentPage <= 1;
                }

                public function withQueryString()
                {
                    return $this;
                }
            },
            'fields' => $this->getFields(),
        ]);
    }

    public function getUpdate(GeneralRequest $request, $id)
    {
        $items = $this->readJson();
        $data = null;
        foreach ($items as $item) {
            if (($item['id'] ?? null) == $id) {
                $data = (object) $item;
                break;
            }
        }

        return $this->views($this->template(), ['model' => $data ? $this->enrichItem($data) : null]);
    }

    protected function readJson(): array
    {
        $path = $this->jsonFilePath();
        if (! file_exists($path)) {
            return [];
        }
        $contents = file_get_contents($path);
        $decoded = json_decode($contents, true);

        return is_array($decoded) ? $decoded : [];
    }

    protected function writeJson(array $data): void
    {
        $path = $this->jsonFilePath();
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    protected function nextId(array $items): int
    {
        $ids = array_map(fn ($i) => (int) ($i['id'] ?? 0), $items);

        return $ids ? max($ids) + 1 : 1;
    }
}
