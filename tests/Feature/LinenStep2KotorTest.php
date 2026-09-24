<?php

use App\Enums\CuciEnum;
use App\Enums\LinenStatusEnum;
use App\Enums\LogType;
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

    // Lanjut dari step1 jika ada — jangan hapus, biar data step1 kepakai
    $existingRs = Rs::where('rs_code', 'STP1')->first();
    $existingRfids = ['STEP1_1', 'STEP1_2', 'STEP1_3'];
    $hasStep1 = $existingRs && DetailLinen::whereIn('detail_rfid', $existingRfids)->where('detail_status_linen', LinenStatusEnum::BERSIH)->count() === 3;

    if ($hasStep1) {
        $this->rs = $existingRs;
        $this->ruangan = Ruangan::where('ruangan_code', 'DHL')->first() ?? Ruangan::create(['ruangan_nama' => 'Ruang Dahlia', 'ruangan_code' => 'DHL']);
        $this->jenis = JenisLinen::first() ?? JenisLinen::create(['jenis_nama' => 'Seprai Single']);
        $this->bahan = JenisBahan::first() ?? JenisBahan::create(['bahan_nama' => 'Katun']);
        $this->supplier = Supplier::first() ?? Supplier::create(['supplier_nama' => 'Supplier Test']);
        $this->admin = User::where('email', 'step1@bka.test')->first() ?? User::create(['name' => 'Admin Step1', 'email' => 'step1@bka.test', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
        DB::table('rs_dan_user')->updateOrInsert(['user_id' => $this->admin->id, 'rs_id' => $this->rs->rs_id], []);
        $this->token = $this->admin->createToken('test-token-'.uniqid())->plainTextToken;
        $this->rfids = $existingRfids;

        return;
    }

    // Fallback standalone (jalan step2 langsung tanpa step1) — buat data sendiri
    $this->rs = Rs::create(['rs_nama' => 'RS Step2', 'rs_code' => 'STP2', 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang Dahlia', 'ruangan_code' => 'DHL']);
    $this->jenis = JenisLinen::create(['jenis_nama' => 'Seprai Single']);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Katun']);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier Test']);
    DB::table('rs_dan_ruangan')->insert(['rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id]);
    DB::table('rs_dan_jenis')->insert(['rs_id' => $this->rs->rs_id, 'jenis_id' => $this->jenis->jenis_id, 'parstock' => 10]);
    DB::table('warehouse')->updateOrInsert(['warehouse_id' => 1], ['warehouse_nama' => 'Gudang Utama']);
    $this->admin = User::create(['name' => 'Admin Step2', 'email' => 'step2@bka.test', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    DB::table('rs_dan_user')->insert(['user_id' => $this->admin->id, 'rs_id' => $this->rs->rs_id]);
    $this->token = $this->admin->createToken('test-token')->plainTextToken;
    $this->rfids = ['STEP2_1', 'STEP2_2', 'STEP2_3'];
    foreach ($this->rfids as $rfid) {
        DetailLinen::create([
            'detail_rfid' => $rfid, 'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
            'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
            'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::BERSIH,
            'detail_report' => now()->subDays(2)->format('Y-m-d'),
            'detail_total_bersih' => 1, 'detail_created_by' => $this->admin->id,
        ]);
        DB::table('config_linen')->insert(['detail_rfid' => $rfid, 'rs_id' => $this->rs->rs_id]);
        DB::table('bersih')->insert([
            'bersih_rfid' => $rfid, 'bersih_status' => 'BERSIH', 'bersih_id_rs' => $this->rs->rs_id, 'bersih_id_ruangan' => $this->ruangan->ruangan_id,
            'bersih_barcode' => generateBarcode(), 'bersih_delivery' => generateDeliveryCode($this->rs->rs_code),
            'bersih_created_at' => now(), 'bersih_updated_at' => now(), 'bersih_created_by' => $this->admin->id, 'bersih_report' => now()->format('Y-m-d'),
        ]);
    }
});

/**
 * STEP 2 — KOTOR (scan kotor via api, cek outstanding, grouping masuk gudang)
 */
it('step2 scan kotor via api, cek outstanding, grouping masuk gudang', function () {
    $rfids = $this->rfids;

    // Guard kotor (port andalan): detail_updated_at harus sudah lewat
    // TRANSACTION_HOURS_ALLOWED jam. Kolomnya tidak fillable → set lewat query builder.
    DetailLinen::whereIn('detail_rfid', $rfids)->update(['detail_updated_at' => now()->subDays(2)]);

    // 1) Scan KOTOR via API — POST /api/transaksi/kotor
    $kotorKey = 'KTR-STEP2-'.strtoupper(uniqid());
    $this->withToken($this->token)->postJson('/api/transaksi/kotor', [
        'rfid' => $rfids, 'rs_id' => $this->rs->rs_id, 'key' => $kotorKey,
    ])->assertOk()->assertJson(['status' => true])->assertJsonPath('data.status', 'KOTOR');

    // Cek transaksi KOTOR terinsert, outstanding SCAN/NORMAL, detail jadi KOTOR
    expect(DB::table('transaksi')->where('transaksi_key', $kotorKey)->where('transaksi_status', 'KOTOR')->count())->toBe(3);
    foreach ($rfids as $rfid) {
        $out = DB::table('outstanding')->where('outstanding_rfid', $rfid)->first();
        expect($out)->not->toBeNull();
        expect($out->outstanding_status_transaksi)->toBe('KOTOR');
        expect($out->outstanding_status_proses)->toBe('SCAN');
        expect($out->outstanding_status_hilang)->toBe('NORMAL');
        expect(DetailLinen::where('detail_rfid', $rfid)->value('detail_status_linen'))->toBe(LinenStatusEnum::KOTOR);
    }

    // 2) Cek report kotor & outstanding vs bersih
    $this->actingAs($this->admin)->get('/report-rekap-kotor/table?rs_id='.$this->rs->rs_id)->assertOk();
    $this->actingAs($this->admin)->get('/report-detail-kotor/table?rs_id='.$this->rs->rs_id)->assertOk();
    $this->actingAs($this->admin)->get('/report-kotor-vs-bersih/table?rs_id='.$this->rs->rs_id)->assertOk();
    expect(DB::table('outstanding')->where('outstanding_status_transaksi', 'KOTOR')->count())->toBe(3);
    expect(DB::table('transaksi')->where('transaksi_status', 'KOTOR')->count())->toBe(3);
    // bersih masih 3 (detail BERSIH sebelumnya) tapi detail sekarang KOTOR, jadi bersih vs kotor terpisah
    expect(DetailLinen::where('detail_status_linen', LinenStatusEnum::BERSIH)->count())->toBe(0);

    // 3) Grouping masuk gudang — GET /api/grouping/{rfid} → outstanding GUDANG, detail GUDANG
    foreach ($rfids as $rfid) {
        $this->withToken($this->token)->getJson("/api/grouping/{$rfid}")->assertOk()->assertJson(['rfid' => $rfid]);
    }

    // Log grouping wajib uppercase GROUPING + ber-subject DetailLinen (RFID),
    // supaya RFID-nya tampil di activity-log/table — bukan subject kosong.
    foreach ($rfids as $rfid) {
        $log = DB::table('activity_log')
            ->where('log_name', LogType::GROUPING)
            ->where('subject_type', DetailLinen::class)
            ->where('subject_id', $rfid)
            ->first();

        expect($log)->not->toBeNull("Log GROUPING untuk {$rfid} harus punya subject RFID");
        expect(json_decode($log->properties, true)['rfid'] ?? null)->toBe($rfid);
    }
    foreach ($rfids as $rfid) {
        expect(DetailLinen::where('detail_rfid', $rfid)->value('detail_status_linen'))->toBe(LinenStatusEnum::GUDANG);
        $out = DB::table('outstanding')->where('outstanding_rfid', $rfid)->first();
        expect($out->outstanding_status_proses)->toBe('GUDANG');
        expect((int) $out->outstanding_id_warehouse)->toBe(1);
    }

    // Grouping mengisi transaksi_grouping_date untuk baris transaksi RFID itu yang masih kosong
    foreach ($rfids as $rfid) {
        expect(DB::table('transaksi')->where('transaksi_rfid', $rfid)->whereNull('transaksi_grouping_date')->count())->toBe(0);
        expect(DB::table('transaksi')->where('transaksi_rfid', $rfid)->where('transaksi_grouping_date', today()->format('Y-m-d'))->exists())->toBeTrue();
    }

    // 4) Cek stok gudang & viewer bersih (stat Antrean Packing = outstanding SCAN/QC/REGISTER/GUDANG)
    expect(DB::table('outstanding')->where('outstanding_status_proses', 'GUDANG')->count())->toBe(3);
    $this->actingAs($this->admin)->get('/bersih/table?tab=packing')->assertOk()->assertSee('Antrean Packing');

    // dedup: scan kotor lagi hari sama tidak nambah transaksi (existing today skip)
    $dupKey = 'KTR-DUP-'.strtoupper(uniqid());
    $this->withToken($this->token)->postJson('/api/transaksi/kotor', [
        'rfid' => $rfids, 'rs_id' => $this->rs->rs_id, 'key' => $dupKey,
    ])->assertOk()->assertJson(['status' => true]);
    expect(DB::table('transaksi')->whereDate('transaksi_created_at', today())->count())->toBe(3);
    expect(DB::table('outstanding')->count())->toBe(3); // outstanding tetap 3, diupdate
});
