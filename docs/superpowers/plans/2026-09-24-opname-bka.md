# Opname BKA Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replicate `andalan` opname module in `bka\web` — opname master, capture (snapshot linen per RS), sync opname (RFID scan → ketemu), and all opname reports — persisting in `test_bka` and `bka` MariaDB.

**Architecture:** Legacy `bka` tables `opname`/`opname_detail` already exist in prod (not migrated) — add MariaDB-compatible migrations for `test_bka` fresh. Models extend `BaseModel` with `OpnameEntity`/`OpnameDetailEntity` field helpers (like `DetailLinenEntity`). Services `CaptureOpnameService` (andalan snapshot) and `SaveOpnameService` (sync) handle bulk chunk inserts. Controller `OpnameController` uses `ControllerTrait` + `Route::auto` for CRUD, plus `getCapture/$code` and `getSync`. Reports via `BuildsOpnameReport` trait, 5 controllers (Rekap/Detail/Summary/Hilang/HilangWarehouse).

**Tech Stack:** Laravel 13, lorisleiva/actions, auto-routes, bensampo/enum, spatie/activitylog, MySQL/MariaDB, Pest 5, Dompdf for report print.

## Global Constraints

- PHP ^8.3, Laravel 13.x, MySQL/MariaDB `bka` + `test_bka` isolated (phpunit.xml mariadb/test_bka, RefreshDatabase)
- Follow AGENTS.md CRUD checklist: Enum → Model → Migration → Policy → ControllerTrait → Route::auto → Views (copy users/table+form)
- Enums extend BenSampo\Enum\Enum with EnumTrait, getOptions() for <x-select>
- Model PK `opname_id`/`opname_detail_id`, $table singular, $primaryKey, $fillable, casts, field_*(), rules(), hasXxx() prefix has
- Policy wajib per model (BasePolicy → config/permision.php) else 403
- API responses via Plugins\Notes envelope (status/code/name/message/data), HTTP 200
- Barcode/delivery codes via Global helpers generateBarcode()/generateDeliveryCode() — capital, short, no DB hit
- Views copy pages/users/table.blade.php + form.blade.php, use <x-filter>, <x-table>, mobile slot, initTable()

---

### Task 1: Migrations — opname, opname_detail, view_opname (MariaDB + sqlite compat)

**Files:**
- Create: `database/migrations/2026_09_24_000000_create_opname_tables.php`
- Modify: `database/migrations/2026_09_23_000000_create_legacy_bka_test_tables.php` (add check)

**Interfaces:**
- Consumes: andalan `andalan/app/Dao/Entities/OpnameEntity.php` (fields), `OpnameDetailEntity.php`, `SHOW CREATE TABLE opname` from bka prod
- Produces: tables `opname` (opname_id PK, opname_nama, opname_mulai, opname_selesai, opname_id_rs, opname_status, opname_capture, timestamps, created_by), `opname_detail` (opname_detail_id PK, opname_detail_id_opname FK, opname_detail_rfid, opname_detail_transaksi, opname_detail_proses, opname_detail_hilang, opname_detail_ketemu, opname_detail_scan_rs, opname_detail_sync, opname_detail_reff, opname_detail_scan_by, opname_detail_waktu, etc.), SQL view `view_opname` or query equivalent

- [ ] **Step 1: Write failing test — migration creates tables in test_bka**

```php
// tests/Feature/OpnameMigrationTest.php
it('creates opname tables in test_bka', function () {
    expect(Schema::hasTable('opname'))->toBeTrue();
    expect(Schema::hasTable('opname_detail'))->toBeTrue();
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=OpnameMigrationTest -v`
Expected: FAIL hasTable false (before migration)

- [ ] **Step 3: Write migration**

