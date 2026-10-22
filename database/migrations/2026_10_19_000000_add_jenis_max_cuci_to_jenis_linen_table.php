<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenis_linen', function (Blueprint $table) {
            if (! Schema::hasColumn('jenis_linen', 'jenis_max_cuci')) {
                $table->integer('jenis_max_cuci')->nullable()->default(100);
            }
        });
    }

    public function down(): void
    {
        Schema::table('jenis_linen', function (Blueprint $table) {
            if (Schema::hasColumn('jenis_linen', 'jenis_max_cuci')) {
                $table->dropColumn('jenis_max_cuci');
            }
        });
    }
};
