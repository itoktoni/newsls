<?php

use App\Models\Rs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::create([
        'name' => 'U', 'email' => 'u@example.com',
        'password' => Hash::make('password123'), 'role' => 'user',
        'verified_at' => now(), 'email_verified_at' => now(),
    ]);
    $this->admin = User::create([
        'name' => 'A', 'email' => 'a@example.com',
        'password' => Hash::make('password123'), 'role' => 'admin',
        'verified_at' => now(), 'email_verified_at' => now(),
    ]);
});

it('lets user view dashboard rs, own tables and reports except invoice', function () {
    $this->actingAs($this->user)->get(route('dashboard.rs'))->assertOk();
    $this->actingAs($this->user)->get(route('detail-linen.getTable'))->assertOk();
    $this->actingAs($this->user)->get(route('transaksi.getTable'))->assertOk();
    $this->actingAs($this->user)->get(route('rs.getTable'))->assertOk();
    $this->actingAs($this->user)->get(route('report-rekap-bersih.getTable'))->assertOk();
});

it('blocks user from write actions on master and linen data', function () {
    $rs = Rs::create(['rs_nama' => 'RS X', 'rs_status' => 'DEDICATED', 'rs_aktif' => 1]);

    $this->actingAs($this->user)->get(route('detail-linen.getCreate'))->assertForbidden();
    $this->actingAs($this->user)->post(route('detail-linen.postCreate'), [])->assertForbidden();
    $this->actingAs($this->user)->post(route('detail-linen.postDelete'), [])->assertForbidden();
    $this->actingAs($this->user)->get(route('rs.getCreate'))->assertForbidden();
    $this->actingAs($this->user)->post(route('rs.postUpdate', $rs->rs_id), [])->assertForbidden();
    $this->actingAs($this->user)->get(route('opname.capture', 'DUMMY'))->assertForbidden();
});

it('blocks user from audit, users, settings, warehouse and invoice', function () {
    $this->actingAs($this->user)->get(route('activity-log.getTable'))->assertForbidden();
    $this->actingAs($this->user)->get(route('user.getTable'))->assertForbidden();
    $this->actingAs($this->user)->get(route('settings.website'))->assertForbidden();
    $this->actingAs($this->user)->get(route('settings.env'))->assertForbidden();
    $this->actingAs($this->user)->get(route('warehouse.getTable'))->assertForbidden();
    $this->actingAs($this->user)->get(route('report-invoice.getTable'))->assertForbidden();
    $this->actingAs($this->user)->get(route('report-invoice.getPrint'))->assertForbidden();
});

it('keeps admin access to invoice and settings', function () {
    $this->actingAs($this->admin)->get(route('report-invoice.getTable'))->assertOk();
    $this->actingAs($this->admin)->get(route('activity-log.getTable'))->assertOk();
});

it('hides restricted menus from user sidebar but keeps them for admin', function () {
    $this->actingAs($this->user)->get(route('dashboard.rs'))
        ->assertOk()
        ->assertDontSee('Pengguna &amp; Audit', false)
        ->assertDontSee('Master Data', false)
        ->assertDontSee('Manajemen Linen', false)
        ->assertDontSee('Invoice', false)
        ->assertDontSee('Environment', false)
        ->assertSee('Rekap Bersih', false);

    $this->actingAs($this->admin)->get(route('dashboard.admin'))
        ->assertOk()
        ->assertSee('Pengguna &amp; Audit', false)
        ->assertSee('Master Data', false)
        ->assertSee('Manajemen Linen', false)
        ->assertSee('Invoice', false);
});
