<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rs', function (Blueprint $table) {
            $table->unsignedBigInteger('rs_id_group')->nullable()->after('rs_id');
            $table->foreign('rs_id_group')->references('group_rs_id')->on('group_rs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rs', function (Blueprint $table) {
            $table->dropForeign(['rs_id_group']);
            $table->dropColumn('rs_id_group');
        });
    }
};
