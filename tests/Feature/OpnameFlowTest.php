<?php

use App\Enums\RsStatusEnum;
use App\Models\DetailLinen;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Opname;
use App\Models\OpnameDetail;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// PERSIST — jangan RefreshDatabase (truncate data step1–4)
// RefreshDatabase di file ini root cause "opname kosong" setelah step4 + full suite

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');
    $this->rs = Rs::updateOrCreate(['rs_code' => 'OPN'], ['rs_nama' => 'RS Opname', 'rs_status' => RsStatusEnum::DEDICATED]);
    $this->ruangan = Ruangan::firstOrCreate(['ruangan_nama' => 'Ruang A Opname'], ['ruangan_code' => 'OPA']);
    $this->jenis = JenisLinen::firstOrCreate(['jenis_nama' => 'Seprai Opname']);
    $this->bahan = JenisBahan::firstOrCreate(['bahan_nama' => 'Katun Opname']);
    $this->supplier = Supplier::firstOrCreate(['supplier_nama' => 'Sup Opname']);
    DB::table('rs_dan_ruangan')->updateOrInsert(['rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id], []);
    DB::table('rs_dan_jenis')->updateOrInsert(['rs_id' => $this->rs->rs_id, 'jenis_id' => $this->jenis->jenis_id], []);
    $this->admin = User::firstOrCreate(
        ['email' => 'opname@bka.test'],
        ['name' => 'Admin Opname', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]
    );
    DB::table('rs_dan_user')->updateOrInsert(['user_id' => $this->admin->id, 'rs_id' => $this->rs->rs_id], []);
    $this->token = $this->admin->createToken('test')->plainTextToken;

    foreach (['OP_RFID_1', 'OP_RFID_2', 'OP_RFID_3'] as $rfid) {
        DetailLinen::updateOrCreate(
            ['detail_rfid' => $rfid],
            [
                'detail_id_rs' => $this->rs->rs_id,
                'detail_id_ruangan' => $this->ruangan->ruangan_id,
                'detail_id_jenis' => $this->jenis->jenis_id,
                'detail_id_bahan' => $this->bahan->bahan_id,
                'detail_id_supplier' => $this->supplier->supplier_id,
                'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
                'detail_status_linen' => 'BERSIH',
                'detail_updated_by' => $this->admin->id,
            ]
        );
        DB::table('config_linen')->updateOrInsert(['detail_rfid' => $rfid, 'rs_id' => $this->rs->rs_id], []);
    }
});

