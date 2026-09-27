<?php

use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Facades\Route;

it('registers the In vs Out report and places it after Kotor vs Bersih', function () {
    $routeNames = collect(Route::getRoutes())
        ->map(fn (IlluminateRoute $route) => $route->getName())
        ->filter()
        ->all();

    expect($routeNames)->toContain(
        'report-in-vs-out.getTable',
        'report-in-vs-out.getPrint',
        'report-in-vs-out.getExportExcel',
    );

    $items = collect(config('menu.sidebar'))
        ->flatMap(fn (array $section) => $section['items'] ?? []);

    $kotorIndex = $items->search(fn (array $item) => $item['route'] === 'report-kotor-vs-bersih.getTable');
    $inOutIndex = $items->search(fn (array $item) => $item['route'] === 'report-in-vs-out.getTable');

    expect($kotorIndex)->not->toBeFalse();
    expect($inOutIndex)->toBe($kotorIndex + 1);
});
