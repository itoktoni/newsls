<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BKA pakai tabel cetak legacy (skema andalan: cetak_code, cetak_id_rs,
     * cetak_type 1=Barcode 2=Delivery). Tambahan BKA: cetak_rfids JSON agar
     * reprint packing/delivery bisa jalan tanpa tabel bersih.
     */
    public function up(): void
    {
        if (Schema::hasTable('cetak') && ! Schema::hasColumn('cetak', 'cetak_rfids')) {
            Schema::table('cetak', function (Blueprint $table) {
                $table->json('cetak_rfids')->nullable()->after('cetak_delivery');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cetak') && Schema::hasColumn('cetak', 'cetak_rfids')) {
            Schema::table('cetak', function (Blueprint $table) {
                $table->dropColumn('cetak_rfids');
            });
        }
    }
};
