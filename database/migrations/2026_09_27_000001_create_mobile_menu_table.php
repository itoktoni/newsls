<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master menu mobile (form yang boleh tampil di aplikasi mobile/desktop)
     * + pivot hak akses per user. Pivot kosong = semua menu (mirip rs_dan_user).
     * Dikelola dari CRUD Menu Mobile; dipilih per user via checkbox di form user.
     */
    public function up(): void
    {
        if (! Schema::hasTable('mobile_menu')) {
            Schema::create('mobile_menu', function (Blueprint $table) {
                $table->id('mobile_menu_id');
                $table->string('mobile_menu_nama');
                $table->string('mobile_menu_code', 50)->nullable()->unique();
                $table->integer('mobile_menu_urut')->default(0);
                $table->boolean('mobile_menu_aktif')->default(true);
                $table->text('mobile_menu_deskripsi')->nullable();
            });
        }

        if (! Schema::hasTable('mobile_menu_dan_user')) {
            Schema::create('mobile_menu_dan_user', function (Blueprint $table) {
                $table->unsignedBigInteger('mobile_menu_id');
                $table->unsignedBigInteger('user_id');
                $table->primary(['mobile_menu_id', 'user_id']);
            });
        }

        // Seed bawaan = 8 form di kontrak login desktop (idempotent).
        $defaults = [
            ['Register Linen', 'register', 1],
            ['Scan Linen Kotor', 'scan_kotor', 2],
            ['Scan Retur Rewash', 'scan_rewash', 3],
            ['Scan Retur Reject', 'scan_reject', 4],
            ['Opname', 'opname', 5],
            ['Opname Harian', 'opname_harian', 6],
            ['Detail Linen', 'detail_linen', 7],
            ['Sync Data', 'sync_data', 8],
        ];

        foreach ($defaults as [$nama, $code, $urut]) {
            $exists = DB::table('mobile_menu')->where('mobile_menu_code', $code)->exists();
            if (! $exists) {
                DB::table('mobile_menu')->insert([
                    'mobile_menu_nama' => $nama,
                    'mobile_menu_code' => $code,
                    'mobile_menu_urut' => $urut,
                    'mobile_menu_aktif' => true,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_menu_dan_user');
        Schema::dropIfExists('mobile_menu');
    }
};
