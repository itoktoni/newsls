# Desain: Migrasi CMS ke Modules/Cms (MariaDB)

> Tanggal: 2026-08-20
> Status: Approved oleh user (desain umum)

## Ringkasan

Pindahkan seluruh fungsionalitas CMS dari aplikasi `ecmsoftware` (referensi) ke
modul `Modules/Cms` pada proyek ini. Storage berganti dari **Orbit flat-file
(JSON)** ke **MariaDB langsung**. App utama yang lama (model Orbit + data
`content/`) dibiarkan dulu; PublicController dan penghapusan CMS lama dilakukan
pada fase terpisah setelah modul terbukti jalan.

## Keputusan kunci

1. **Modular**: seluruh kode CMS hidup di `Modules/Cms` (nwidart), namespace `Modules\Cms`.
2. **Storage**: MariaDB (Eloquent biasa), bukan Orbit. Module membawa migration sendiri.
3. **Data lama**: JSON `content/` diimpor ke MariaDB via command `cms:import-orbit`
   (bukan dibuang). Tidak ada folder category/tag/menu di JSON → tabel itu mulai kosong.
4. **Scope admin** (dari `config/menu.php` ecmsoftware): Types, Fields, Sections,
   Content, Categories, Tags, Menus — 7 CRUD.
5. **Media**: model `Media` + `Api\MediaController` (list/upload/destroy) +
   `SecureImageUploadService` ikut masuk modul (tabel `cms_media`).
   Tidak ada halaman admin media di ecmsoftware → tetap API seperti sumbernya.
5. **Frontend publik** (PublicController / blog / documentation / Api\CmsController):
   migrasi menyusul di fase terpisah. `Api\CmsController` tetap ikut dibangun di modul
   karena bagian dari CMS module.
6. **App lama tidak dihapus** dulu (pilihan user: "migrasi dulu, hapus belakangan").

## Struktur modul

```
Modules/Cms/
├── module.json / config/config.php
├── database/migrations/            # 9 tabel CMS
├── app/
│   ├── Models/        Type, Section, Field, Content, Category, Tag, Menu, CustomField
│   ├── Http/Controllers/  TypeController, FieldController, SectionController,
│   │                     ContentController, CategoryController, TagController, MenuController
│   ├── Policies/       (extends App\Policies\BasePolicy)
│   ├── Services/       ContentEntryExtractor, ContainerRenderer, CmsHelper
│   └── Providers/      CmsServiceProvider (registrasi policy + import command)
├── routes/web.php  (auth + Route::auto)  ·  routes/api.php (Api\CmsController)
└── resources/views/pages/{type,field,section,content,category,tag,menu}/
```

## Database (migration)

Prefix tabel `cms_`. Skema mengikuti model MySQL di `ecmsoftware`.

| Tabel | Kolom kunci |
|---|---|
| `cms_types` | id, name, slug, type, description, supports(json), is_active, menu_position, menu_icon, timestamps |
| `cms_sections` | id, name, description, icon, content_type_id(FK cms_types), field_ids(json), sort_order, is_active, timestamps |
| `cms_fields` | id, name, label, type, config(json), rules(json), is_required, default_value, sort_order, parent_id(FK cms_fields), mode, min, max, collapsed, sortable, cloneable, layouts(json), type_id, timestamps |
| `cms_contents` | id, content_type_id(FK cms_types), title, slug, content(longtext), excerpt, status, published_at, author_id, featured_image, menu_order, meta(json), active_sections(json), timestamps |
| `cms_categories` | id, name, slug, description, parent_id(FK cms_categories), sort_order, deleted_at, timestamps |
| `cms_tags` | id, name, slug, deleted_at, timestamps |
| `cms_menus` | id, name, slug, location, items(json), is_active, sort_order, deleted_at, timestamps |
| `cms_media` | id, filename, original_filename, mime_type, size, disk, path, thumbnail_path, alt, title, caption, width, height, user_id, meta(json), deleted_at, timestamps |
| `cms_content_category` | content_id, category_id (pivot) |
| `cms_content_tag` | content_id, tag_id (pivot) |

`cms_custom_fields` bukan tabel terpisah — `CustomField extends Field` memakai
tabel `cms_fields` yang sama (model alias, tanpa migration).

Keputusan: relasi Content↔Category dan Content↔Tag memakai **pivot table**
(`belongsToMany`), bukan kolom JSON `category_ids`/`tag_ids` — memanfaatkan DB
sungguhan dan sesuai catatan AGENTS.md ("re-add when migrating to real DB").

