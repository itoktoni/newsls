<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * subject_id harus bisa menampung primary key bertipe string.
     *
     * DetailLinen memakai PK string (detail_rfid, contoh "E280689400004025AAE544BA"),
     * sementara nullableMorphs('subject') membuat subject_id bertipe
     * bigint unsigned. Akibatnya setiap DetailLinen::create() gagal di event
     * 'created' milik spatie/laravel-activitylog dengan:
     *
     *   SQLSTATE[22007]: Invalid datetime format: 1366 Incorrect integer value:
     *   'E2806894...' for column `bka`.`activity_log`.`subject_id`
     *
     * Artinya fitur register linen (form web Data Linen maupun API) selalu gagal,
     * dan tabel activity_log tetap kosong meski modelnya memakai LogsActivity.
     *
     * subject_id dibuat varchar supaya aman untuk model ber-PK string (DetailLinen)
     * maupun ber-PK integer (User, dan model lain).
     */
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->string('subject_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable()->change();
        });
    }
};
