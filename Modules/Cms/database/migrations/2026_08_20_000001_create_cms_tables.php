<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('type')->default('custom');
            $table->text('description')->nullable();
            $table->json('supports')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('menu_position')->nullable();
            $table->string('menu_icon')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->foreignId('content_type_id')->nullable()->constrained('cms_types')->nullOnDelete();
            $table->json('field_ids')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cms_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('label')->nullable();
            $table->string('type')->default('text');
            $table->json('config')->nullable();
            $table->json('rules')->nullable();
            $table->boolean('is_required')->default(false);
            $table->text('default_value')->nullable();
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('mode')->default('multiple');
            $table->integer('min')->nullable();
            $table->integer('max')->nullable();
            $table->boolean('collapsed')->default(false);
            $table->boolean('sortable')->default(false);
            $table->boolean('cloneable')->default(false);
            $table->json('layouts')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_type_id')->nullable()->constrained('cms_types')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('featured_image')->nullable();
            $table->integer('menu_order')->default(0);
            $table->json('meta')->nullable();
            $table->json('active_sections')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cms_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cms_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('location')->nullable();
            $table->json('items')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cms_media', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('thumbnail_path')->nullable();
            $table->string('alt')->nullable();
            $table->string('title')->nullable();
            $table->string('caption')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cms_content_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('cms_contents')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('cms_categories')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('cms_content_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('cms_contents')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('cms_tags')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_content_tag');
        Schema::dropIfExists('cms_content_category');
        Schema::dropIfExists('cms_media');
        Schema::dropIfExists('cms_menus');
        Schema::dropIfExists('cms_tags');
        Schema::dropIfExists('cms_categories');
        Schema::dropIfExists('cms_contents');
        Schema::dropIfExists('cms_fields');
        Schema::dropIfExists('cms_sections');
        Schema::dropIfExists('cms_types');
    }
};
