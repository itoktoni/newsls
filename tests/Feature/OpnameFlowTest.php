<?php

use App\Enums\RsStatusEnum;
use App\Models\DetailLinen;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Opname;
use App\Models\OpnameDetail;
use App\Models\Outstanding;
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
    $this->actingAs($this->admin)->get('/report-opname-mutasi/table?opname_id='.$opname->opname_id)->assertOk()->assertSee('Report Opname Mutasi')->assertSee('Ringkasan Mutasi Harian');

    $print = fn (string $path) => $this->actingAs($this->admin)->get($path.'?opname_id='.$opname->opname_id)->assertOk();
    $print('/report-rekap-opname/print')->assertSee('REKAP OPNAME')->assertSee('Seprai Opname');
    $print('/report-opname-detail/print')->assertSee('REPORT OPNAME DETAIL')->assertSee('OP_RFID_1')->assertSee('Seprai Opname');
    $print('/report-opname-summary/print')->assertSee('REPORT OPNAME SUMMARY')->assertSee('Total Snapshot');
    $print('/report-opname-hilang/print')->assertSee('BELUM TERBACA');
    $print('/report-opname-hilang-warehouse/print')->assertSee('WAREHOUSE');
    $print('/report-opname-mutasi/print')->assertSee('REPORT OPNAME MUTASI')->assertSee('BELUM')->assertSee('TOTAL OPNAME');
    $this->actingAs($this->admin)->get('/report-rekap-opname/export-excel?opname_id='.$opname->opname_id)->assertOk()->assertHeader('Content-Disposition');
    $this->actingAs($this->admin)->get('/report-opname-detail/export-excel?opname_id='.$opname->opname_id)->assertOk()->assertHeader('Content-Disposition');
    $this->actingAs($this->admin)->get('/report-opname-summary/export-excel?opname_id='.$opname->opname_id)->assertOk()->assertHeader('Content-Disposition');
    $this->actingAs($this->admin)->get('/report-opname-hilang/export-excel?opname_id='.$opname->opname_id)->assertOk()->assertHeader('Content-Disposition');
    $this->actingAs($this->admin)->get('/report-opname-hilang-warehouse/export-excel?opname_id='.$opname->opname_id)->assertOk()->assertHeader('Content-Disposition');
    $this->actingAs($this->admin)->get('/report-opname-mutasi/export-excel?opname_id='.$opname->opname_id)->assertOk()->assertHeader('Content-Disposition');

    // 6) API opname list/detail + record tetap ada
    $listRes = $this->actingAs($this->admin, 'sanctum')->getJson('/api/opname')->assertOk();
    expect($listRes->json('status'))->toBeTrue()
        ->and($listRes->json('name'))->toBe('List')
        ->and($listRes->json('message'))->toBe('Data berhasil diambil');

    // Kontrak legacy: {opname_id, opname_start, opname_end, rs_id, rs_nama}
    $row = collect($listRes->json('data'))->firstWhere('opname_id', $opname->opname_id);
    expect($row)->toMatchArray([
        'opname_id' => $opname->opname_id,
        'opname_start' => now()->format('Y-m-d'),
        'opname_end' => now()->addDays(1)->format('Y-m-d'),
        'rs_id' => $this->rs->rs_id,
        'rs_nama' => 'RS Opname',
    ])->and(array_keys($row))->toBe(['opname_id', 'opname_start', 'opname_end', 'rs_id', 'rs_nama']);

    // Legacy andalan: hanya envelope + data, tanpa key rs/ruangan/opname
    expect(array_keys($listRes->json()))->toBe(['status', 'code', 'name', 'message', 'data']);

    $this->actingAs($this->admin, 'sanctum')->getJson("/api/opname/{$opname->opname_id}/detail")->assertOk();

    // 6b) halaman detail web — semua RFID capture + nama linen dari detail_linen
    $detailPage = $this->actingAs($this->admin)->get('/opname/detail/'.$opname->opname_id);
    $detailPage->assertOk()->assertSee('OP_RFID_1')->assertSee('OP_RFID_2')->assertSee('OP_RFID_3');
    $detailPage->assertSee('Seprai Opname'); // jenis dari join detail_linen

    expect(Opname::where('opname_id', $opname->opname_id)->exists())->toBeTrue();

    // 7) Alias legacy: POST /api/opname sama dengan /api/opname/sync, responsnya
    //    baris opname_detail (satu baris per RFID, sync = 1).
    $aliasRes = $this->actingAs($this->admin, 'sanctum')->postJson('/api/opname', [
        'opname_id' => $opname->opname_id,
        'rfid' => ['OP_RFID_1'],
        'code' => 'SYNC-ALIAS',
    ])->assertOk()->assertJson(['status' => true, 'name' => 'Create']);
    expect($aliasRes->json('data'))->toHaveCount(1)
        ->and($aliasRes->json('data.0.opname_detail_rfid'))->toBe('OP_RFID_1')
        ->and($aliasRes->json('data.0.opname_detail_sync'))->toBe(1);

    // Key & urutan item harus sama persis dengan andalan (SaveOpnameService::$sent)
    expect(array_keys($aliasRes->json('data.0')))->toBe([
        'opname_detail_rfid',
        'opname_detail_id_opname',
        'opname_detail_code',
        'opname_detail_register',
        'opname_detail_updated_at',
        'opname_detail_updated_by',
        'opname_detail_transaksi',
        'opname_detail_proses',
        'opname_detail_scan_rs',
        'opname_detail_ketemu',
        'opname_detail_reff',
        'opname_detail_scan_by',
        'opname_detail_waktu',
        'opname_detail_sync',
    ]);

    // Tanggal harus string 'Y-m-d H:i:s' seperti andalan, bukan ISO8601 UTC
    expect($aliasRes->json('data.0.opname_detail_waktu'))->toMatch('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/')
        ->and($aliasRes->json('data.0.opname_detail_updated_at'))->toMatch('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/');

    // RFID yang sama dikirim dua kali tetap menghasilkan satu baris
    $dupRes = $this->actingAs($this->admin, 'sanctum')->postJson('/api/opname', [
        'opname_id' => $opname->opname_id,
        'rfid' => ['OP_RFID_1', 'OP_RFID_1'],
        'code' => 'SYNC-DUP',
    ])->assertOk();

    expect($dupRes->json('data'))->toHaveCount(1);
});

