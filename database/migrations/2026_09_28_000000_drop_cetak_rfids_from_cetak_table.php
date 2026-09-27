<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cabut kolom cetak.cetak_rfids — isi batch pengiriman dibaca dari tabel
 * `bersih` (bersih_barcode/bersih_delivery per RFID), cetak hanya registry
 * kode untuk list/reprint. Guarded agar aman di produksi maupun sqlite test.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cetak') && Schema::hasColumn('cetak', 'cetak_rfids')) {
            Schema::table('cetak', function (Blueprint $table) {
                $table->dropColumn('cetak_rfids');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cetak') && ! Schema::hasColumn('cetak', 'cetak_rfids')) {
            Schema::table('cetak', function (Blueprint $table) {
                $table->text('cetak_rfids')->nullable()->after('cetak_delivery');
            });
        }
    }
};
