# Dashboard Redesign 3 Peran Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Pisahkan `/dashboard` menjadi router + 3 dashboard (Admin / RS / Laundry) berdasarkan role, default = laundry untuk role legacy/tak dikenal.

**Architecture:** Route `GET /dashboard` → `DashboardRouterController` (resolve default per role). Route terpisah `/dashboard/{admin|rs|laundry}` → invokable controller di `app/Http/Controllers/Dashboard/`. Salah role → `redirect()->route('dashboard')` + flash. View blade per dashboard; data lama (users/notif chart) pindah ke Admin.

**Tech Stack:** Laravel 13, Pest, Blade + Flux components (`x-card`, `x-stat-widget`, `x-chart-widget`), Larapex charts, `User::scopeRs`.

**Spec:** `docs/superpowers/specs/2026-09-23-dashboard-3-role-design.md`

## Global Constraints

- Bahasa UI label linen: **Indonesia**.
- Role legacy (`user`, `editor`, unknown, null) → **default `dashboard.laundry`** (bukan 403).
- Role salah buka path dashboard lain → `redirect()->route('dashboard')` + flash info, **bukan 403**.
- Fortify `config/fortify.php` `home` = `/dashboard` — **tidak diubah**.
- Menu `config/menu.php` `route => 'dashboard'` — **tidak diubah** (router handle).
- Tabel `pending` **tidak ada** di migration test_bka → pending count wajib `Schema::hasTable('pending')` → fallback outstanding atau 0. **Jangan 500.**
- Parstock: `rs_dan_jenis.parstock` nullable / 0 → tampilkan stok + "par —" tanpa persen.
- Test suite persist: `OpnameFlowTest` dll **tanpa RefreshDatabase**; test baru `DashboardRoleTest` boleh `RefreshDatabase` (pola `RsUserScopeTest`).
- Assert notes envelope API lama: HTTP 200 + `body.code` — tidak disentuh plan ini.
- Pint: `vendor/bin/pint --test` file yang disentuh; full `php artisan test`.
- Jangan ubah `routes/api.php` / desktop.

---

### Task 1: RoleEnum + config dashboard + trait resolve role

**Files:**
- Modify: `app/Enums/RoleEnum.php`
- Create: `config/dashboard.php`
- Create: `app/Concerns/ResolvesDashboardRole.php`
- Test: `tests/Feature/DashboardRoleTest.php` (mulai; routing di Task 2)

**Interfaces:**
- Produces: `RoleEnum::RS = 'rs'`, `RoleEnum::LAUNDRY = 'laundry'`; `RoleEnum::getDescription` untuk dua value baru; `config('dashboard.understock_threshold')` = `0.6`, `top_ruangan` = `12`, `chart_days` = `7`; trait method `defaultDashboardRoute(?string $role): string` → route name.

- [ ] **Step 1: Tulis test unit role resolve (fail dulu)**

