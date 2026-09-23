<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/**
 * ponytail: test ini SENGAJA memakai disk 'public' asli, bukan Storage::fake('public'),
 * karena UsersController::deleteUserFile() menghapus lewat
 * storage_path('app/public/'.$path) + unlink() — fake disk tidak terlihat oleh unlink()
 * sehingga assertion "file terhapus" jadi palsu. Sisa file dibersihkan di afterEach().
 */
beforeEach(function () {
    $this->admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
        'verified_at' => now(),
    ]);

    $this->storedFiles = [];
});

afterEach(function () {
    foreach ($this->storedFiles as $path) {
        if (filled($path)) {
            Storage::disk('public')->delete($path);
        }
    }
});

it('stores the uploaded avatar on the public disk when creating a user', function () {
    $this->actingAs($this->admin)
        ->from('/user/create')
        ->post('/user/create', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'role' => 'user',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ])
        ->assertRedirect();

    $user = User::where('email', 'new@example.com')->firstOrFail();
    $this->storedFiles[] = $user->avatar;

    expect($user->avatar)->toStartWith('users/')
        ->and(Storage::disk('public')->exists($user->avatar))->toBeTrue()
        ->and($user->avatar_url)->toBe('/storage/'.$user->avatar)
        // regresi: dulu file mendarat di disk 'local' (storage/app/private/public/users/)
        ->and(Storage::disk('local')->exists('public/'.$user->avatar))->toBeFalse();

    // End-to-end: /storage/* hanya hidup kalau symlink public/storage ada. Tanpa ini file
    // tersimpan benar tapi browser tetap 404 — jalankan `php artisan storage:link`.
    $this->assertTrue(
        is_file(public_path('storage/'.$user->avatar)),
        'public/storage belum ada (jalankan: php artisan storage:link) sehingga '
        .$user->avatar_url.' akan 404.'
    );
});

it('replaces the previous avatar file when updating a user', function () {
    Storage::disk('public')->put('users/previous.png', 'stale');

    $user = User::create([
        'name' => 'Target User',
        'email' => 'target@example.com',
        'password' => Hash::make('password123'),
        'role' => 'user',
        'avatar' => 'users/previous.png',
    ]);

    $this->actingAs($this->admin)
        ->from('/user/update/'.$user->id)
        ->post('/user/update/'.$user->id, [
            'name' => 'Target User',
            'email' => 'target@example.com',
            'role' => 'user',
            'avatar' => UploadedFile::fake()->image('replacement.jpg'),
        ])
        ->assertRedirect();

    $user->refresh();
    $this->storedFiles[] = $user->avatar;

    expect($user->avatar)->toStartWith('users/')
        ->and($user->avatar)->not->toBe('users/previous.png')
        ->and(Storage::disk('public')->exists($user->avatar))->toBeTrue()
        ->and(Storage::disk('public')->exists('users/previous.png'))->toBeFalse();
});

it('clears the avatar when the remove checkbox is submitted', function () {
    Storage::disk('public')->put('users/removable.png', 'stale');

    $user = User::create([
        'name' => 'Target User',
        'email' => 'target@example.com',
        'password' => Hash::make('password123'),
        'role' => 'user',
        'avatar' => 'users/removable.png',
    ]);

    $this->actingAs($this->admin)
        ->from('/user/update/'.$user->id)
        ->post('/user/update/'.$user->id, [
            'name' => 'Target User',
            'email' => 'target@example.com',
            'role' => 'user',
            'remove_avatar' => '1',
        ])
        ->assertRedirect();

    expect($user->fresh()->avatar)->toBeNull()
        ->and(Storage::disk('public')->exists('users/removable.png'))->toBeFalse();
});

it('rejects an oversized avatar with an inline validation error', function () {
    Storage::disk('public')->put('users/kept.png', 'stale');

    $user = User::create([
        'name' => 'Target User',
        'email' => 'target@example.com',
        'password' => Hash::make('password123'),
        'role' => 'user',
        'avatar' => 'users/kept.png',
    ]);

    $this->actingAs($this->admin)
        ->from('/user/update/'.$user->id)
        ->post('/user/update/'.$user->id, [
            'name' => 'Target User',
            'email' => 'target@example.com',
            'role' => 'user',
            'avatar' => UploadedFile::fake()->image('big.jpg')->size(4096),
        ])
        ->assertRedirect('/user/update/'.$user->id)
        ->assertSessionHasErrors('avatar');

    expect($user->fresh()->avatar)->toBe('users/kept.png')
        ->and(Storage::disk('public')->exists('users/kept.png'))->toBeTrue();

    $this->storedFiles[] = 'users/kept.png';
});
