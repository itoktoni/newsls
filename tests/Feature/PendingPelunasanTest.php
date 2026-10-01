<?php

use App\Actions\PendingPelunasanAction;
use App\Enums\CuciEnum;
use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Models\DetailLinen;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $uniq = strtoupper(uniqid());
    $this->rs = Rs::create(['rs_nama' => 'RS Lunasi', 'rs_code' => 'LNS'.$uniq, 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang Lunasi', 'ruangan_code' => 'LNS'.$uniq]);
    $this->jenis = JenisLinen::create(['jenis_nama' => 'Duk Lunasi '.$uniq]);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Katun Lunasi '.$uniq]);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier Lunasi '.$uniq]);
    $this->admin = User::create(['name' => 'Admin Lunasi', 'email' => 'lunasi-'.$uniq.'@bka.test',
        'password' => Hash::make('password'), 'role' => 'admin',
        'verified_at' => now(), 'email_verified_at' => now()]);

    $this->tKotor = now()->subDays(2)->format('Y-m-d H:i:s');

    // 10 RFID kotor 2 hari lalu
    for ($i = 1; $i <= 10; $i++) {
        $rfid = "LNS-K-{$uniq}-{$i}";
        DetailLinen::create([
            'detail_rfid' => $rfid, 'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
            'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
            'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::KOTOR,
            'detail_created_by' => $this->admin->id,
        ]);
        DB::table('transaksi')->insert([
            'transaksi_key' => 'KTR-'.$rfid, 'transaksi_rfid' => $rfid,
            'transaksi_rs_ori' => $this->rs->rs_id, 'transaksi_rs_scan' => $this->rs->rs_id,
            'transaksi_beda_rs' => 'TIDAK', 'transaksi_id_ruangan' => $this->ruangan->ruangan_id,
            'transaksi_status' => 'KOTOR',
            'transaksi_created_at' => $this->tKotor, 'transaksi_created_by' => $this->admin->id,
            'transaksi_updated_at' => $this->tKotor, 'transaksi_updated_by' => $this->admin->id,
        ]);
    }

    // Bayar 5 dengan RFID BERBEDA di hari yang sama
    for ($i = 1; $i <= 5; $i++) {
        $rfid = "LNS-B1-{$uniq}-{$i}";
        DetailLinen::create([
            'detail_rfid' => $rfid, 'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
            'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
            'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::BERSIH,
            'detail_created_by' => $this->admin->id,
        ]);
        DB::table('bersih')->insert([
            'bersih_rfid' => $rfid, 'bersih_status' => 'BERSIH',
            'bersih_id_rs' => $this->rs->rs_id, 'bersih_id_ruangan' => $this->ruangan->ruangan_id,
            'bersih_barcode' => 'BC1-'.$rfid, 'bersih_delivery' => 'BSH1-'.$uniq,
            'bersih_created_at' => $this->tKotor, 'bersih_updated_at' => $this->tKotor,
            'bersih_created_by' => $this->admin->id, 'bersih_updated_by' => $this->admin->id,
            'bersih_report' => now()->subDays(2)->format('Y-m-d'),
        ]);
    }

    // Bayar 3 lagi hari ini (RFID beda lagi)
    for ($i = 1; $i <= 3; $i++) {
        $rfid = "LNS-B2-{$uniq}-{$i}";
        DetailLinen::create([
            'detail_rfid' => $rfid, 'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
            'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
            'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::BERSIH,
            'detail_created_by' => $this->admin->id,
        ]);
        DB::table('bersih')->insert([
            'bersih_rfid' => $rfid, 'bersih_status' => 'BERSIH',
            'bersih_id_rs' => $this->rs->rs_id, 'bersih_id_ruangan' => $this->ruangan->ruangan_id,
            'bersih_barcode' => 'BC2-'.$rfid, 'bersih_delivery' => 'BSH2-'.$uniq,
            'bersih_created_at' => now()->format('Y-m-d H:i:s'),
            'bersih_updated_at' => now()->format('Y-m-d H:i:s'),
            'bersih_created_by' => $this->admin->id, 'bersih_updated_by' => $this->admin->id,
            'bersih_report' => now()->format('Y-m-d'),
        ]);
    }

    // 1 lagi hari ini, kode delivery BERBEDA tanggal SAMA → gabung 1 baris
    $rfid = "LNS-B3-{$uniq}-1";
    DetailLinen::create([
        'detail_rfid' => $rfid, 'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
        'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
        'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
        'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::BERSIH,
        'detail_created_by' => $this->admin->id,
    ]);
    DB::table('bersih')->insert([
        'bersih_rfid' => $rfid, 'bersih_status' => 'BERSIH',
        'bersih_id_rs' => $this->rs->rs_id, 'bersih_id_ruangan' => $this->ruangan->ruangan_id,
        'bersih_barcode' => 'BC3-'.$rfid, 'bersih_delivery' => 'BSH3-'.$uniq,
        'bersih_created_at' => now()->format('Y-m-d H:i:s'),
        'bersih_updated_at' => now()->format('Y-m-d H:i:s'),
        'bersih_created_by' => $this->admin->id, 'bersih_updated_by' => $this->admin->id,
        'bersih_report' => now()->format('Y-m-d'),
    ]);
});

