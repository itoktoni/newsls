<?php

use App\Enums\RsStatusEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;

uses(RefreshDatabase::class);

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $this->admin = User::create([
        'name' => 'Admin Download',
        'email' => 'download@bka.test',
        'password' => Hash::make('password'),
        'role' => 'admin',
        'verified_at' => now(),
        'email_verified_at' => now(),
    ]);
    $this->token = $this->admin->createToken('test-download')->plainTextToken;

    $this->rsId = (int) DB::table('rs')->insertGetId([
        'rs_nama' => 'RS Download',
        'rs_code' => 'DLD',
        'rs_status' => RsStatusEnum::DEDICATED,
        'rs_aktif' => 1,
    ]);
    $this->ruanganId = (int) DB::table('ruangan')->insertGetId([
        'ruangan_nama' => 'Ruang Download',
        'ruangan_code' => 'DLDR',
    ]);
    $this->jenisId = (int) DB::table('jenis_linen')->insertGetId(['jenis_nama' => 'Seprai Download']);

    DB::table('rs_dan_ruangan')->insert(['rs_id' => $this->rsId, 'ruangan_id' => $this->ruanganId]);
    DB::table('rs_dan_jenis')->insert(['rs_id' => $this->rsId, 'jenis_id' => $this->jenisId, 'parstock' => 5]);
    DB::table('rs_dan_user')->insert(['user_id' => $this->admin->id, 'rs_id' => $this->rsId]);

    // Helper: 1 baris detail_linen milik RS test ini.
    $this->seedLinen = function (string $rfid, ?string $updatedAt = null): void {
        DB::table('detail_linen')->insert([
            'detail_rfid' => $rfid,
            'detail_id_rs' => $this->rsId,
            'detail_id_ruangan' => $this->ruanganId,
            'detail_id_jenis' => $this->jenisId,
            'detail_status_linen' => 'BERSIH',
            'detail_updated_at' => $updatedAt ?? '2026-01-01 00:00:00',
        ]);
    };

    // Helper: ambil + decode body streamed JSON.
    $this->download = function (?string $token = null, ?int $rsId = null) {
        $response = $this->withToken($token ?? $this->token)
            ->get('/api/download/'.($rsId ?? $this->rsId));

        return [$response, json_decode($response->streamedContent(), true)];
    };
});

it('requires authentication for the download endpoint', function () {
    $this->getJson("/api/download/{$this->rsId}")
        ->assertOk()
        ->assertJsonPath('status', false)
        ->assertJsonPath('code', 401);
});

it('returns the not found envelope when the RS has no linen', function () {
    $this->withToken($this->token)
        ->getJson("/api/download/{$this->rsId}")
        ->assertOk()
        ->assertJsonPath('status', false)
        ->assertJsonPath('code', 404)
        ->assertJsonPath('message', 'Data Tidak Ditemukan !');
});

it('rejects an RS outside the user scope', function () {
    $other = User::create([
        'name' => 'Admin Lain',
        'email' => 'download-other@bka.test',
        'password' => Hash::make('password'),
        'role' => 'admin',
        'verified_at' => now(),
        'email_verified_at' => now(),
    ]);
    $otherRsId = (int) DB::table('rs')->insertGetId(['rs_nama' => 'RS Lain', 'rs_code' => 'DLD2']);
    DB::table('rs_dan_user')->insert(['user_id' => $other->id, 'rs_id' => $otherRsId]);
    $token = $other->createToken('test-download-other')->plainTextToken;

    $this->withToken($token)
        ->getJson("/api/download/{$this->rsId}")
        ->assertOk()
        ->assertJsonPath('status', false)
        ->assertJsonPath('code', 403);
});

