<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Index rekap Pending per Jenis (anti-keos): kedua sisi query (masuk dari
 * transaksi, keluar dari bersih) selalu terbatasi status + RS + tanggal,
 * sehingga report tidak pernah full-table scan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            if (! $this->hasIndex('transaksi', 'transaksi_pending_jenis_idx')) {
                $table->index(['transaksi_status', 'transaksi_rs_scan', 'transaksi_created_at'], 'transaksi_pending_jenis_idx');
            }
        });

        Schema::table('bersih', function (Blueprint $table) {
            if (! $this->hasIndex('bersih', 'bersih_pending_jenis_idx')) {
                $table->index(['bersih_delivery', 'bersih_id_rs', 'bersih_status', 'bersih_updated_at'], 'bersih_pending_jenis_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropIndex('transaksi_pending_jenis_idx');
        });

        Schema::table('bersih', function (Blueprint $table) {
            $table->dropIndex('bersih_pending_jenis_idx');
        });
    }

    private function hasIndex(string $table, string $index): bool
    {
        $db = DB::connection()->getDatabaseName();

        return DB::selectOne(
            'SELECT COUNT(*) AS c FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$db, $table, $index]
        )->c > 0;
    }
};
