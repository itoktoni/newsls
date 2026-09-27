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
    // dan totals. CRUD user via API SENGAJA tidak ada (keputusan keamanan —
    // lihat komentar di routes/api.php); kelola user hanya via web /user/*.
    expect($apiUris)->toBe([
        'api/cms/content-type/{slug}',
        'api/cms/content-type/{slug}/blueprint',
        'api/cms/content/{content}',
        'api/configuration',
        'api/content/{slug?}',
        'api/delivery',
        'api/delivery/{code}',
        'api/detail/rfid',
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
        'api/opname/{id}',
        'api/opname/{id}/detail',
        'api/packing',
        'api/packing/{code}',
        'api/push-subscribe',
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
