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

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    // Lanjut dari step1-3 jika ada BERSIH linen — jangan hapus
    $existingRs = Rs::where('rs_code', 'STP1')->first();
    $hasBersih = $existingRs && DetailLinen::where('detail_id_rs', $existingRs->rs_id)->where('detail_status_linen', 'BERSIH')->count() >= 3;
    if ($hasBersih) {
        $this->rs = $existingRs;
        $this->ruangan = Ruangan::where('ruangan_code', 'DHL')->first() ?? Ruangan::create(['ruangan_nama' => 'Ruang Opname']);
        $this->jenis = JenisLinen::first() ?? JenisLinen::create(['jenis_nama' => 'Seprai']);
        $this->bahan = JenisBahan::first() ?? JenisBahan::create(['bahan_nama' => 'Katun']);
        $this->supplier = Supplier::first() ?? Supplier::create(['supplier_nama' => 'Sup']);
        $this->admin = User::where('email', 'step1@bka.test')->first() ?? User::create(['name' => 'Admin Step4', 'email' => 'step4@bka.test', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
        DB::table('rs_dan_user')->updateOrInsert(['user_id' => $this->admin->id, 'rs_id' => $this->rs->rs_id], []);
        $this->rfids = DetailLinen::where('detail_id_rs', $this->rs->rs_id)->where('detail_status_linen', 'BERSIH')->pluck('detail_rfid')->take(4)->all();
        if (count($this->rfids) < 4) {
            $this->rfids = ['STEP4_1', 'STEP4_2', 'STEP4_3', 'STEP4_4'];
        }

        return;
    }

    $this->rs = Rs::updateOrCreate(['rs_code' => 'STP4'], ['rs_nama' => 'RS Step4', 'rs_status' => RsStatusEnum::DEDICATED]);
    $this->ruangan = Ruangan::firstOrCreate(['ruangan_nama' => 'Ruang Opname'], ['ruangan_code' => 'OPN']);
    $this->jenis = JenisLinen::firstOrCreate(['jenis_nama' => 'Seprai'], []);
    $this->bahan = JenisBahan::firstOrCreate(['bahan_nama' => 'Katun'], []);
    $this->supplier = Supplier::firstOrCreate(['supplier_nama' => 'Sup'], []);
    DB::table('rs_dan_ruangan')->updateOrInsert(['rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id], []);
    DB::table('rs_dan_jenis')->updateOrInsert(['rs_id' => $this->rs->rs_id, 'jenis_id' => $this->jenis->jenis_id], ['parstock' => 10]);
    $this->admin = User::firstOrCreate(['email' => 'step4@bka.test'], ['name' => 'Admin Step4', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    DB::table('rs_dan_user')->updateOrInsert(['user_id' => $this->admin->id, 'rs_id' => $this->rs->rs_id], []);
    foreach (['STEP4_1', 'STEP4_2', 'STEP4_3', 'STEP4_4'] as $rfid) {
        DetailLinen::updateOrCreate(['detail_rfid' => $rfid], ['detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id, 'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED, 'detail_status_linen' => 'BERSIH', 'detail_updated_by' => $this->admin->id]);
        DB::table('config_linen')->updateOrInsert(['detail_rfid' => $rfid, 'rs_id' => $this->rs->rs_id], []);
    }
    $this->rfids = ['STEP4_1', 'STEP4_2', 'STEP4_3', 'STEP4_4'];
});

/**
 * STEP 4 — OPNAME (capture, sync, dan semua report opname)
 * Replikasi andalan: opname → capture snapshot → sync RFID → report rekap/detail/summary/hilang
 */
it('step4 opname capture sync dan semua report opname', function () {
    // 1) Buat (atau lanjutkan) record opname — persist, idempoten
    $opname = Opname::updateOrCreate(
        ['opname_nama' => 'Opname Step4', 'opname_id_rs' => $this->rs->rs_id],
        [
            'opname_mulai' => now()->format('Y-m-d'),
            'opname_selesai' => now()->addDays(1)->format('Y-m-d'),
            'opname_status' => 1,
            'opname_created_by' => $this->admin->id,
        ]
    );
    expect(Opname::where('opname_id', $opname->opname_id)->exists())->toBeTrue();

    // reset capture bila sudah pernah — record opname tetap, snapshot diulang
    OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->delete();
    $opname->opname_capture = null;
    $opname->save();
    expect($opname->opname_capture)->toBeNull();

    // 2) Capture via tombol Capture (web) → ketemu=0 untuk semua
    $this->actingAs($this->admin)->get("/opname/capture/{$opname->opname_id}")->assertRedirect();
    $opname->refresh();
    expect($opname->opname_capture)->not->toBeNull();
    $total = count($this->rfids);
    expect(OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->count())->toBe($total);
    expect(OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_ketemu', 0)->count())->toBe($total);
    expect(OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_ketemu', 1)->count())->toBe(0);

    // 3) Scan via API sync → ketemu=1
    $sync1 = array_slice($this->rfids, 0, 2);
    $this->actingAs($this->admin, 'sanctum')->postJson('/api/opname/sync', [
        'opname_id' => $opname->opname_id,
        'rfid' => $sync1,
        'code' => 'SYNC-STEP4',
    ])->assertOk()->assertJson(['status' => true]);
    expect(OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_ketemu', 1)->count())->toBe(min(2, $total));

    // 4) Sync lagi — sisa
    $sync2 = array_slice($this->rfids, 2, 1);
    if (! empty($sync2)) {
        $this->actingAs($this->admin, 'sanctum')->postJson('/api/opname/sync', [
            'opname_id' => $opname->opname_id,
            'rfid' => $sync2,
            'code' => 'SYNC-STEP4-2',
        ])->assertOk();
    }
    $expectedKetemu = min(3, $total);
    expect(OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_ketemu', 1)->count())->toBe($expectedKetemu);

    // 4b) form sync web (rfid_text per baris) — sisa rfid ketemu
    $rest = array_slice($this->rfids, 3);
    if (! empty($rest)) {
        $this->actingAs($this->admin)->post("/opname/sync/{$opname->opname_id}", [
            'rfid_text' => implode("\n", $rest),
        ])->assertRedirect();
        expect(OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_ketemu', 1)->count())->toBe($total);
    }

    // 5) Cek opname detail via API
    $this->actingAs($this->admin, 'sanctum')->getJson("/api/opname/{$opname->opname_id}/detail")->assertOk()->assertJson(['status' => true]);
    $this->actingAs($this->admin, 'sanctum')->getJson('/api/opname')->assertOk();

    // 5b) table opname (ada tombol Capture/Sync di view)
    $this->actingAs($this->admin)->get('/opname/table')->assertOk()->assertSee('Opname Step4');

    // 5c) halaman detail web: semua RFID capture + join detail_linen
    $detailRes = $this->actingAs($this->admin)->get('/opname/detail/'.$opname->opname_id);
    $detailRes->assertOk()->assertSee('List RFID')->assertSee($this->rfids[0]);
    foreach ($this->rfids as $rfid) {
        $detailRes->assertSee($rfid);
    }

    // 6) Semua report opname — table + print
    $this->actingAs($this->admin)->get('/report-rekap-opname/table?opname_id='.$opname->opname_id)->assertOk()->assertSee('Rekap Opname');
    $this->actingAs($this->admin)->get('/report-opname-detail/table?opname_id='.$opname->opname_id)->assertOk()->assertSee('Report Opname Detail');
    $this->actingAs($this->admin)->get('/report-opname-summary/table?opname_id='.$opname->opname_id)->assertOk()->assertSee('Report Opname Summary');
    $this->actingAs($this->admin)->get('/report-opname-hilang/table?opname_id='.$opname->opname_id)->assertOk()->assertSee('Report Opname Belum Terbaca');
    $this->actingAs($this->admin)->get('/report-opname-hilang-warehouse/table?opname_id='.$opname->opname_id)->assertOk()->assertSee('Hilang Warehouse');

    $this->actingAs($this->admin)->get('/report-rekap-opname/print?opname_id='.$opname->opname_id)
        ->assertOk()->assertSee('REKAP OPNAME');
    $this->actingAs($this->admin)->get('/report-opname-detail/print?opname_id='.$opname->opname_id)
        ->assertOk()->assertSee('REPORT OPNAME DETAIL')->assertSee($this->rfids[0]);
    $this->actingAs($this->admin)->get('/report-opname-summary/print?opname_id='.$opname->opname_id)
        ->assertOk()->assertSee('REPORT OPNAME SUMMARY')->assertSee('Total Snapshot');
    $this->actingAs($this->admin)->get('/report-rekap-opname/export-excel?opname_id='.$opname->opname_id)
        ->assertOk()->assertHeader('Content-Disposition');
    $this->actingAs($this->admin)->get('/report-opname-detail/export-excel?opname_id='.$opname->opname_id)
        ->assertOk()->assertHeader('Content-Disposition');

    // 7) ketemu + hilang = total snapshot
    $ketemuCount = OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_ketemu', 1)->count();
    $hilangCount = OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_ketemu', 0)->count();
    expect($ketemuCount + $hilangCount)->toBe($total);
    expect($ketemuCount)->toBe($expectedKetemu + count($rest));
    expect($hilangCount)->toBe($total - ($expectedKetemu + count($rest)));

    // record opname tetap ada setelah full flow
    expect(Opname::where('opname_id', $opname->opname_id)->exists())->toBeTrue();
});
