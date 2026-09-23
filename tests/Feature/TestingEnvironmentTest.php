<?php

use Illuminate\Support\Facades\DB;

/**
 * Kontrak testing (lihat komentar di phpunit.xml):
 * suite HARUS jalan di isolated DB dan APP_ENV=testing.
 *
 * Awal: sqlite :memory:. Sekarang: mariadb test_bka (request: pakai mariadb test_bka
 * bukan bka produksi, bukan :memory:). Guard ini mencegah RefreshDatabase
 * migrate:fresh menghapus bka.
 */
it('runs the suite on an isolated mariadb test_bka database', function () {
    expect(config('database.default'))->toBe('mariadb')
        ->and(DB::connection()->getDriverName())->toBeIn(['mysql', 'mariadb'])
        ->and(DB::connection()->getDatabaseName())->toBe('test_bka')
        ->and(app()->environment())->toBe('testing');
});

it('resolves the dotenv repository to the mariadb test_bka database', function () {
    expect(env('DB_CONNECTION'))->toBe('mariadb')
        ->and(env('DB_DATABASE'))->toBe('test_bka');
});
