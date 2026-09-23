<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_linen', function (Blueprint $table) {
            $table->string('detail_rfid')->primary();
            $table->integer('detail_id_rs')->nullable();
            $table->integer('detail_id_ruangan')->nullable();
            $table->integer('detail_id_jenis')->nullable();
            $table->integer('detail_id_bahan')->nullable();
            $table->integer('detail_id_supplier')->nullable();
            $table->string('detail_deskripsi')->nullable();
            $table->string('detail_status_cuci')->nullable();
            $table->string('detail_status_kepemilikan')->nullable();
            $table->string('detail_status_linen')->nullable();
            $table->date('detail_tgl_cek')->nullable();
            $table->date('detail_report')->nullable();
            $table->integer('detail_created_by')->nullable();
            $table->integer('detail_updated_by')->nullable();
            $table->timestamp('detail_created_at')->nullable();
            $table->timestamp('detail_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_linen');
    }
};
