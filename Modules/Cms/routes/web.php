<?php

use Illuminate\Support\Facades\Route;
use Modules\Cms\Http\Controllers\Api\MediaController;
use Modules\Cms\Http\Controllers\CategoryController;
use Modules\Cms\Http\Controllers\ContentController;
use Modules\Cms\Http\Controllers\FieldController;
use Modules\Cms\Http\Controllers\MenuController;
use Modules\Cms\Http\Controllers\SectionController;
use Modules\Cms\Http\Controllers\TagController;
use Modules\Cms\Http\Controllers\TypeController;

Route::auto('/cms/type', TypeController::class, ['name' => 'cms-type']);
Route::auto('/cms/field', FieldController::class, ['name' => 'field']);
Route::auto('/cms/section', SectionController::class, ['name' => 'section']);
Route::auto('/cms/content', ContentController::class, ['name' => 'content']);
Route::auto('/cms/category', CategoryController::class, ['name' => 'category']);
Route::auto('/cms/tag', TagController::class, ['name' => 'tag']);
Route::auto('/cms/menu', MenuController::class, ['name' => 'menu']);

Route::get('/cms/content/field-group-html/{id}', [ContentController::class, 'getSectionHtml'])->name('cms.section.html');

Route::prefix('api/media')->group(function () {
    Route::get('/', [MediaController::class, 'index']);
    Route::post('/upload', [MediaController::class, 'upload']);
    Route::delete('/{media}', [MediaController::class, 'destroy']);
});