```php
// 2026_09_24_000000_create_opname_tables.php
if (!Schema::hasTable('opname')) { Schema::create('opname', function($t){ $t->id('opname_id'); $t->string('opname_nama')->nullable(); $t->date('opname_mulai')->nullable(); $t->date('opname_selesai')->nullable(); $t->unsignedBigInteger('opname_id_rs')->nullable(); $t->string('opname_status')->default('Proses'); $t->dateTime('opname_capture')->nullable(); $t->dateTime('opname_created_at')->nullable(); $t->dateTime('opname_updated_at')->nullable(); $t->integer('opname_created_by')->nullable(); $t->integer('opname_updated_by')->nullable(); }); }
if (!Schema::hasTable('opname_detail')) { Schema::create('opname_detail', function($t){ $t->id('opname_detail_id'); $t->unsignedBigInteger('opname_detail_id_opname'); $t->string('opname_detail_rfid'); $t->string('opname_detail_transaksi')->nullable(); $t->string('opname_detail_proses')->nullable(); $t->string('opname_detail_hilang')->nullable()->default('NORMAL'); $t->string('opname_detail_ketemu')->default('TIDAK'); $t->string('opname_detail_scan_rs')->default('TIDAK'); $t->string('opname_detail_sync')->default('TIDAK'); $t->string('opname_detail_reff')->nullable(); $t->string('opname_detail_scan_by')->nullable(); $t->dateTime('opname_detail_waktu')->nullable(); $t->index(['opname_detail_id_opname','opname_detail_rfid']); }); }
// view_opname as DB::statement CREATE VIEW if not exists
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=OpnameMigrationTest -v` → PASS

- [ ] **Step 5: Commit**

```bash
git add database/migrations/2026_09_24_000000_create_opname_tables.php tests/Feature/OpnameMigrationTest.php
git commit -m "feat(opname): migrations for opname/opname_detail + view"
```

---

### Task 2: Models & Enums — Opname, OpnameDetail, OpnameType, Traits

**Files:**
- Create: `app/Enums/OpnameStatusEnum.php`
- Create: `app/Properties/OpnameEntity.php`, `OpnameDetailEntity.php` (copy DetailLinenEntity pattern)
- Create: `app/Models/Opname.php`, `app/Models/OpnameDetail.php`
- Modify: `app/Policies/OpnamePolicy.php` (extends BasePolicy)

**Interfaces:**
- Consumes: Task 1 tables
- Produces: Opname::field_*(), hasRs(), hasDetail(); OpnameDetail::field_*(), hasOpname(), hasView(); OpnameStatusEnum::PROSES/SELESAI

- [ ] **Step 1: Write failing test — model mappings**

```php
it('maps Opname to bka table', function () {
    expect((new Opname)->getTable())->toBe('opname');
    expect(Opname::field_name())->toBe('opname_nama');
});
```

- [ ] **Step 2: Run → FAIL**

- [ ] **Step 3: Implement Enum + Traits + Models (Booted default status, casts, rules, $filterColumns)**

- [ ] **Step 4: Run → PASS**

- [ ] **Step 5: Commit**

---

### Task 3: Services — CaptureOpnameService (snapshot) & SaveOpnameService (sync)

**Files:**
- Create: `app/Actions/CaptureOpnameAction.php` (or Services/CaptureOpnameService.php) — replicate andalan CaptureOpnameService: where config_linen + outstanding + detail → chunk insert opname_detail with ketemu=TIDAK
- Create: `app/Actions/SyncOpnameAction.php` — replicate SaveOpnameService: rfid[] + opname_id + code, bulk update/insert opname_detail ketemu=YA, sync=YA, scan_by=OPNAME

**Interfaces:**
- Consumes: Task 2 models, DetailLinen/Outstanding/ConfigLinen
- Produces: CaptureOpnameAction::run(Opname $opname): array, SyncOpnameAction::run(opname_id, rfid[], code): array

- [ ] **Step 1: Write failing test — capture creates opname_detail rows**

```php
it('capture snapshots linen per rs', function () {
    $opname = Opname::create([...]);
    CaptureOpnameAction::run($opname);
    expect(OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->count())->toBeGreaterThan(0);
});
```

