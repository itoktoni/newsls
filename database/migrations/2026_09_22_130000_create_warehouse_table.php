<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Gudang warehouse: tabel master (sudah ada di produksi: 1|Gudang Utama).
     * Posisi gudang memakai kolom bawaan outstanding.outstanding_id_warehouse.
     * Gudang utama dibaca dari env GUDANG_UTAMA_ID (default 1).
     */
    public function up(): void
    {
        if (! Schema::hasTable('warehouse')) {
            Schema::create('warehouse', function (Blueprint $table) {
                $table->id('warehouse_id');
                $table->string('warehouse_nama');
            });
        }

        if (! DB::table('warehouse')->where('warehouse_id', 1)->exists()) {
            DB::table('warehouse')->insert([
                'warehouse_id' => 1,
                'warehouse_nama' => 'Gudang Utama',
            ]);
        }

        // ponytail: tabel outstanding dibuat manual di luar migrasi (Pending tapi
        // fisik ada di MySQL dev). Di sqlite test tabel belum ada → guard agar
        // migrate:fresh tidak 500 "no such table: outstanding".
        if (Schema::hasTable('outstanding') && ! Schema::hasColumn('outstanding', 'outstanding_id_warehouse')) {
            Schema::table('outstanding', function (Blueprint $table) {
                $table->unsignedBigInteger('outstanding_id_warehouse')->nullable()->after('outstanding_id_ruangan');
            });
        }
    }

    public function down(): void
    {
        // Kolom bawaan outstanding dipertahankan.
    }
};
