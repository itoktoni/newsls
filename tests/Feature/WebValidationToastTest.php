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
});

it('flashes a validation toast when the user create form is submitted with invalid data', function () {
    $this->actingAs($this->admin)
        ->from('/user/create')
        ->post('/user/create', [
            'name' => '',
        ])
        ->assertRedirect('/user/create')
        ->assertSessionHasErrors('name')
        ->assertSessionHas('toasts');

    $toasts = session('toasts');
    expect($toasts)->toBeArray()
        ->and($toasts[0]['type'])->toBe('danger');
});

it('flashes a success toast when the user create form is submitted with valid data', function () {
    $this->actingAs($this->admin)
        ->from('/user/create')
        ->post('/user/create', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'role' => 'user',
        ])
        ->assertRedirect()
        ->assertSessionHas('toasts');

    $toasts = session('toasts');
    expect($toasts)->toBeArray()
        ->and($toasts[0]['type'])->toBe('success');
});
