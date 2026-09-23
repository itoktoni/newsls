# Detail Linen + Registrasi Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the `detail_linen` master module (RFID-keyed linen registry) with a registration form that forces new linen into REGISTER status.

**Architecture:** One Property Entity + one Model (BaseModel, string PK, custom timestamps, booted() defaults; PowerJoins used as Builder macro — the installed v5 package has no model trait) + 3 new enums (kepemilikan reuses RsStatusEnum) + Policy + Controller (dropdown share + getData join override + duplicate-RFID guard) + 2 canon views + 1 Route::auto line. Zero migrations (reuse `bka`).

**Tech Stack:** Laravel 13, PHP 8.3, bensampo/laravel-enum, kirschbaum PowerJoins, izniburak auto-routes, Pest.

## Global Constraints

- PHP ^8.3, Laravel ^13.7.
- Reuse DB `bka` 100 percent. NO migration files.
- Single quotes (Pint Laravel preset — established Task 8: preset wins over the AGENTS.md double-quote line), 4-space indent, return types on all methods.
- `protected $fillable`, never `#[Fillable]`.
- Casts only in `protected function casts(): array`.
- Relationship methods prefixed `has`.
- Views follow `resources/views/pages/users/table.blade.php` + `form.blade.php` exactly.
- One Policy per model, `class XPolicy extends BasePolicy {}` with no extra code.
- Entity traits must NOT define `field_primary`, `getFieldPrimaryAttribute`, `field_name`, or `getFieldNameAttribute` (DefaultEntity collision fatal).
- Trait aliasing for `postCreate`/`postUpdate` overrides must use `as private` (Route::auto exposes public aliases as routes).
- Views folder for `DetailLinenController` is `pages.detaillinen` (from `template()`: class name lowercased).

---

### Task 1: Enums (RegisterEnum, CuciEnum, LinenStatusEnum)

**Files:**
- Create: `app/Enums/RegisterEnum.php`
- Create: `app/Enums/CuciEnum.php`
- Create: `app/Enums/LinenStatusEnum.php`
- Modify: `tests/Feature/MasterDataTest.php` (append 3 tests)

**Interfaces:**
- Consumes: `App\Concerns\EnumTrait` (existing)
- Produces: `RegisterEnum::getOptions()`, `CuciEnum::getOptions()`, `LinenStatusEnum::getOptions()` for the DetailLinen form dropdowns. Kepemilikan reuses `RsStatusEnum::getOptions()` — no new enum.

- [ ] **Step 1: Create `app/Enums/RegisterEnum.php`**

```php
<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class RegisterEnum extends Enum
{
    use EnumTrait;

    const REGISTER = 'REGISTER';

    const GANTI_CHIP = 'GANTI_CHIP';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::REGISTER => 'Register',
            self::GANTI_CHIP => 'Ganti Chip',
            default => parent::getDescription($value),
        };
    }
}
```

- [ ] **Step 2: Create `app/Enums/CuciEnum.php`**

```php
<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class CuciEnum extends Enum
{
    use EnumTrait;

    const CUCI = 'CUCI';

    const RENTAL = 'RENTAL';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::CUCI => 'Cuci',
            self::RENTAL => 'Rental',
            default => parent::getDescription($value),
        };
    }
}
```

- [ ] **Step 3: Create `app/Enums/LinenStatusEnum.php` (values match the `detail_status_linen` column enum)**

```php
<?php

namespace App\Enums;

use App\Concerns\EnumTrait;
use BenSampo\Enum\Enum;

final class LinenStatusEnum extends Enum
{
    use EnumTrait;

    const REGISTER = 'REGISTER';

    const KOTOR = 'KOTOR';

    const BERSIH = 'BERSIH';

    const GUDANG = 'GUDANG';

    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::REGISTER => 'Register',
            self::KOTOR => 'Kotor',
            self::BERSIH => 'Bersih',
            self::GUDANG => 'Gudang',
            default => parent::getDescription($value),
        };
    }
}
```

- [ ] **Step 4: Append tests and run**

Append to `tests/Feature/MasterDataTest.php`:

```php
use App\Enums\CuciEnum;
use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;

it('exposes linen registration enums', function () {
    expect(RegisterEnum::getOptions())->toBe([
        'REGISTER' => 'Register',
        'GANTI_CHIP' => 'Ganti Chip',
    ])->and(CuciEnum::getOptions())->toBe([
        'CUCI' => 'Cuci',
        'RENTAL' => 'Rental',
    ])->and(LinenStatusEnum::getOptions())->toBe([
        'REGISTER' => 'Register',
        'KOTOR' => 'Kotor',
        'BERSIH' => 'Bersih',
        'GUDANG' => 'Gudang',
    ]);
});
```

Run: `php artisan test tests/Feature/MasterDataTest.php`
Expected: PASS (8 tests)

---

### Task 2: DetailLinenEntity + DetailLinen model

**Files:**
- Create: `app/Properties/DetailLinenEntity.php`
- Create: `app/Models/DetailLinen.php`
- Modify: `tests/Feature/MasterDataTest.php` (append mapping test)

**Interfaces:**
- Consumes: `Kategori`, `Rs` models not needed here; `LinenStatusEnum`, `RegisterEnum`, `CuciEnum` for name accessors
- Produces: `DetailLinen::field_name()` = `'detail_rfid'`, relations `hasRs`, `hasRuangan`, `hasJenis`, `hasBahan`, `hasSupplier` for Task 3 joins

Notes locked in: `field_status_process` from the old trait is DROPPED (column `detail_status_proses` does not exist in `bka`). The broken old accessors calling `$this->detail_total_bersih()` / `$this->detail_total_reject()` / `$this->detail_total_rewash()` (methods that do not exist) are DROPPED. `field_status_transaction` maps to `detail_status_linen` and its name accessor uses `LinenStatusEnum` (old code used TransactionType whose values do not match this column).

- [ ] **Step 1: Create `app/Properties/DetailLinenEntity.php`**

```php
<?php

namespace App\Properties;

use App\Enums\CuciEnum;
use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;

trait DetailLinenEntity
{
    public static function field_description()
    {
        return 'detail_deskripsi';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{static::field_description()};
    }

    public static function field_rs_id()
    {
        return 'detail_id_rs';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{static::field_rs_id()};
    }

    public static function field_ruangan_id()
    {
        return 'detail_id_ruangan';
    }

    public function getFieldRuanganIdAttribute()
    {
        return $this->{static::field_ruangan_id()};
    }

    public static function field_jenis_id()
    {
        return 'detail_id_jenis';
    }

    public function getFieldJenisIdAttribute()
    {
        return $this->{static::field_jenis_id()};
    }

    public static function field_bahan_id()
    {
        return 'detail_id_bahan';
    }

    public function getFieldBahanIdAttribute()
    {
        return $this->{static::field_bahan_id()};
    }

    public static function field_supplier_id()
    {
        return 'detail_id_supplier';
    }

    public function getFieldSupplierIdAttribute()
    {
        return $this->{static::field_supplier_id()};
    }

    public static function field_status_cuci()
    {
        return 'detail_status_cuci';
    }

    public function getFieldStatusCuciAttribute()
    {
        return $this->{static::field_status_cuci()};
    }

    public function getFieldStatusCuciNameAttribute(): string
    {
        return CuciEnum::getDescription($this->getFieldStatusCuciAttribute());
    }

    public static function field_status_register()
    {
        return 'detail_status_register';
    }

    public function getFieldStatusRegisterAttribute()
    {
        return $this->{static::field_status_register()};
    }

    public function getFieldStatusRegisterNameAttribute(): string
    {
        return RegisterEnum::getDescription($this->getFieldStatusRegisterAttribute());
    }

    public static function field_status_linen()
    {
        return 'detail_status_linen';
    }

    public function getFieldStatusLinenAttribute()
    {
        return $this->{static::field_status_linen()};
    }

    public function getFieldStatusLinenNameAttribute(): string
    {
        return LinenStatusEnum::getDescription($this->getFieldStatusLinenAttribute());
    }

    public static function field_status_kepemilikan()
    {
        return 'detail_status_kepemilikan';
    }

    public function getFieldStatusKepemilikanAttribute()
    {
        return $this->{static::field_status_kepemilikan()};
    }

    public static function field_created_at()
    {
        return 'detail_created_at';
    }

    public static function field_created_by()
    {
        return 'detail_created_by';
    }

    public static function field_updated_at()
    {
        return 'detail_updated_at';
    }

    public static function field_updated_by()
    {
        return 'detail_updated_by';
    }

    public static function field_cek()
    {
        return 'detail_tgl_cek';
    }

    public function getFieldCekAttribute()
    {
        return $this->{static::field_cek()};
    }

    public static function field_report()
    {
        return 'detail_report';
    }

    public function getFieldReportAttribute()
    {
        return $this->{static::field_report()};
    }
}
```

