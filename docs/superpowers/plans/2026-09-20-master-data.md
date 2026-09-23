# Master Data Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Port 6 master modules (Rs, Ruangan, Kategori, JenisBahan, Supplier, JenisLinen) from `andalan/` to the new stack, reusing DB `bka` with zero migrations.

**Architecture:** Each module = Property Entity + Model (BaseModel) + Policy + Controller (ControllerTrait) + 2 Blade views (users canon) + 1 Route::auto line. One new enum (RsStatusEnum). Verification via Pest model-mapping tests + route:list.

**Tech Stack:** Laravel 13, PHP 8.3, bensampo/laravel-enum, kirschbaum PowerJoins, izniburak auto-routes, Pest.

## Global Constraints

- PHP ^8.3, Laravel ^13.7, Livewire ^4.1.
- Reuse DB `bka` 100 percent. NO migration files for master tables.
- Double quotes for strings, 4-space indent, declare return types on all methods.
- `protected $fillable`, never `#[Fillable]`.
- Casts only in `protected function casts(): array`.
- Relationship methods prefixed `has` (hasRuangan, hasCategory, hasRs).
- Views follow `resources/views/pages/users/table.blade.php` + `form.blade.php` exactly.
- One Policy per model, `class XPolicy extends BasePolicy {}` with no extra code.
- CRITICAL: new Entity traits must NOT define `field_primary`, `getFieldPrimaryAttribute`, `field_name`, or `getFieldNameAttribute` — those come from `DefaultEntity` via `BaseModel`. Redefining them causes a PHP trait collision fatal. The model class itself defines `field_name()`.

---

### Task 1: RsStatusEnum

**Files:**
- Create: `app/Enums/RsStatusEnum.php`
- Test: `tests/Feature/MasterDataTest.php` (created in this task, extended later)

**Interfaces:**
- Consumes: `App\Concerns\EnumTrait` (existing)
- Produces: `RsStatusEnum::getOptions()` returning `["FREE" => "Free", "DEDICATED" => "Dedicated"]` for the Rs form dropdown

- [ ] **Step 1: Create the enum file `app/Enums/RsStatusEnum.php`**

```php
<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class RsStatusEnum extends Enum
{
    use EnumTrait;

    const FREE = "FREE";

    const DEDICATED = "DEDICATED";

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::FREE => "Free",
            self::DEDICATED => "Dedicated",
            default => parent::getDescription($value),
        };
    }
}
```

- [ ] **Step 2: Create `tests/Feature/MasterDataTest.php` with the enum test**

```php
<?php

use App\Enums\RsStatusEnum;

it("exposes rs status options", function () {
    expect(RsStatusEnum::getOptions())->toBe([
        "FREE" => "Free",
        "DEDICATED" => "Dedicated",
    ]);
});
```

- [ ] **Step 3: Run the test**

Run: `php artisan test tests/Feature/MasterDataTest.php`
Expected: PASS (1 test)

---

### Task 2: Rs module (exemplar — follow this shape for Tasks 3-7)

**Files:**
- Create: `app/Properties/RsEntity.php`
- Create: `app/Models/Rs.php`
- Create: `app/Policies/RsPolicy.php`
- Create: `app/Http/Controllers/RsController.php`
- Create: `resources/views/pages/rs/table.blade.php`
- Create: `resources/views/pages/rs/form.blade.php`
- Modify: `routes/web.php` (add one Route::auto line inside the auth group)
- Test: `tests/Feature/MasterDataTest.php` (append)

**Interfaces:**
- Consumes: `RsStatusEnum::getOptions()` from Task 1
- Produces: `Rs::field_name()` = `"rs_nama"`, routes `rs.getTable`, `rs.getCreate`, views `pages.rs.table`, `pages.rs.form`

- [ ] **Step 1: Create `app/Properties/RsEntity.php` (column methods only, no primary/name)**

```php
<?php

namespace App\Properties;

trait RsEntity
{
    public static function field_description()
    {
        return "rs_deskripsi";
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{static::field_description()};
    }

    public static function field_alamat()
    {
        return "rs_alamat";
    }

    public function getFieldAlamatAttribute()
    {
        return $this->{static::field_alamat()};
    }

    public static function field_harga_cuci()
    {
        return "rs_harga_cuci";
    }

    public function getFieldHargaCuciAttribute()
    {
        return $this->{static::field_harga_cuci()};
    }

    public static function field_harga_sewa()
    {
        return "rs_harga_sewa";
    }

    public function getFieldHargaSewaAttribute()
    {
        return $this->{static::field_harga_sewa()};
    }

    public static function field_code()
    {
        return "rs_code";
    }

    public function getFieldCodeAttribute()
    {
        return $this->{static::field_code()};
    }

    public static function field_status()
    {
        return "rs_status";
    }

    public function getFieldStatusAttribute()
    {
        return $this->{static::field_status()};
    }
}
```