```php
<?php

use App\Concerns\ResolvesDashboardRole;
use App\Enums\RoleEnum;
use App\Models\User;

uses(RefreshDatabase::class);

function resolver(): string
{
    return new class
    {
        use ResolvesDashboardRole;
    }->defaultDashboardRoute(request()->user()?->role);
}

// helper lokal — dipanggil via actingAs + GET di Task 2; unit trait:
it('maps roles to default dashboard route names', function () {
    $r = new class
    {
        use ResolvesDashboardRole;
    };

    expect($r->defaultDashboardRoute(RoleEnum::ADMIN))->toBe('dashboard.admin')
        ->and($r->defaultDashboardRoleName(RoleEnum::DEVELOPER))->toBe('dashboard.admin')
        ->and($r->defaultDashboardRoute(RoleEnum::RS))->toBe('dashboard.rs')
        ->and($r->defaultDashboardRoute(RoleEnum::LAUNDRY))->toBe('dashboard.laundry')
        ->and($r->defaultDashboardRoute(RoleEnum::USER))->toBe('dashboard.laundry')
        ->and($r->defaultDashboardRoute(RoleEnum::EDITOR))->toBe('dashboard.laundry')
        ->and($r->defaultDashboardRoute(null))->toBe('dashboard.laundry')
        ->and($r->defaultDashboardRoute('gak-ada'))->toBe('dashboard.laundry');
});

it('exposes rs and laundry in RoleEnum options', function () {
    expect(RoleEnum::RS)->toBe('rs')
        ->and(RoleEnum::LAUNDRY)->toBe('laundry')
        ->and(RoleEnum::getOptions())->toHaveKey('rs')
        ->and(RoleEnum::getOptions())->toHaveKey('laundry');
});

it('keeps dashboard config thresholds', function () {
    expect(config('dashboard.understock_threshold'))->toBe(0.6)
        ->and(config('dashboard.top_ruangan'))->toBe(12)
        ->and(config('dashboard.chart_days'))->toBe(7);
});
```

Catatan: fix method name typo — keduanya `defaultDashboardRoute`.

- [ ] **Step 2: Run test → FAIL (class/config belum ada)**

Run: `php artisan test --filter=DashboardRoleTest`
Expected: FAIL

- [ ] **Step 3: Implement RoleEnum + config + trait**

`app/Enums/RoleEnum.php` tambah:

```php
const RS = 'rs';

const LAUNDRY = 'laundry';
```

dan di `getDescription`:

```php
self::RS => 'Rumah Sakit',
self::LAUNDRY => 'Petugas Laundry',
```

`config/dashboard.php`:

```php
<?php

return [
    'understock_threshold' => 0.6,
    'top_ruangan' => 12,
    'chart_days' => 7,
];
```

`app/Concerns/ResolvesDashboardRole.php`:

```php
<?php

namespace App\Concerns;

use App\Enums\RoleEnum;

trait ResolvesDashboardRole
{
    public function defaultDashboardRoute(?string $role): string
    {
        return match ($role) {
            RoleEnum::ADMIN, RoleEnum::DEVELOPER => 'dashboard.admin',
            RoleEnum::RS => 'dashboard.rs',
            default => 'dashboard.laundry',
        };
    }
}
```

- [ ] **Step 4: Run test → PASS**

Run: `php artisan test --filter=DashboardRoleTest`
Expected: PASS

- [ ] **Step 5: Pint file yang disentuh**

Run: `vendor/bin/pint app/Enums/RoleEnum.php app/Concerns/ResolvesDashboardRole.php config/dashboard.php tests/Feature/DashboardRoleTest.php`
Expected: OK

---

### Task 2: Routes + Router + 3 invokable controller (view minimal)

**Files:**
- Modify: `routes/web.php` (ganti baris `DashboardController`)
- Create: `app/Http/Controllers/DashboardRouterController.php`
- Create: `app/Http/Controllers/Dashboard/AdminDashboardController.php`
- Create: `app/Http/Controllers/Dashboard/RsDashboardController.php`
- Create: `app/Http/Controllers/Dashboard/LaundryDashboardController.php`
- Create: `resources/views/dashboard/admin.blade.php` (placeholder KPI)
- Create: `resources/views/dashboard/rs.blade.php`
- Create: `resources/views/dashboard/laundry.blade.php`
- Modify: `tests/Feature/DashboardRoleTest.php`

**Interfaces:**
- Consumes: `ResolvesDashboardRole::defaultDashboardRoute`
- Produces: route names `dashboard`, `dashboard.admin`, `dashboard.rs`, `dashboard.laundry`; controller menerima `Illuminate\Http\Request` (atau invokable tanpa argumen + `auth()->user()`); view menerima `$title` string minimal.

- [ ] **Step 1: Tulis test routing redirect (fail dulu)**

Tambah ke `DashboardRoleTest.php`:

