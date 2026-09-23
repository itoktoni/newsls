<?php

use Illuminate\Support\Facades\Route;
use Modules\Cms\Http\Controllers\PublicController;

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/api/content/{slug?}', [PublicController::class, 'api'])->name('api.content');

Route::get('/services', [PublicController::class, 'services'])->name('services');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicController::class, 'postContact'])->name('contact.post');
Route::get('/blog', [PublicController::class, 'blog'])->name('blog');
Route::get('/blog/category/{slug}', [PublicController::class, 'category'])->name('blog.category');
Route::get('/blog/tag/{slug}', [PublicController::class, 'tag'])->name('blog.tag');
Route::get('/blog/{slug}', [PublicController::class, 'post'])->name('blog.post');
Route::get('/search', [PublicController::class, 'search'])->name('search');
Route::get('/captcha/contact', [PublicController::class, 'captchaImage'])->name('captcha.contact');

// Catch-all — MUST be registered last so it never shadows admin/dashboard routes.
Route::get('/page/{slug}', [PublicController::class, 'page'])->name('page');