it('opname capture, sync, dan semua report opname', function () {
    $opname = Opname::updateOrCreate(
        ['opname_nama' => 'Opname Test', 'opname_id_rs' => $this->rs->rs_id],
        [
            'opname_mulai' => now()->format('Y-m-d'),
            'opname_selesai' => now()->addDays(1)->format('Y-m-d'),
            'opname_status' => 1,
            'opname_created_by' => $this->admin->id,
        ]
    );

    // reset capture supaya idempoten (record tetap, detail di-snapshot ulang)
    OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->delete();
    $opname->opname_capture = null;
    $opname->save();
    expect($opname->opname_capture)->toBeNull();

    // 2) Capture — snapshot 3 RFID, semua ketemu=0
    $this->actingAs($this->admin)->get("/opname/capture/{$opname->opname_id}")->assertRedirect();
    $opname->refresh();
    expect($opname->opname_capture)->not->toBeNull();
    expect(OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->count())->toBe(3);
    expect(OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_ketemu', 0)->count())->toBe(3);
    expect(OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_ketemu', 1)->count())->toBe(0);

    // 3) Sync via API — 2 RFID ketemu
    $this->actingAs($this->admin, 'sanctum')->postJson('/api/opname/sync', [
        'opname_id' => $opname->opname_id,
        'rfid' => ['OP_RFID_1', 'OP_RFID_2'],
        'code' => 'SYNC-TEST',
    ])->assertOk()->assertJson(['status' => true]);
    expect(OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_ketemu', 1)->count())->toBe(2);
    expect(OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_ketemu', 0)->count())->toBe(1);

    // 4) Sync via API lagi — 1 lagi
    $this->actingAs($this->admin, 'sanctum')->postJson('/api/opname/sync', [
        'opname_id' => $opname->opname_id,
        'rfid' => ['OP_RFID_3'],
        'code' => 'SYNC-API2',
    ])->assertOk();
    expect(OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_ketemu', 1)->count())->toBe(3);

    // 5) Semua report opname — table + print
    $this->actingAs($this->admin)->get('/opname/table')->assertOk();
    $this->actingAs($this->admin)->get('/report-rekap-opname/table?opname_id='.$opname->opname_id)->assertOk()->assertSee('Rekap Opname');
    $this->actingAs($this->admin)->get('/report-opname-detail/table?opname_id='.$opname->opname_id)->assertOk()->assertSee('Report Opname Detail');
    $this->actingAs($this->admin)->get('/report-opname-summary/table?opname_id='.$opname->opname_id)->assertOk()->assertSee('Report Opname Summary');
    $this->actingAs($this->admin)->get('/report-opname-hilang/table?opname_id='.$opname->opname_id)->assertOk()->assertSee('Report Opname Belum Terbaca');
    $this->actingAs($this->admin)->get('/report-opname-hilang-warehouse/table?opname_id='.$opname->opname_id)->assertOk()->assertSee('Hilang Warehouse');

    $print = fn (string $path) => $this->actingAs($this->admin)->get($path.'?opname_id='.$opname->opname_id)->assertOk();
    $print('/report-rekap-opname/print')->assertSee('REKAP OPNAME')->assertSee('Seprai Opname');
    $print('/report-opname-detail/print')->assertSee('REPORT OPNAME DETAIL')->assertSee('OP_RFID_1')->assertSee('Seprai Opname');
    $print('/report-opname-summary/print')->assertSee('REPORT OPNAME SUMMARY')->assertSee('Total Snapshot');
    $print('/report-opname-hilang/print')->assertSee('BELUM TERBACA');
    $print('/report-opname-hilang-warehouse/print')->assertSee('WAREHOUSE');
    $this->actingAs($this->admin)->get('/report-rekap-opname/export-excel?opname_id='.$opname->opname_id)->assertOk()->assertHeader('Content-Disposition');
    $this->actingAs($this->admin)->get('/report-opname-detail/export-excel?opname_id='.$opname->opname_id)->assertOk()->assertHeader('Content-Disposition');
    $this->actingAs($this->admin)->get('/report-opname-summary/export-excel?opname_id='.$opname->opname_id)->assertOk()->assertHeader('Content-Disposition');
    $this->actingAs($this->admin)->get('/report-opname-hilang/export-excel?opname_id='.$opname->opname_id)->assertOk()->assertHeader('Content-Disposition');
    $this->actingAs($this->admin)->get('/report-opname-hilang-warehouse/export-excel?opname_id='.$opname->opname_id)->assertOk()->assertHeader('Content-Disposition');

    // 6) API opname list/detail + record tetap ada
    $this->actingAs($this->admin, 'sanctum')->getJson('/api/opname')->assertOk()->assertJson(['status' => true]);
    $this->actingAs($this->admin, 'sanctum')->getJson("/api/opname/{$opname->opname_id}/detail")->assertOk();

    // 6b) halaman detail web — semua RFID capture + nama linen dari detail_linen
    $detailPage = $this->actingAs($this->admin)->get('/opname/detail/'.$opname->opname_id);
    $detailPage->assertOk()->assertSee('OP_RFID_1')->assertSee('OP_RFID_2')->assertSee('OP_RFID_3');
    $detailPage->assertSee('Seprai Opname'); // jenis dari join detail_linen

    expect(Opname::where('opname_id', $opname->opname_id)->exists())->toBeTrue();
});