it('streams the legacy download contract with rs, ruangan and opname', function () {
    ($this->seedLinen)('DLD_1', '2026-01-02 08:00:00');
    ($this->seedLinen)('DLD_2', '2026-01-03 08:00:00');
    ($this->seedLinen)('DLD_3', '2026-01-04 08:00:00');

    // DLD_1 sedang di laundry: punya transaksi + outstanding KOTOR/SCAN.
    DB::table('transaksi')->insert([
        'transaksi_key' => 'KTR-DLD-1', 'transaksi_rfid' => 'DLD_1', 'transaksi_status' => 'KOTOR',
        'transaksi_rs_ori' => $this->rsId, 'transaksi_rs_scan' => $this->rsId,
        'transaksi_created_at' => now(), 'transaksi_updated_at' => now(),
    ]);
    DB::table('outstanding')->insert([
        'outstanding_rfid' => 'DLD_1', 'outstanding_key' => 'KTR-DLD-1',
        'outstanding_rs_ori' => $this->rsId, 'outstanding_rs_scan' => $this->rsId,
        'outstanding_status_transaksi' => 'KOTOR', 'outstanding_status_proses' => 'SCAN',
        'outstanding_status_hilang' => 'NORMAL',
        'outstanding_created_at' => now(), 'outstanding_updated_at' => now(),
    ]);

    // DLD_2 pernah ditransaksi tapi outstanding-nya sudah tidak ada (sudah bersih).
    DB::table('transaksi')->insert([
        'transaksi_key' => 'KTR-DLD-2', 'transaksi_rfid' => 'DLD_2', 'transaksi_status' => 'KOTOR',
        'transaksi_rs_ori' => $this->rsId, 'transaksi_rs_scan' => $this->rsId,
        'transaksi_created_at' => now(), 'transaksi_updated_at' => now(),
    ]);
    // DLD_3 belum pernah ditransaksi sama sekali → tetap BERSIH.

    // Opname aktif: DLD_1 ketemu, DLD_2 belum.
    $opnameId = (int) DB::table('opname')->insertGetId([
        'opname_nama' => 'Opname Download',
        'opname_id_rs' => $this->rsId,
        'opname_status' => 1,
        'opname_created_at' => now(),
    ]);
    DB::table('opname_detail')->insert([
        ['opname_detail_id_opname' => $opnameId, 'opname_detail_rfid' => 'DLD_1', 'opname_detail_ketemu' => 1],
        ['opname_detail_id_opname' => $opnameId, 'opname_detail_rfid' => 'DLD_2', 'opname_detail_ketemu' => 0],
    ]);

    [$response, $payload] = ($this->download)();

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/json')
        ->and($response->headers->get('x-accel-buffering'))->toBe('no')
        ->and(json_last_error())->toBe(JSON_ERROR_NONE);

    expect($payload)->toMatchArray([
        'status' => true,
        'code' => 200,
        'name' => 'List',
        'message' => 'Data berhasil diambil',
        'total' => 3,
    ]);
    expect($payload['rs'])->toBe(['rs_id' => $this->rsId, 'rs_nama' => 'RS Download'])
        ->and($payload['ruangan'])->toBe([['ruangan_id' => $this->ruanganId, 'ruangan_nama' => 'Ruang Download']])
        ->and($payload['opname'])->toBe(['DLD_1']);

    $rows = collect($payload['data'])->keyBy('rfid');
    expect($rows)->toHaveCount(3);
    expect(array_keys($rows['DLD_1']))->toBe([
        'rfid', 'rs_id', 'rs_nama', 'ruangan_id', 'ruangan_nama', 'jenis_id', 'jenis_nama',
        'status_transaksi', 'status_proses', 'tanggal',
    ]);

    // Di laundry → status dari outstanding, tanggal = waktu generate.
    expect($rows['DLD_1']['status_transaksi'])->toBe('KOTOR')
        ->and($rows['DLD_1']['status_proses'])->toBe('SCAN')
        ->and($rows['DLD_1']['rs_id'])->toBe($this->rsId)
        ->and($rows['DLD_1']['rs_nama'])->toBe('RS Download')
        ->and($rows['DLD_1']['ruangan_id'])->toBe($this->ruanganId)
        ->and($rows['DLD_1']['ruangan_nama'])->toBe('Ruang Download')
        ->and($rows['DLD_1']['jenis_id'])->toBe($this->jenisId)
        ->and($rows['DLD_1']['jenis_nama'])->toBe('Seprai Download')
        ->and($rows['DLD_1']['tanggal'])->not->toBe('2026-01-02 08:00:00');

    // Tidak di laundry → BERSIH, tanggal = detail_updated_at apa adanya.
    expect($rows['DLD_2']['status_transaksi'])->toBe('BERSIH')
        ->and($rows['DLD_2']['status_proses'])->toBe('BERSIH')
        ->and($rows['DLD_2']['tanggal'])->toBe('2026-01-03 08:00:00');
    expect($rows['DLD_3']['status_transaksi'])->toBe('BERSIH')
        ->and($rows['DLD_3']['status_proses'])->toBe('BERSIH')
        ->and($rows['DLD_3']['tanggal'])->toBe('2026-01-04 08:00:00');
});

