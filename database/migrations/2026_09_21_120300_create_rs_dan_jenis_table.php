<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rs_dan_jenis', function (Blueprint $table) {
            $table->integer('rs_id');
            $table->integer('jenis_id');
            $table->integer('parstock')->nullable();
            $table->primary(['rs_id', 'jenis_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rs_dan_jenis');
    }
};
