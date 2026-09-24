<?php

use App\Enums\CuciEnum;
use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Models\DetailLinen;
use App\Models\GantiChip;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Outstanding;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Supplier;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Guard: pastikan test jalan di test_bka bukan bka produksi (dan bukan :memory: lagi)
    $dbName = DB::connection()->getDatabaseName();
    expect($dbName)->toBe('test_bka', "Test harus di test_bka, bukan $dbName (bka produksi bahaya migrate:fresh)");
    expect($dbName)->not->toBe('bka');

    $this->rs = Rs::create([
        'rs_nama' => 'RS Lifecycle Test',
        'rs_code' => 'LCT',
        'rs_status' => RsStatusEnum::DEDICATED,
        'rs_aktif' => 1,
    ]);

    $this->ruangan = Ruangan::create([
        'ruangan_nama' => 'Ruang Dahlia',
        'ruangan_code' => 'DHL',
    ]);

    $this->jenis = JenisLinen::create([
        'jenis_nama' => 'Seprai Single',
        'jenis_deskripsi' => 'Seprai single 160x250',
    ]);

    $this->bahan = JenisBahan::create([
        'bahan_nama' => 'Katun',
        'bahan_deskripsi' => 'Katun 100%',
    ]);

    $this->supplier = Supplier::create([
        'supplier_nama' => 'Supplier Test',
        'supplier_email' => 'supplier@test.local',
    ]);

    // pivot rs_dan_ruangan & rs_dan_jenis agar PackingDeliveryController lolos validasi kepemilikan
    DB::table('rs_dan_ruangan')->insert([
        'rs_id' => $this->rs->rs_id,
        'ruangan_id' => $this->ruangan->ruangan_id,
    ]);
    DB::table('rs_dan_jenis')->insert([
        'rs_id' => $this->rs->rs_id,
        'jenis_id' => $this->jenis->jenis_id,
        'parstock' => 10,
    ]);

    $this->admin = User::create([
        'name' => 'Admin Lifecycle',
        'email' => 'lifecycle@bka.test',
        'password' => Hash::make('password'),
        'role' => 'admin',
        'verified_at' => now(),
        'email_verified_at' => now(),
    ]);

    // pivot rs_dan_user: admin boleh akses RS ini (jika kosong = semua, tapi eksplisit lebih aman)
    DB::table('rs_dan_user')->insert([
        'user_id' => $this->admin->id,
        'rs_id' => $this->rs->rs_id,
    ]);

    $this->token = $this->admin->createToken('test-token')->plainTextToken;

    // helper closure untuk assert state linen yang rapi
    $this->assertLinenState = function (string $rfid, array $expected) {
        $detail = DetailLinen::where('detail_rfid', $rfid)->first();
        expect($detail)->not->toBeNull("DetailLinen $rfid harus ada");

        foreach ($expected as $col => $val) {
            expect($detail->{$col})->toBe($val, "DetailLinen $rfid kolom $col");
        }

        return $detail;
    };
});

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------
function registerPayload(array $rfids, $ctx): array
{
    return [
        'rfid' => $rfids,
        'jenis_id' => $ctx->jenis->jenis_id,
        'bahan_id' => $ctx->bahan->bahan_id,
        'supplier_id' => $ctx->supplier->supplier_id,
        'status_cuci' => CuciEnum::CUCI,
        'rs_id' => $ctx->rs->rs_id,
        'ruangan_id' => $ctx->ruangan->ruangan_id,
        'status_register' => RegisterEnum::REGISTER,
        'status_kepemilikan' => RsStatusEnum::DEDICATED,
        'deskripsi' => 'Registrasi lifecycle TEST',
        'tgl_cek' => now()->format('Y-m-d'),
    ];
}

