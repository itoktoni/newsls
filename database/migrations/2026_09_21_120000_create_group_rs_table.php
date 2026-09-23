<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_rs', function (Blueprint $table) {
            $table->id('group_rs_id');
            $table->string('group_rs_nama');
            $table->string('group_rs_code')->nullable()->unique();
            $table->text('group_rs_deskripsi')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_rs');
    }
};
