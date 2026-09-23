# CMS Module (MariaDB) — Implementation Plan

> Feature: Migrate all CMS functionality from `ecmsoftware/` into `Modules/Cms/` (nwidart), stored in MariaDB (no Orbit/JSON), following `AGENTS.md` conventions.
> Date: 2026-08-20. Spec: `docs/superpowers/specs/2026-08-20-cms-module-mariadb-design.md`.

## Summary

Port the CMS admin (Types, Fields, Sections, Content, Categories, Tags, Menus) plus Media API and public Content API from the `ecmsoftware/` folder into the existing `Modules/Cms/` module. Data storage moves from Orbit flat-file JSON (`content/`) to MariaDB tables prefixed `cms_`. Old app-CMS models/views/controllers stay in place (deleted in a later phase) — nothing in `app/` or `content/` is removed.

Scope of this phase (per user decisions):
- Admin CRUD for: Type, Field, Section, Content, Category, Tag, Menu.
- Media: model + API (`index`, `upload`, `destroy`) — no admin page.
- Public content API (`Api\CmsController`): `show`, `indexByType`, `getBlueprintSchema`.
- `php artisan cms:import-orbit` — one-time + idempotent import of `content/*.json` → MariaDB (types, sections, fields, contents; custom_fields dir is empty; website_settings skipped).
- PublicController/frontend migration and deletion of old CMS are OUT of scope (later phase).

All code follows `AGENTS.md`: `has*` relation prefix, `#[Fillable([...])]`, `protected function casts()`, `field_name()`, `rules()`, policy per model, `$model::getModel()`, `Route::auto`, view `cms::pages.{module}.{action}`, double quotes, 4-space indent, `composer lint`.

## Locked Decisions

1. **Storage**: MariaDB only. Tables: `cms_types`, `cms_sections`, `cms_fields`, `cms_contents`, `cms_categories`, `cms_tags`, `cms_menus`, `cms_media` + pivots `cms_content_category`, `cms_content_tag`. **No `cms_custom_fields` table** — `CustomField` is an alias class extending `Field`.
2. **Content ↔ Category/Tag** use belongsToMany pivots (NOT json `category_ids`/`tag_ids` columns).
3. **Relations renamed to `has*` prefix** per AGENTS.md:
   - Type: `has_contents()`, `has_sections()`, `has_field_groups()` (alias of has_sections)
   - Section: `has_type()` (belongsTo Type via content_type_id)
   - Field: `has_parent()`, `has_children()` (self-referencing parent_id)
   - Content: `has_type()`, `has_author()`, `has_categories()`, `has_tags()`
   - Category: `has_parent()`, `has_children()`, `has_contents()`
   - Tag: `has_contents()`
   - Media: `has_user()`
   - Menu: none
4. **Purity filter for Content tabs** uses relation key `filters.has_type.slug.$eq` (was `filters.type.slug.$eq`).
5. **Routes** (module `routes/web.php`, all behind `['web','auth']` via RouteServiceProvider):
   - `Route::auto('/cms/type', TypeController::class, ['name' => 'cms-type'])`
   - `Route::auto('/cms/field', FieldController::class, ['name' => 'field'])`
   - `Route::auto('/cms/section', SectionController::class, ['name' => 'section'])`
   - `Route::auto('/cms/content', ContentController::class, ['name' => 'content'])`
   - `Route::auto('/cms/category', CategoryController::class, ['name' => 'category'])`
   - `Route::auto('/cms/tag', TagController::class, ['name' => 'tag'])`
   - `Route::auto('/cms/menu', MenuController::class, ['name' => 'menu'])`
   - `Route::get('/cms/content/field-group-html/{id}', [ContentController::class, 'getSectionHtml'])->name('cms.section.html')` (manual — non-standard action)
   - Media API (auth, matches main layout JS which calls `/api/media`):
     `Route::prefix('api/media')->group(fn => [GET /, POST /upload, DELETE /{media}])`
6. **Public API** in module `routes/api.php` (no auth), auto-prefixed `api` + name `api.`:
   - `cms/content/{slug}` → `show`
   - `cms/content-type/{slug}` → `indexByType`
   - `cms/content-type/{slug}/blueprint` → `getBlueprintSchema`