- [ ] **Step 2: Create `app/Models/Rs.php`**

```php
<?php

namespace App\Models;

use App\Properties\RsEntity;

class Rs extends BaseModel
{
    use RsEntity;

    protected $table = "rs";

    protected $primaryKey = "rs_id";

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        "rs_nama",
        "rs_alamat",
        "rs_deskripsi",
        "rs_harga_cuci",
        "rs_harga_sewa",
        "rs_aktif",
        "rs_code",
        "rs_status",
        "rs_logo",
    ];

    public static $filterColumns = ["rs_nama", "rs_code", "rs_status"];

    public static $sortColumns = ["rs_nama", "rs_code", "rs_alamat"];

    protected function casts(): array
    {
        return [
            "rs_id" => "integer",
            "rs_harga_cuci" => "integer",
            "rs_harga_sewa" => "integer",
        ];
    }

    public static function field_name(): string
    {
        return "rs_nama";
    }

    public function rules(): array
    {
        return [
            "rs_nama" => "required|string|max:255",
            "rs_alamat" => "nullable|string",
            "rs_deskripsi" => "nullable|string",
            "rs_harga_cuci" => "nullable|integer|min:0",
            "rs_harga_sewa" => "nullable|integer|min:0",
            "rs_aktif" => "nullable|integer",
            "rs_code" => "nullable|string|max:255",
            "rs_status" => "nullable|string|max:50",
            "rs_logo" => "nullable|string|max:255",
        ];
    }

    public function hasRuangan()
    {
        return $this->belongsToMany(Ruangan::class, "rs_dan_ruangan", "rs_id", "ruangan_id");
    }

    public function hasJenis()
    {
        return $this->belongsToMany(JenisLinen::class, "rs_dan_jenis", "rs_id", "jenis_id")->withPivot(["parstock"]);
    }
}
```

- [ ] **Step 3: Create `app/Policies/RsPolicy.php` and `app/Http/Controllers/RsController.php`**

RsPolicy.php:
```php
<?php

namespace App\Policies;

class RsPolicy extends BasePolicy {}
```

RsController.php:
```php
<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Enums\RsStatusEnum;
use App\Models\Rs;

class RsController extends Controller
{
    use ControllerTrait;

    public function __construct(Rs $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        $default = [
            "model" => $this->model,
            "status" => RsStatusEnum::getOptions(),
        ];

        return array_merge($default, $data);
    }
}
```

- [ ] **Step 4: Create `resources/views/pages/rs/table.blade.php`**

```blade
<?php /** @var App\Models\Rs $table */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => '/dashboard', 'label' => 'Home'], ['url' => '', 'label' => moduleLabel()]]" />
    <div class="content mt-4 lg:mt-0">
        <x-filter :per-page="25" :fields="$fields">
            <x-slot:advanced>
                @foreach ($fields as $key => $advance)
                <x-filter-item :label="$advance" :name="$key"/>
                @endforeach

                <x-button variant="primary" class="btn-block" onclick="applyAdvanced()">Apply</x-button>
                <x-button variant="soft" class="btn-block" onclick="resetAdvanced()">Reset</x-button>
            </x-slot:advanced>
        </x-filter>

        @php
            $currentSort = request('sort.0', '');
            $sortField = str_replace(':desc','',str_replace(':asc','',$currentSort));
            $sortDir = str_contains($currentSort, ':desc') ? 'desc' : 'asc';
        @endphp

        <x-table>
            <x-slot:head>
                <x-table-checkbox :model="$model" onchange="toggleAll(this)" />
                <th>Actions</th>
                @foreach ($model::$sortColumns as $column)
                <x-table-sort field="{{ $column }}" label="{{ formatLabel($column) }}" :sortField="$sortField" :sortDir="$sortDir" />
                @endforeach
            </x-slot:head>

            <x-slot:body>
                @foreach($data as $table)
                <tr>
                    <x-table-row-checkbox :model="$model" :value="$table->field_primary" />
                    <x-table-action :model="$model" :id="$table->field_primary" />
                    @foreach ($model::$sortColumns as $column)
                    <td>{{ $table->$column }}</td>
                    @endforeach
                </tr>
                @endforeach
            </x-slot:body>

            <x-slot:mobile>
                <x-table-mobile-select :model="$model" :total="$data"/>
                <div class="p-3 space-y-3" id="mBody">
                    @foreach($data as $table)
                    <div class="border border-outline-variant rounded-xl p-4 bg-surface-container-lowest shadow-sm" data-id="{{ $table->field_primary }}">
                        <p class="text-sm font-bold text-on-surface truncate mb-3">{{ $table->{ $model::field_name() } }}</p>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            @foreach ($model::$sortColumns as $column)
                            <div>
                                <p class="text-[10px] text-on-surface-variant uppercase tracking-wide mb-0.5">{{ formatLabel($column) }}</p>
                                <p class="text-xs font-medium text-on-surface truncate">{{ $table->$column }}</p>
                            </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-outline-variant/50">
                            <span class="text-[9px] font-mono text-on-surface-variant bg-surface-container px-2 py-0.5 rounded">{{ $table->field_primary }}</span>
                            <div class="flex gap-1" onclick="event.stopPropagation()">
                                <x-table-action :model="$model" :id="$table->field_primary" />
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-slot:mobile>

        </x-table>

        <x-pagination :paginator="$data" />
        <x-action :model="$model" :action="['create', 'delete']"/>

    </div>

    <input type="hidden" class="module" value="{{ Str::beforeLast(request()->route()->uri(), '/') }}">
    <script src="/js/table.js"></script>
    <script>initTable('{{ $sortField }}', '{{ $sortDir }}');</script>
</x-layouts::app>
```

