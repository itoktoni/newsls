<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Di DB legacy, outstanding dibuat langsung di produksi (tanpa migration),
     * dan outstanding_status_proses tidak mengenal 'GUDANG' (enum legacy
     * ProcessType hanya SCAN/QC/REGISTER/PACKING/dll, atau varchar sempit).
     * GroupingController menulis 'GUDANG' (6 char) sehingga insert gagal:
     * "SQLSTATE[01000]: Warning: 1265 Data truncated for column
     * 'outstanding_status_proses'".
     *
     * Skema aplikasi sendiri memakai string, jadi migrasi ini melebarkan
     * kolom ke VARCHAR(20) — cukup untuk SCAN/QC/REGISTER/GUDANG/PACKING/
     * PENDING — hanya bila kolom masih enum tanpa GUDANG atau varchar < 6.
     * Di sqlite (testing) dilewati.
     */
    public function up(): void
    {
        $info = $this->columnInfo();

        if ($info === null) {
            return;
        }

        ['type' => $type, 'length' => $length] = $info;

        if (str_starts_with($type, 'enum(')) {
            if (str_contains($type, "'GUDANG'")) {
                return;
            }
        } elseif (str_starts_with($type, 'varchar(')) {
            if ($length !== null && $length >= 20) {
                return;
            }
            // varchar sempit (< 6) pasti memotong GUDANG; varchar 6-19
            // dilebarkan juga agar muat REGISTER(8)/PACKING(7)/PENDING(7).
        } else {
            return;
        }

        DB::statement('ALTER TABLE `outstanding` MODIFY `outstanding_status_proses` VARCHAR(20) NULL');
    }

    public function down(): void
    {
        // Tidak dikembalikan: pelebaran kolom aman dan data GUDANG yang
        // sudah tersimpan akan rusak bila dipaksa kembali ke tipe sempit.
    }

    /**
     * @return array{type: string, length: int|null}|null
     */
    private function columnInfo(): ?array
    {
        if (! Schema::hasTable('outstanding') || ! in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            return null;
        }

        $row = DB::selectOne(
            'SELECT COLUMN_TYPE AS type, CHARACTER_MAXIMUM_LENGTH AS length FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            ['outstanding', 'outstanding_status_proses']
        );

        if (! $row) {
            return null;
        }

        return ['type' => strtolower($row->type), 'length' => $row->length !== null ? (int) $row->length : null];
    }
};
