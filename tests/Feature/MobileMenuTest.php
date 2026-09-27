<?php

use App\Models\MobileMenu;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// PERSIST — jangan RefreshDatabase (test lain memakai data yang sama di test_bka).
// Kontrak: login mengembalikan `menu[]` (nama form mobile), diatur per user
// via pivot mobile_menu_dan_user; pivot kosong = semua menu aktif.

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $this->user = User::updateOrCreate(
        ['email' => 'menu@bka.test'],
        ['name' => 'User Menu', 'password' => Hash::make('password'), 'role' => 'user', 'verified_at' => now(), 'email_verified_at' => now()]
    );
    DB::table('mobile_menu_dan_user')->where('user_id', $this->user->id)->delete();
});

it('login mengembalikan semua menu aktif bila user tanpa mapping', function () {
    $res = $this->postJson('/api/login', ['username' => 'menu@bka.test', 'password' => 'password'])
        ->assertOk()->assertJson(['status' => true, 'name' => 'Token']);

    $expected = MobileMenu::where('mobile_menu_aktif', true)
        ->orderBy('mobile_menu_urut')->orderBy('mobile_menu_id')
        ->pluck('mobile_menu_nama')->all();

    expect($expected)->not->toBeEmpty()
        ->and($res->json('data.menu'))->toBe($expected);
});

it('login hanya mengembalikan menu yang dipilih di form user', function () {
    $picked = MobileMenu::where('mobile_menu_aktif', true)
        ->orderBy('mobile_menu_urut')->orderBy('mobile_menu_id')
        ->take(2)->get();

    User::syncMobileMenus($this->user->id, $picked->pluck('mobile_menu_id')->all());

    $res = $this->postJson('/api/login', ['username' => 'menu@bka.test', 'password' => 'password'])
        ->assertOk()->assertJson(['status' => true]);

    expect($res->json('data.menu'))->toBe($picked->pluck('mobile_menu_nama')->all());
});

it('sync menu mengabaikan id yang tidak ada di master', function () {
    User::syncMobileMenus($this->user->id, [999999, 'bukan-angka']);

    expect(User::menuIdsFor($this->user->id))->toBe([])
        ->and(User::menuNamesFor($this->user->id))->not->toBeEmpty();
});