Reuse this exact table file for every module in Tasks 3-7, changing only the `@var` docblock model class.

- [ ] **Step 5: Create `resources/views/pages/rs/form.blade.php`**

```blade
<?php /** @var App\Models\Rs $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="rs_nama" />
                <x-input col="6" name="rs_code" />
                <x-input col="6" name="rs_alamat" />
                <x-select col="6" name="rs_status" :options="$status" />
                <x-input col="6" name="rs_harga_cuci" type="number" />
                <x-input col="6" name="rs_harga_sewa" type="number" />
                <x-textarea col="12" name="rs_deskripsi" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']" />
    </x-form>
</x-layouts::app>
```

- [ ] **Step 6: Add the route and append the Rs test, then verify**

In `routes/web.php`, inside the existing `Route::middleware(["auth", "verified", "access"])->group(...)`, after the `Route::auto("/user", ...)` line, add:

```php
Route::auto("/rs", "RsController", ["name" => "rs"]);
```

Append to `tests/Feature/MasterDataTest.php`:

```php
use App\Models\Rs;

it("maps Rs to the bka table", function () {
    $model = new Rs;

    expect($model->getTable())->toBe("rs")
        ->and($model->getKeyName())->toBe("rs_id")
        ->and(Rs::field_name())->toBe("rs_nama")
        ->and((new Rs)->rules()["rs_nama"])->toContain("required");
});
```

Run: `php artisan test tests/Feature/MasterDataTest.php`
Expected: PASS (2 tests). Then run `php artisan route:list --name=rs` and confirm `rs.getTable`, `rs.getCreate` appear.

---

### Task 3: Ruangan module

**Files:**
- Create: `app/Properties/RuanganEntity.php`, `app/Models/Ruangan.php`, `app/Policies/RuanganPolicy.php`, `app/Http/Controllers/RuanganController.php`, `resources/views/pages/ruangan/table.blade.php`, `resources/views/pages/ruangan/form.blade.php`
- Modify: `routes/web.php`, `tests/Feature/MasterDataTest.php`

**Interfaces:**
- Consumes: table view pattern from Task 2 Step 4 (same file, `@var App\Models\Ruangan`)
- Produces: routes `ruangan.*`, views `pages.ruangan.*`

- [ ] **Step 1: Create `app/Properties/RuanganEntity.php`**

```php
<?php

namespace App\Properties;

trait RuanganEntity
{
    public static function field_description()
    {
        return "ruangan_deskripsi";
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{static::field_description()};
    }

    public static function field_code()
    {
        return "ruangan_code";
    }

    public function getFieldCodeAttribute()
    {
        return $this->{static::field_code()};
    }
}
```

- [ ] **Step 2: Create `app/Models/Ruangan.php`**

