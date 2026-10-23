<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * total_qty = BERAT kotor timbangan (kg, desimal), bukan hitung RFID.
 * master_total_bersih = Σ jenis_berat per RFID terdaftar (dihitung server).
 * Selisih (kotor - bersih) dihitung on-the-fly, tidak disimpan.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transaksi_master')) {
            return;
        }

        Schema::table('transaksi_master', function (Blueprint $table) {
            if (! Schema::hasColumn('transaksi_master', 'master_total_bersih')) {
                $table->decimal('master_total_bersih', 12, 3)->nullable()->after('master_total_qty');
            }
        });

        if (Schema::hasColumn('transaksi_master', 'master_total_qty')) {
            Schema::table('transaksi_master', function (Blueprint $table) {
                $table->decimal('master_total_qty', 12, 3)->nullable()->change();
            });
        } else {
            Schema::table('transaksi_master', function (Blueprint $table) {
                $table->decimal('master_total_qty', 12, 3)->nullable()->after('master_tanggal');
            });
        }

        $this->backfillBerat();
    }

    /**
     * Isi master_total_bersih untuk master lama = SUM(jenis_berat) detail
     * dalam key yang sama. RFID tak terregister (tanpa jenis) tidak ikut.
     */
    private function backfillBerat(): void
    {
        if (! Schema::hasTable('transaksi') || ! Schema::hasTable('detail_linen') || ! Schema::hasTable('jenis_linen')) {
            return;
        }

        if (DB::table('transaksi_master')->whereNotNull('master_total_bersih')->count() > 0) {
            return;
        }

        set_time_limit(0);

        $sums = DB::table('transaksi')
            ->join('detail_linen', 'detail_linen.detail_rfid', '=', 'transaksi.transaksi_rfid')
            ->join('jenis_linen', 'jenis_linen.jenis_id', '=', 'detail_linen.detail_id_jenis')
            ->selectRaw('transaksi.transaksi_key as kode, SUM(jenis_linen.jenis_berat) as bersih')
            ->whereNotNull('transaksi.transaksi_key')
            ->where('transaksi.transaksi_key', '!=', '')
            ->groupBy('transaksi.transaksi_key')
            ->cursor();

        foreach ($sums as $row) {
            DB::table('transaksi_master')
                ->where('master_kode', $row->kode)
                ->update(['master_total_bersih' => $row->bersih ?? 0]);
        }

        DB::table('transaksi_master')->whereNull('master_total_bersih')->update(['master_total_bersih' => 0]);
    }

    public function down(): void
    {
        if (Schema::hasTable('transaksi_master') && Schema::hasColumn('transaksi_master', 'master_total_bersih')) {
            Schema::table('transaksi_master', function (Blueprint $table) {
                $table->dropColumn('master_total_bersih');
            });
        }
    }
};