/**
 * Skenario user: kotor 10 → kirim 5 (pending 5) → 2 hari kemudian bayar 3.
 * Lot: terbayar 8, sisa 2. Alokasi per delivery terlacak.
 */
it('pelunasan FIFO: lot 10 dibayar 5 lalu 3, sisa 2 dengan alokasi per delivery', function () {
    $result = PendingPelunasanAction::run(['rs_id' => $this->rs->rs_id]);

    expect($result['lots'])->toHaveCount(1);
    $lot = $result['lots'][0];
    expect($lot['jumlah'])->toBe(10);
    expect($lot['terbayar'])->toBe(9);
    expect($lot['sisa'])->toBe(1);
    expect($lot['lunas'])->toBeFalse();
    expect($result['total_sisa'])->toBe(1);

    expect($result['payments'])->toHaveCount(3);
    expect($result['payments'][0]['jumlah'])->toBe(5);
    expect($result['payments'][0]['kelebihan'])->toBe(0);
    expect($result['payments'][0]['alokasi'][0]['qty'])->toBe(5);
    expect($result['payments'][1]['jumlah'])->toBe(3);
    expect($result['payments'][1]['alokasi'][0]['qty'])->toBe(3);

    // Cicilan: 1 row per pembayaran (5, 3, 1) + sisa BERJALAN per row
    expect($lot['cicilan'])->toHaveCount(3);
    expect($lot['cicilan'][0]['qty'])->toBe(5);
    expect($lot['cicilan'][0]['sisa_sebelum'])->toBe(10);
    expect($lot['cicilan'][0]['sisa_setelah'])->toBe(5);
    expect($lot['cicilan'][0]['lunas_setelah'])->toBeFalse();
    expect($lot['cicilan'][1]['qty'])->toBe(3);
    expect($lot['cicilan'][1]['sisa_setelah'])->toBe(2);
    expect($lot['cicilan'][2]['qty'])->toBe(1);
    expect($lot['cicilan'][2]['sisa_setelah'])->toBe(1);
    expect($lot['cicilan'][2]['lunas_setelah'])->toBeFalse();
    expect($lot['cicilan'][1]['tanggal'])->toBe($lot['cicilan'][2]['tanggal']);

    // Halaman report hidup
    $this->actingAs($this->admin)->get('/report-pelunasan-pending/print?rs_id='.$this->rs->rs_id)
        ->assertOk()->assertSee('PELUNASAN PENDING')->assertSee('Total Sisa Hutang');
});

/**
 * Summary pelunasan: 1 baris per jenis (masuk, terbayar, pending).
 */
it('summary pelunasan menampilkan total per jenis', function () {
    $summary = PendingPelunasanAction::summarize(PendingPelunasanAction::run(['rs_id' => $this->rs->rs_id])['lots']);

    expect($summary)->toHaveCount(1);
    expect($summary[0]['jenis_nama'])->toBe($this->jenis->jenis_nama);
    expect($summary[0]['masuk'])->toBe(10);
    expect($summary[0]['terbayar'])->toBe(9);
    expect($summary[0]['pending'])->toBe(1);

    $this->actingAs($this->admin)->get('/report-summary-pelunasan/print?rs_id='.$this->rs->rs_id)
        ->assertOk()->assertSee('SUMMARY PELUNASAN PER JENIS')->assertSee('TOTAL');
});