```php
beforeEach(function () {
    $this->admin = User::create([
        'name' => 'A', 'email' => 'a@example.com',
        'password' => Hash::make('password123'), 'role' => 'admin',
        'verified_at' => now(), 'email_verified_at' => now(),
    ]);
    $this->rsUser = User::create([
        'name' => 'R', 'email' => 'r@example.com',
        'password' => Hash::make('password123'), 'role' => 'rs',
        'verified_at' => now(), 'email_verified_at' => now(),
    ]);
    $this->laundry = User::create([
        'name' => 'L', 'email' => 'l@example.com',
        'password' => Hash::make('password123'), 'role' => 'laundry',
        'verified_at' => now(), 'email_verified_at' => now(),
    ]);
    $this->legacy = User::create([
        'name' => 'U', 'email' => 'u@example.com',
        'password' => Hash::make('password123'), 'role' => 'user',
        'verified_at' => now(), 'email_verified_at' => now(),
    ]);
});

it('routes each role default dashboard to matching content', function () {
    $this->actingAs($this->admin)->get('/dashboard')
        ->assertOk()->assertSee('Dashboard Admin', false);

    $this->actingAs($this->rsUser)->get('/dashboard')
        ->assertOk()->assertSee('Dashboard Rumah Sakit', false);

    $this->actingAs($this->laundry)->get('/dashboard')
        ->assertOk()->assertSee('Dashboard Petugas Laundry', false);
});

it('falls back legacy and unknown roles to laundry dashboard', function () {
    $this->actingAs($this->legacy)->get('/dashboard')
        ->assertOk()->assertSee('Dashboard Petugas Laundry', false);

    $user = User::create([
        'name' => 'X', 'email' => 'x@example.com',
        'password' => Hash::make('password123'), 'role' => 'gak-ada',
        'verified_at' => now(), 'email_verified_at' => now(),
    ]);
    $this->actingAs($user)->get('/dashboard')
        ->assertOk()->assertSee('Dashboard Petugas Laundry', false);
});

it('redirects wrong-role dashboard paths to default router', function () {
    $this->actingAs($this->laundry)->get('/dashboard/admin')
        ->assertRedirect(route('dashboard'));
    $this->actingAs($this->laundry)->get('/dashboard/rs')
        ->assertRedirect(route('dashboard'));
    $this->actingAs($this->admin)->get('/dashboard/rs')
        ->assertRedirect(route('dashboard'));
    $this->actingAs($this->rsUser)->get('/dashboard/laundry')
        ->assertRedirect(route('dashboard'));
    $this->actingAs($this->admin)->get('/dashboard/laundry')
        ->assertRedirect(route('dashboard'));
});

it('allows direct path for matching role', function () {
    $this->actingAs($this->admin)->get('/dashboard/admin')
        ->assertOk()->assertSee('Dashboard Admin', false);
    $this->actingAs($this->rsUser)->get('/dashboard/rs')
        ->assertOk()->assertSee('Dashboard Rumah Sakit', false);
    $this->actingAs($this->laundry)->get('/dashboard/laundry')
        ->assertOk()->assertSee('Dashboard Petugas Laundry', false);
});

it('requires auth for dashboard', function () {
    $this->get('/dashboard')->assertRedirect(route('login'));
});
```

- [ ] **Step 2: Run → FAIL (route lama / 404)**

Run: `php artisan test --filter=DashboardRoleTest`
Expected: FAIL

- [ ] **Step 3: Implement controllers + routes + placeholder views**

`routes/web.php` — ganti import & baris dashboard:

