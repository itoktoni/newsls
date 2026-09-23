<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/api/storage-image/{path}', function (string $path) {
    $fullPath = 'images/'.$path;

    if (! Storage::disk('public')->exists($fullPath)) {
        abort(404);
    }

    $file = Storage::disk('public')->get($fullPath);
    $mime = Storage::disk('public')->mimeType($fullPath);

    return new Response($file, 200, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=31536000',
        'Access-Control-Allow-Origin' => '*',
    ]);
})->where('path', '.*');