it('selesaikan siklus lengkap registrasi → ganti chip → grouping → packing → delivery bersih → kotor → grouping → packing → delivery lagi untuk TEST1-3 via web/api', function () {

    $rfids = ['TEST1', 'TEST2', 'TEST3'];
    $rfidGantiBaru = 'TEST2_NEW';

    // ------------------------------------------------------------------
    // 1) REGISTRASI massal TEST1-3 via POST /api/register (web = sumber kebenaran, desktop push via API)
    // ------------------------------------------------------------------
    $this->withToken($this->token)
        ->postJson('/api/register', registerPayload($rfids, $this))
        ->assertOk()
        ->assertJson(['status' => true]);

    foreach ($rfids as $rfid) {
        ($this->assertLinenState)($rfid, [
            'detail_status_linen' => LinenStatusEnum::REGISTER,
            'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_id_rs' => $this->rs->rs_id,
        ]);
        expect(DB::table('config_linen')->where('detail_rfid', $rfid)->where('rs_id', $this->rs->rs_id)->exists())->toBeTrue();
    }
    expect(DetailLinen::whereIn('detail_rfid', $rfids)->count())->toBe(3);

    // ------------------------------------------------------------------
    // 2) GANTI CHIP — REGISTER dengan status_register=GANTI_CHIP menghasilkan KOTOR
    //    Simulasi: chip TEST2 rusak, ganti dengan TEST2_NEW (atau TEST_GANTI1)
    // ------------------------------------------------------------------
    // 2a) Register chip baru dengan GANTI_CHIP
    $this->withToken($this->token)
        ->postJson('/api/register', array_merge(registerPayload([$rfidGantiBaru], $this), [
            'status_register' => RegisterEnum::GANTI_CHIP,
        ]))
        ->assertOk()
        ->assertJson(['status' => true]);

    ($this->assertLinenState)($rfidGantiBaru, [
        'detail_status_linen' => LinenStatusEnum::KOTOR, // GANTI_CHIP → KOTOR (lihat RegisterLinenAction:87)
        'detail_status_register' => RegisterEnum::GANTI_CHIP,
    ]);

    // 2b) Catat jejak ganti chip di tabel ganti_chip (untuk report penggantian linen)
    GantiChip::create([
        'ganti_rfid_lama' => 'TEST2',
        'ganti_rfid_baru' => $rfidGantiBaru,
        'ganti_tanggal' => now(),
        'ganti_by' => $this->admin->id,
        'ganti_keterangan' => 'Chip TEST2 rusak, ganti lifecycle test',
    ]);

    expect(GantiChip::where('ganti_rfid_lama', 'TEST2')->where('ganti_rfid_baru', $rfidGantiBaru)->exists())->toBeTrue();
    // rantai transitif A→B→C harus bisa ditemukan via GantiChip::forRfid
    expect(GantiChip::forRfid('TEST2')->pluck('ganti_rfid_baru')->contains($rfidGantiBaru))->toBeTrue();
    expect(GantiChip::forRfid($rfidGantiBaru)->pluck('ganti_rfid_lama')->contains('TEST2'))->toBeTrue();

    // ------------------------------------------------------------------
    // 3) GROUPING (QC) — GET /api/grouping/{rfid} mengubah REGISTER → GUDANG dan buat Outstanding
    // ------------------------------------------------------------------
    foreach ($rfids as $rfid) {
        $res = $this->withToken($this->token)->getJson("/api/grouping/{$rfid}");
        // grouping return raw object (bukan envelope Notes) — status HTTP 200, body punya rfid
        $res->assertOk();
        $res->assertJson(['rfid' => $rfid]);
    }

    foreach ($rfids as $rfid) {
        ($this->assertLinenState)($rfid, [
            'detail_status_linen' => LinenStatusEnum::GUDANG,
        ]);
        $out = Outstanding::where('outstanding_rfid', $rfid)->first();
        expect($out)->not->toBeNull("Outstanding $rfid harus ada setelah grouping");
        // Outstanding harus GUDANG (QC lolos = masuk gudang utama)
        expect($out->outstanding_status_proses)->toBe('GUDANG');
        expect((int) $out->outstanding_rs_scan)->toBe($this->rs->rs_id);
    }

    // Outstanding GUDANG juga harus terlihat di BersihController stats (packing queue)
    // via Outstanding SCAN/QC/REGISTER/GUDANG
    expect(Outstanding::whereIn('outstanding_status_proses', ['SCAN', 'QC', 'REGISTER', 'GUDANG'])->count())->toBe(3);

    // ------------------------------------------------------------------
    // 4) PACKING — POST /api/packing : Outstanding GUDANG/REGISTER → PACKING + cetak type 1
    // ------------------------------------------------------------------
    // Validasi guard: tanpa grouping (Outstanding SCAN) tidak boleh packing — sudah terlewati di atas,
    // sekarang packing happy path. Untuk REGISTER, status_transaksi = REGISTER
    $packingRes = $this->withToken($this->token)
        ->postJson('/api/packing', [
            'rfid' => $rfids,
            'rs_id' => $this->rs->rs_id,
            'ruangan_id' => $this->ruangan->ruangan_id,
            'status_transaksi' => 'REGISTER', // sesuai Outstanding grouping REGISTER (bukan KOTOR)
        ]);
    $packingRes->assertOk()->assertJson(['status' => true]);

    // Packing harus ubah Outstanding ke PACKING
    foreach ($rfids as $rfid) {
        expect(Outstanding::where('outstanding_rfid', $rfid)->value('outstanding_status_proses'))->toBe('PACKING');
    }

    // Cetak legacy type 1 (Barcode) harus ada 1 baris dengan rfids JSON TEST1-3
    $cetakPacking = DB::table('cetak')->where('cetak_type', 1)->where('cetak_id_rs', $this->rs->rs_id)->latest('cetak_id')->first();
    expect($cetakPacking)->not->toBeNull();
    $decoded = json_decode($cetakPacking->cetak_rfids, true);
    sort($decoded);
    $expectedSorted = $rfids;
    sort($expectedSorted);
    expect($decoded)->toBe($expectedSorted);
    $packingCode = $cetakPacking->cetak_code;

    // Reprint packing harus mengembalikan DetailLinen yang sama persis
    $this->withToken($this->token)
        ->getJson("/api/packing/{$packingCode}")
        ->assertOk()
        ->assertJson(['status' => true])
        ->assertJsonCount(3, 'data');

    // Total delivery (Outstanding PACKING) harus 3
    $this->withToken($this->token)
        ->getJson("/api/total/delivery/{$this->rs->rs_id}/REGISTER")
        ->assertOk()
        ->assertJsonPath('data.total', 3);

    // View web Bersih tab packing menampilkan 3 RFID yang sudah PACKING
    $this->actingAs($this->admin)
        ->get('/bersih/table?tab=packing')
        ->assertOk()
        ->assertSee('PACKING');

    // ------------------------------------------------------------------
    // 5) DELIVERY BERSIH — POST /api/delivery : PACKING → hapus Outstanding, Detail jadi BERSIH, cetak type 2
    // ------------------------------------------------------------------
    $deliveryRes = $this->withToken($this->token)
        ->postJson('/api/delivery', [
            'rs_id' => $this->rs->rs_id,
            'status_transaksi' => 'REGISTER',
        ]);
    $deliveryRes->assertOk()->assertJson(['status' => true]);

    // Outstanding harus kosong (sudah bersih, tidak lagi stok laundry)
    expect(Outstanding::whereIn('outstanding_rfid', $rfids)->count())->toBe(0);

    // DetailLinen harus BERSIH + detail_total_bersih +1 (mirror andalan UpdateDeliveryService)
    foreach ($rfids as $rfid) {
        ($this->assertLinenState)($rfid, [
            'detail_status_linen' => LinenStatusEnum::BERSIH,
        ]);
        expect((int) DetailLinen::where('detail_rfid', $rfid)->value('detail_total_bersih'))->toBe(1);
        // report date terisi (besok jika jam >13:00, else hari ini) — minimal tidak null
        expect(DetailLinen::where('detail_rfid', $rfid)->value('detail_report'))->not->toBeNull();
    }

    // Cetak type 2 (Delivery) harus ada 1 baris dengan rfids JSON TEST1-3
    $cetakDelivery1 = DB::table('cetak')->where('cetak_type', 2)->where('cetak_id_rs', $this->rs->rs_id)->latest('cetak_id')->first();
    expect($cetakDelivery1)->not->toBeNull();
    $decodedD1 = json_decode($cetakDelivery1->cetak_rfids, true);
    sort($decodedD1);
    expect($decodedD1)->toBe($expectedSorted);
    $deliveryCode1 = $cetakDelivery1->cetak_code;

    // Reprint delivery harus mengembalikan 3 DetailLinen
    $this->withToken($this->token)
        ->getJson("/api/delivery/{$deliveryCode1}")
        ->assertOk()
        ->assertJsonCount(3, 'data');

    // Tab packing = baris bersih hari ini (hasil packing hari itu)
    $this->actingAs($this->admin)
        ->get('/bersih/table?tab=packing')
        ->assertOk()
        ->assertSee($packingCode);

    // Tab delivery = baris bersih yang sudah terkirim (bersih_delivery terisi)
    $this->actingAs($this->admin)
        ->get('/bersih/table?tab=delivery')
        ->assertOk()
        ->assertSee($deliveryCode1);

    // Stats bersih_hari_ini harus >=3 (DetailLinen BERSIH hari ini)
    expect(DetailLinen::where('detail_status_linen', 'BERSIH')->whereDate('detail_updated_at', today())->count())->toBeGreaterThanOrEqual(3);

    // ------------------------------------------------------------------
    // 6) SCAN KOTOR lagi via web API — POST /api/transaksi/kotor untuk TEST1-3 (siklus kedua)
    //    Setelah bersih, linen dipakai lagi → kotor → perlu dicuci ulang.
    //    Menggunakan endpoint transaksi (bukan andalan BersihController) — sesuai routes/api.php
    // ------------------------------------------------------------------
    // Guard kotor (port andalan): detail_updated_at harus sudah lewat
    // TRANSACTION_HOURS_ALLOWED jam, jadi mundurkan dulu.
    DetailLinen::whereIn('detail_rfid', $rfids)->update(['detail_updated_at' => now()->subDays(2)]);

    $kotorKey = 'KTR-TEST-'.now()->format('YmdHis').'-'.uniqid();
    $kotorRes = $this->withToken($this->token)
        ->postJson('/api/transaksi/kotor', [
            'rfid' => $rfids,
            'rs_id' => $this->rs->rs_id,
            'key' => $kotorKey,
        ]);
    $kotorRes->assertOk()->assertJson(['status' => true, 'message' => 'Transaksi berhasil.']);
    $kotorRes->assertJsonPath('data.key', $kotorKey);
    $kotorRes->assertJsonPath('data.status', 'KOTOR');
    $kotorRes->assertJsonPath('data.rfid_count', 3);

    // Transaksi harus ada 3 baris KOTOR dengan key yang sama
    expect(Transaksi::where('transaksi_key', $kotorKey)->where('transaksi_status', 'KOTOR')->count())->toBe(3);
    foreach ($rfids as $rfid) {
        expect(Transaksi::where('transaksi_rfid', $rfid)->where('transaksi_key', $kotorKey)->exists())->toBeTrue();
    }

    // Outstanding harus muncul lagi SCAN (stok laundry)
    foreach ($rfids as $rfid) {
        $out = Outstanding::where('outstanding_rfid', $rfid)->first();
        expect($out)->not->toBeNull();
        expect($out->outstanding_status_transaksi)->toBe('KOTOR');
        expect($out->outstanding_status_proses)->toBe('SCAN');
    }

    // DetailLinen harus kembali KOTOR
    foreach ($rfids as $rfid) {
        ($this->assertLinenState)($rfid, [
            'detail_status_linen' => LinenStatusEnum::KOTOR,
        ]);
    }

    // ------------------------------------------------------------------
    // 7) GROUPING lagi (siklus kedua) — KOTOR → GUDANG
    // ------------------------------------------------------------------
    foreach ($rfids as $rfid) {
        $this->withToken($this->token)->getJson("/api/grouping/{$rfid}")->assertOk();
    }
    foreach ($rfids as $rfid) {
        ($this->assertLinenState)($rfid, [
            'detail_status_linen' => LinenStatusEnum::GUDANG,
        ]);
        expect(Outstanding::where('outstanding_rfid', $rfid)->value('outstanding_status_proses'))->toBe('GUDANG');
        // status_transaksi tetap KOTOR dari outstanding scan
        expect(Outstanding::where('outstanding_rfid', $rfid)->value('outstanding_status_transaksi'))->toBe('KOTOR');
    }

    // ------------------------------------------------------------------
    // 8) PACKING lagi (siklus kedua) — sekarang status_transaksi = KOTOR
    // ------------------------------------------------------------------
    $packingRes2 = $this->withToken($this->token)
        ->postJson('/api/packing', [
            'rfid' => $rfids,
            'rs_id' => $this->rs->rs_id,
            'ruangan_id' => $this->ruangan->ruangan_id,
            'status_transaksi' => 'KOTOR',
        ]);
    $packingRes2->assertOk()->assertJson(['status' => true]);

    foreach ($rfids as $rfid) {
        expect(Outstanding::where('outstanding_rfid', $rfid)->value('outstanding_status_proses'))->toBe('PACKING');
    }

    // Cetak type 1 kedua harus ada, code berbeda dari packing pertama
    $cetakPacking2 = DB::table('cetak')->where('cetak_type', 1)->where('cetak_id_rs', $this->rs->rs_id)->latest('cetak_id')->first();
    expect($cetakPacking2->cetak_code)->not->toBe($packingCode);
    $decoded2 = json_decode($cetakPacking2->cetak_rfids, true);
    sort($decoded2);
    expect($decoded2)->toBe($expectedSorted);
    $packingCode2 = $cetakPacking2->cetak_code;

    // Reprint packing kedua juga harus konsisten
    $this->withToken($this->token)
        ->getJson("/api/packing/{$packingCode2}")
        ->assertOk()
        ->assertJsonCount(3, 'data');

    // ------------------------------------------------------------------
    // 9) DELIVERY BERSIH lagi (siklus kedua) via web
    // ------------------------------------------------------------------
    $deliveryRes2 = $this->withToken($this->token)
        ->postJson('/api/delivery', [
            'rs_id' => $this->rs->rs_id,
            'status_transaksi' => 'KOTOR',
        ]);
    $deliveryRes2->assertOk()->assertJson(['status' => true]);

    expect(Outstanding::whereIn('outstanding_rfid', $rfids)->count())->toBe(0);

    foreach ($rfids as $rfid) {
        ($this->assertLinenState)($rfid, [
            'detail_status_linen' => LinenStatusEnum::BERSIH,
        ]);
        // total bersih sekarang 2 (satu siklus pertama + satu siklus kedua)
        expect((int) DetailLinen::where('detail_rfid', $rfid)->value('detail_total_bersih'))->toBe(2);
    }

    // Cetak type 2 kedua harus ada, code berbeda dari delivery pertama
    $cetakDelivery2 = DB::table('cetak')->where('cetak_type', 2)->where('cetak_id_rs', $this->rs->rs_id)->latest('cetak_id')->first();
    expect($cetakDelivery2->cetak_code)->not->toBe($deliveryCode1);
    $decodedD2 = json_decode($cetakDelivery2->cetak_rfids, true);
    sort($decodedD2);
    expect($decodedD2)->toBe($expectedSorted);

    // Total cetak: 2 packing + 2 delivery = 4
    expect(DB::table('cetak')->where('cetak_type', 1)->where('cetak_id_rs', $this->rs->rs_id)->count())->toBe(2);
    expect(DB::table('cetak')->where('cetak_type', 2)->where('cetak_id_rs', $this->rs->rs_id)->count())->toBe(2);
    expect(DB::table('cetak')->where('cetak_id_rs', $this->rs->rs_id)->count())->toBe(4);

    // Outstanding benar-benar bersih (JIT queue drain — gudang kosong)
    expect(Outstanding::where('outstanding_rs_scan', $this->rs->rs_id)->count())->toBe(0);

    // Transaksi history tidak hilang: minimal 3 KOTOR dari scan kedua (plus mungkin dari siklus pertama)
    expect(Transaksi::whereIn('transaksi_rfid', $rfids)->where('transaksi_status', 'KOTOR')->count())->toBeGreaterThanOrEqual(3);

    // Web Bersih riwayat harus menampilkan 4 baris cetak
    $this->actingAs($this->admin)->get('/bersih/table?tab=delivery')->assertOk();

    // Guard dedup: coba scan kotor lagi di hari yang sama dengan key berbeda harus tetap update Outstanding
    // tapi transaksi hari ini tidak duplikat (TransaksiApiController skip if existsToday)
    $kotorKeyDedup = 'KTR-DEDUP-'.uniqid();
    $this->withToken($this->token)
        ->postJson('/api/transaksi/kotor', [
            'rfid' => $rfids,
            'rs_id' => $this->rs->rs_id,
            'key' => $kotorKeyDedup,
        ])
        ->assertOk()->assertJson(['status' => true]);

    // Guard kotor (port andalan): detail_report masih hari ini (hasil delivery siklus
    // kedua), jadi scan kotor di hari yang sama DIBLOKIR — tidak ada outstanding
    // maupun transaksi baru.
    expect(Outstanding::whereIn('outstanding_rfid', $rfids)->count())->toBe(0);
    expect(Transaksi::where('transaksi_key', $kotorKeyDedup)->count())->toBe(0);
    // Jumlah transaksi KOTOR hari ini tetap 3 (dari scan siklus kedua)
    $todayCount = Transaksi::whereIn('transaksi_rfid', $rfids)->whereDate('transaksi_created_at', today())->count();
    expect($todayCount)->toBe(3);

    // Negative: packing RFID yang tidak ada di Outstanding harus ditolak
    $this->withToken($this->token)
        ->postJson('/api/packing', [
            'rfid' => ['BOGUS_RFID_999'],
            'rs_id' => $this->rs->rs_id,
            'ruangan_id' => $this->ruangan->ruangan_id,
            'status_transaksi' => 'KOTOR',
        ])
        ->assertStatus(200) // Notes::validation returns 200 HTTP (body code 422) — cek body
        ->assertJson(['status' => false]);
});