```php
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\LaundryDashboardController;
use App\Http\Controllers\Dashboard\RsDashboardController;
use App\Http\Controllers\DashboardRouterController;
// hapus use DashboardController lama setelah Task 5 pindah konten;
// sementara: jangan pakai DashboardController lagi di route.

Route::middleware(['auth', 'verified', 'access'])->group(function () {
    Route::get('dashboard', DashboardRouterController::class)->name('dashboard');
    Route::get('dashboard/admin', AdminDashboardController::class)->name('dashboard.admin');
    Route::get('dashboard/rs', RsDashboardController::class)->name('dashboard.rs');
    Route::get('dashboard/laundry', LaundryDashboardController::class)->name('dashboard.laundry');
    // Route::auto(...) lainnya tetap
});
```

`app/Http/Controllers/DashboardRouterController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Concerns\ResolvesDashboardRole;
use Illuminate\Http\RedirectResponse;

class DashboardRouterController extends Controller
{
    use ResolvesDashboardRole;

    public function __invoke(): RedirectResponse
    {
        return redirect()->route($this->defaultDashboardRoute(auth()->user()?->role));
    }
}
```

Base trait helper untuk cek role (pakai di 3 controller — boleh inline):

```php
// di tiap Admin/Rs/Laundry controller:
protected function authorizeRole(Request $request, string ...$roles): ?RedirectResponse
{
    $role = $request->user()?->role;
    if (! in_array($role, $roles, true)) {
        return redirect()
            ->route('dashboard')
            ->with('info', 'Halaman dashboard tidak sesuai peran Anda — dialihkan ke dashboard default.');
    }

    return null;
}
```

Admin (boleh admin|developer):

```php
<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        if (! in_array($request->user()?->role, [RoleEnum::ADMIN, RoleEnum::DEVELOPER], true)) {
            return redirect()->route('dashboard')
                ->with('info', 'Halaman dashboard tidak sesuai peran Anda — dialihkan ke dashboard default.');
        }

        return view('dashboard.admin', [
            'title' => 'Dashboard Admin',
        ]);
    }
}
```

RS:

```php
// role === RoleEnum::RS saja; else redirect+flash; return view('dashboard.rs', ['title' => 'Dashboard Rumah Sakit'])
```

Laundry:

```php
// role === RoleEnum::LAUNDRY saja; else redirect+flash; return view('dashboard.laundry', ['title' => 'Dashboard Petugas Laundry'])
```

Placeholder views (Task 3–5 ganti isi):

`resources/views/dashboard/admin.blade.php` (ringkas, pola opname detail):

```blade
<x-layouts::app :title="$title">
    <div class="content mt-4 lg:mt-0 space-y-4">
        <x-card label="{{ $title }}" icon="admin_panel_settings">
            <div class="col-span-12">
                <p class="text-sm text-on-surface-variant">Memuat data…</p>
            </div>
        </x-card>
    </div>
</x-layouts::app>
```

Serupa untuk `rs.blade.php` label `Dashboard Rumah Sakit` icon `local_hospital`, dan `laundry.blade.php` label `Dashboard Petugas Laundry` icon `local_laundry_service`.

Flash: layout biasa sudah render session flash — bila tidak, tetap `with('info', …)` cukup untuk test `assertRedirect`.

- [ ] **Step 4: Run test routing**

Run: `php artisan test --filter=DashboardRoleTest`
Expected: PASS

- [ ] **Step 5: Pastikan suite lama tidak rusak (route name `dashboard` tetap)**

Run: `php artisan test --filter=ExampleTest`
Expected: PASS

- [ ] **Step 6: Pint file baru**

Run: `vendor/bin/pint app/Http/Controllers/DashboardRouterController.php app/Http/Controllers/Dashboard config/dashboard.php routes/web.php tests/Feature/DashboardRoleTest.php`

---

### Task 3: Dashboard RS — sebaran bersih per ruangan + pending + alert

**Files:**
- Create: `app/Http/Controllers/Dashboard/RsDashboardController.php` (query) — **modify** Task 2
- Modify: `resources/views/dashboard/rs.blade.php`
- Modify: `tests/Feature/DashboardRoleTest.php`

