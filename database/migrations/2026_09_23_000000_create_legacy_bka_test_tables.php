<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Legacy BKA tables (transaksi, outstanding, cetak) tidak punya migration
 * di repo ini karena dibuat langsung di DB produksi. Tanpa ini,
 * RefreshDatabase di sqlite :memory: (phpunit) akan gagal "no such table"
 * saat test lifecycle menyentuh Packing/Delivery/Transaksi.
 *
 * Migrasi ini membuat tabel minimal sqlite-compatible untuk testing.
 * Di MySQL/MariaDB produksi, Schema::hasTable akan skip jika sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transaksi')) {
            Schema::create('transaksi', function (Blueprint $table) {
                $table->bigIncrements('transaksi_id');
                $table->string('transaksi_key')->nullable()->default('');
                $table->string('transaksi_status')->nullable();
                $table->string('transaksi_rfid')->nullable();
                $table->integer('transaksi_rs_scan')->nullable();
                $table->integer('transaksi_rs_ori')->nullable();
                $table->integer('transaksi_id_ruangan')->nullable();
                $table->string('transaksi_beda_rs')->nullable();
                $table->dateTime('transaksi_created_at')->nullable();
                $table->dateTime('transaksi_updated_at')->nullable();
                $table->integer('transaksi_created_by')->nullable();
                $table->integer('transaksi_updated_by')->nullable();
                $table->date('transaksi_report_date')->nullable();
                $table->string('transaksi_grouping')->nullable();
                $table->index('transaksi_rfid');
            });
        }

        if (! Schema::hasTable('outstanding')) {
            Schema::create('outstanding', function (Blueprint $table) {
                $table->string('outstanding_rfid')->primary();
                $table->string('outstanding_key')->nullable();
                $table->integer('outstanding_rs_ori')->nullable();
                $table->integer('outstanding_rs_scan')->nullable();
                $table->integer('outstanding_id_ruangan')->nullable();
                $table->string('outstanding_status_transaksi')->nullable();
                $table->string('outstanding_status_hilang')->default('NORMAL');
                $table->string('outstanding_status_proses')->nullable();
                $table->string('outstanding_status_beda_rs')->nullable();
                $table->dateTime('outstanding_created_at')->nullable();
                $table->dateTime('outstanding_updated_at')->nullable();
                $table->integer('outstanding_created_by')->nullable();
                $table->integer('outstanding_updated_by')->nullable();
                $table->dateTime('outstanding_pending_created_at')->nullable();
                $table->dateTime('outstanding_pending_updated_at')->nullable();
                $table->dateTime('outstanding_hilang_created_at')->nullable();
                $table->dateTime('outstanding_hilang_updated_at')->nullable();
                $table->integer('outstanding_id_warehouse')->nullable();
            });
        }

        if (! Schema::hasTable('cetak')) {
            Schema::create('cetak', function (Blueprint $table) {
                $table->increments('cetak_id');
                $table->string('cetak_code')->nullable();
                $table->date('cetak_date')->nullable();
                $table->string('cetak_user')->nullable();
                $table->integer('cetak_id_rs')->nullable();
                $table->integer('cetak_id_ruangan')->nullable();
                $table->tinyInteger('cetak_type')->nullable();
                $table->string('cetak_barcode')->nullable();
                $table->string('cetak_delivery')->nullable();
                $table->text('cetak_rfids')->nullable();
            });
        } else {
            // cetak sudah ada di produksi tapi mungkin belum punya cetak_rfids (ditambah 2026_09_21_150351)
            if (! Schema::hasColumn('cetak', 'cetak_rfids')) {
                Schema::table('cetak', function (Blueprint $table) {
                    $table->text('cetak_rfids')->nullable()->after('cetak_delivery');
                });
            }
        }

        // Pastikan detail_linen punya kolom yang dipakai lifecycle (untuk fresh sqlite)
        if (Schema::hasTable('detail_linen')) {
            Schema::table('detail_linen', function (Blueprint $table) {
                if (! Schema::hasColumn('detail_linen', 'detail_status_kepemilikan')) {
                    $table->string('detail_status_kepemilikan')->nullable();
                }
                if (! Schema::hasColumn('detail_linen', 'detail_status_register')) {
                    $table->string('detail_status_register')->nullable();
                }
                if (! Schema::hasColumn('detail_linen', 'detail_status_linen')) {
                    $table->string('detail_status_linen')->nullable();
                }
                if (! Schema::hasColumn('detail_linen', 'detail_report')) {
                    $table->date('detail_report')->nullable();
                }
                if (! Schema::hasColumn('detail_linen', 'detail_tgl_cek')) {
                    $table->date('detail_tgl_cek')->nullable();
                }
            });
        }

        // Master data legacy tanpa migration (ruangan, jenis_linen, jenis_bahan, supplier, kategori, warehouse)
        if (! Schema::hasTable('ruangan')) {
            Schema::create('ruangan', function (Blueprint $table) {
                $table->increments('ruangan_id');
                $table->string('ruangan_nama')->nullable();
                $table->string('ruangan_deskripsi')->nullable();
                $table->string('ruangan_code')->nullable();
            });
        }
        if (! Schema::hasTable('kategori')) {
            Schema::create('kategori', function (Blueprint $table) {
                $table->increments('kategori_id');
                $table->string('kategori_nama')->default('');
                $table->string('kategori_deskripsi')->nullable();
            });
        }
        if (! Schema::hasTable('jenis_linen')) {
            Schema::create('jenis_linen', function (Blueprint $table) {
                $table->increments('jenis_id');
                $table->integer('jenis_id_rs')->nullable();
                $table->integer('jenis_id_kategori')->nullable();
                $table->string('jenis_nama')->default('');
                $table->string('jenis_deskripsi')->nullable();
                $table->string('jenis_gambar')->nullable();
                $table->double('jenis_berat')->nullable();
            });
        }
        if (! Schema::hasTable('jenis_bahan')) {
            Schema::create('jenis_bahan', function (Blueprint $table) {
                $table->increments('bahan_id');
                $table->string('bahan_nama')->nullable();
                $table->string('bahan_deskripsi')->nullable();
            });
        }
        if (! Schema::hasTable('supplier')) {
            Schema::create('supplier', function (Blueprint $table) {
                $table->increments('supplier_id');
                $table->string('supplier_nama')->nullable();
                $table->text('supplier_alamat')->nullable();
                $table->string('supplier_phone')->nullable();
                $table->string('supplier_kontak')->nullable();
                $table->string('supplier_email')->nullable();
            });
        }
        if (! Schema::hasTable('warehouse')) {
            Schema::create('warehouse', function (Blueprint $table) {
                $table->increments('warehouse_id');
                $table->string('warehouse_nama')->nullable();
            });
            // seed gudang utama id 1 untuk grouping
            DB::table('warehouse')->insert(['warehouse_id' => 1, 'warehouse_nama' => 'Gudang Utama']);
        }

        // bersih legacy — dipakai report pengiriman (BuildsDeliveryReport:161) + delivery lama
        if (! Schema::hasTable('bersih')) {
            Schema::create('bersih', function (Blueprint $table) {
                $table->bigIncrements('bersih_id');
                $table->string('bersih_rfid')->nullable();
                $table->string('bersih_status')->nullable();
                $table->integer('bersih_id_rs')->nullable();
                $table->integer('bersih_id_ruangan')->nullable();
                $table->string('bersih_barcode')->nullable();
                $table->string('bersih_delivery')->nullable();
                $table->dateTime('bersih_created_at')->nullable();
                $table->dateTime('bersih_updated_at')->nullable();
                $table->integer('bersih_created_by')->nullable();
                $table->integer('bersih_updated_by')->nullable();
                $table->date('bersih_report')->nullable();
                $table->index('bersih_rfid');
            });
        }
    }

    public function down(): void
    {
        // tidak drop di down untuk menjaga data produksi; hanya untuk test rollback
        // Schema::dropIfExists('transaksi');
        // Schema::dropIfExists('outstanding');
        // Schema::dropIfExists('cetak');
    }
};
