<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('opname')) {
            Schema::create('opname', function (Blueprint $table) {
                $table->increments('opname_id');
                $table->date('opname_mulai')->nullable();
                $table->date('opname_selesai')->nullable();
                $table->text('opname_nama')->nullable();
                $table->integer('opname_id_rs')->nullable();
                $table->dateTime('opname_created_at')->nullable();
                $table->integer('opname_created_by')->nullable();
                $table->dateTime('opname_updated_at')->nullable();
                $table->integer('opname_updated_by')->nullable();
                $table->tinyInteger('opname_status')->default(0);
                $table->dateTime('opname_capture')->nullable();
            });
        }

        if (! Schema::hasTable('opname_detail')) {
            Schema::create('opname_detail', function (Blueprint $table) {
                $table->increments('opname_detail_id');
                $table->integer('opname_detail_id_opname')->nullable()->index();
                $table->string('opname_detail_code')->nullable();
                $table->string('opname_detail_rfid')->nullable()->index();
                $table->dateTime('opname_detail_waktu')->nullable();
                $table->string('opname_detail_transaksi')->nullable();
                $table->string('opname_detail_proses')->nullable();
                $table->string('opname_detail_hilang')->nullable()->default('NORMAL');
                $table->tinyInteger('opname_detail_ketemu')->default(0);
                $table->dateTime('opname_detail_created_at')->nullable();
                $table->dateTime('opname_detail_updated_at')->nullable();
                $table->integer('opname_detail_created_by')->nullable();
                $table->integer('opname_detail_updated_by')->nullable();
                $table->tinyInteger('opname_detail_register')->nullable();
                $table->dateTime('opname_detail_hilang_at')->nullable();
                $table->dateTime('opname_detail_pending_at')->nullable();
                $table->tinyInteger('opname_detail_scan_rs')->default(0);
                $table->tinyInteger('opname_detail_sync')->default(0);
                $table->string('opname_detail_reff')->nullable();
                $table->string('opname_detail_scan_by')->nullable();
            });
        } else {
            // bka prod sudah ada tapi belum punya sync (andalan punya) — tambah
            if (! Schema::hasColumn('opname_detail', 'opname_detail_sync')) {
                Schema::table('opname_detail', function (Blueprint $table) {
                    $table->tinyInteger('opname_detail_sync')->default(0)->after('opname_detail_scan_rs');
                });
            }
        }

        // View view_opname tidak dibuat di test (sqlite) — query diganti join langsung. Di mariadb bka sudah ada view legacy, biarkan.
        // Jika MariaDB test_bka belum punya view, buat minimal untuk report (join opname_detail + detail_linen)
        if (DB::connection()->getDriverName() !== 'sqlite') {
            try {
                DB::statement("CREATE OR REPLACE VIEW view_opname AS
                    SELECT od.*, d.detail_id_rs as rs_id, d.detail_id_jenis as jenis_id, d.detail_id_ruangan as ruangan_id,
                           jl.jenis_nama, r.ruangan_nama, rs.rs_nama
                    FROM opname_detail od
                    LEFT JOIN detail_linen d ON d.detail_rfid = od.opname_detail_rfid
                    LEFT JOIN jenis_linen jl ON jl.jenis_id = d.detail_id_jenis
                    LEFT JOIN ruangan r ON r.ruangan_id = d.detail_id_ruangan
                    LEFT JOIN rs ON rs.rs_id = d.detail_id_rs");
            } catch (\Throwable $e) {
            }
        }
    }

    public function down(): void
    {
        try { DB::statement("DROP VIEW IF EXISTS view_opname"); } catch (\Throwable $e) {}
        Schema::dropIfExists('opname_detail');
        Schema::dropIfExists('opname');
    }
};
