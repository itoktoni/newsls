<?php

use App\Support\DashboardCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::flush();
});

it('caches payload under scope segment and reuses hit', function () {
    $calls = 0;
    $closure = function () use (&$calls) {
        $calls++;

        return ['total' => 42];
    };

    $first = DashboardCache::remember('admin:all', 'kpi', $closure);
    $second = DashboardCache::remember('admin:all', 'kpi', $closure);

    expect($first)->toBe(['total' => 42])
        ->and($second)->toBe(['total' => 42])
        ->and($calls)->toBe(1);
});

it('busts all dashboard keys after flush', function () {
    $value = DashboardCache::remember('admin:all', 'kpi', fn () => 'before');
    expect($value)->toBe('before');

    DashboardCache::flush();

    $after = DashboardCache::remember('admin:all', 'kpi', fn () => 'after');
    expect($after)->toBe('after');
});

it('scopes keys per role so different roles do not share cache', function () {
    $a = DashboardCache::remember('rs:all', 'kpi', fn () => 'rs-value');
    $b = DashboardCache::remember('laundry:all', 'kpi', fn () => 'laundry-value');

    expect($a)->toBe('rs-value')
        ->and($b)->toBe('laundry-value')
        ->and(DashboardCache::key('rs:all', 'kpi'))
        ->not->toBe(DashboardCache::key('laundry:all', 'kpi'));
});

it('uses role all scope for guest', function () {
    expect(DashboardCache::userScope())->toBe('guest:all');
});

it('exposes cache ttl from dashboard config', function () {
    expect(config('dashboard.cache_ttl'))->toBe(60);
});
