<?php

use App\Enums\LinenStatusEnum;
use App\Models\DetailLinen;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');
    // seed flows via seeder logic inline (tanpa artisan) biar RefreshDatabase tetap isolasi
    $this->seed(\Database\Seeders\TestBkaFlowsSeeder::class);
});

it('memiliki 3 flow terpisah kotor/bersih/pending di test_bka', function () {
    // KOTOR: outstanding SCAN/NORMAL, detail KOTOR
    expect(DetailLinen::whereIn('detail_rfid', ['TEST_KOTOR_1','TEST_KOTOR_2','TEST_KOTOR_3'])->where('detail_status_linen', LinenStatusEnum::KOTOR)->count())->toBe(3);
    expect(DB::table('outstanding')->whereIn('outstanding_rfid', ['TEST_KOTOR_1','TEST_KOTOR_2','TEST_KOTOR_3'])->where('outstanding_status_proses', 'SCAN')->where('outstanding_status_hilang', 'NORMAL')->count())->toBe(3);
    expect(DB::table('transaksi')->whereIn('transaksi_rfid', ['TEST_KOTOR_1','TEST_KOTOR_2','TEST_KOTOR_3'])->where('transaksi_status', 'KOTOR')->count())->toBe(3);

    // BERSIH: detail BERSIH, tidak ada outstanding, ada bersih + cetak
    expect(DetailLinen::whereIn('detail_rfid', ['TEST_BERSIH_1','TEST_BERSIH_2','TEST_BERSIH_3'])->where('detail_status_linen', LinenStatusEnum::BERSIH)->count())->toBe(3);
    expect(DB::table('outstanding')->whereIn('outstanding_rfid', ['TEST_BERSIH_1','TEST_BERSIH_2','TEST_BERSIH_3'])->count())->toBe(0);
    expect(DB::table('bersih')->whereIn('bersih_rfid', ['TEST_BERSIH_1','TEST_BERSIH_2','TEST_BERSIH_3'])->where('bersih_status', 'BERSIH')->count())->toBe(3);
    expect(DB::table('cetak')->where('cetak_type', 2)->count())->toBe(1);

    // PENDING: outstanding PENDING/PENDING (hilang pending), detail masih KOTOR
    expect(DetailLinen::whereIn('detail_rfid', ['TEST_PENDING_1','TEST_PENDING_2','TEST_PENDING_3'])->where('detail_status_linen', LinenStatusEnum::KOTOR)->count())->toBe(3);
    expect(DB::table('outstanding')->whereIn('outstanding_rfid', ['TEST_PENDING_1','TEST_PENDING_2','TEST_PENDING_3'])->where('outstanding_status_proses', 'PENDING')->where('outstanding_status_hilang', 'PENDING')->count())->toBe(3);
    // pending tidak ada di bersih
    expect(DB::table('bersih')->whereIn('bersih_rfid', ['TEST_PENDING_1','TEST_PENDING_2','TEST_PENDING_3'])->count())->toBe(0);
});

it('totals dan report konsisten per flow', function () {
    $rsId = DB::table('rs')->value('rs_id');
    $ruanganId = DB::table('ruangan')->value('ruangan_id');
    $jenisId = DB::table('jenis_linen')->value('jenis_id');

    // total/kotor via outstanding SCAN = 3
    $kotorOutstanding = DB::table('outstanding')->where('outstanding_status_transaksi', 'KOTOR')->where('outstanding_status_proses', 'SCAN')->count();
    expect($kotorOutstanding)->toBe(3);

    // pending count = 3
    expect(DB::table('outstanding')->where('outstanding_status_hilang', 'PENDING')->count())->toBe(3);

    // bersih detail = 3 (dari flow bersih)
    expect(DetailLinen::where('detail_status_linen', LinenStatusEnum::BERSIH)->count())->toBe(3);
    // transaksi KOTOR total = 3(kotor) +3(bersih history) +3(pending) =9
    expect(DB::table('transaksi')->where('transaksi_status', 'KOTOR')->count())->toBe(9);

    // API totals (packaged in PackingDeliveryController) — hit via transaksi/outstanding
    // cetak delivery = 1 batch berisi 3 RFID bersih
    $cetak = DB::table('cetak')->where('cetak_type', 2)->first();
    expect(json_decode($cetak->cetak_rfids, true))->toHaveCount(3);
});
