<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Berat bersih (Σ jenis_berat) TIDAK disimpan di transaksi_master —
 * dihitung live dari baris detail (transaksi + detail_linen + jenis_linen).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transaksi_master') && Schema::hasColumn('transaksi_master', 'master_total_bersih')) {
            Schema::table('transaksi_master', function (Blueprint $table) {
                $table->dropColumn('master_total_bersih');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transaksi_master') && ! Schema::hasColumn('transaksi_master', 'master_total_bersih')) {
            Schema::table('transaksi_master', function (Blueprint $table) {
                $table->decimal('master_total_bersih', 12, 3)->nullable()->after('master_qty');
            });
        }
    }
};
