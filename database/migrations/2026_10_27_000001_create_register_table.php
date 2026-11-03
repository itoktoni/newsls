<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nomor register batch (satu nomor untuk satu kali simpan register massal)
     * + kolom keterangan. Nomor diambil dari auto-increment DB (register_id),
     * jadi dijamin unik oleh database, bukan random di aplikasi.
     */
    public function up(): void
    {
        Schema::create('register', function (Blueprint $table) {
            $table->increments('register_id');
            $table->string('register_no')->nullable()->unique();
            $table->text('register_keterangan')->nullable();
            $table->integer('register_total')->default(0);
            $table->integer('register_created_by')->nullable();
            $table->timestamp('register_created_at')->nullable();
            $table->timestamp('register_updated_at')->nullable();
        });

        Schema::table('detail_linen', function (Blueprint $table) {
            if (! Schema::hasColumn('detail_linen', 'detail_no_register')) {
                $table->string('detail_no_register')->nullable()->after('detail_rfid')->index();
            }
            // Keterangan diinput dari textarea desktop — butuh TEXT, bukan string(255).
            $table->text('detail_deskripsi')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('detail_linen', function (Blueprint $table) {
            if (Schema::hasColumn('detail_linen', 'detail_no_register')) {
                $table->dropColumn('detail_no_register');
            }
        });

        Schema::dropIfExists('register');
    }
};
