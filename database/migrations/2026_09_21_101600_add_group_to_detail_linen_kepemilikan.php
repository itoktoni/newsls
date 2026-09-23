<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan 'GROUP' ke detail_linen.detail_status_kepemilikan.
     *
     * Kolom ini di database produksi masih ENUM('FREE','DEDICATED') dari project
     * lama, sementara kode project ini sudah memakai RsStatusEnum yang punya GROUP:
     *   - App\Enums\RsStatusEnum::GROUP
     *   - App\Models\ConfigLinen::isAllowed() / komentar syncRs()
     *   - App\Http\Controllers\DetailLinenController::resolveOwnership()
     *     (DEDICATED min 1 RS, GROUP min 2 RS — mis. siloam A + siloam B)
     *
     * Tanpa migrasi ini, register/pindah kepemilikan ke GROUP gagal dengan:
     *   SQLSTATE[01000]: Warning: 1265 Data truncated for column
     *   'detail_status_kepemilikan'  (atau 1366 Incorrect integer value)
     */
    public function up(): void
    {
        Schema::table('detail_linen', function (Blueprint $table) {
            $table->enum('detail_status_kepemilikan', ['FREE', 'DEDICATED', 'GROUP'])->nullable()->change();
        });
    }

    public function down(): void
    {
        // GROUP tidak dikenal oleh enum lama, jadi barisnya diturunkan ke DEDICATED
        // lebih dulu supaya rollback tidak memotong data jadi string kosong.
        DB::table('detail_linen')
            ->where('detail_status_kepemilikan', 'GROUP')
            ->update(['detail_status_kepemilikan' => 'DEDICATED']);

        Schema::table('detail_linen', function (Blueprint $table) {
            $table->enum('detail_status_kepemilikan', ['FREE', 'DEDICATED'])->nullable()->change();
        });
    }
};