```php
<?php

namespace App\Models;

use App\Properties\RuanganEntity;

class Ruangan extends BaseModel
{
    use RuanganEntity;

    protected $table = "ruangan";

    protected $primaryKey = "ruangan_id";

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        "ruangan_nama",
        "ruangan_deskripsi",
        "ruangan_code",
    ];

    public static $filterColumns = ["ruangan_nama", "ruangan_code"];

    public static $sortColumns = ["ruangan_nama", "ruangan_code", "ruangan_deskripsi"];

    protected function casts(): array
    {
        return [
            "ruangan_id" => "integer",
        ];
    }

    public static function field_name(): string
    {
        return "ruangan_nama";
    }

    public function rules(): array
    {
        return [
            "ruangan_nama" => "required|string|max:255",
            "ruangan_deskripsi" => "nullable|string",
            "ruangan_code" => "nullable|string|max:255",
        ];
    }
}
```

- [ ] **Step 3: Create `app/Policies/RuanganPolicy.php` and `app/Http/Controllers/RuanganController.php`**

```php
<?php

namespace App\Policies;

class RuanganPolicy extends BasePolicy {}
```

```php
<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Ruangan;

class RuanganController extends Controller
{
    use ControllerTrait;

    public function __construct(Ruangan $model)
    {
        $this->model = $model::getModel();
    }
}
```

- [ ] **Step 4: Create the two views**

`resources/views/pages/ruangan/table.blade.php`: copy Task 2 Step 4 verbatim, changing only line 1 to `<?php /** @var App\Models\Ruangan $table */ ?>`.

`resources/views/pages/ruangan/form.blade.php`:
```blade
<?php /** @var App\Models\Ruangan $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="ruangan_nama" />
                <x-input col="6" name="ruangan_code" />
                <x-textarea col="12" name="ruangan_deskripsi" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']" />
    </x-form>
</x-layouts::app>
```

- [ ] **Step 5: Route + test + verify**

Add after the rs line in `routes/web.php`:
```php
Route::auto("/ruangan", "RuanganController", ["name" => "ruangan"]);
```

Append to `tests/Feature/MasterDataTest.php`:
```php
use App\Models\Ruangan;

it("maps Ruangan to the bka table", function () {
    expect((new Ruangan)->getTable())->toBe("ruangan")
        ->and((new Ruangan)->getKeyName())->toBe("ruangan_id")
        ->and(Ruangan::field_name())->toBe("ruangan_nama");
});
```

Run: `php artisan test tests/Feature/MasterDataTest.php` (expect PASS, 3 tests) and `php artisan route:list --name=ruangan`.

---

### Task 4: Kategori module

**Files:**
- Create: `app/Properties/KategoriEntity.php`, `app/Models/Kategori.php`, `app/Policies/KategoriPolicy.php`, `app/Http/Controllers/KategoriController.php`, `resources/views/pages/kategori/table.blade.php`, `resources/views/pages/kategori/form.blade.php`
- Modify: `routes/web.php`, `tests/Feature/MasterDataTest.php`

**Interfaces:**
- Consumes: table view pattern from Task 2
- Produces: routes `kategori.*`, views `pages.kategori.*`

- [ ] **Step 1: Create `app/Properties/KategoriEntity.php`**

```php
<?php

namespace App\Properties;

trait KategoriEntity
{
    public static function field_description()
    {
        return "kategori_deskripsi";
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{static::field_description()};
    }
}
```

- [ ] **Step 2: Create `app/Models/Kategori.php`**

```php
<?php

namespace App\Models;

use App\Properties\KategoriEntity;

class Kategori extends BaseModel
{
    use KategoriEntity;

    protected $table = "kategori";

    protected $primaryKey = "kategori_id";

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        "kategori_nama",
        "kategori_deskripsi",
    ];

    public static $filterColumns = ["kategori_nama"];

    public static $sortColumns = ["kategori_nama", "kategori_deskripsi"];

    protected function casts(): array
    {
        return [
            "kategori_id" => "integer",
        ];
    }

    public static function field_name(): string
    {
        return "kategori_nama";
    }

    public function rules(): array
    {
        return [
            "kategori_nama" => "required|string|max:255",
            "kategori_deskripsi" => "nullable|string",
        ];
    }
}
```

- [ ] **Step 3: Create `app/Policies/KategoriPolicy.php` and `app/Http/Controllers/KategoriController.php`**

```php
<?php

namespace App\Policies;

class KategoriPolicy extends BasePolicy {}
```

```php
<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Kategori;

class KategoriController extends Controller
{
    use ControllerTrait;

    public function __construct(Kategori $model)
    {
        $this->model = $model::getModel();
    }
}
```

- [ ] **Step 4: Create the two views**

