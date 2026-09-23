<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rs_dan_ruangan', function (Blueprint $table) {
            $table->integer('rs_id');
            $table->integer('ruangan_id');
            $table->primary(['rs_id', 'ruangan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rs_dan_ruangan');
    }
};
