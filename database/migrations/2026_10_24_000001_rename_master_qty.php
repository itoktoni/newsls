<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rename master_total_qty -> master_qty (berat kotor timbangan, kg).
 * Data ikut pindah (renameColumn), tipe decimal tetap.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transaksi_master')) {
            return;
        }

        if (Schema::hasColumn('transaksi_master', 'master_qty')) {
            return;
        }

        if (! Schema::hasColumn('transaksi_master', 'master_total_qty')) {
            Schema::table('transaksi_master', function (Blueprint $table) {
                $table->decimal('master_qty', 12, 3)->nullable()->after('master_tanggal');
            });

            return;
        }

        Schema::table('transaksi_master', function (Blueprint $table) {
            $table->renameColumn('master_total_qty', 'master_qty');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('transaksi_master')) {
            return;
        }

        if (Schema::hasColumn('transaksi_master', 'master_qty') && ! Schema::hasColumn('transaksi_master', 'master_total_qty')) {
            Schema::table('transaksi_master', function (Blueprint $table) {
                $table->renameColumn('master_qty', 'master_total_qty');
            });
        }
    }
};
