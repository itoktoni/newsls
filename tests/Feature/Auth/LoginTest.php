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
    ]);
});

it('returns an access token on successful login', function () {
    $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ])->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('code', 200)
        ->assertJsonPath('name', 'Token')
        ->assertJsonStructure([
            'data' => ['api_token', 'id', 'name', 'email', 'role'],
        ])
        ->assertJsonPath('data.email', 'test@example.com');
});

it('returns 400 body code on wrong credentials', function () {
    $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ])->assertOk()
        ->assertJsonPath('status', false)
        ->assertJsonPath('code', 400)
        ->assertJsonPath('message', 'Login Gagal')
        ->assertJsonPath('errors.username.0', 'username yang dipilih tidak valid.');
});

it('returns 422 body code on missing fields', function () {
    $this->postJson('/api/login', [
        'email' => '',
        'password' => '',
    ])->assertOk()
        ->assertJsonPath('status', false)
        ->assertJsonPath('code', 422);
});

it('requires authentication for the profile endpoint', function () {
    $this->getJson('/api/me')->assertOk()
        ->assertJsonPath('status', false)
        ->assertJsonPath('code', 401);
});
