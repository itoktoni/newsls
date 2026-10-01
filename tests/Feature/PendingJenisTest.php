<?php

use App\Actions\PendingJenisRecapAction;
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
    $this->rs = Rs::create(['rs_nama' => 'RS Hutang', 'rs_code' => 'HTG'.$uniq, 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang Hutang', 'ruangan_code' => 'HTG'.$uniq]);
    $this->jenisDuk = JenisLinen::create(['jenis_nama' => 'Duk Test '.$uniq]);
    $this->jenisLunas = JenisLinen::create(['jenis_nama' => 'Lunas Test '.$uniq]);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Katun Hutang '.$uniq]);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier Hutang '.$uniq]);

    $this->makeDetail = function (string $rfid, int $jenisId) {
        DetailLinen::create([
            'detail_rfid' => $rfid, 'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
            'detail_id_jenis' => $jenisId, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
            'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::KOTOR,
            'detail_created_by' => 1,
        ]);
        DB::table('config_linen')->insert(['detail_rfid' => $rfid, 'rs_id' => $this->rs->rs_id]);
    };

    $this->makeKotor = function (string $rfid) {
        DB::table('transaksi')->insert([
            'transaksi_key' => 'KTR-'.$rfid, 'transaksi_rfid' => $rfid,
            'transaksi_rs_ori' => $this->rs->rs_id, 'transaksi_rs_scan' => $this->rs->rs_id,
            'transaksi_beda_rs' => 'TIDAK', 'transaksi_id_ruangan' => $this->ruangan->ruangan_id,
            'transaksi_status' => 'KOTOR',
            'transaksi_created_at' => now()->format('Y-m-d H:i:s'),
            'transaksi_created_by' => 1,
            'transaksi_updated_at' => now()->format('Y-m-d H:i:s'),
            'transaksi_updated_by' => 1,
        ]);
    };

    $this->makeKirim = function (string $rfid, int $jenisId) {
        DB::table('bersih')->insert([
            'bersih_rfid' => $rfid, 'bersih_status' => 'BERSIH',
            'bersih_id_rs' => $this->rs->rs_id, 'bersih_id_ruangan' => $this->ruangan->ruangan_id,
            'bersih_barcode' => 'BC-'.$rfid, 'bersih_delivery' => 'BSH-'.$rfid,
            'bersih_created_at' => now()->format('Y-m-d H:i:s'),
            'bersih_updated_at' => now()->format('Y-m-d H:i:s'),
            'bersih_created_by' => 1, 'bersih_updated_by' => 1,
            'bersih_report' => now()->format('Y-m-d'),
        ]);
    };
});

/**
 * Skenario user: jenis DUK kotor 10, dikirim 5 (RFID BERBEDA) → pending 5.
 */
it('pending per jenis = masuk kotor − keluar bersih, lunas boleh RFID berbeda', function () {
    $uniq = strtoupper(uniqid());

    // MASUK: 10 RFID kotor jenis DUK
    for ($i = 1; $i <= 10; $i++) {
        $rfid = "DUK-KOTOR-{$uniq}-{$i}";
        ($this->makeDetail)($rfid, $this->jenisDuk->jenis_id);
        ($this->makeKotor)($rfid);
    }

    // KELUAR: 5 RFID BERBEDA jenis DUK sudah delivery
    for ($i = 1; $i <= 5; $i++) {
        $rfid = "DUK-BERSIH-{$uniq}-{$i}";
        ($this->makeDetail)($rfid, $this->jenisDuk->jenis_id);
        ($this->makeKirim)($rfid, $this->jenisDuk->jenis_id);
    }

    // Jenis LUNAS: masuk 2 keluar 2 → tidak muncul di rekap
    for ($i = 1; $i <= 2; $i++) {
        $rfid = "LNS-{$uniq}-{$i}";
        ($this->makeDetail)($rfid, $this->jenisLunas->jenis_id);
        ($this->makeKotor)($rfid);
        ($this->makeKirim)($rfid, $this->jenisLunas->jenis_id);
    }

    $recap = PendingJenisRecapAction::run(['rs_id' => $this->rs->rs_id]);

    expect($recap)->toHaveCount(1);
    $row = $recap->first();
    expect($row['jenis_nama'])->toBe($this->jenisDuk->jenis_nama);
    expect($row['status'])->toBe('KOTOR');
    expect($row['masuk'])->toBe(10);
    expect($row['keluar'])->toBe(5);
    expect($row['pending'])->toBe(5);

    // Total hutang semua = 5
    expect($recap->sum('pending'))->toBe(5);

    // Halaman report hidup: filter + cetak + total
    $admin = User::create(['name' => 'Admin Hutang', 'email' => 'hutang-'.$uniq.'@bka.test',
        'password' => Hash::make('password'), 'role' => 'admin',
        'verified_at' => now(), 'email_verified_at' => now()]);
    $this->actingAs($admin)->get('/report-pending-jenis/table?rs_id='.$this->rs->rs_id)->assertOk();
    $this->actingAs($admin)->get('/report-pending-jenis/print?rs_id='.$this->rs->rs_id)
        ->assertOk()->assertSee('REKAP PENDING PER JENIS')->assertSee('TOTAL');
});