## Model

- Eloquent biasa, extends `App\Models\BaseModel`, namespace `Modules\Cms\Models`.
- Konvensi AGENTS.md: `#[Fillable([...])]`, `rules()`, `field_name()`,
  `$filterColumns` / `$sortColumns`, relasi ber-prefix `has`.
- Relasi:
  - `Type hasMany Section` (`content_type_id`), `hasMany Content`
  - `Section belongsTo Type`, accessor `fields` (urut berdasar `field_ids`)
  - `Field belongsTo parent`, `hasMany children`
  - `Content belongsTo Type`, `belongsTo User(author)`, `belongsToMany Category/Tag`
  - `Category belongsTo parent`, `hasMany children`, `belongsToMany Content`
  - `Tag belongsToMany Content`
  - `Menu`: casts `items` array, `getByLocation(string)`
- Logic bantu dari ecmsoftware dipertahankan: `Type::getTypeOptions()`,
  `getSupportsOptions()`, `generateUniqueSlug()`, `Section::getJsonSchema()`,
  `Field::getJsonSchema()` / `isContainerType()` / `getLayouts()`.

## Controller + Policy + View

- 7 controller CRUD pakai `App\Concerns\ControllerTrait` (`getCreate`,
  `getUpdate`, `getTable`, `postCreate`, `postUpdate`, `getDelete`, `postDelete`).
- `ContentController`: override `share()` (contentTypes, allSections, allFields,
  categories, tags, typeTabs), override `getData()` (filter default type
  `homepage`), `getSectionHtml($id)` endpoint.
- `TypeController`: `share()` dengan typeOptions, supportsOptions, sectionCounts.
- 7 policy `extends App\Policies\BasePolicy` (di `Modules\Cms\Policies`),
  didaftarkan via `Gate::policy()` di `CmsServiceProvider::boot()`.
- View di `Modules/Cms/resources/views/pages/...`, memakai komponen proyek
  (`<x-layouts::app>`, `<x-card>`, `<x-input>`, `<x-select>`, `<x-action>`,
  `<x-table>`, `<x-pagination>`). File sumber dari ecmsoftware:
  `pages/{type,section,field,content,category,tag,menu}` + `pages/content/partials/*`.

## Route / Config / Menu / Permission

- `Modules/Cms/routes/web.php`: `Route::auto('/cms/type', ...)` dst,
  dibungkus `auth` middleware (RouteServiceProvider modul diadaptasi agar web
  routes memakai middleware `web` + `auth`).
- `Modules/Cms/routes/api.php`: `Api\CmsController` → `show`, `indexByType`,
  `getBlueprintSchema`, prefix `api.cms`.
- `config/menu.php` (root): tambah section "CMS" dengan 7 item
  (`cms-type.getTable`, `field.getTable`, `section.getTable`, `content.getTable`,
  `category.getTable`, `tag.getTable`, `menu.getTable`).
- `config/permision.php`: tambah 7 modul ke daftar aksi per role.

## Import command `php artisan cms:import-orbit`

- Membaca `content/` JSON: `types`, `sections`, `fields`, `contents`, `custom_fields`.
- Insert ke tabel `cms_*` dengan **ID dipertahankan** agar relasi
  (`content_type_id`, `field_ids`, `parent_id`) tetap valid.
- Pivot Content↔Category/Tag tidak diisi dari JSON (tidak ada datanya).
- **Idempotent**: insert by primary key; kalau id sudah ada → skip (atau update
  sesuai flag). Aman dijalankan 2x.

## Verifikasi

1. `php artisan module:list` → Cms Enabled.
2. `php artisan migrate` → 9 migration sukses (termasuk pivot).
3. `php artisan cms:import-orbit` → data JSON masuk DB; dijalankan ulang tanpa error.
4. Login admin → menu CMS muncul → semua 7 halaman CRUD bisa buka, create/update/delete jalan.
5. `composer lint` / `php artisan test` tidak regresi pada bagian non-CMS.

## Fase eksekusi

1. Migration + model
2. Controller + policy
3. View
4. Route / config / menu / permission
5. Import command
6. Verifikasi + test

Fase lanjutan (di luar spec ini): migrasi PublicController & frontend publik ke
module, lalu penghapusan CMS lama (app/models Orbit + `content/`).