**Interfaces:**
- Consumes: `User::scopeRs`, `LinenStatusEnum::BERSIH`, `DetailLinen`, `Ruangan`, `Schema::hasTable('pending')`, `config('dashboard.understock_threshold'|'top_ruangan')`
- Produces view data: `$sebaran` (collection rows: `ruangan_id`, `ruangan_nama`, `stok`, `par` nullable, `fill` float|null, `level` `ok|warn|danger`), `$pendingCount` int, `$alertCount` int, `$kpi` array `['bersih','kotor','pending','register']`.

Query sebaran (scoped):

```php
$sebaran = User::scopeRs(
    DetailLinen::query()->where('detail_status_linen', LinenStatusEnum::BERSIH),
    'detail_linen.detail_id_rs'
)
    ->join('ruangan', 'ruangan.ruangan_id', '=', 'detail_linen.detail_id_ruangan')
    ->selectRaw('detail_linen.detail_id_ruangan as ruangan_id, ruangan.ruangan_nama, COUNT(*) as stok')
    ->groupBy('detail_linen.detail_id_ruangan', 'ruangan.ruangan_nama')
    ->orderBy('stok')
    ->limit((int) config('dashboard.top_ruangan', 12))
    ->get()
    ->map(function ($row) {
        // par: total parstock RS user utk fill level kasar? 
        // Spec: par per ruangan tidak ada di pivot — par = null utk per-ruangan,
        // fill hanya jika ada par aggregate; else stok + "par —".
        $threshold = (float) config('dashboard.understock_threshold', 0.6);
        $stok = (int) $row->stok;
        $level = $stok === 0 ? 'danger' : ($stok < 5 ? 'warn' : 'ok');
        return (object) [
            'ruangan_id' => $row->ruangan_id,
            'ruangan_nama' => $row->ruangan_nama,
            'stok' => $stok,
            'par' => null,
            'fill' => null,
            'level' => $level,
        ];
    });
```

**Keputusan implement (tertulis eksplisit):** par per ruangan **tidak** ada di schema (`rs_dan_jenis` per jenis). Sebaran tampil **stok absolut**; alert = `stok === 0` (danger) atau stok < `ceil(0.05 * totalStokRS)` bila total > 0 (warn kasar) — **lebih sederhana:** alert = `stok === 0` + strip "Kurang" bila stok < threshold absolut `5`. Threshold config `0.6` dipakai **opsional fill** hanya jika nanti tambah par; untuk v1 UI: badge hijau stok ≥ 5, amber 1–4, merah 0.

Pending:

```php
$pendingCount = 0;
if (Schema::hasTable('pending')) {
    $pendingCount = User::scopeRs(
        DB::table('pending')->whereNull('pending_bersih_at'),
        'pending.pending_id_rs'
    )->count();
} else {
    $pendingCount = User::scopeRs(
        Outstanding::query()->whereNotNull('outstanding_pending_created_at')
            ->whereNull('outstanding_pending_updated_at') // bukan — lihat bawah
        ,
        'outstanding.outstanding_rs_scan'
    )->count();
}
```

**Keputusan pending tanpa tabel `pending`:** fallback outstanding = `whereNotNull('outstanding_pending_created_at')` (status hilang/lama di laundry). Lebih tepat: `ReportDetailPendingLinenController` pakai outstanding + `outstanding_pending_created_at` — pakai itu **selalu** (lebih murah, tabel ada), **skip tabel `pending`**:

```php
$pendingCount = User::scopeRs(
    Outstanding::query()->whereNotNull('outstanding_pending_created_at'),
    'outstanding.outstanding_rs_scan'
)->count();
```

KPI lain:

```php
$bersih = User::scopeRs(
    DetailLinen::query()->where('detail_status_linen', LinenStatusEnum::BERSIH),
    'detail_linen.detail_id_rs'
)->count();
$kotor = User::scopeRs(
    DetailLinen::query()->where('detail_status_linen', LinenStatusEnum::KOTOR),
    'detail_linen.detail_id_rs'
)->count();
$register = User::scopeRs(
    DetailLinen::query()->where('detail_status_linen', LinenStatusEnum::REGISTER),
    'detail_linen.detail_id_rs'
)->count();
```

- [ ] **Step 1: Test content RS (fail)**

```php
it('shows ruangan sebaran and pending on rs dashboard', function () {
    // seed minimal via RefreshDatabase: RS, ruangan, 1 linen BERSIH, 1 outstanding pending
    $this->actingAs($this->rsUser)->get('/dashboard/rs')
        ->assertOk()
        ->assertSee('Sebaran', false)
        ->assertSee('Pending', false)
        ->assertSee('Dashboard Rumah Sakit', false);
});
```

Tambah seed di test: `Rs::create`, `Ruangan::create`, `DetailLinen::create` status BERSIH, `Outstanding::create` dengan `outstanding_pending_created_at`.

- [ ] **Step 2: Run → FAIL**

- [ ] **Step 3: Implement query + view**

View `dashboard/rs.blade.php`:

- Breadcrumb opsional: `[['url' => route('dashboard.rs'), 'label' => 'Dashboard RS']]`
- `<x-stat-widget :items="[...]" />` — items Register / Kotor / Pending / Bersih (Indonesia)
- `<x-card label="Sebaran Linen Bersih per Ruangan" icon="grid_view">` — loop `$sebaran` grid card: nama ruangan, angka stok, badge level (`bg-green-100 text-green-800` / amber / red), teks `par —`
- Alert strip: daftar ruangan `stok === 0` — "Ruangan kurang stok: …"
- Link drill: `route('detail-linen.getTable')` text "Lihat Data Linen"
- Empty state bila `$sebaran` kosong: "Belum ada linen bersih tercatat."

- [ ] **Step 4: Run test RS → PASS**

- [ ] **Step 5: Pint**

---

### Task 4: Dashboard Laundry — pipeline KPI + antrean + warehouse + chart

**Files:**
- Modify: `app/Http/Controllers/Dashboard/LaundryDashboardController.php`
- Modify: `resources/views/dashboard/laundry.blade.php`
- Modify: `app/Charts/DashboardChart.php` (method baru `kotorVsBersih7Days`)
- Modify: `tests/Feature/DashboardRoleTest.php`

**Interfaces:**
- Consumes: `Outstanding` status proses `SCAN|QC|REGISTER|GUDANG|PACKING`, `Warehouse::utamaId()`, `BersihController` stats pattern, `Transaksi` KOTOR 7 hari, `bersih` table 7 hari, Larapex
- Produces: `$kpi` = `['register','kotor','pending','bersih','delivered','warehouse']`, `$antrean` = `['packing','delivery']`, `$gudangPerJenis`, `$chart` Larapex area 2 series

KPI:

```php
$register = User::scopeRs(
    DetailLinen::query()->where('detail_status_linen', LinenStatusEnum::REGISTER),
    'detail_linen.detail_id_rs'
)->count();
$kotor = User::scopeRs(
    DetailLinen::query()->where('detail_status_linen', LinenStatusEnum::KOTOR),
    'detail_linen.detail_id_rs'
)->count();
$pending = User::scopeRs(
    Outstanding::query()->whereNotNull('outstanding_pending_created_at'),
    'outstanding.outstanding_rs_scan'
)->count();
$bersih = User::scopeRs(
    DetailLinen::query()->where('detail_status_linen', LinenStatusEnum::BERSIH),
    'detail_linen.detail_id_rs'
)->count();
$delivered = User::scopeRs(
    DB::table('bersih')->whereDate('bersih_created_at', today()),
    'bersih.bersih_id_rs'
)->count();
$warehouse = User::scopeRs(
    Outstanding::query()
        ->where('outstanding_status_proses', 'GUDANG')
        ->where('outstanding_id_warehouse', \App\Models\Warehouse::utamaId()),
    'outstanding.outstanding_rs_scan'
)->count();
$antreanPacking = User::scopeRs(
    Outstanding::whereIn('outstanding_status_proses', ['SCAN', 'QC', 'REGISTER', 'GUDANG']),
    'outstanding.outstanding_rs_scan'
)->count();
$siapDelivery = User::scopeRs(
    Outstanding::query()->where('outstanding_status_proses', 'PACKING'),
    'outstanding.outstanding_rs_scan'
)->count();
```

