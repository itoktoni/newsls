<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Master header transaksi kotor/retur/rewash.
 *
 * Detail per-RFID tetap di `transaksi` (1 baris per RFID, grup per
 * transaksi_key). Master ini 1 baris per transaksi_key:
 * nama RS, operator, total_qty, kode, status, tanggal.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transaksi_master')) {
            Schema::create('transaksi_master', function (Blueprint $table) {
                $table->increments('master_id');
                $table->string('master_kode')->unique();
                $table->integer('master_rs_id')->nullable()->index();
                $table->string('master_status', 20)->nullable()->index();
                $table->date('master_tanggal')->nullable()->index();
                $table->integer('master_total_qty')->default(0);
                $table->integer('master_created_by')->nullable();
                $table->integer('master_updated_by')->nullable();
                $table->dateTime('master_created_at')->nullable();
                $table->dateTime('master_updated_at')->nullable();
            });
        }

        $this->backfill();
    }

    /**
     * Backfill 1 master per transaksi_key yang sudah ada (termasuk key GRP
     * dari grouping QC). Best-effort: RS/status/operator = nilai MAX grup,
     * tanggal = hari dari created_at paling awal, total = jumlah baris grup.
     */
    private function backfill(): void
    {
        if (! Schema::hasTable('transaksi') || ! Schema::hasTable('transaksi_master')) {
            return;
        }

        if (DB::table('transaksi_master')->count() > 0) {
            return;
        }

        set_time_limit(0);

        $groups = DB::table('transaksi')
            ->selectRaw('transaksi_key, MAX(transaksi_rs_scan) as rs, MAX(transaksi_status) as status, MIN(transaksi_created_at) as min_at, MAX(transaksi_updated_at) as max_at, MAX(transaksi_created_by) as created_by, MAX(transaksi_updated_by) as updated_by, COUNT(*) as total')
            ->whereNotNull('transaksi_key')
            ->where('transaksi_key', '!=', '')
            ->groupBy('transaksi_key')
            ->cursor();

        $batch = [];

        foreach ($groups as $row) {
            $batch[] = [
                'master_kode' => $row->transaksi_key,
                'master_rs_id' => $row->rs,
                'master_status' => $row->status,
                'master_tanggal' => $row->min_at ? substr((string) $row->min_at, 0, 10) : null,
                'master_total_qty' => (int) $row->total,
                'master_created_by' => $row->created_by,
                'master_updated_by' => $row->updated_by,
                'master_created_at' => $row->min_at,
                'master_updated_at' => $row->max_at,
            ];

            if (count($batch) >= 500) {
                DB::table('transaksi_master')->insertOrIgnore($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            DB::table('transaksi_master')->insertOrIgnore($batch);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_master');
    }
};
