<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Akses RS per user (pivot). Baris kosong = akses semua RS.
     * Dikelola dari checkbox di form user.
     */
    public function up(): void
    {
        // Idempotent: tabel rs/detail/config dibuat manual di luar migrasi
        // (status Pending tapi fisik ada), jadi guard agar migrate parsial aman.
        if (Schema::hasTable('rs_dan_user')) {
            return;
        }

        Schema::create('rs_dan_user', function (Blueprint $table) {
            $table->integer('rs_id');
            $table->integer('user_id');
            $table->primary(['rs_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rs_dan_user');
    }
};