Chart method di `DashboardChart`:

```php
public function kotorVsBersih(int $days = 7): LarapexChart
{
    $start = Carbon::today()->subDays($days - 1);
    $labels = [];
    $kotor = [];
    $bersih = [];
    for ($i = 0; $i < $days; $i++) {
        $d = Carbon::today()->subDays($days - 1 - $i);
        $labels[] = $d->format('d M');
        $kotor[] = \App\Models\Transaksi::where('transaksi_status', 'KOTOR')
            ->whereDate('transaksi_created_at', $d)->count();
        $bersih[] = \Illuminate\Support\Facades\DB::table('bersih')
            ->where('bersih_status', 'BERSIH')
            ->whereDate('bersih_created_at', $d)->count();
    }

    return (new LarapexChart)->areaChart()
        ->setTitle('Kotor vs Bersih (7 hari)')
        ->addData($kotor)->addData($bersih)
        ->setXAxis($labels)
        ->setColors(['#dc2626', '#16a34a'])
        ->setGrid()
        ->setMarkers(['#dc2626', '#16a34a'], 4, 6);
}
```

- [ ] **Step 1: Test content laundry (fail)** — assertSee `Pipeline`, `Warehouse`, `Antrean`, title
- [ ] **Step 2: FAIL**
- [ ] **Step 3: Implement controller + view + chart**
- [ ] **Step 4: PASS**
- [ ] **Step 5: Pint**

View laundry:

- `<x-stat-widget>` 5–6 item: Register, Kotor, Pending, Bersih, Delivered Hari Ini, Gudang (grid `grid-cols-2 lg:grid-cols-4` — 6 item wrap natural)
- `<x-card label="Antrean Packing / Siap Delivery">` dua angka + link `route('bersih.getTable')`
- `<x-card label="Warehouse" icon="warehouse">` total + top per jenis (loop `$gudangPerJenis` — query pola `WarehouseController` limit 12)
- `<x-chart-widget title="Kotor vs Bersih 7 Hari" :chart="$chart" />` di `@push('scripts')` bila widget sudah include script

---

### Task 5: Dashboard Admin — KPI global + sebaran + health + opname + system

**Files:**
- Modify: `app/Http/Controllers/Dashboard/AdminDashboardController.php`
- Modify: `resources/views/dashboard/admin.blade.php`
- Modify: `tests/Feature/DashboardRoleTest.php`
- Optional: pindah logic `DashboardController` lama ke Admin (users/notif/recent) — **hapus/rename** `DashboardController` bila tidak dipakai

**Interfaces:**
- Consumes: KPI sama seperti laundry **tanpa** `scopeRs` (global), `Rs`, `JenisLinen`, `ConfigLinen`, `Opname`, `User`, `Notification`, `DashboardChart`
- Produces: `$kpi`, `$sebaran`, `$health`, `$opname`, `$stats` (lama), `$recentUsers`, `$userChart`, `$notifChart`

KPI global (tanpa scopeRs):

```php
$register = DetailLinen::where('detail_status_linen', LinenStatusEnum::REGISTER)->count();
$kotor = DetailLinen::where('detail_status_linen', LinenStatusEnum::KOTOR)->count();
// … pending, bersih, warehouse, outstanding total
```

Health:

```php
$health = [
    'rs' => \App\Models\Rs::count(),
    'jenis_linen' => \App\Models\JenisLinen::count(),
    'config_linen' => \Illuminate\Support\Facades\DB::table('config_linen')->count(),
    'outstanding' => \App\Models\Outstanding::count(),
    'pending' => \App\Models\Outstanding::whereNotNull('outstanding_pending_created_at')->count(),
];
```

Opname:

```php
$opname = [
    'total' => \App\Models\Opname::count(),
    'selesai' => \App\Models\Opname::whereNotNull('opname_capture')->count(),
    'proses' => \App\Models\Opname::whereNull('opname_capture')->count(),
];
```

System overview: pindahkan isi `DashboardController` (`$stats`, `$recentUsers`, `$userChart`, `$notifChart`) ke Admin controller + view bagian bawah (table recent users + 2 chart lama).

- [ ] **Step 1: Test admin content** — assertSee `Dashboard Admin`, `System Overview`, `Data Kesehatan` / `Opname`, `Sebaran`
- [ ] **Step 2: FAIL**
- [ ] **Step 3: Implement** — KPI + sebaran + health + opname + system; **hapus route lama**; delete atau keep `DashboardController` (jika keep, **tidak dipakai route** — hapus file agar tidak dead code; pastikan tidak ada `use DashboardController` lain)
- [ ] **Step 4: PASS + grep tidak ada referensi `DashboardController` route**
- [ ] **Step 5: Pint**

---

### Task 6: Flash info + polish menu/breadcrumb + empty states

**Files:**
- Grep layout flash: `resources/views/layouts/**` pastikan `session('info')` tampil
- Modify: `config/menu.php` — **tidak diubah** (label Dashboard sudah benar)
- Modify views: empty state konsisten Indonesia

- [ ] **Step 1: Cek flash `info` dirender di layout app**
- [ ] **Step 2: Bila tidak, tambah strip alert kecil di ketiga view (atau layout bila pola global)**
- [ ] **Step 3: Empty state ketiga dashboard** — "Belum ada data…"
- [ ] **Step 4: Run DashboardRoleTest → PASS**

---

### Task 7: Full verification

- [ ] **Step 1: Pint semua file yang disentuh**

Run: `vendor/bin/pint app/Enums/RoleEnum.php app/Concerns/ResolvesDashboardRole.php app/Http/Controllers/DashboardRouterController.php app/Http/Controllers/Dashboard app/Charts/DashboardChart.php config/dashboard.php routes/web.php tests/Feature/DashboardRoleTest.php resources/views/dashboard`

- [ ] **Step 2: Full suite**

Run: `php artisan test`
Expected: semua PASS (termasuk 57+ persist opname/linen)

- [ ] **Step 3: Spot check manual via tinker/HTTP (opsional)** — actingAs per role

- [ ] **Step 4: Fix bila ada failure** — jangan skip

---

## Self-review (plan writer)

- Spec §4 RoleEnum → Task 1 ✓
- Spec §4.2 routes + redirect → Task 2 ✓
- Spec §4.3 controllers path → Task 2 ✓
- Spec §5.1 RS → Task 3 ✓ (pending = outstanding fallback; par per-ruangan tidak ada → stok absolut + badge)
- Spec §5.2 Laundry → Task 4 ✓
- Spec §5.3 Admin → Task 5 ✓
- Spec §6 UI components → tasks 3–5 views ✓
- Spec §7 menu tidak berubah → Task 6 ✓
- Spec §8 config → Task 1 ✓
- Spec §9 edge cases → pending schema guard dihindari pakai outstanding; par null ✓
- Spec §10 tests → tasks 1–5 + 7 ✓
- Spec §11 order ≈ task order ✓
- Placeholder scan: tidak ada TBD; query concrete ✓
- Type consistency: `defaultDashboardRoute(?string): string` konsisten Task 1–2 ✓
