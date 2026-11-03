<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $uniq = strtoupper(uniqid());
    $this->rsA = \App\Models\Rs::create(['rs_nama' => 'RS Report A '.$uniq, 'rs_code' => 'RPA'.$uniq, 'rs_status' => \App\Enums\RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->rsB = \App\Models\Rs::create(['rs_nama' => 'RS Report B '.$uniq, 'rs_code' => 'RPB'.$uniq, 'rs_status' => \App\Enums\RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->reportUser = User::create(['name' => 'Report Only', 'email' => 'reportonly-'.$uniq.'@bka.test', 'password' => Hash::make('password'), 'role' => 'user', 'verified_at' => now(), 'email_verified_at' => now()]);
    DB::table('rs_dan_user')->insert(['user_id' => $this->reportUser->id, 'rs_id' => $this->rsA->rs_id]);
});

it('role user dialihkan dari dashboard ke report tanpa loop', function () {
    $this->actingAs($this->reportUser)->get('/dashboard')
        ->assertRedirect(route('report-rekap-kotor.getTable'));
});

it('role user hanya boleh membuka halaman report', function () {
    $this->actingAs($this->reportUser)->get('/report-rekap-kotor/table')->assertOk();
    $this->actingAs($this->reportUser)->get('/rs/table')->assertForbidden();
    $this->actingAs($this->reportUser)->get('/dashboard/admin')->assertForbidden();
    $this->actingAs($this->reportUser)->get('/transaksi/table')->assertForbidden();
});

it('role user tidak boleh membuka invoice, settings, dan cms', function () {
    $this->actingAs($this->reportUser)->get('/report-invoice/table')->assertForbidden();
    $this->actingAs($this->reportUser)->get('/settings/profile')->assertForbidden();
    $this->actingAs($this->reportUser)->get('/cms/content/table')->assertForbidden();
});

it('role user hanya boleh mengakses rs yang dipetakan', function () {
    $this->actingAs($this->reportUser)
        ->get("/report-rekap-kotor/print?rs_id={$this->rsA->rs_id}")
        ->assertOk();
    $this->actingAs($this->reportUser)
        ->get("/report-rekap-kotor/print?rs_id={$this->rsB->rs_id}")
        ->assertForbidden();
});
