<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Index pendukung GET /api/download/{rsid} (DownloadApiController):
 * sync 12rb+ RFID harus full-scan ringan + index lookup per baris.
 *
 * - config_linen(rs_id, detail_rfid): hitung & cek kepemilikan per RS tanpa
 *   memindai seluruh PK (detail_rfid, rs_id).
 * - opname(opname_id_rs, opname_status, opname_id): cari opname aktif milik RS.
 * - opname_detail(opname_detail_id_opname, opname_detail_ketemu): daftar RFID
 *   ketemu pada opname aktif.
 * - detail_linen(detail_status_kepemilikan, detail_rfid): dropdown RFID form
 *   config-linen (whereNotIn + orderBy + limit) dan filter status fully
 *   index-covered, tanpa full scan + filesort tiap request.
 *
 * Additive saja (tidak mengubah kolom/data). Tiap index dibungkus try/catch
 * supaya tetap aman bila DB legacy sudah punya index sejenis.
 */
return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('config_linen', function (Blueprint $table) {
                $table->index(['rs_id', 'detail_rfid'], 'config_linen_rs_id_detail_rfid_index');
            });
        } catch (\Throwable $e) {
        }

        try {
            Schema::table('opname', function (Blueprint $table) {
                $table->index(['opname_id_rs', 'opname_status', 'opname_id'], 'opname_rs_status_id_index');
            });
        } catch (\Throwable $e) {
        }

        try {
            Schema::table('opname_detail', function (Blueprint $table) {
                $table->index(['opname_detail_id_opname', 'opname_detail_ketemu'], 'opname_detail_opname_ketemu_index');
            });
        } catch (\Throwable $e) {
        }

        try {
            Schema::table('detail_linen', function (Blueprint $table) {
                $table->index(['detail_status_kepemilikan', 'detail_rfid'], 'detail_linen_status_rfid_index');
            });
        } catch (\Throwable $e) {
        }
    }

    public function down(): void
    {
        foreach ([
            ['config_linen', 'config_linen_rs_id_detail_rfid_index'],
            ['opname', 'opname_rs_status_id_index'],
            ['opname_detail', 'opname_detail_opname_ketemu_index'],
            ['detail_linen', 'detail_linen_status_rfid_index'],
        ] as [$table, $index]) {
            try {
                Schema::table($table, function (Blueprint $table) use ($index) {
                    $table->dropIndex($index);
                });
            } catch (\Throwable $e) {
            }
        }
    }
};