- [ ] **Step 2: Create `app/Models/DetailLinen.php`**

The model uses the `PowerJoins` trait (required for `leftJoinRelationship` in Task 3 — `BaseModel` does not include it). Registration defaults live in `booted()`: new rows always enter as REGISTER/REGISTER with the operator stamped, so the form never asks for these statuses.

```php
<?php

namespace App\Models;

use App\Properties\DetailLinenEntity;

class DetailLinen extends BaseModel
{
    use DetailLinenEntity;

    protected $table = 'detail_linen';

    protected $primaryKey = 'detail_rfid';

    public $timestamps = true;

    public $incrementing = false;

    protected $keyType = 'string';

    const CREATED_AT = 'detail_created_at';

    const UPDATED_AT = 'detail_updated_at';

    protected $fillable = [
        'detail_rfid',
        'detail_id_rs',
        'detail_id_ruangan',
        'detail_id_jenis',
        'detail_id_bahan',
        'detail_id_supplier',
        'detail_deskripsi',
        'detail_status_cuci',
        'detail_status_register',
        'detail_status_kepemilikan',
        'detail_status_linen',
        'detail_tgl_cek',
        'detail_report',
    ];

    public static $filterColumns = [
        'detail_rfid',
        'detail_status_cuci',
        'detail_status_register',
        'detail_status_linen',
        'detail_id_rs',
        'detail_id_jenis',
    ];

    public static $sortColumns = [
        'detail_rfid',
        'rs_nama',
        'ruangan_nama',
        'jenis_nama',
        'detail_status_cuci',
        'detail_status_register',
        'detail_status_linen',
        'detail_created_at',
    ];

    protected function casts(): array
    {
        return [
            'detail_rfid' => 'string',
            'detail_tgl_cek' => 'date',
            'detail_report' => 'date',
        ];
    }

    public static function field_name(): string
    {
        return 'detail_rfid';
    }

    public function rules(): array
    {
        return [
            'detail_rfid' => 'required|string|max:255',
            'detail_id_rs' => 'nullable|integer',
            'detail_id_ruangan' => 'nullable|integer',
            'detail_id_jenis' => 'nullable|integer',
            'detail_id_bahan' => 'nullable|integer',
            'detail_id_supplier' => 'nullable|integer',
            'detail_deskripsi' => 'nullable|string',
            'detail_status_cuci' => 'nullable|string|max:50',
            'detail_status_register' => 'nullable|string|max:50',
            'detail_status_kepemilikan' => 'nullable|string|max:50',
            'detail_status_linen' => 'nullable|string|max:50',
            'detail_tgl_cek' => 'nullable|date',
            'detail_report' => 'nullable|date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->detail_status_register ??= 'REGISTER';
            $model->detail_status_linen ??= 'REGISTER';
            $model->detail_created_by ??= auth()->id();
        });

        static::updating(function (self $model) {
            $model->detail_updated_by = auth()->id();
        });
    }

    public function hasRs()
    {
        return $this->hasOne(Rs::class, 'rs_id', 'detail_id_rs');
    }

    public function hasRuangan()
    {
        return $this->hasOne(Ruangan::class, 'ruangan_id', 'detail_id_ruangan');
    }

    public function hasJenis()
    {
        return $this->hasOne(JenisLinen::class, 'jenis_id', 'detail_id_jenis');
    }

    public function hasBahan()
    {
        return $this->hasOne(JenisBahan::class, 'bahan_id', 'detail_id_bahan');
    }

    public function hasSupplier()
    {
        return $this->hasOne(Supplier::class, 'supplier_id', 'detail_id_supplier');
    }
}
```

Why no `unique` rule on `detail_rfid`: the same `rules()` feed both create and update, so a `unique` rule would reject every update of an existing row. Duplicates are rejected in `postCreate` instead (Task 3 Step 2).

