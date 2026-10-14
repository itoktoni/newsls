<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Index jalur kotor-vs-bersih (EXPLAIN sebelumnya type=ALL di semua query
 * totals/packing/delivery) + buang index redundan yang menduplikasi PK.
 *
 * - outstanding.outstanding_rfid sekunder == duplikat PRIMARY → drop.
 * - config_linen.rs_id sekunder == duplikat kiri PRIMARY(rs_id, detail_rfid) → drop.
 * - idx_outstanding_scan_status melayani: totalOutstanding, totalDelivery,
 *   assertReadyForPacking, collectPackingRfids, updateDetailRuangan.
 * - idx_detail_bersih melayani: totalBersih (+ varian filter jenis).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outstanding', function (Blueprint $table) {
            if ($this->hasIndex('outstanding', 'outstanding_rfid')) {
                $table->dropIndex('outstanding_rfid');
            }
            if (! $this->hasIndex('outstanding', 'idx_outstanding_scan_status')) {
                $table->index(
                    ['outstanding_rs_scan', 'outstanding_status_transaksi', 'outstanding_status_proses', 'outstanding_id_ruangan'],
                    'idx_outstanding_scan_status'
                );
            }
        });

        Schema::table('detail_linen', function (Blueprint $table) {
            if (! $this->hasIndex('detail_linen', 'idx_detail_bersih')) {
                $table->index(
                    ['detail_id_rs', 'detail_id_ruangan', 'detail_status_linen', 'detail_id_jenis'],
                    'idx_detail_bersih'
                );
            }
        });

        Schema::table('config_linen', function (Blueprint $table) {
            if ($this->hasIndex('config_linen', 'rs_id')) {
                $table->dropIndex('rs_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('outstanding', function (Blueprint $table) {
            $table->dropIndex('idx_outstanding_scan_status');
        });

        Schema::table('detail_linen', function (Blueprint $table) {
            $table->dropIndex('idx_detail_bersih');
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