7. **View namespace**: module views under `cms::` (nwidart alias `cms`). Module base `Controller` overrides `template()` to prefix `cms::pages.…` (backtrace-safe, since `App\Concerns\ControllerTrait::template()` relies on `debug_backtrace()[1]`).
8. **Policies**: module policies extend `App\Policies\BasePolicy`; registered via `Gate::policy()` in `CmsServiceProvider::boot()` (module policies aren't auto-discovered).
9. **Permission/menu config**: `config/permision.php` stays empty (permissive, matching current app state). Add "CMS" section to `config/menu.php` sidebar.
10. **Naming**: all labels rely on `module()`/`moduleLabel()` helpers (route action name) — works because auto-route names are `{name}.{method}` (verified: `user.getTable`).

## Baseline state (already done)

- composer.json root `extra.merge-plugin` includes `Modules/*/composer.json`; `composer dump-autoload` run; `Modules\Cms\` autoload active; module enabled (`modules_statuses.json` = `{"Cms": true}`); `php artisan module:list` shows Cms Enabled.
- Uncommitted: `composer.json` (merge-plugin), `.gitignore` (M), `Modules/` (untracked), `modules_statuses.json` (untracked). Commit these at the end of Task 0.
- `App\Models\BaseModel` provides: `DefaultEntity`, `Filterable`, `Sortable`, `OptionTrait`, `getFieldPrimaryAttribute`, `rules()`. Module models extend it.
- Main layout `<x-layouts::app>` already ships `imgBrowser`/`openImageBrowser`/`handleImageDrop`/`handleImageFileSelect`/`imgPickerRemove` wired to `/api/media` (`resources/views/layouts/script.blade.php`) → ported content form works as-is.

## Task 0 — Git hygiene

- Commit current state: composer.json merge-plugin, .gitignore, Modules scaffold, modules_statuses.json.
- Message: `Add composer merge-plugin and scaffold Cms module`.

## Task 1 — Migrations

Create `Modules/Cms/database/migrations/2026_08_20_000001_create_cms_tables.php` — single migration, all 10 tables (nwidart auto-registers module migrations with `php artisan migrate`).

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('type')->default('custom');
            $table->text('description')->nullable();
            $table->json('supports')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('menu_position')->nullable();
            $table->string('menu_icon')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->foreignId('content_type_id')->nullable()->constrained('cms_types')->nullOnDelete();
            $table->json('field_ids')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cms_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('label')->nullable();
            $table->string('type')->default('text');
            $table->json('config')->nullable();
            $table->json('rules')->nullable();
            $table->boolean('is_required')->default(false);
            $table->text('default_value')->nullable();
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('mode')->default('multiple');
            $table->integer('min')->nullable();
            $table->integer('max')->nullable();
            $table->boolean('collapsed')->default(false);
            $table->boolean('sortable')->default(false);
            $table->boolean('cloneable')->default(false);
            $table->json('layouts')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_type_id')->nullable()->constrained('cms_types')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('featured_image')->nullable();
            $table->integer('menu_order')->default(0);
            $table->json('meta')->nullable();
            $table->json('active_sections')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cms_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cms_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('location')->nullable();
            $table->json('items')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cms_media', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('thumbnail_path')->nullable();
            $table->string('alt')->nullable();
            $table->string('title')->nullable();
            $table->string('caption')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cms_content_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('cms_contents')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('cms_categories')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('cms_content_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('cms_contents')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('cms_tags')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_content_tag');
        Schema::dropIfExists('cms_content_category');
        Schema::dropIfExists('cms_media');
        Schema::dropIfExists('cms_menus');
        Schema::dropIfExists('cms_tags');
        Schema::dropIfExists('cms_categories');
        Schema::dropIfExists('cms_contents');
        Schema::dropIfExists('cms_fields');
        Schema::dropIfExists('cms_sections');
        Schema::dropIfExists('cms_types');
    }
};
```

Run `php artisan migrate` → confirm 10 tables. Commit: `Add CMS module MariaDB migrations`.

## Task 2 — Models

All in `Modules/Cms/app/Models/`, extend `App\Models\BaseModel`, use `#[Fillable([...])]` (import `Illuminate\Database\Eloquent\Attributes\Fillable`), casts via `protected function casts(): array`, define `field_name()`, `rules()`, `$filterColumns`, `$sortColumns`, `has*` relations. Port the exact bodies from `ecmsoftware/app/Models/*.php` with the renames listed below.

### 2.1 Type.php

Source: `ecmsoftware/app/Models/Type.php`. Changes:
- `namespace Modules\Cms\Models;`
- `use App\Models\BaseModel;`
- `contents()` → `has_contents()`, `sections()` → `has_sections()`, `fieldGroups()` → `has_field_groups()` (body: `return $this->has_sections();`), `hasContents()` boolean helper stays as `hasContents()` (a method, not relation — keep name to match ecmsoftware usage; it's used nowhere critical).
- Relations import `Illuminate\Database\Eloquent\Relations\HasMany`.
- `booted()` slug auto-generation kept verbatim (uses `static::generateUniqueSlug`).
- rules kept verbatim.

### 2.2 Section.php

Source: `ecmsoftware/app/Models/Section.php`. Changes:
- `type()` → `has_type()` (BelongsTo Type, `content_type_id`).
- `getFieldsAttribute()` accessor: `Field::with('children')` → `Field::with('has_children')`.
- `getJsonSchema()` uses `$this->fields` accessor — unchanged.
- `booted()` sort_order default kept.

### 2.3 Field.php

Source: `ecmsoftware/app/Models/Field.php`. Changes:
- `parent()` → `has_parent()` (BelongsTo Field, `parent_id`), `children()` → `has_children()` (HasMany Field, `parent_id`).
- `getJsonSchema()`: `$this->children` → `$this->has_children`.
- `getTypeOptions()`, `getContainerModes()`, `isContainerType()`, `getLayouts()` kept.
- `field_name()` returns `'label'` (as source).

### 2.4 CustomField.php (new, no migration)

```php
<?php

namespace Modules\Cms\Models;

class CustomField extends Field {}
```

### 2.5 Content.php

Source: `ecmsoftware/app/Models/Content.php`. Changes:
- `type()` → `has_type()`, `author()` → `has_author()`.
- Remove `category_ids`/`tag_ids` from fillable (pivot instead). Add relations:
  ```php
  public function has_categories(): BelongsToMany
  {
      return $this->belongsToMany(Category::class, 'cms_content_category', 'content_id', 'category_id');
  }

  public function has_tags(): BelongsToMany
  {
      return $this->belongsToMany(Tag::class, 'cms_content_tag', 'content_id', 'tag_id');
  }
  ```
- Keep `getMeta`, `getAllMeta`, `scopePublished`, `getIsPublishedAttribute`, `getNormalizedData`, `normalizeContainerMeta`, `addTypeToContainer`, `getBlueprintSchema` — update inside `getNormalizedData`: `$this->type->slug` → `$this->has_type?->slug`; inside `getBlueprintSchema`: `$contentType->sections()` → `$contentType->has_sections()`.
- rules: keep, PLUS `'category_ids' => ['nullable','array'], 'category_ids.*' => ['integer'], 'tag_ids' => ['nullable','array'], 'tag_ids.*' => ['integer']` (validated but not fillable).

### 2.6 Category.php

Source: `ecmsoftware/app/Models/Category.php`. Changes:
- `use SoftDeletes;`
- `parent()` → `has_parent()`, `children()` → `has_children()`, `entries()` → `has_contents()` with explicit pivot: `belongsToMany(Content::class, 'cms_content_category', 'category_id', 'content_id')`.
- rules: `unique:categories,slug` → `unique:cms_categories,slug,{id}`; `exists:categories,id` → `exists:cms_categories,id`.

### 2.7 Tag.php

Source: `ecmsoftware/app/Models/Tag.php`. Changes:
- `use SoftDeletes;`
- `entries()` → `has_contents()`: `belongsToMany(Content::class, 'cms_content_tag', 'tag_id', 'content_id')`.
- rules: `unique:tags,slug` → `unique:cms_tags,slug,{id}`.

### 2.8 Menu.php

Source: `ecmsoftware/app/Models/Menu.php`. Changes: namespace + `use App\Models\BaseModel;` only. Keep `items` cast array, `getItemsCollection()`, `getByLocation()`. `field_name()` = `'name'`.

### 2.9 Media.php

Source: `ecmsoftware/app/Models/Media.php`. Changes:
- `user()` → `has_user()` (BelongsTo User).
- Keep `getUrlAttribute`, `getThumbnailAttribute`, `isImage`, `getHumanSizeAttribute`, `scopeOfType`, `scopeImages`.
- `field_name()` = `'filename'`.

Add `use App\Models\User;` where relations reference User.

Run `php artisan tinker` quick smoke: `Modules\Cms\Models\Type::getModel()`, `Type::getOptions()`. Commit: `Add CMS module models`.

## Task 3 — Policies

Create 8 policies in `Modules/Cms/app/Policies/`:

```php
<?php

namespace Modules\Cms\Policies;

use App\Policies\BasePolicy;

class TypePolicy extends BasePolicy {}
```

Same minimal class for `SectionPolicy`, `FieldPolicy`, `ContentPolicy`, `CategoryPolicy`, `TagPolicy`, `MenuPolicy`, `MediaPolicy` (namespace `Modules\Cms\Policies`). Registered in Task 6. No migration. Commit: `Add CMS module policies`.

## Task 4 — Controllers

### 4.1 Module base `Modules/Cms/app/Http/Controllers/Controller.php`

```php
<?php

namespace Modules\Cms\Http\Controllers;

use App\Concerns\ControllerTrait;

abstract class Controller extends \App\Http\Controllers\Controller
{
    use ControllerTrait;

    protected function template($file = null, $folder = null, $core = false)
    {
        $action = 'table';

        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $frame) {
            if (isset($frame['function']) && preg_match('/^(get|post)/', $frame['function'])) {
                $action = strtolower(preg_replace('/^(get|post)/', '', $frame['function']));
                break;
            }
        }

        if (in_array($action, ['update', 'create'])) {
            $action = 'form';
        }

        if ($file === true) {
            return $this->moduleName();
        }

        if ($file) {
            $action = $file;
        }

        $module = $this->moduleName();
        if ($folder) {
            $module = $folder;
        }

        $path = $core ? 'core.' : 'pages.';

        return 'cms::'.$path.$module.'.'.$action;
    }

    protected function moduleName(): string
    {
        return strtolower(str_replace('Controller', '', class_basename(get_class($this))));
    }
}
```

> Why not `parent::template()`: the trait reads `debug_backtrace()[1]['function']`; a wrapper layer would resolve to `template`, breaking action detection. The scan finds the first `get*`/`post*` frame (the controller action).

### 4.2 TypeController.php

Port `ecmsoftware/.../Cms/TypeController.php`. `namespace Modules\Cms\Http\Controllers;`, imports `Modules\Cms\Models\Section`/`Type`. Extends the module base Controller (do NOT `use ControllerTrait` — inherited). Keep `share()` (`typeOptions`, `supportsOptions`, `sectionCounts`) and `getCreate`.

### 4.3 SectionController.php

Port from source. Imports `Modules\Cms\Models\Field`/`Section`/`Type`. Keep `share()` (`contentTypes`, `allFields` = top-level fields `whereNull('parent_id')->orderBy('sort_order')`) and `getCreate`.

### 4.4 FieldController.php

Port from source with relation renames and namespace changes:
- `use Modules\Cms\Models\Field;`
- `$this->model = new Field;`
- `saveField()`: `$field->children()` → `$field->has_children()`, `$c->children()` → `$c->has_children()`.
- `syncChildren()`: `$parent->children()` → `$parent->has_children()`, `$remove->children()` → `$remove->has_children()`.
- `getUpdate()`: `$model->children()` → `$model->has_children()`.
- Keep `share()` (typeOptions, modeOptions, types), `getData()` (`whereNull('parent_id')->filter()->sort()`), `postCreate`/`postUpdate`/`saveField`/`syncChildren`/`buildChildTree`.
- Validate with `(new Field)->rules()` as source.

### 4.5 ContentController.php

Port from source with renames, plus pivot sync. Structure:

```php
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

    public function postCreate(GeneralRequest $request)
    {
        $data = $request->validate($this->model->rules());

        try {
            $content = $this->model->create($data);
            $this->syncRelations($content, $request);

            return $this->response(['status' => true, 'message' => TOAST_SUCCESS, 'data' => $content]);
        } catch (\Throwable $th) {
            return $this->response(['status' => false, 'message' => TOAST_FAILED, 'data' => $th->getMessage()]);
        }
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $content = $this->model->findOrFail($id);
        $data = $request->validate($this->model->rules());

        try {
            $content->update($data);
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
}
```

> Note: `preview()` from source is dropped (no route maps it; parity is not needed).

### 4.6 CategoryController.php / TagController.php / MenuController.php

Port from source (constructor `$this->model = $model::getModel();`), extend module base Controller, imports module models. No other changes.

### 4.7 Api/CmsController.php

Port `ecmsoftware/.../Api/CmsController.php`:
- `namespace Modules\Cms\Http\Controllers\Api;`
- imports `Modules\Cms\Models\Content` / `Type`.
- `show(Content $content)` → `$content->getNormalizedData()`.
- `indexByType(string $slug)`: `$type->contents()` → `$type->has_contents()->published()->get()->map(fn ($entry) => $entry->getNormalizedData())`.
- `getBlueprintSchema(string $slug)`: `$type->sections()` → `$type->has_sections()->where('is_active', true)->get()`.

### 4.8 Api/MediaController.php

Port `ecmsoftware/.../Api/MediaController.php` verbatim, replacing `App\Models\Media` → `Modules\Cms\Models\Media` and namespace. Keep `index`, `upload`, `destroy`, `createThumbnail`.

Commit: `Add CMS module controllers`.

## Task 5 — Views (port from ecmsoftware)

All module views live under `Modules/Cms/resources/views/`. Source trees:

- `ecmsoftware/resources/views/pages/{type,section,field,category,tag,menu}/*` (form+table each)
- `ecmsoftware/resources/views/pages/content/{form,table,partials/section-card}.blade.php`
- `ecmsoftware/resources/views/pages/contententry/partials/{basic-field,container-field}.blade.php`

Destination:

```
Modules/Cms/resources/views/pages/
├── type/{form,table}.blade.php
├── section/{form,table}.blade.php
├── field/{form,table}.blade.php
├── content/{form,table}.blade.php
├── content/partials/{section-card,basic-field,container-field}.blade.php
├── category/{form,table}.blade.php
├── tag/{form,table}.blade.php
└── menu/{form,table}.blade.php
```

Copy files first, then apply the transforms below per file.

### Global transforms (all files)

1. No namespace/tag changes to `<x-layouts::app>`, `<x-card>`, `<x-action>`, `<x-table>`, `<x-filter>`, `<x-pagination>`, `<x-button>` — these are the main app components already used by main app views.
2. `<?php /** @var App\Models\X $model */ ?>` docblocks → `Modules\Cms\Models\X`.

### 5.1 type/form.blade.php

Copy verbatim. OK as-is (uses `$typeOptions`, `$supportsOptions`, `$model->supports`).

### 5.2 type/table.blade.php

Copy verbatim. OK as-is (uses `$model::$sortColumns`, `$sectionCounts`).

### 5.3 section/form.blade.php

Copy, then:
- `description` line: `<x-input col="6" name="description" type="textarea" />` → `<x-textarea col="6" name="description" />` (main `<x-input>` renders `<input type="{{ $type }}">` only; it cannot render textarea).

### 5.4 section/table.blade.php

Copy verbatim. OK as-is.

### 5.5 field/form.blade.php

Copy, then:
- `$typeOptions = \App\Models\Field::getTypeOptions();` → `$typeOptions = \Modules\Cms\Models\Field::getTypeOptions();`.
- Keep all JS (container child fields builder) — uses `$existingChildrenJson`, `@json($typeOptions)`.
- The `x-input type="number"` for sort_order is fine.

### 5.6 field/table.blade.php

Copy verbatim. OK as-is.

### 5.7 category/form.blade.php

Copy, then:
- `description`: `<x-input col="6" name="description" type="textarea" />` → `<x-textarea col="6" name="description" />`.

### 5.8 category/table.blade.php

Copy, then:
- `$table->parent->name` → `$table->has_parent->name` (both desktop `<td>` and mobile row).

### 5.9 tag/form.blade.php + 5.10 tag/table.blade.php

Copy verbatim. OK as-is.

### 5.11 menu/form.blade.php

Copy, then rewrite styling (source used DaisyUI classes `input input-bordered`, `select select-bordered`, `btn btn-*`, `badge`):
- `is_active`: `<x-input col="6" name="is_active" type="true_false" />` → `<x-select col="6" name="is_active" :options="['1' => 'Active', '0' => 'Inactive']" />`.
- Input class string in PHP-rendered rows and JS template strings:
  `input input-bordered input-sm flex-1 min-w-[200px]` → `w-full h-10 px-3 bg-white border border-outline-variant rounded-lg text-sm flex-1 min-w-[200px]`
  (same for icon/sort fields with their own min-w).
- Select `select select-bordered select-sm` → `w-full h-10 px-3 bg-white border border-outline-variant rounded-lg text-sm appearance-none flex-1 min-w-[140px]`.
- Buttons `btn btn-sm btn-error btn-outline` / `btn btn-xs btn-ghost text-primary` / `btn btn-sm btn-primary` → main-app style e.g. `inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 border border-blue-200` (red variant for delete: `text-red-600 bg-red-50 hover:bg-red-100 border-red-200`).
- `bg-base-200`/`bg-base-100`/`border-base-300` → `bg-gray-50`/`bg-white`/`border-gray-200`.
- Badge in menu/table: `badge badge-success`/`badge badge-danger` → same span pills used in section table (green/red rounded-full).
- `menuItemIndex` JS stays.

### 5.12 menu/table.blade.php

Copy, then swap `badge badge-success`/`badge badge-danger` → green/red rounded-full span pills (same as section table).

### 5.13 content/table.blade.php

Copy, then:
- Tab query filter key `filters['type']['slug']` → `filters['has_type']['slug']` (the `$tabQuery['filters']['type']['slug'] = ['$eq' => ...]` line).
- `$activeTypeSlug` already provided by share.

### 5.14 content/form.blade.php (1000 lines — port nearly verbatim)

Copy, then apply:
1. `@include('pages.contententry.partials.container-field', ...)` → `@include('cms::pages.content.partials.container-field', ...)` (3 occurrences).
2. `@include('pages.contententry.partials.basic-field', ...)` → `@include('cms::pages.content.partials.basic-field', ...)` (2 occurrences).
3. Fetch URL in `addGroupSection`: `fetch('/cms/content-entry/field-group-html/' + groupId, ...)` → `fetch('/cms/content/field-group-html/' + groupId, ...)`.
4. `$selectedCategories = old('category_ids', $model->category_ids ?? []);` → `$selectedCategories = old('category_ids', $model->has_categories->pluck('id')->all());`
5. `$selectedTags = old('tag_ids', $model->tag_ids ?? []);` → `$selectedTags = old('tag_ids', $model->has_tags->pluck('id')->all());`
6. Children-tree builder: rename the property assigned so partials stay consistent with the `has_children` relation:
   - `$child->children = $buildFieldChildren($k['id']);` → `$child->has_children = $buildFieldChildren($k['id']);`
   - `$field->children = $buildFieldChildren($field->id);` → `$field->has_children = $buildFieldChildren($field->id);`
7. Keep the whole TinyMCE block, image picker markup, `@push('scripts')` tinymce loader, Sortable CDN, all JS functions. The picker globals (`openImageBrowser`, `handleImageDrop`, `handleImageFileSelect`, `imgPickerRemove`, `imgBrowser`) come from the main layout script — do NOT redefine them.
8. Docblock `@var App\Models\Content` → `Modules\Cms\Models\Content`.

### 5.15 content/partials/section-card.blade.php

Copy from `ecmsoftware/.../pages/content/partials/section-card.blade.php`, then:
- includes → `cms::pages.content.partials.container-field` / `cms::pages.content.partials.basic-field`.

### 5.16 content/partials/basic-field.blade.php

Copy from `ecmsoftware/.../pages/contententry/partials/basic-field.blade.php`. No changes needed (uses `$field->type`, `$field->config`, `$field->is_required`, `$field->default_value`, and layout-global picker JS). Keep as-is.

### 5.17 content/partials/container-field.blade.php

Copy from `ecmsoftware/.../pages/contententry/partials/container-field.blade.php`, then:
- `$children = $field->children ?? collect();` → `$children = $field->has_children ?? collect();`
- `route('custom-field.getUpdate', $field->id)` → `route('field.getUpdate', $field->id)` (single-mode "Add child fields" link).
- includes → `cms::pages.content.partials.container-field` / `cms::pages.content.partials.basic-field` (4 occurrences).
- Uses `\renderFieldInput()` global helper (exists in `function/Global.php:477`) — keep.

Delete the now-unused module scaffold view `Modules/Cms/resources/views/index.blade.php` (or leave; it's harmless — but remove to keep module clean) and the sample `CmsController.php` if still present.

Run `composer lint`. Commit: `Add CMS module views`.

## Task 6 — Routes + Providers

### 6.1 Modules/Cms/routes/web.php (replace scaffold)

```php
<?php

use Modules\Cms\Http\Controllers\Api\MediaController;
use Modules\Cms\Http\Controllers\CategoryController;
use Modules\Cms\Http\Controllers\ContentController;
use Modules\Cms\Http\Controllers\FieldController;
use Modules\Cms\Http\Controllers\MenuController;
use Modules\Cms\Http\Controllers\SectionController;
use Modules\Cms\Http\Controllers\TagController;
use Modules\Cms\Http\Controllers\TypeController;

Route::auto('/cms/type', TypeController::class, ['name' => 'cms-type']);
Route::auto('/cms/field', FieldController::class, ['name' => 'field']);
Route::auto('/cms/section', SectionController::class, ['name' => 'section']);
Route::auto('/cms/content', ContentController::class, ['name' => 'content']);
Route::auto('/cms/category', CategoryController::class, ['name' => 'category']);
Route::auto('/cms/tag', TagController::class, ['name' => 'tag']);
Route::auto('/cms/menu', MenuController::class, ['name' => 'menu']);

Route::get('/cms/content/field-group-html/{id}', [ContentController::class, 'getSectionHtml'])->name('cms.section.html');

Route::prefix('api/media')->group(function () {
    Route::get('/', [MediaController::class, 'index']);
    Route::post('/upload', [MediaController::class, 'upload']);
    Route::delete('/{media}', [MediaController::class, 'destroy']);
});
```

(`Route::auto` uses `Buki\AutoRoute\Facades\Route` — the module file already imports Laravel `Route`; the macro is registered app-wide so plain `Route::auto(...)` resolves. Verify with `php artisan route:list`.)

### 6.2 Modules/Cms/routes/api.php (replace scaffold)

```php
<?php

use Modules\Cms\Http\Controllers\Api\CmsController;

Route::prefix('cms')->group(function () {
    Route::get('/content/{slug}', [CmsController::class, 'show']);
    Route::get('/content-type/{slug}', [CmsController::class, 'indexByType']);
    Route::get('/content-type/{slug}/blueprint', [CmsController::class, 'getBlueprintSchema']);
});
```

RouteServiceProvider already prefixes `api` + `api.` name.

### 6.3 Modules/Cms/app/Providers/RouteServiceProvider.php

Change `mapWebRoutes` middleware to include auth:

```php
protected function mapWebRoutes(): void
{
    Route::middleware(['web', 'auth'])->group(module_path($this->name, '/routes/web.php'));
}
```

### 6.4 Modules/Cms/app/Providers/CmsServiceProvider.php

- Add command registration: `protected array $commands = [\Modules\Cms\Console\CmsImportOrbitCommand::class];`
- Register policies in `boot()`:

```php
use Illuminate\Support\Facades\Gate;
use Modules\Cms\Models\Category;
use Modules\Cms\Models\Content;
use Modules\Cms\Models\Field;
use Modules\Cms\Models\Media;
use Modules\Cms\Models\Menu;
use Modules\Cms\Models\Section;
use Modules\Cms\Models\Tag;
use Modules\Cms\Models\Type;
use Modules\Cms\Policies\CategoryPolicy;
use Modules\Cms\Policies\ContentPolicy;
use Modules\Cms\Policies\FieldPolicy;
use Modules\Cms\Policies\MediaPolicy;
use Modules\Cms\Policies\MenuPolicy;
use Modules\Cms\Policies\SectionPolicy;
use Modules\Cms\Policies\TagPolicy;
use Modules\Cms\Policies\TypePolicy;

public function boot(): void
{
    parent::boot();

    Gate::policy(Type::class, TypePolicy::class);
    Gate::policy(Section::class, SectionPolicy::class);
    Gate::policy(Field::class, FieldPolicy::class);
    Gate::policy(CustomField::class, FieldPolicy::class);
    Gate::policy(Content::class, ContentPolicy::class);
    Gate::policy(Category::class, CategoryPolicy::class);
    Gate::policy(Tag::class, TagPolicy::class);
    Gate::policy(Menu::class, MenuPolicy::class);
    Gate::policy(Media::class, MediaPolicy::class);
}
```

> `CustomField` extends `Field` — its policy lookup falls through to `Field`'s policy anyway (Eloquent walks the parent class), the explicit line is just for clarity.

Run `php artisan optimize:clear && php artisan route:list` and confirm all CMS + media routes exist and are named. Commit: `Wire CMS module routes, auth, and policies`.

## Task 7 — Config

### 7.1 config/menu.php (root)

Add a CMS section to `sidebar` (after "Master Data"):

```php
[
    'label' => 'CMS',
    'items' => [
        ['route' => 'cms-type.getTable', 'icon' => 'category', 'label' => 'Types', 'match' => ['cms-type.*']],
        ['route' => 'field.getTable', 'icon' => 'input', 'label' => 'Fields', 'match' => ['field.*']],
        ['route' => 'section.getTable', 'icon' => 'view_agenda', 'label' => 'Sections', 'match' => ['section.*']],
        ['route' => 'content.getTable', 'icon' => 'article', 'label' => 'Content', 'match' => ['content.*']],
        ['route' => 'category.getTable', 'icon' => 'folder', 'label' => 'Categories', 'match' => ['category.*']],
        ['route' => 'tag.getTable', 'icon' => 'sell', 'label' => 'Tags', 'match' => ['tag.*']],
        ['route' => 'menu.getTable', 'icon' => 'menu', 'label' => 'Menus', 'match' => ['menu.*']],
    ],
],
```

This also powers `moduleLabel()` (matches `{module}.*`).

### 7.2 config/permision.php (root)

Leave `$restrict = [];` (permissive, current app behavior). Note in a `//` comment that CMS module permissions can be restricted here later.

Commit: `Add CMS menu and permission config`.

## Task 8 — Import command

`Modules/Cms/app/Console/CmsImportOrbitCommand.php`:

```php
<?php

namespace Modules\Cms\Console;

use Illuminate\Console\Command;
use Modules\Cms\Models\Content;
use Modules\Cms\Models\Field;
use Modules\Cms\Models\Section;
use Modules\Cms\Models\Type;

class CmsImportOrbitCommand extends Command
{
    protected $signature = 'cms:import-orbit';

    protected $description = 'Import CMS data from content/*.json (Orbit) into MariaDB (idempotent)';

    public function handle(): int
    {
        $this->info('Importing CMS Orbit data into MariaDB...');

        $this->importFrom('types', fn (array $d) => Type::updateOrCreate(
            ['id' => $d['id']],
            $this->only($d, ['name', 'slug', 'type', 'description', 'supports', 'is_active', 'menu_position', 'menu_icon'])
        ));

        $this->importFrom('sections', fn (array $d) => Section::updateOrCreate(
            ['id' => $d['id']],
            $this->only($d, ['name', 'description', 'icon', 'content_type_id', 'field_ids', 'sort_order', 'is_active'])
        ));

        $this->importFrom('fields', fn (array $d) => Field::updateOrCreate(
            ['id' => $d['id']],
            $this->only($d, ['name', 'label', 'type', 'config', 'rules', 'is_required', 'default_value', 'sort_order', 'parent_id', 'mode', 'min', 'max', 'collapsed', 'sortable', 'cloneable', 'layouts', 'type_id'])
        ));

        // Legacy custom_fields dir (currently empty) maps to the same Field table.
        $this->importFrom('custom_fields', fn (array $d) => Field::updateOrCreate(
            ['id' => $d['id']],
            $this->only($d, ['name', 'label', 'type', 'config', 'rules', 'is_required', 'default_value', 'sort_order', 'parent_id', 'mode', 'min', 'max', 'collapsed', 'sortable', 'cloneable', 'layouts', 'type_id'])
        ));

        $this->importFrom('contents', function (array $d) {
            $content = Content::updateOrCreate(
                ['id' => $d['id']],
                $this->only($d, ['content_type_id', 'title', 'slug', 'content', 'excerpt', 'status', 'published_at', 'author_id', 'featured_image', 'menu_order', 'meta', 'active_sections'])
            );

            if (! empty($d['category_ids'])) {
                $content->has_categories()->sync($d['category_ids']);
            }
            if (! empty($d['tag_ids'])) {
                $content->has_tags()->sync($d['tag_ids']);
            }

            return $content;
        });

        $this->info('Done.');
        return self::SUCCESS;
    }

    private function importFrom(string $dir, callable $mapper): void
    {
        $path = base_path('content/'.$dir);
        if (! is_dir($path)) {
            $this->warn("content/{$dir} not found, skipped.");

            return;
        }

        $count = 0;
        foreach (glob($path.'/*.json') as $file) {
            $data = json_decode((string) file_get_contents($file), true);
            if (! is_array($data)) {
                continue;
            }
            $mapper($data);
            $count++;
        }

        $this->info("  - {$dir}: {$count} record(s)");
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
```

Notes:
- `website_settings` intentionally skipped (separate concern, not in CMS admin menu).
- Idempotent via `updateOrCreate` — safe to run twice; counts stay stable.
- JSON arrays (`supports`, `field_ids`, `meta`, `active_sections`, `config`, `rules`, `layouts`) are stored as-is; the model JSON casts handle encode/decode.

Run `php artisan cms:import-orbit` twice; expect ~104 records each for types/sections/fields/contents, 0 for custom_fields, no errors on second run. Commit: `Add cms:import-orbit command`.

## Task 9 — Verification

1. `php artisan migrate` → 10 `cms_*` tables.
2. `php artisan cms:import-orbit` (twice) → stable counts, no errors.
3. `php artisan module:list` → Cms Enabled.
4. `php artisan route:list` → all `cms-*`, `field.*`, `section.*`, `content.*`, `category.*`, `tag.*`, `menu.*`, `api.media.*`, `api.cms.*`, `cms.section.html` present.
5. DB spot-check via envkit MCP `databases_overview` / `laravel-boost` `database-query`:
   - `SELECT COUNT(*) FROM cms_types;` = 104, sections = 104, fields = 104, contents = 104.
6. `composer lint` (Pint) passes on module + touched files.
7. `php artisan test` → existing suite still green.
8. Manual smoke (needs login): load `/cms/content/table` (tabs render), open an existing content edit (sections render, category/tag selects populate), `/cms/field/create` (container child builder), `/cms/menu/form` (menu builder), media upload via `/api/media/upload`.
9. Confirm main app still boots: `/dashboard`, `/user/table`.

## Out of Scope (later phase)

- PublicController/frontend migration to consume `api.cms.*`.
- Deleting old `app/Models` Orbit CMS models + `content/` data + `ecmsoftware/` folder.
- WebsiteSettings / WebsiteSettingController.
- Restricting permissions in `config/permision.php`.