<?php

// 1) Register: supplier/bahan/status_cuci opsional (tidak wajib).
// 2) rs?type=register: daftar jenis datar ikut hak akses RS user.
// 3) Download: index pendukung dipastikan ada tanpa file migrasi.

use App\Enums\CuciEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Models\DetailLinen;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $uniq = strtoupper(uniqid());
    $this->rsA = Rs::create(['rs_nama' => 'RS RegOpt A '.$uniq, 'rs_code' => 'ROA'.$uniq, 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->rsB = Rs::create(['rs_nama' => 'RS RegOpt B '.$uniq, 'rs_code' => 'ROB'.$uniq, 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang RegOpt '.$uniq, 'ruangan_code' => 'RO'.$uniq]);
    $this->jenisA = JenisLinen::create(['jenis_nama' => 'Jenis A '.$uniq]);
    $this->jenisB = JenisLinen::create(['jenis_nama' => 'Jenis B '.$uniq]);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Bahan RegOpt '.$uniq]);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier RegOpt '.$uniq]);
    DB::table('rs_dan_ruangan')->insert(['rs_id' => $this->rsA->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id]);
    DB::table('rs_dan_jenis')->insert(['rs_id' => $this->rsA->rs_id, 'jenis_id' => $this->jenisA->jenis_id, 'parstock' => 5]);
    DB::table('rs_dan_jenis')->insert(['rs_id' => $this->rsB->rs_id, 'jenis_id' => $this->jenisB->jenis_id, 'parstock' => 5]);

    $this->admin = User::create(['name' => 'Admin RegOpt', 'email' => 'regopt-'.$uniq.'@bka.test', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    $this->token = $this->admin->createToken('test-token')->plainTextToken;

    // User RS biasa: hanya boleh RS A.
    $this->rsUser = User::create(['name' => 'User RS', 'email' => 'rsuser-'.$uniq.'@bka.test', 'password' => Hash::make('password'), 'role' => 'rs', 'verified_at' => now(), 'email_verified_at' => now()]);
    DB::table('rs_dan_user')->insert(['user_id' => $this->rsUser->id, 'rs_id' => $this->rsA->rs_id]);
    $this->rsToken = $this->rsUser->createToken('rs-token')->plainTextToken;
});

it('register tanpa supplier/bahan/status_cuci tetap 201 dan tersimpan null', function () {
    $rfid = 'REGOPT-'.strtoupper(uniqid());

    $this->withToken($this->token)->postJson('/api/register', [
        'rfid' => [$rfid],
        'jenis_id' => $this->jenisA->jenis_id,
        'rs_id' => $this->rsA->rs_id,
        'ruangan_id' => $this->ruangan->ruangan_id,
        // tanpa bahan_id, supplier_id, status_cuci
    ])->assertOk()->assertJson(['status' => true]);

    $row = DetailLinen::where('detail_rfid', $rfid)->firstOrFail();
    expect($row->detail_id_bahan)->toBeNull();
    expect($row->detail_id_supplier)->toBeNull();
    expect($row->detail_status_cuci)->toBeNull();
    expect($row->detail_status_register)->toBe(RegisterEnum::REGISTER);
});

it('daftar jenis register untuk admin memuat semua jenis', function () {
    $res = $this->withToken($this->token)->getJson('/api/rs?type=register')->assertOk();
    $names = collect($res->json('jenis'))->pluck('jenis_nama')->all();
    expect($names)->toContain($this->jenisA->jenis_nama)
        ->and($names)->toContain($this->jenisB->jenis_nama);
});

it('daftar jenis register ikut hak akses RS user (bukan admin)', function () {
    // Satu request terautentikasi per test: guard auth antar-request dalam
    // satu test tidak di-reset (artefak test), jadi admin & user diuji
    // terpisah. Di produksi tiap request HTTP prosesnya terpisah.
    $res = $this->withToken($this->rsToken)->getJson('/api/rs?type=register')->assertOk();
    $names = collect($res->json('jenis'))->pluck('jenis_nama')->all();
    expect($names)->toContain($this->jenisA->jenis_nama)
        ->and($names)->not->toContain($this->jenisB->jenis_nama);
});

it('download memastikan index transaksi_rfid ada tanpa file migrasi', function () {
    $rfid = 'REGDLI-'.strtoupper(uniqid());
    DetailLinen::create([
        'detail_rfid' => $rfid, 'detail_id_rs' => $this->rsA->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
        'detail_id_jenis' => $this->jenisA->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id,
        'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => 'BERSIH',
        'detail_created_by' => $this->admin->id,
    ]);

    // Simulasi prod lama tanpa index: cabut dulu.
    foreach (Schema::getIndexes('transaksi') as $index) {
        if (in_array('transaksi_rfid', (array) ($index['columns'] ?? []), true)) {
            Schema::table('transaksi', fn ($t) => $t->dropIndex($index['name']));
        }
    }

    $res = $this->withToken($this->token)->get('/api/download/'.$this->rsA->rs_id);
    $res->assertOk();
    $json = json_decode($res->streamedContent(), true);
    expect($json['status'])->toBeTrue();
    expect($json['total'])->toBe(1);

    // Index dibuat ulang oleh endpoint.
    $names = array_column(Schema::getIndexes('transaksi'), 'name');
    expect($names)->toContain('transaksi_rfid_index');
});