it('menolak packing RFID yang belum di-grouping atau sudah bersih', function () {
    $rfid = 'TEST_ISOLATED_001';

    // Registrasi single RFID
    $this->withToken($this->token)
        ->postJson('/api/register', registerPayload([$rfid], $this))
        ->assertOk();

    // Tanpa grouping, langsung packing harus ditolak (ready packing tidak ditemukan)
    $this->withToken($this->token)
        ->postJson('/api/packing', [
            'rfid' => [$rfid],
            'rs_id' => $this->rs->rs_id,
            'ruangan_id' => $this->ruangan->ruangan_id,
            'status_transaksi' => 'KOTOR',
        ])
        ->assertJson(['status' => false]);

    // Setelah grouping, packing lalu delivery, packing lagi harus ditolak (sudah bersih, Outstanding kosong)
    $this->withToken($this->token)->getJson("/api/grouping/{$rfid}")->assertOk();
    $this->withToken($this->token)
        ->postJson('/api/packing', [
            'rfid' => [$rfid],
            'rs_id' => $this->rs->rs_id,
            'ruangan_id' => $this->ruangan->ruangan_id,
            'status_transaksi' => 'REGISTER',
        ])->assertJson(['status' => true]);
    $this->withToken($this->token)
        ->postJson('/api/delivery', [
            'rs_id' => $this->rs->rs_id,
            'status_transaksi' => 'REGISTER',
        ])->assertJson(['status' => true]);

    // Sekarang sudah BERSIH — packing harus gagal lagi
    $this->withToken($this->token)
        ->postJson('/api/packing', [
            'rfid' => [$rfid],
            'rs_id' => $this->rs->rs_id,
            'ruangan_id' => $this->ruangan->ruangan_id,
            'status_transaksi' => 'REGISTER',
        ])
        ->assertJson(['status' => false]);
});