Why joined aliases (`rs_nama` etc.) in `$sortColumns` are safe: `ControllerTrait::getData()` builds the query from `$this->model->query()` and only executes it in `getTable()` via `cursorPaginate()` — after Task 3 Step 3 adds the joins to the same builder. Builder call order does not matter; the final SQL contains both JOIN and ORDER BY.

- [ ] **Step 3: Append mapping test and run**

Append to `tests/Feature/MasterDataTest.php`:

```php
use App\Models\DetailLinen;

it('maps DetailLinen to the bka table', function () {
    $model = new DetailLinen;

    expect($model->getTable())->toBe('detail_linen')
        ->and($model->getKeyName())->toBe('detail_rfid')
        ->and($model->getIncrementing())->toBeFalse()
        ->and($model->getKeyType())->toBe('string')
        ->and(DetailLinen::field_name())->toBe('detail_rfid')
        ->and((new DetailLinen)->rules()['detail_rfid'])->toContain('required');
});
```

Run: `php artisan test tests/Feature/MasterDataTest.php`
Expected: PASS (9 tests)

---

### Task 3: Policy + Controller

**Files:**
- Create: `app/Policies/DetailLinenPolicy.php`
- Create: `app/Http/Controllers/DetailLinenController.php`

**Interfaces:**
- Consumes: `Rs::getOptions()`, `Ruangan::getOptions()`, `JenisLinen::getOptions()`, `JenisBahan::getOptions()`, `Supplier::getOptions()` (Tasks 2-7 of the master-data plan), enum `getOptions()` from Task 1
- Produces: routes `detail-linen.*` (Task 5), `getData()` with joined name columns for Task 4 table

- [ ] **Step 1: Create `app/Policies/DetailLinenPolicy.php`**

```php
<?php

namespace App\Policies;

class DetailLinenPolicy extends BasePolicy {}
```

- [ ] **Step 2: Create `app/Http/Controllers/DetailLinenController.php`**

```php
<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Enums\CuciEnum;
use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Http\Requests\GeneralRequest;
use App\Models\DetailLinen;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Supplier;
use Illuminate\Validation\ValidationException;

class DetailLinenController extends Controller
{
    use ControllerTrait {
        postCreate as private traitPostCreate;
        getData as private traitGetData;
    }

    public function __construct(DetailLinen $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        $default = [
            'model' => $this->model,
            'rs' => Rs::getOptions(),
            'ruangan' => Ruangan::getOptions(),
            'jenis' => JenisLinen::getOptions(),
            'bahan' => JenisBahan::getOptions(),
            'supplier' => Supplier::getOptions(),
            'cuci' => CuciEnum::getOptions(),
            'register' => RegisterEnum::getOptions(),
            'linen' => LinenStatusEnum::getOptions(),
            'milik' => RsStatusEnum::getOptions(),
        ];

        return array_merge($default, $data);
    }

    protected function getData()
    {
        return $this->traitGetData()
            ->leftJoinRelationship('hasRs')
            ->leftJoinRelationship('hasRuangan')
            ->leftJoinRelationship('hasJenis')
            ->addSelect([
                'detail_linen.*',
                'rs.rs_nama as rs_nama',
                'ruangan.ruangan_nama as ruangan_nama',
                'jenis_linen.jenis_nama as jenis_nama',
            ]);
    }

    public function postCreate(GeneralRequest $request)
    {
        if ($this->model->where('detail_rfid', $request->input('detail_rfid'))->exists()) {
            throw ValidationException::withMessages(['detail_rfid' => 'RFID sudah terdaftar.']);
        }

        return $this->traitPostCreate($request);
    }
}
```

`filterColumns` contains only real `detail_linen` columns on purpose: the trait auto-joins dot-notation filters (`hasRs.rs_nama`), which would duplicate the manual joins above and break the query.

---

### Task 4: Views

**Files:**
- Create: `resources/views/pages/detaillinen/table.blade.php`
- Create: `resources/views/pages/detaillinen/form.blade.php`

**Interfaces:**
- Consumes: `$sortColumns` with joined aliases from Task 2, shared dropdowns from Task 3
- Produces: `pages.detaillinen.table`, `pages.detaillinen.form`