- [ ] **Step 2: Run → FAIL**

- [ ] **Step 3: Implement services (chunk 500, env TRANSACTION_CHUNK, handle andalan logic for pending/hilang)**

- [ ] **Step 4: Run → PASS**

- [ ] **Step 5: Commit**

---

### Task 4: OpnameController — CRUD + capture + sync (web) + API sync

**Files:**
- Create: `app/Http/Controllers/OpnameController.php` (use ControllerTrait, __construct model, share(), getData() with joins, getCapture/$code, postSync)
- Create: `app/Http/Controllers/Api/OpnameApiController.php` (POST /api/opname/sync, GET /api/opname/{id}/detail)
- Modify: `routes/web.php` add Route::auto('/opname', 'OpnameController'), `routes/api.php` add sync routes
- Modify: `config/menu.php` add Opname, `config/permision.php` add policy

**Interfaces:**
- Consumes: Tasks 2-3
- Produces: web routes opname/getTable, getCreate/postCreate, getCapture/{code}, postSync; api routes opname/sync

- [ ] **Step 1: Write failing test — web capture endpoint**

```php
it('captures opname via web', function () {
    $this->actingAs($admin)->get("/opname/capture/{$opname->opname_id}")->assertRedirect();
    expect(Opname::find($opname->opname_id)->opname_capture)->not->toBeNull();
});
```

- [ ] **Step 2: Run → FAIL**

- [ ] **Step 3: Implement controller (follow DetailLinenController pattern, copy users/table view)**

- [ ] **Step 4: Run → PASS**

- [ ] **Step 5: Commit**

---

### Task 5: Reports — 5 opname reports (Rekap, Detail, Summary, Hilang, HilangWarehouse)

**Files:**
- Create: `app/Http/Controllers/ReportRekapOpnameController.php`, `ReportOpnameDetailController.php`, `ReportOpnameSummaryController.php`, `ReportOpnameHilangController.php`, `ReportOpnameHilangWarehouseController.php` (each extends BuildsOpnameReport trait or simple query)
- Create: `resources/views/pages/report-opname-*/table.blade.php` + print/pdf
- Create: `app/Charts/OpnameChart.php` (optional)

**Interfaces:**
- Consumes: Task 2 models, view_opname
- Produces: routes /report-rekap-opname/table, /report-opname-detail/table etc., PDF via dompdf

- [ ] **Step 1: Write failing test — report rekap returns data**

```php
it('shows rekap opname', function () {
    $this->actingAs($admin)->get('/report-rekap-opname/table?opname_id='.$opname->opname_id)->assertOk();
});
```

- [ ] **Step 2: Run → FAIL**

- [ ] **Step 3: Implement reports (copy ReportRekapKotor pattern, query ViewOpname, group by rs/ruangan/jenis)**

- [ ] **Step 4: Run → PASS**

- [ ] **Step 5: Commit**

---

### Task 6: Views, Menu, Policy, Seed & Final Tests

**Files:**
- Create: `resources/views/pages/opname/table.blade.php`, `form.blade.php`, `capture.blade.php` (copy users)
- Modify: `config/menu.php`, `config/permision.php`
- Create: `database/seeders/OpnameSeeder.php`
- Modify: `tests/Feature/LinenStep*.php` to include opname flow if needed

**Interfaces:**
- Consumes: Tasks 1-5
- Produces: navigable UI, seeded test_bka data, full test suite green (except MasterDataTest legacy)

- [ ] **Step 1: Write failing test — UI renders**

```php
it('renders opname table', function () { $this->actingAs($admin)->get('/opname/table')->assertOk()->assertSee('Opname'); });
```

- [ ] **Step 2: Run → FAIL**

- [ ] **Step 3: Implement views + menu**

- [ ] **Step 4: Run all Linen+Opname tests → PASS (19+6)**

- [ ] **Step 5: Commit**