Table: copy Task 2 Step 4 verbatim with `@var App\Models\Kategori`. Form:
```blade
<?php /** @var App\Models\Kategori $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="kategori_nama" />
                <x-textarea col="6" name="kategori_deskripsi" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']" />
    </x-form>
</x-layouts::app>
```

- [ ] **Step 5: Route + test + verify**

```php
Route::auto("/kategori", "KategoriController", ["name" => "kategori"]);
```

```php
use App\Models\Kategori;

it("maps Kategori to the bka table", function () {
    expect((new Kategori)->getTable())->toBe("kategori")
        ->and((new Kategori)->getKeyName())->toBe("kategori_id")
        ->and(Kategori::field_name())->toBe("kategori_nama");
});
```

Run: `php artisan test tests/Feature/MasterDataTest.php` (expect PASS, 4 tests) and `php artisan route:list --name=kategori`.

---

### Task 5: JenisBahan module (note: PK column prefix is `bahan_`, views folder is `pages.jenisbahan`)

**Files:**
- Create: `app/Properties/JenisBahanEntity.php`, `app/Models/JenisBahan.php`, `app/Policies/JenisBahanPolicy.php`, `app/Http/Controllers/JenisBahanController.php`, `resources/views/pages/jenisbahan/table.blade.php`, `resources/views/pages/jenisbahan/form.blade.php`
- Modify: `routes/web.php`, `tests/Feature/MasterDataTest.php`

**Interfaces:**
- Consumes: table view pattern from Task 2
- Produces: routes `jenis-bahan.*`, views `pages.jenisbahan.*` (folder name comes from `template()`: class `JenisBahanController` lowercased)

- [ ] **Step 1: Create `app/Properties/JenisBahanEntity.php`**

```php
<?php

namespace App\Properties;

trait JenisBahanEntity
{
    public static function field_description()
    {
        return "bahan_deskripsi";
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{static::field_description()};
    }
}
```

- [ ] **Step 2: Create `app/Models/JenisBahan.php`**

```php
<?php

namespace App\Models;

use App\Properties\JenisBahanEntity;

class JenisBahan extends BaseModel
{
    use JenisBahanEntity;

    protected $table = "jenis_bahan";

    protected $primaryKey = "bahan_id";

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        "bahan_nama",
        "bahan_deskripsi",
    ];

    public static $filterColumns = ["bahan_nama"];

    public static $sortColumns = ["bahan_nama", "bahan_deskripsi"];

    protected function casts(): array
    {
        return [
            "bahan_id" => "integer",
        ];
    }

    public static function field_name(): string
    {
        return "bahan_nama";
    }

    public function rules(): array
    {
        return [
            "bahan_nama" => "required|string|max:255",
            "bahan_deskripsi" => "nullable|string",
        ];
    }
}
```

- [ ] **Step 3: Create `app/Policies/JenisBahanPolicy.php` and `app/Http/Controllers/JenisBahanController.php`**

```php
<?php

namespace App\Policies;

class JenisBahanPolicy extends BasePolicy {}
```

```php
<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\JenisBahan;

class JenisBahanController extends Controller
{
    use ControllerTrait;

    public function __construct(JenisBahan $model)
    {
        $this->model = $model::getModel();
    }
}
```

- [ ] **Step 4: Create the two views under `resources/views/pages/jenisbahan/`**

Table: copy Task 2 Step 4 verbatim with `@var App\Models\JenisBahan`. Form:
```blade
<?php /** @var App\Models\JenisBahan $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="bahan_nama" />
                <x-textarea col="6" name="bahan_deskripsi" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']" />
    </x-form>
</x-layouts::app>
```

- [ ] **Step 5: Route + test + verify**

```php
Route::auto("/jenis-bahan", "JenisBahanController", ["name" => "jenis-bahan"]);
```

```php
use App\Models\JenisBahan;

it("maps JenisBahan to the bka table", function () {
    expect((new JenisBahan)->getTable())->toBe("jenis_bahan")
        ->and((new JenisBahan)->getKeyName())->toBe("bahan_id")
        ->and(JenisBahan::field_name())->toBe("bahan_nama");
});
```

Run: `php artisan test tests/Feature/MasterDataTest.php` (expect PASS, 5 tests) and `php artisan route:list --name=jenis-bahan`.

---

### Task 6: Supplier module

**Files:**
- Create: `app/Properties/SupplierEntity.php`, `app/Models/Supplier.php`, `app/Policies/SupplierPolicy.php`, `app/Http/Controllers/SupplierController.php`, `resources/views/pages/supplier/table.blade.php`, `resources/views/pages/supplier/form.blade.php`
- Modify: `routes/web.php`, `tests/Feature/MasterDataTest.php`

