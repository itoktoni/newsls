<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom transaksi_grouping_date sudah ada di DB produksi (dibuat manual),
 * tapi belum ada di migrasi — sehingga DB test (dibangun migrate:fresh)
 * tidak memilikinya dan grouping (yang mengisinya) gagal.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transaksi')) {
            return;
        }

        if (! Schema::hasColumn('transaksi', 'transaksi_grouping_date')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->date('transaksi_grouping_date')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transaksi') && Schema::hasColumn('transaksi', 'transaksi_grouping_date')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->dropColumn('transaksi_grouping_date');
            });
        }
    }
};
