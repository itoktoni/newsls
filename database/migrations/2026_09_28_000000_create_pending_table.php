<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel pending = antrean linen nyangkut di laundry (diisi scheduler
 * check:pending-dedicated, dibaca report-pending-linen, ditutup delivery).
 * Legacy: tabel ini dibuat manual di produksi — migrasi ini hanya membuatnya
 * bila belum ada, dengan kolom persis tabel legacy (tanpa PK).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pending')) {
            return;
        }

        Schema::create('pending', function (Blueprint $table) {
            $table->string('pending_rfid')->nullable();
            $table->string('pending_key')->nullable();
            $table->unsignedBigInteger('pending_id_rs')->nullable();
            $table->unsignedBigInteger('pending_id_ruangan')->nullable();
            $table->unsignedBigInteger('pending_id_jenis')->nullable();
            $table->dateTime('pending_created_at')->nullable();
            $table->dateTime('pending_updated_at')->nullable();
            $table->dateTime('pending_kotor_at')->nullable();
            $table->dateTime('pending_bersih_at')->nullable();
            $table->unsignedBigInteger('pending_created_by')->nullable();
            $table->unsignedBigInteger('pending_updated_by')->nullable();
            $table->unsignedBigInteger('pending_kotor_by')->nullable();
            $table->unsignedBigInteger('pending_bersih_by')->nullable();
            $table->string('pending_status')->nullable();
            $table->string('pending_delivery')->nullable();
            $table->string('pending_transaksi')->nullable();
            $table->string('pending_proses')->nullable();
            $table->index('pending_rfid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending');
    }
};
