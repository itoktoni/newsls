<?php

// Regresi: filter global _q di halaman dengan join (config-linen join
// detail_linen yang juga punya detail_rfid) wajib qualified — sebelumnya
// SQLSTATE 1052 Column 'detail_rfid' in WHERE is ambiguous.

use App\Models\ConfigLinen;
use App\Models\Rs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $this->admin = User::create([
        'name' => 'Admin Config',
        'email' => 'admin-config@bka.test',
        'password' => Hash::make('password'),
        'role' => 'admin',
        'verified_at' => now(),
        'email_verified_at' => now(),
    ]);

    $this->rs = Rs::create(['rs_nama' => 'RS Config', 'rs_code' => 'CFG'.strtoupper(uniqid()), 'rs_status' => 'DEDICATED', 'rs_aktif' => 1]);

    DB::table('config_linen')->insert([
        ['detail_rfid' => 'CFG-75-A', 'rs_id' => $this->rs->rs_id],
        ['detail_rfid' => 'CFG-XX-B', 'rs_id' => $this->rs->rs_id],
    ]);
});

it('filter _q detail_rfid di config-linen tidak ambiguous (1052)', function () {
    $html = $this->actingAs($this->admin)
        ->get('/config-linen/table?filters[detail_rfid][$contains]=75&_field=detail_rfid&_q=75&per_page=25')
        ->assertOk()
        ->getContent();

    expect($html)->toContain('CFG-75-A')
        ->and($html)->not->toContain('CFG-XX-B');
});
