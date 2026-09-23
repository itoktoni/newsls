<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('config_linen', function (Blueprint $table) {
            $table->string('detail_rfid');
            $table->unsignedBigInteger('rs_id');
            $table->primary(['detail_rfid', 'rs_id']);
            $table->foreign('rs_id')->references('rs_id')->on('rs')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('config_linen');
    }
};
