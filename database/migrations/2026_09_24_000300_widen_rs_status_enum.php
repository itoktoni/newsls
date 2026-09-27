<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Di DB legacy, rs_status dibuat sebagai enum('FREE','DEDICATED') sehingga
     * menyimpan 'GROUP' (register multi-RS) gagal dengan "Data truncated for column
     * 'rs_status'". Skema aplikasi sendiri memakai string (default 'ACTIVE'), jadi
     * migrasi ini HANYA melebarkan kolom yang memang sudah berupa enum.
     */
    public function up(): void
    {
        $type = $this->columnType();

        if ($type === null || ! str_starts_with($type, 'enum(') || str_contains($type, "'GROUP'")) {
            return;
        }

        DB::statement("ALTER TABLE rs MODIFY rs_status ENUM('FREE','DEDICATED','GROUP') NULL");
    }

    public function down(): void
    {
        if (! str_starts_with((string) $this->columnType(), 'enum(')) {
            return;
        }

        DB::table('rs')->where('rs_status', 'GROUP')->update(['rs_status' => 'DEDICATED']);

        DB::statement("ALTER TABLE rs MODIFY rs_status ENUM('FREE','DEDICATED') NULL");
    }

    private function columnType(): ?string
    {
        if (! Schema::hasTable('rs') || ! in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            return null;
        }

        return DB::selectOne(
            'SELECT COLUMN_TYPE AS type FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            ['rs', 'rs_status']
        )?->type;
    }
};
