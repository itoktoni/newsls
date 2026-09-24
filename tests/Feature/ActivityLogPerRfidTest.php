<?php

use App\Enums\LogType;
use App\Enums\RsStatusEnum;
use App\Models\DetailLinen;
use App\Models\Rs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

/**
 * Aturan: setiap operasi RFID ditulis satu baris di activity_log (tanpa dedupe),
 * supaya N RFID yang diproses bersamaan tetap menghasilkan N log.
 */
beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $this->admin = User::create([
        'name' => 'Admin Per RFID',
        'email' => 'admin-per-rfid@bka.test',
        'password' => 'password',
        'role' => 'admin',
    ]);

    $this->rs = Rs::create([
        'rs_nama' => 'RS Per RFID',
        'rs_code' => 'PRF',
        'rs_status' => RsStatusEnum::DEDICATED,
        'rs_aktif' => 1,
    ]);

    $this->token = $this->admin->createToken('per-rfid-token')->plainTextToken;

    $this->actingAs($this->admin);
});

it('menyimpan tiap log walau log_name sama di detik yang sama', function () {
    activity(LogType::GROUPING)->log('Grouping QC RFID PRF-1');
    activity(LogType::GROUPING)->log('Grouping QC RFID PRF-2');

    expect(DB::table('activity_log')->where('log_name', LogType::GROUPING)->count())->toBe(2);
});

it('menyimpan 1 log REGISTER untuk tiap RFID register massal di detik yang sama', function () {
    foreach (['PRF-1', 'PRF-2', 'PRF-3'] as $rfid) {
        DetailLinen::create([
            'detail_rfid' => $rfid,
            'detail_id_rs' => $this->rs->rs_id,
            'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
        ]);
    }

    expect(DB::table('activity_log')->where('log_name', LogType::REGISTER)->count())->toBe(3);
});

it('menyimpan 1 log KOTOR untuk tiap RFID scan kotor di detik yang sama', function () {
    foreach (['PRF-1', 'PRF-2', 'PRF-3'] as $rfid) {
        DetailLinen::create([
            'detail_rfid' => $rfid,
            'detail_id_rs' => $this->rs->rs_id,
            'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_status_linen' => 'BERSIH',
            'detail_report' => now()->subDays(2)->format('Y-m-d'),
        ]);
    }

    // detail_updated_at tidak fillable → set lewat query builder (guard kotor).
    DetailLinen::whereIn('detail_rfid', ['PRF-1', 'PRF-2', 'PRF-3'])
        ->update(['detail_updated_at' => now()->subDays(2)]);

    $this->withToken($this->token)->postJson('/api/transaksi/kotor', [
        'rfid' => ['PRF-1', 'PRF-2', 'PRF-3'],
        'rs_id' => $this->rs->rs_id,
        'key' => 'PRF-KOTOR-1',
    ])->assertOk()->assertJson(['status' => true]);

    expect(DB::table('activity_log')->where('log_name', LogType::KOTOR)->count())->toBe(3);
});