it('mencegah akses RS di luar hak user dan menolak RFID duplikat saat registrasi', function () {
    $otherRs = Rs::create(['rs_nama' => 'RS Lain', 'rs_code' => 'OTH', 'rs_status' => RsStatusEnum::DEDICATED]);
    DB::table('rs_dan_ruangan')->insert(['rs_id' => $otherRs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id]);
    DB::table('rs_dan_jenis')->insert(['rs_id' => $otherRs->rs_id, 'jenis_id' => $this->jenis->jenis_id]);

    // Registrasi RFID baru
    $this->withToken($this->token)
        ->postJson('/api/register', registerPayload(['DUPE_001'], $this))
        ->assertOk();

    // Registrasi duplikat RFID harus 422 (ValidationException via payload — HTTP tetap 200 per Notes, code di body 422)
    $this->withToken($this->token)
        ->postJson('/api/register', registerPayload(['DUPE_001'], $this))
        ->assertOk()
        ->assertJson(['status' => false, 'code' => 422]);

    // Packing dengan rs_id di luar hak (otherRs) harus 403 (HTTP 200 per Notes, code 403 di body)
    $this->withToken($this->token)
        ->postJson('/api/packing', [
            'rfid' => ['DUPE_001'],
            'rs_id' => $otherRs->rs_id,
            'ruangan_id' => $this->ruangan->ruangan_id,
            'status_transaksi' => 'REGISTER',
        ])
        ->assertOk()
        ->assertJson(['status' => false, 'code' => 403]);

    // Transaksi kotor dengan rs_id di luar hak juga 403
    $this->withToken($this->token)
        ->postJson('/api/transaksi/kotor', [
            'rfid' => ['DUPE_001'],
            'rs_id' => $otherRs->rs_id,
            'key' => 'KTR-FORBIDDEN-'.uniqid(),
        ])
        ->assertOk()
        ->assertJson(['status' => false, 'code' => 403]);
});
