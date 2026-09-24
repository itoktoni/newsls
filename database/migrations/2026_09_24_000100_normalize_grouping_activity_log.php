<?php

use App\Models\DetailLinen;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalisasi log grouping lama.
     *
     * Route GET /api/grouping/{rfid} dulu menulis `activity('grouping')` tanpa
     * performedOn(), sehingga:
     *   - log_name tersimpan lowercase ('grouping'), tidak sesuai LogType::GROUPING;
     *   - subject_type/subject_id kosong → RFID hilang dari activity-log/table
     *     padahal RFID-nya sudah ada di properties.rfid.
     *
     * Backfill: samakan log_name ke uppercase dan isi subject dari properties.rfid
     * supaya setiap log grouping punya RFID seperti log operasi lain.
     */
    public function up(): void
    {
        DB::table('activity_log')
            ->where('log_name', 'grouping')
            ->update(['log_name' => 'GROUPING']);

        DB::table('activity_log')
            ->whereNull('subject_id')
            ->whereNotNull('properties')
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(properties, '$.rfid')) IS NOT NULL")
            ->update([
                'subject_type' => DetailLinen::class,
                'subject_id' => DB::raw("JSON_UNQUOTE(JSON_EXTRACT(properties, '$.rfid'))"),
            ]);
    }

    public function down(): void
    {
        // Perbaikan data satu arah — tidak dikembalikan.
    }
};
