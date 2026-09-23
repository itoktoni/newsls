<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
        'role' => 'user',
        'verified_at' => now(),
    ]);

    $this->token = $this->user->createToken('test-token')->plainTextToken;
});

it('requires authentication for profile endpoints', function () {
    $this->getJson('/api/me')->assertOk()
        ->assertJsonPath('status', false)
        ->assertJsonPath('code', 401);

    $this->putJson('/api/me', ['name' => 'X', 'phone' => '081234567890'])->assertOk()
        ->assertJsonPath('status', false)
        ->assertJsonPath('code', 401);
});

it('returns the authenticated user profile', function () {
    $this->withHeader('Authorization', 'Bearer '.$this->token)
        ->getJson('/api/me')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.user.id', $this->user->id)
        ->assertJsonPath('data.user.email', 'test@example.com');
});

it('updates the authenticated user profile', function () {
    $this->withHeader('Authorization', 'Bearer '.$this->token)
        ->putJson('/api/me', ['name' => 'Updated Name', 'phone' => '081234567890'])
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.user.name', 'Updated Name')
        ->assertJsonPath('data.user.id', $this->user->id);

    expect($this->user->fresh()->name)->toBe('Updated Name');
});

it('revokes the token on logout', function () {
    $this->withHeader('Authorization', 'Bearer '.$this->token)
        ->postJson('/api/logout')
        ->assertOk();

    expect($this->user->tokens()->count())->toBe(0);
});