**Interfaces:**
- Consumes: table view pattern from Task 2
- Produces: routes `supplier.*`, views `pages.supplier.*`

- [ ] **Step 1: Create `app/Properties/SupplierEntity.php`**

```php
<?php

namespace App\Properties;

trait SupplierEntity
{
    public static function field_alamat()
    {
        return "supplier_alamat";
    }

    public function getFieldAlamatAttribute()
    {
        return $this->{static::field_alamat()};
    }

    public static function field_phone()
    {
        return "supplier_phone";
    }

    public function getFieldPhoneAttribute()
    {
        return $this->{static::field_phone()};
    }

    public static function field_contact()
    {
        return "supplier_kontak";
    }

    public function getFieldContactAttribute()
    {
        return $this->{static::field_contact()};
    }

    public static function field_email()
    {
        return "supplier_email";
    }

    public function getFieldEmailAttribute()
    {
        return $this->{static::field_email()};
    }
}
```

- [ ] **Step 2: Create `app/Models/Supplier.php`**

```php
<?php

namespace App\Models;

use App\Properties\SupplierEntity;

class Supplier extends BaseModel
{
    use SupplierEntity;

    protected $table = "supplier";

    protected $primaryKey = "supplier_id";

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        "supplier_nama",
        "supplier_alamat",
        "supplier_phone",
        "supplier_kontak",
        "supplier_email",
    ];

    public static $filterColumns = ["supplier_nama", "supplier_email"];

    public static $sortColumns = ["supplier_nama", "supplier_kontak", "supplier_email", "supplier_phone"];

    protected function casts(): array
    {
        return [
            "supplier_id" => "integer",
        ];
    }

    public static function field_name(): string
    {
        return "supplier_nama";
    }

    public function rules(): array
    {
        return [
            "supplier_nama" => "required|string|max:255",
            "supplier_alamat" => "nullable|string",
            "supplier_phone" => "nullable|string|max:50",
            "supplier_kontak" => "nullable|string|max:255",
            "supplier_email" => "nullable|email|max:255",
        ];
    }
}
```

- [ ] **Step 3: Create `app/Policies/SupplierPolicy.php` and `app/Http/Controllers/SupplierController.php`**

```php
<?php

namespace App\Policies;

class SupplierPolicy extends BasePolicy {}
```

```php
<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Models\Supplier;

class SupplierController extends Controller
{
    use ControllerTrait;

    public function __construct(Supplier $model)
    {
        $this->model = $model::getModel();
    }
}
```

- [ ] **Step 4: Create the two views**

Table: copy Task 2 Step 4 verbatim with `@var App\Models\Supplier`. Form:
```blade
<?php /** @var App\Models\Supplier $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="supplier_nama" />
                <x-input col="6" name="supplier_kontak" />
                <x-input col="6" name="supplier_phone" />
                <x-input col="6" name="supplier_email" type="email" />
                <x-textarea col="12" name="supplier_alamat" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']" />
    </x-form>
</x-layouts::app>
```

- [ ] **Step 5: Route + test + verify**

```php
Route::auto("/supplier", "SupplierController", ["name" => "supplier"]);
```

```php
use App\Models\Supplier;

it("maps Supplier to the bka table", function () {
    expect((new Supplier)->getTable())->toBe("supplier")
        ->and((new Supplier)->getKeyName())->toBe("supplier_id")
        ->and(Supplier::field_name())->toBe("supplier_nama");
});
```

Run: `php artisan test tests/Feature/MasterDataTest.php` (expect PASS, 6 tests) and `php artisan route:list --name=supplier`.

---

### Task 7: JenisLinen module (relations + kategori/rs dropdowns + gambar upload)

**Files:**
- Create: `app/Properties/JenisLinenEntity.php`, `app/Models/JenisLinen.php`, `app/Policies/JenisLinenPolicy.php`, `app/Http/Controllers/JenisLinenController.php`, `resources/views/pages/jenislinen/table.blade.php`, `resources/views/pages/jenislinen/form.blade.php`
- Modify: `routes/web.php`, `tests/Feature/MasterDataTest.php`

**Interfaces:**
- Consumes: `Kategori::getOptions()` (Task 4 model), `Rs::getOptions()` (Task 2 model) via `OptionTrait`
- Produces: routes `jenis-linen.*`, views `pages.jenislinen.*`

- [ ] **Step 1: Create `app/Properties/JenisLinenEntity.php` (fixes old `field_kategori()` bug by using `field_category_id()`)**