it('mengambil linen berdasarkan kepemilikan config_linen + linen FREE', function () {
    // RS lain yang jadi pemilik sah salah satu linen.
    $otherRsId = (int) DB::table('rs')->insertGetId([
        'rs_nama' => 'RS Pemilik Lain',
        'rs_code' => 'DLD3',
        'rs_status' => RsStatusEnum::DEDICATED,
        'rs_aktif' => 1,
    ]);

    // Dimiliki RS ini (config_linen) walau detail_id_rs-nya RS lain → harus muncul.
    ($this->seedLinen)('DLD_OWN');
    DB::table('detail_linen')->where('detail_rfid', 'DLD_OWN')->update(['detail_id_rs' => $otherRsId]);
    DB::table('config_linen')->insert(['detail_rfid' => 'DLD_OWN', 'rs_id' => $this->rsId]);

    // Dimiliki RS lain → tidak boleh muncul di download RS ini.
    ($this->seedLinen)('DLD_OTHER');
    DB::table('config_linen')->insert(['detail_rfid' => 'DLD_OTHER', 'rs_id' => $otherRsId]);

    // Tanpa baris config_linen → FREE, boleh dipakai semua RS.
    ($this->seedLinen)('DLD_FREE');

    [, $payload] = ($this->download)();

    $rfids = collect($payload['data'])->pluck('rfid')->all();

    expect($payload['total'])->toBe(2)
        ->and($rfids)->toContain('DLD_OWN')
        ->and($rfids)->toContain('DLD_FREE')
        ->and($rfids)->not->toContain('DLD_OTHER');
});

it('streams 12000 rows without buffering them all in memory', function () {
    $rows = [];
    for ($i = 1; $i <= 12000; $i++) {
        $rows[] = [
            'detail_rfid' => 'BULK_'.str_pad((string) $i, 5, '0', STR_PAD_LEFT),
            'detail_id_rs' => $this->rsId,
            'detail_id_ruangan' => $this->ruanganId,
            'detail_id_jenis' => $this->jenisId,
            'detail_status_linen' => 'BERSIH',
            'detail_updated_at' => '2026-01-01 00:00:00',
        ];
    }
    foreach (array_chunk($rows, 1000) as $chunk) {
        DB::table('detail_linen')->insert($chunk);
    }

    // Tidak di-json_decode: ukur memory sisi app, bukan memory test.
    $before = memory_get_usage();
    $response = $this->withToken($this->token)->get("/api/download/{$this->rsId}");
    $raw = $response->streamedContent();
    $delta = memory_get_usage() - $before;

    $response->assertOk();
    expect($response->baseResponse)->toBeInstanceOf(StreamedJsonResponse::class);

    // 12rb baris lengkap: total, jumlah baris, dan tidak ada duplikat
    // (offset chunk 2.000 → 6 chunk, tidak boleh ada baris terlewat).
    expect(substr_count($raw, '"rfid":'))->toBe(12000)
        ->and(substr_count($raw, '"total":12000'))->toBe(1)
        ->and(substr_count($raw, '"rfid":"BULK_00001"'))->toBe(1)
        ->and(substr_count($raw, '"rfid":"BULK_12000"'))->toBe(1)
        // Body ~3MB (string hasil stream); kalau barisnya dihidrasi jadi model
        // atau payload di-encode dua kali, delta-nya jauh di atas ini.
        ->and($delta)->toBeLessThan(32 * 1024 * 1024);
});