it('capture memetakan proses GUDANG ke QC (enum opname_detail_proses tidak punya GUDANG)', function () {
    $opname = Opname::updateOrCreate(
        ['opname_nama' => 'Opname Gudang', 'opname_id_rs' => $this->rs->rs_id],
        [
            'opname_mulai' => now()->format('Y-m-d'),
            'opname_selesai' => now()->addDays(1)->format('Y-m-d'),
            'opname_status' => 1,
            'opname_capture' => null,
            'opname_created_by' => $this->admin->id,
        ]
    );
    OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->delete();

    Outstanding::updateOrCreate(
        ['outstanding_rfid' => 'OP_RFID_1'],
        [
            'outstanding_rs_scan' => $this->rs->rs_id,
            'outstanding_status_transaksi' => 'KOTOR',
            'outstanding_status_proses' => 'GUDANG',
            'outstanding_status_hilang' => 'NORMAL',
        ]
    );

    $this->actingAs($this->admin)->get("/opname/capture/{$opname->opname_id}")->assertRedirect();

    $captured = OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)
        ->where('opname_detail_rfid', 'OP_RFID_1')
        ->first();

    expect($captured)->not->toBeNull()
        ->and($captured->opname_detail_proses)->toBe('QC')
        ->and($captured->opname_detail_transaksi)->toBe('KOTOR');
});