```php
<?php

namespace App\Properties;

trait JenisLinenEntity
{
    public static function field_description()
    {
        return "jenis_deskripsi";
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{static::field_description()};
    }

    public static function field_rs_id()
    {
        return "jenis_id_rs";
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{static::field_rs_id()};
    }

    public static function field_category_id()
    {
        return "jenis_id_kategori";
    }

    public function getFieldCategoryIdAttribute()
    {
        return $this->{static::field_category_id()};
    }

    public static function field_weight()
    {
        return "jenis_berat";
    }

    public function getFieldWeightAttribute()
    {
        return $this->{static::field_weight()} ?? 0;
    }

    public static function field_image()
    {
        return "jenis_gambar";
    }

    public function getFieldImageAttribute()
    {
        return $this->{static::field_image()};
    }

    public function getGambarUrlAttribute(): string
    {
        return fileUrl($this->{static::field_image()});
    }
}
```

- [ ] **Step 2: Create `app/Models/JenisLinen.php`**

```php
<?php

namespace App\Models;

use App\Properties\JenisLinenEntity;

class JenisLinen extends BaseModel
{
    use JenisLinenEntity;

    protected $table = "jenis_linen";

    protected $primaryKey = "jenis_id";

    public $timestamps = false;

    public $incrementing = true;

    protected $fillable = [
        "jenis_id_kategori",
        "jenis_nama",
        "jenis_deskripsi",
        "jenis_id_rs",
        "jenis_gambar",
        "jenis_berat",
    ];

    public static $filterColumns = ["jenis_nama", "jenis_id_kategori", "jenis_id_rs"];

    public static $sortColumns = ["jenis_nama", "jenis_berat", "jenis_deskripsi"];

    protected function casts(): array
    {
        return [
            "jenis_id" => "integer",
            "jenis_id_kategori" => "integer",
            "jenis_id_rs" => "integer",
            "jenis_berat" => "float",
        ];
    }

    public static function field_name(): string
    {
        return "jenis_nama";
    }

    public function rules(): array
    {
        return [
            "jenis_id_kategori" => "nullable|integer",
            "jenis_nama" => "required|string|max:255",
            "jenis_deskripsi" => "nullable|string",
            "jenis_id_rs" => "nullable|integer",
            "jenis_gambar" => "nullable|string|max:255",
            "jenis_berat" => "nullable|numeric|min:0",
        ];
    }

    public function hasCategory()
    {
        return $this->hasOne(Kategori::class, "kategori_id", "jenis_id_kategori");
    }

    public function hasRs()
    {
        return $this->hasOne(Rs::class, "rs_id", "jenis_id_rs");
    }
}
```

- [ ] **Step 3: Create `app/Policies/JenisLinenPolicy.php` and `app/Http/Controllers/JenisLinenController.php` (upload via trait aliasing, same shape as UsersController avatar)**

```php
<?php

namespace App\Policies;

class JenisLinenPolicy extends BasePolicy {}
```

```php
<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\JenisLinen;
use App\Models\Kategori;
use App\Models\Rs;
use Illuminate\Validation\ValidationException;

class JenisLinenController extends Controller
{
    use ControllerTrait {
        postCreate as private traitPostCreate;
        postUpdate as private traitPostUpdate;
    }

    public function __construct(JenisLinen $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        $default = [
            "model" => $this->model,
            "kategori" => Kategori::getOptions(),
            "rs" => Rs::getOptions(),
        ];

        return array_merge($default, $data);
    }

    public function postCreate(GeneralRequest $request)
    {
        $gambar = $this->handleGambar($request, null);
        if ($gambar !== null) {
            $request->merge(["jenis_gambar" => $gambar]);
        }

        return $this->traitPostCreate($request);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $existing = $this->model->findOrFail($id)->jenis_gambar ?? null;

        $gambar = $this->handleGambar($request, $existing);
        if ($gambar !== $existing) {
            $request->merge(["jenis_gambar" => $gambar]);
        }

        return $this->traitPostUpdate($request, $id);
    }

    private function handleGambar(GeneralRequest $request, ?string $existing): ?string
    {
        if ($request->hasFile("jenis_gambar")) {
            try {
                $path = uploadFile($request->file("jenis_gambar"), "jenis", ["max_size" => 2048]);
                $this->deleteJenisFile($existing);

                return $path;
            } catch (\InvalidArgumentException $e) {
                throw ValidationException::withMessages(["jenis_gambar" => $e->getMessage()]);
            }
        }

        if ($request->boolean("remove_jenis_gambar")) {
            $this->deleteJenisFile($existing);

            return null;
        }

        return $existing;
    }

    private function deleteJenisFile(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        $file = storage_path("app/public/" . $path);
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
```

