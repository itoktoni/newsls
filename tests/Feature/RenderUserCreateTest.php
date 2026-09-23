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

it('renders the user create page without swallowing toast markup into a script', function () {
    session()->put('toasts', [
        ['message' => 'Kolom name wajib diisi.', 'type' => 'danger', 'heading' => null, 'duration' => 5000],
    ]);

    $response = $this->actingAs($this->admin)->get('/user/create');
    $response->assertOk();

    $html = $response->getContent();
    file_put_contents(base_path('storage/framework/testing/page.html'), $html);

    expect(strpos($html, 'id="toast-container"'))->not->toBeFalse();
});
