<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rs', function (Blueprint $table) {
            $table->id('rs_id');
            $table->string('rs_nama');
            $table->text('rs_alamat')->nullable();
            $table->text('rs_deskripsi')->nullable();
            $table->integer('rs_harga_cuci')->nullable();
            $table->integer('rs_harga_sewa')->nullable();
            $table->boolean('rs_aktif')->default(true);
            $table->string('rs_code')->nullable()->unique();
            $table->string('rs_status')->default('ACTIVE');
            $table->string('rs_logo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rs');
    }
};
