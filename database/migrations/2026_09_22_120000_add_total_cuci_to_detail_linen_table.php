<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Counter dicuci — setiap delivery (dicuci + dikirim, masuk bersih/table
     * riwayat) +1 sesuai asal: KOTOR->bersih, REWASH->rewash, REJECT/RETUR->reject.
     * Kolom sudah ada di DB produksi, migrasi ini untuk fresh install.
     */
    public function up(): void
    {
        Schema::table('detail_linen', function (Blueprint $table) {
            if (! Schema::hasColumn('detail_linen', 'detail_total_rewash')) {
                $table->integer('detail_total_rewash')->nullable()->default(0);
            }
            if (! Schema::hasColumn('detail_linen', 'detail_total_reject')) {
                $table->integer('detail_total_reject')->nullable()->default(0);
            }
            if (! Schema::hasColumn('detail_linen', 'detail_total_bersih')) {
                $table->integer('detail_total_bersih')->nullable()->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('detail_linen', function (Blueprint $table) {
            $table->dropColumn(['detail_total_rewash', 'detail_total_reject', 'detail_total_bersih']);
        });
    }
};
