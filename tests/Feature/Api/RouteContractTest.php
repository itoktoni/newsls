<?php

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

it('exposes exactly the expected API route surface', function () {
    $apiUris = collect(RouteFacade::getRoutes())
        ->map(fn (Route $route) => $route->uri())
        ->filter(fn (string $uri) => str_starts_with($uri, 'api/'))
        ->unique()
        ->sort()
        ->values()
        ->all();

    // ponytail: kontrak diperbarui — surface API mencakup CMS, transaksi RFID
    // (kotor/retur/rewash/register/packing/delivery), master data rs, opname,
    // totals, dan users. `api/users/boot` sudah hilang karena UsersController::boot() dihapus.
    expect($apiUris)->toBe([
        'api/cms/content-type/{slug}',
        'api/cms/content-type/{slug}/blueprint',
        'api/cms/content/{content}',
        'api/configuration',
        'api/content/{slug?}',
        'api/delivery',
        'api/delivery/{code}',
        'api/download/{rsid}',
        'api/grouping/{rfid}',
        'api/kotor',
        'api/list/delivery/{rsid}',
        'api/list/packing/{rsid}',
        'api/login',
        'api/logout',
        'api/me',
        'api/media',
        'api/media/upload',
        'api/media/{media}',
        'api/opname',
        'api/opname/capture/{id}',
        'api/opname/sync',
        'api/opname/{id}/detail',
        'api/packing',
        'api/packing/{code}',
        'api/register',
        'api/register/config',
        'api/retur',
        'api/rewash',
        'api/rs',
        'api/rs/{rsid}',
        'api/rs_lite',
        'api/total/bersih/{rsid}/{ruangan}/{jenis}/{transaksi}',
        'api/total/delivery/{rsid}/{status}',
        'api/total/outstanding/{rsid}/{ruangan}/{jenis}/{transaksi}',
        'api/transaksi/{type}',
        'api/users',
        'api/users/create',
        'api/users/delete',
        'api/users/delete/{id}',
        'api/users/export-excel',
        'api/users/show/{id}',
        'api/users/table',
        'api/users/update/{id}',
    ]);
});

it('does not expose routable controller lifecycle methods as endpoints', function () {
    // ponytail: Route::auto() memetakan SEMUA public method jadi route. Method publik yang
    // kebetulan bernama boot()/traitPostX() akan bocor jadi endpoint (mis. user/boot) — alias
    // trait karena itu wajib `private` (lihat JenisLinenController & UsersController).
    $leaked = collect(RouteFacade::getRoutes())
        ->map(fn (Route $route) => $route->uri())
        ->filter(fn (string $uri) => str_ends_with($uri, 'boot') || str_contains($uri, 'trait-post'))
        ->values()
        ->all();

    expect($leaked)->toBe([]);
});
