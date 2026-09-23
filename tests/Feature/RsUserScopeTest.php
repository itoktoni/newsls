<?php

use App\Models\Rs;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
        'verified_at' => now(),
    ]);
});

it('scopes rs options and guards rs access per user', function () {
    $user = User::create([
        'name' => 'RS User',
        'email' => 'rs@example.com',
        'password' => Hash::make('password123'),
        'role' => 'user',
        'verified_at' => now(),
    ]);

    $rsA = Rs::create(['rs_nama' => 'RS A']);
    $rsB = Rs::create(['rs_nama' => 'RS B']);
    $rsC = Rs::create(['rs_nama' => 'RS C']);

    User::syncRs($user->id, [$rsA->rs_id, $rsB->rs_id]);

    $ids = User::rsIdsFor($user->id);
    sort($ids);
    expect($ids)->toBe(collect([$rsA->rs_id, $rsB->rs_id])->sort()->values()->all());

    $this->actingAs($user);

    $allowed = User::allowedRsIds();
    sort($allowed);
    expect($allowed)->toBe(collect([$rsA->rs_id, $rsB->rs_id])->sort()->values()->all());

    $options = array_keys(User::rsOptions());
    sort($options);
    expect($options)->toBe(collect([$rsA->rs_id, $rsB->rs_id])->sort()->values()->all());

    // Guard: RS di luar hak ditolak 403.
    try {
        User::ensureRsAccess($rsC->rs_id);
        $this->fail('ensureRsAccess should abort for RS outside user scope');
    } catch (Symfony\Component\HttpKernel\Exception\HttpException $e) {
        expect($e->getStatusCode())->toBe(403);
    }

    // Filter kosong = batasi ke RS milik user.
    $filtered = User::applyRsFilter(Rs::query(), 'rs.rs_id', null)->pluck('rs_id')->all();
    sort($filtered);
    expect($filtered)->toBe(collect([$rsA->rs_id, $rsB->rs_id])->sort()->values()->all());
});