it('capture tidak menyertakan linen milik RS lain walau sedang berada di RS opname', function () {
    $otherRs = Rs::updateOrCreate(['rs_code' => 'OPN2'], ['rs_nama' => 'RS Opname Lain', 'rs_status' => RsStatusEnum::DEDICATED]);

    // OP_RFID_3 sedang di RS opname (detail_id_rs) tapi kepemilikannya (config_linen) RS lain
    DB::table('config_linen')->where('detail_rfid', 'OP_RFID_3')->delete();
    DB::table('config_linen')->insert(['detail_rfid' => 'OP_RFID_3', 'rs_id' => $otherRs->rs_id]);

    $opname = Opname::updateOrCreate(
        ['opname_nama' => 'Opname Join', 'opname_id_rs' => $this->rs->rs_id],
        [
            'opname_mulai' => now()->format('Y-m-d'),
            'opname_selesai' => now()->addDays(1)->format('Y-m-d'),
            'opname_status' => 1,
            'opname_capture' => null,
            'opname_created_by' => $this->admin->id,
        ]
    );
    OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->delete();

    $this->actingAs($this->admin)->get("/opname/capture/{$opname->opname_id}")->assertRedirect();

    $rfids = OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->pluck('opname_detail_rfid');

    expect($rfids)->toContain('OP_RFID_1')
        ->and($rfids)->not->toContain('OP_RFID_3');
});

it('report opname mutasi menghitung kolom harian dan saldo belum terbaca', function () {
    $opname = Opname::updateOrCreate(
        ['opname_nama' => 'Opname Mutasi', 'opname_id_rs' => $this->rs->rs_id],
        [
            'opname_mulai' => now()->format('Y-m-d'),
            'opname_selesai' => now()->addDays(2)->format('Y-m-d'),
            'opname_status' => 1,
            'opname_created_by' => $this->admin->id,
        ]
    );
    OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->delete();

    $hari1 = now()->format('Y-m-d').' 08:00:00';
    $hari2 = now()->addDay()->format('Y-m-d').' 08:00:00';

    $insert = function (string $rfid, ?string $waktu, ?string $transaksi, ?string $proses, int $ketemu) use ($opname) {
        DB::table('opname_detail')->insert([
            'opname_detail_id_opname' => $opname->opname_id,
            'opname_detail_rfid' => $rfid,
            'opname_detail_waktu' => $waktu,
            'opname_detail_transaksi' => $transaksi,
            'opname_detail_proses' => $proses,
            'opname_detail_ketemu' => $ketemu,
        ]);
    };

    // 5 linen terdaftar: 2 terbaca hari-1, 1 terbaca hari-2 (masih proses QC),
    // 2 tidak pernah terbaca dan tercatat pada hari-1.
    $insert('MUT_1', $hari1, 'BERSIH', 'BERSIH', 1);
    $insert('MUT_2', $hari1, 'BERSIH', 'BERSIH', 1);
    $insert('MUT_3', $hari2, 'KOTOR', 'QC', 1);
    $insert('MUT_4', $hari1, 'BERSIH', 'BERSIH', 0);
    $insert('MUT_5', $hari1, 'BERSIH', 'BERSIH', 0);

    $res = $this->actingAs($this->admin)->get('/report-opname-mutasi/table?opname_id='.$opname->opname_id)->assertOk();
    $rows = $res->viewData('rows');

    expect($rows)->toHaveCount(3)
        // hari-1: register 5, scan 2, belum = 5-2-0, masih proses 0,
        // total 4 (2 terbaca + 2 belum terbaca yang tercatat di hari-1)
        ->and($rows[0])->toMatchArray(['register' => 5, 'scan' => 2, 'belum' => 3, 'proses' => 0, 'total' => 4])
        // hari-2: scan 1 (QC masih proses), belum = 3-1
        ->and($rows[1])->toMatchArray(['scan' => 1, 'belum' => 2, 'proses' => 1, 'total' => 1])
        // hari-3: tidak ada aktivitas, saldo tetap
        ->and($rows[2])->toMatchArray(['scan' => 0, 'belum' => 2, 'proses' => 0, 'total' => 0]);

    // Total kolom TOTAL OPNAME = REGISTER (semua linen terdaftar terbagi habis ke hari-hari)
    expect($res->viewData('sum'))->toMatchArray(['register' => 5, 'scan' => 3, 'belum' => 2, 'proses' => 1, 'total' => 5]);
});
