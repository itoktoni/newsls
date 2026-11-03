<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nomor + keterangan register murni di detail_linen
     * (detail_no_register, detail_deskripsi). Tabel header `register`
     * tidak jadi dipakai — tidak ada baris datanya selain struktur.
     */
    public function up(): void
    {
        Schema::dropIfExists('register');
    }

    public function down(): void
    {
        Schema::create('register', function ($table) {
            $table->increments('register_id');
            $table->string('register_no')->nullable()->unique();
            $table->text('register_keterangan')->nullable();
            $table->integer('register_total')->default(0);
            $table->integer('register_created_by')->nullable();
            $table->timestamp('register_created_at')->nullable();
            $table->timestamp('register_updated_at')->nullable();
        });
    }
};
