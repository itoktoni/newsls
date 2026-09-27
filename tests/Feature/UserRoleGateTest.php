<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

/*
 * Keamanan modul user:
 * - API /api/users/* TIDAK didaftarkan (lihat routes/api.php) — token apa pun
 *   yang memanggilnya dapat envelope 404, bukan data.
 * - Web /user/* terkunci role admin/developer via BasePolicy::before() +
 *   config/permision.php (token/sesi role lain = 403).
 */

function gateUser(string $role = 'user'): User
{
    return User::create([
        'name' => 'Gate '.uniqid(),
        'email' => 'gate-'.uniqid().'@bka.test',
        'password' => Hash::make('password123'),
        'role' => $role,
        'verified_at' => now(),
    ]);
}

it('mengembalikan 404 untuk semua endpoint api/users yang sudah dihapus', function () {
    // ponytail: envelope API selalu HTTP 200 — tidak-adanya route dibaca dari
    // body.status=false + body.code=404 (bootstrap/app.php).
    $token = gateUser('admin')->createToken('t')->plainTextToken;

    foreach (['/api/users/table', '/api/users', '/api/users/show/1'] as $url) {
        $this->withToken($token)->getJson($url)->assertOk()
            ->assertJsonPath('status', false)->assertJsonPath('code', 404);
    }

    $this->withToken($token)->postJson('/api/users/create', [
        'name' => 'Hacker', 'email' => 'hacker@bka.test', 'password' => 'password123', 'role' => 'admin',
    ])->assertOk()->assertJsonPath('status', false)->assertJsonPath('code', 404);

    // tidak ada user yang tercipta lewat jalur ini
    expect(User::where('email', 'hacker@bka.test')->exists())->toBeFalse();
});

it('menolak user biasa di web /user/*', function () {
    $user = gateUser('user');

    $this->actingAs($user)->get('/user/table')->assertForbidden();
    $this->actingAs($user)->get('/user/create')->assertForbidden();
});

it('tetap mengizinkan admin dan developer di web /user/*', function () {
    foreach (['admin', 'developer'] as $role) {
        $this->actingAs(gateUser($role))->get('/user/table')->assertOk();
    }
});