The form deliberately omits `detail_status_register` and `detail_status_linen`: `booted()` forces REGISTER on create, so operators cannot register linen straight into circulation.

- [ ] **Step 1: Create `resources/views/pages/detaillinen/table.blade.php`**

Copy `resources/views/pages/rs/table.blade.php` verbatim, changing only line 1 to `<?php /** @var App\Models\DetailLinen $table */ ?>`.

- [ ] **Step 2: Create `resources/views/pages/detaillinen/form.blade.php`**

```blade
<?php /** @var App\Models\DetailLinen $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="detail_rfid" label="RFID" />
                <x-select col="6" name="detail_id_rs" label="Rumah Sakit" :options="$rs" />
                <x-select col="6" name="detail_id_ruangan" label="Ruangan" :options="$ruangan" />
                <x-select col="6" name="detail_id_jenis" label="Jenis Linen" :options="$jenis" />
                <x-select col="6" name="detail_id_bahan" label="Bahan" :options="$bahan" />
                <x-select col="6" name="detail_id_supplier" label="Supplier" :options="$supplier" />
                <x-select col="6" name="detail_status_cuci" label="Status Cuci" :options="$cuci" />
                <x-select col="6" name="detail_status_kepemilikan" label="Kepemilikan" :options="$milik" />
                <x-input col="6" name="detail_tgl_cek" label="Tanggal Cek" type="date" />
                <x-textarea col="12" name="detail_deskripsi" label="Deskripsi" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']" />
    </x-form>
</x-layouts::app>
```

---

### Task 5: Route + menu + verification

**Files:**
- Modify: `routes/web.php` (one line), `config/menu.php` (one line)

**Interfaces:**
- Consumes: all Tasks 1-4
- Produces: working `/detail-linen` module, green suite

- [ ] **Step 1: Add the route**

In `routes/web.php`, inside the auth group after the jenis-linen line, add:

```php
Route::auto('/detail-linen', 'DetailLinenController', ['name' => 'detail-linen']);
```

- [ ] **Step 2: Add the menu item**

In `config/menu.php` Master Data items, after the Jenis Linen line, add:

```php
['route' => 'detail-linen.getTable', 'icon' => 'qr_code_2', 'label' => 'Data Linen', 'match' => ['detail-linen.*']],
```

- [ ] **Step 3: Run full verification**

Run: `php vendor/bin/pint --test app/Enums/RegisterEnum.php app/Enums/CuciEnum.php app/Enums/LinenStatusEnum.php app/Properties/DetailLinenEntity.php app/Models/DetailLinen.php app/Policies/DetailLinenPolicy.php app/Http/Controllers/DetailLinenController.php tests/Feature/MasterDataTest.php routes/web.php config/menu.php`
Expected: PASS (no diff output). If a file is flagged, run `php vendor/bin/pint` on exactly those files (never bare `composer lint` — it rewrites legacy files).

Run: `php artisan test tests/Feature/MasterDataTest.php`
Expected: PASS (9 tests)

Run: `php artisan route:list --name=detail-linen`
Expected: 9 routes, no `traitPostCreate` leak.

Run: `php artisan view:cache`, then `php artisan view:clear`
Expected: both succeed (proves the 2 new blades compile).

- [ ] **Step 4: Manual browser checklist (report results, do not skip)**

Login, open Data Linen table (renders existing `detail_linen` rows with RS/ruangan/jenis names), register one test RFID (lands with status REGISTER/REGISTER), edit it, delete it; remove test row afterwards.

---

## Non-goals (later phases)

- Ganti-chip flow (needs new-RFID row + history + `detail_lama`/`penggantian` stamping — separate design).
- History tab per RFID (`history` module phase).
- Bulk RFID paste filter on the table (old `getData` rfid explode).
- Scan-API outbox + state machine (sirkulasi phase per design doc).
- `ViewDetailLinen` read model (only if the join override proves too slow at volume).

## Self-Review

- Spec coverage: design-doc sirkulasi scope, master-data slice only; registration semantics enforced at model layer.
- Placeholder scan: all code complete; joined-alias sort safety reasoned inline; unique-rule trap avoided with explicit pre-check.
- Type consistency: `field_name()` string everywhere; `:options` arrays from `getOptions()`; `field_primary` inherited, never redefined.
