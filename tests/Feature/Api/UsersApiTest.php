<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
        'verified_at' => now(),
    ]);

    $this->token = $this->admin->createToken('test-token')->plainTextToken;
});

it('requires authentication for users routes', function () {
    $this->getJson('/api/users/table')->assertOk()
        ->assertJsonPath('status', false)
        ->assertJsonPath('code', 401);

    $this->postJson('/api/users/create', [
        'name' => 'New User',
        'email' => 'new@example.com',
        'password' => 'password123',
    ])->assertOk()
        ->assertJsonPath('status', false)
        ->assertJsonPath('code', 401);
});

it('lists users', function () {
    User::create([
        'name' => 'Other User',
        'email' => 'other@example.com',
        'password' => Hash::make('password123'),
        'role' => 'user',
    ]);

    $this->withToken($this->token)
        ->getJson('/api/users/table')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonStructure([
            'data' => ['data', 'fields'],
        ]);
});

it('creates a user', function () {
    $response = $this->withToken($this->token)
        ->postJson('/api/users/create', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'role' => 'user',
        ]);

    $response->assertOk()
        ->assertJson(['status' => true]);

    $user = User::where('email', 'new@example.com')->first();

    expect($user)->not->toBeNull()
        ->and(Hash::check('password123', $user->password))->toBeTrue();
});

it('shows a user', function () {
    $target = User::create([
        'name' => 'Target User',
        'email' => 'target@example.com',
        'password' => Hash::make('password123'),
        'role' => 'user',
    ]);

    $this->withToken($this->token)
        ->getJson('/api/users/show/'.$target->id)
        ->assertOk()
        ->assertJson([
            'status' => true,
            'data' => ['id' => $target->id, 'email' => 'target@example.com'],
        ]);
});

it('updates a user', function () {
    $target = User::create([
        'name' => 'Target User',
        'email' => 'target@example.com',
        'password' => Hash::make('password123'),
        'role' => 'user',
    ]);

    $this->withToken($this->token)
        ->postJson('/api/users/update/'.$target->id, [
            'name' => 'Renamed User',
            'email' => 'target@example.com',
        ])->assertOk()
        ->assertJson(['status' => true]);

    expect($target->fresh()->name)->toBe('Renamed User');
});

it('deletes users', function () {
    $target = User::create([
        'name' => 'Target User',
        'email' => 'target@example.com',
        'password' => Hash::make('password123'),
        'role' => 'user',
    ]);

    $this->withToken($this->token)
        ->postJson('/api/users/delete', [
            'ids' => [$target->id],
        ])->assertOk()
        ->assertJson(['status' => true]);

    expect(User::find($target->id))->toBeNull();
});
