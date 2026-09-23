<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ganti_chip', function (Blueprint $table) {
            $table->id('ganti_id');
            $table->string('ganti_rfid_lama');
            $table->string('ganti_rfid_baru');
            $table->dateTime('ganti_tanggal')->nullable();
            $table->unsignedBigInteger('ganti_by')->nullable();
            $table->text('ganti_keterangan')->nullable();
            $table->index(['ganti_rfid_lama', 'ganti_rfid_baru']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ganti_chip');
    }
};
