<?php

use Illuminate\Support\Facades\Route;
use Modules\Cms\Http\Controllers\Api\CmsController;

Route::get('/cms/content/{content}', [CmsController::class, 'show']);
Route::get('/cms/content-type/{slug}', [CmsController::class, 'indexByType']);
Route::get('/cms/content-type/{slug}/blueprint', [CmsController::class, 'getBlueprintSchema']);