- [ ] **Step 4: Create the two views under `resources/views/pages/jenislinen/`**

Table: copy Task 2 Step 4 verbatim with `@var App\Models\JenisLinen`. Form:
```blade
<?php /** @var App\Models\JenisLinen $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="jenis_nama" />
                <x-input col="6" name="jenis_berat" type="number" />
                <x-select col="6" name="jenis_id_kategori" :options="$kategori" />
                <x-select col="6" name="jenis_id_rs" :options="$rs" />
                <x-textarea col="12" name="jenis_deskripsi" />

                <x-file
                    name="jenis_gambar"
                    label="Gambar Linen"
                    col="12"
                    accept="image/*"
                    capture="environment"
                    :preview="true"
                    :value="$model?->gambar_url"
                    helper="Foto jenis linen untuk identifikasi" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']" />
    </x-form>
</x-layouts::app>
```

- [ ] **Step 5: Route + test + verify**

```php
Route::auto("/jenis-linen", "JenisLinenController", ["name" => "jenis-linen"]);
```

```php
use App\Models\JenisLinen;

it("maps JenisLinen to the bka table", function () {
    expect((new JenisLinen)->getTable())->toBe("jenis_linen")
        ->and((new JenisLinen)->getKeyName())->toBe("jenis_id")
        ->and(JenisLinen::field_name())->toBe("jenis_nama");
});
```

Run: `php artisan test tests/Feature/MasterDataTest.php` (expect PASS, 7 tests) and `php artisan route:list --name=jenis-linen`.

---

### Task 8: Menu + permission wiring + full verification

**Files:**
- Modify: `config/menu.php` (add 6 items under Master Data)
- Modify: `config/permision.php` (no change needed — empty `$restrict` means allow; verify only)
- Test: manual browser checklist below

**Interfaces:**
- Consumes: all routes from Tasks 2-7
- Produces: sidebar navigation for 6 modules, green test suite

- [ ] **Step 1: Add menu items in `config/menu.php`**

In the `Master Data` section `items` array, after the Users line, add:

```php
["route" => "rs.getTable", "icon" => "local_hospital", "label" => "Rumah Sakit", "match" => ["rs.*"]],
["route" => "ruangan.getTable", "icon" => "meeting_room", "label" => "Ruangan", "match" => ["ruangan.*"]],
["route" => "kategori.getTable", "icon" => "category", "label" => "Kategori", "match" => ["kategori.*"]],
["route" => "jenis-bahan.getTable", "icon" => "texture", "label" => "Bahan", "match" => ["jenis-bahan.*"]],
["route" => "supplier.getTable", "icon" => "local_shipping", "label" => "Supplier", "match" => ["supplier.*"]],
["route" => "jenis-linen.getTable", "icon" => "laundry", "label" => "Jenis Linen", "match" => ["jenis-linen.*"]],
```

- [ ] **Step 2: Run full verification**

Run: `composer lint:check`
Expected: no Pint errors in new files. If Pint flags a file, run `composer lint` to fix, then re-run check.

Run: `php artisan test tests/Feature/MasterDataTest.php`
Expected: PASS, 7 tests.

Run: `php artisan route:list --name=rs`, `--name=ruangan`, `--name=kategori`, `--name=jenis-bahan`, `--name=supplier`, `--name=jenis-linen`
Expected: each shows getTable/getCreate/postCreate/getUpdate/postUpdate/getDelete/postDelete.

- [ ] **Step 3: Manual browser checklist (report results, do not skip)**

Login, open each of the 6 table pages, confirm data from `bka` renders; open each create form, submit one test record, edit it, delete it; confirm dropdowns populate on JenisLinen form (kategori, rs) and image upload stores to `storage/app/public/jenis/`. Remove test records afterwards.

---

## Self-Review

- Spec coverage: master modules + reuse-100% + scan-API noted as later phase (scan outbox is NOT in this plan — next plan). ReportEngine not in this plan — next plan. Correct scope: master data only.
- Placeholder scan: all code blocks complete; no TBD/TODO; JenisLinenEntity old bug fixed inline with note; `jenisbaham`/`jenislinen` folder naming derived from `template()` and stated explicitly.
- Type consistency: `field_name()` returns string in all models; `getOptions()` consumed as `:options` arrays; `field_primary` accessor inherited from `DefaultEntity`, never redefined.
