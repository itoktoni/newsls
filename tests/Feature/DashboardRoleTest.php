<?php

use App\Concerns\ResolvesDashboardRole;
use App\Enums\RoleEnum;
use App\Models\DetailLinen;
use App\Models\Outstanding;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

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

it('maps roles to default dashboard route names', function () {
    $r = new class
    {
        use ResolvesDashboardRole;
    };

    expect($r->defaultDashboardRoute(RoleEnum::ADMIN))->toBe('dashboard.admin')
        ->and($r->defaultDashboardRoute(RoleEnum::DEVELOPER))->toBe('dashboard.admin')
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

it('routes each role default dashboard to matching content', function () {
    $this->actingAs($this->admin)->get('/dashboard')
        ->assertRedirect(route('dashboard.admin'));
    $this->actingAs($this->admin)->get(route('dashboard.admin'))
        ->assertOk()->assertSee('Dashboard Admin', false);

    $this->actingAs($this->rsUser)->get('/dashboard')
        ->assertRedirect(route('dashboard.rs'));
    $this->actingAs($this->rsUser)->get(route('dashboard.rs'))
        ->assertOk()->assertSee('Dashboard Rumah Sakit', false);

    $this->actingAs($this->laundry)->get('/dashboard')
        ->assertRedirect(route('dashboard.laundry'));
    $this->actingAs($this->laundry)->get(route('dashboard.laundry'))
        ->assertOk()->assertSee('Dashboard Petugas Laundry', false);
});

it('falls back legacy and unknown roles to laundry dashboard', function () {
    $this->actingAs($this->legacy)->get('/dashboard')
        ->assertRedirect(route('dashboard.laundry'));

    $user = User::create([
        'name' => 'X', 'email' => 'x@example.com',
        'password' => Hash::make('password123'), 'role' => 'gak-ada',
        'verified_at' => now(), 'email_verified_at' => now(),
    ]);
    $this->actingAs($user)->get('/dashboard')
        ->assertRedirect(route('dashboard.laundry'));
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
    // Flash toast info ter-set di session untuk redirect.
    expect(session('toasts'))->not->toBeNull();
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

it('shows ruangan sebaran and pending on rs dashboard', function () {
    $rs = Rs::create(['rs_nama' => 'RS Dash', 'rs_status' => 'DEDICATED', 'rs_aktif' => 1]);
    $ruangan = Ruangan::create(['ruangan_nama' => 'Ruang Mawar']);
    User::syncRs($this->rsUser->id, [$rs->rs_id]);

    DetailLinen::create([
        'detail_rfid' => 'DASH_1',
        'detail_id_rs' => $rs->rs_id,
        'detail_id_ruangan' => $ruangan->ruangan_id,
        'detail_status_linen' => 'BERSIH',
    ]);
    Outstanding::create([
        'outstanding_rfid' => 'DASH_OUT_1',
        'outstanding_rs_scan' => $rs->rs_id,
        'outstanding_pending_created_at' => now(),
    ]);

    $this->actingAs($this->rsUser)->get('/dashboard/rs')
        ->assertOk()
        ->assertSee('Sebaran', false)
        ->assertSee('Pending', false)
        ->assertSee('Ruang Mawar', false)
        ->assertSee('Dashboard Rumah Sakit', false);
});

it('shows pipeline antrean and warehouse on laundry dashboard', function () {
    $this->actingAs($this->laundry)->get('/dashboard/laundry')
        ->assertOk()
        ->assertSee('Pipeline', false)
        ->assertSee('Warehouse', false)
        ->assertSee('Antrean Packing', false)
        ->assertSee('Kotor vs Bersih', false)
        ->assertSee('Dashboard Petugas Laundry', false);
});

it('shows kpi health opname and system overview on admin dashboard', function () {
    $this->actingAs($this->admin)->get('/dashboard/admin')
        ->assertOk()
        ->assertSee('KPI Global', false)
        ->assertSee('Data Kesehatan', false)
        ->assertSee('Opname', false)
        ->assertSee('System Overview', false)
        ->assertSee('Kotor vs Bersih', false)
        ->assertSee('Status Linen', false)
        ->assertDontSee('User Registrations', false)
        ->assertSee('Dashboard Admin', false);
});

it('allows developer role on admin dashboard', function () {
    $dev = User::create([
        'name' => 'Dev', 'email' => 'dev@example.com',
        'password' => Hash::make('password123'), 'role' => 'developer',
        'verified_at' => now(), 'email_verified_at' => now(),
    ]);
    $this->actingAs($dev)->get('/dashboard/admin')
        ->assertOk()->assertSee('Dashboard Admin', false);
});
