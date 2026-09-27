<?php

use App\Enums\RsStatusEnum;
use App\Models\DetailLinen;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Opname;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// PERSIST — jangan RefreshDatabase (test lain memakai data yang sama di test_bka).
// File ini menyamakan bentuk respons API dengan kontrak legacy andalan.

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $this->rs = Rs::updateOrCreate(['rs_code' => 'LEG'], ['rs_nama' => 'RS Legacy', 'rs_status' => RsStatusEnum::DEDICATED]);
    $this->ruangan = Ruangan::firstOrCreate(['ruangan_nama' => 'Ruang Legacy'], ['ruangan_code' => 'LEG']);
    $this->jenis = JenisLinen::firstOrCreate(['jenis_nama' => 'Seprai Legacy']);
    $this->bahan = JenisBahan::firstOrCreate(['bahan_nama' => 'Katun Legacy']);
    $this->supplier = Supplier::firstOrCreate(['supplier_nama' => 'Sup Legacy']);

    DB::table('rs_dan_ruangan')->updateOrInsert(['rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id], []);
    DB::table('rs_dan_jenis')->updateOrInsert(['rs_id' => $this->rs->rs_id, 'jenis_id' => $this->jenis->jenis_id], []);

    $this->admin = User::updateOrCreate(
        ['email' => 'legacy@bka.test'],
        ['name' => 'Admin Legacy', 'password' => Hash::make('password'), 'role' => 'developer', 'verified_at' => now(), 'email_verified_at' => now()]
    );
    DB::table('rs_dan_user')->where('user_id', $this->admin->id)->delete();

    DetailLinen::updateOrCreate(
        ['detail_rfid' => 'LEG_RFID_1'],
        [
            'detail_id_rs' => $this->rs->rs_id,
            'detail_id_ruangan' => $this->ruangan->ruangan_id,
            'detail_id_jenis' => $this->jenis->jenis_id,
            'detail_id_bahan' => $this->bahan->bahan_id,
            'detail_id_supplier' => $this->supplier->supplier_id,
            'detail_status_linen' => 'BERSIH',
            'detail_total_bersih' => 4,
            'detail_updated_by' => $this->admin->id,
        ]
    );
});

it('POST /api/detail/rfid mengembalikan satu item per RFID dengan kontrak legacy', function () {
    $res = $this->actingAs($this->admin, 'sanctum')->postJson('/api/detail/rfid', [
        'rfid' => ['LEG_RFID_1', 'LEG_TIDAK_ADA'],
    ])->assertOk()->assertJson(['status' => true, 'name' => 'List']);

    $data = $res->json('data');

    expect($data)->toHaveCount(2)
        ->and(array_keys($data[0]))->toBe([
            'rfid', 'jenis_id', 'jenis_nama', 'bahan_id', 'bahan_nama', 'supplier_id',
            'supplier_nama', 'rs_id', 'rs_nama', 'ruangan_id', 'ruangan_nama',
            'status_register', 'status_cuci', 'status_transaksi', 'status_proses',
            'tanggal_create', 'tanggal_update', 'pemakaian', 'user_nama',
        ])
        ->and($data[0]['rfid'])->toBe('LEG_RFID_1')
        ->and($data[0]['jenis_id'])->toBe($this->jenis->jenis_id)
        ->and($data[0]['bahan_id'])->toBe($this->bahan->bahan_id)
        ->and($data[0]['supplier_id'])->toBe($this->supplier->supplier_id)
        ->and($data[0]['rs_nama'])->toBe('RS Legacy')
        ->and($data[0]['ruangan_nama'])->toBe('Ruang Legacy')
        ->and($data[0]['pemakaian'])->toBe(4)
        ->and($data[1]['rfid'])->toBe('LEG_TIDAK_ADA');

    // RFID tak dikenal tetap dikembalikan, nilainya null
    foreach (array_keys($data[1]) as $key) {
        if ($key !== 'rfid') {
            expect($data[1][$key])->toBeNull();
        }
    }
});

it('GET /api/opname/{id} mengembalikan satu opname seperti legacy', function () {
    $opname = Opname::updateOrCreate(
        ['opname_nama' => 'Opname Legacy', 'opname_id_rs' => $this->rs->rs_id],
        [
            'opname_mulai' => now()->format('Y-m-d'),
            'opname_selesai' => now()->addDay()->format('Y-m-d'),
            'opname_status' => 1,
            'opname_created_by' => $this->admin->id,
        ]
    );

    $res = $this->actingAs($this->admin, 'sanctum')->getJson("/api/opname/{$opname->opname_id}")
        ->assertOk()->assertJson(['status' => true, 'name' => 'List']);

    expect($res->json('data'))->toMatchArray([
        'opname_id' => $opname->opname_id,
        'rs_id' => $this->rs->rs_id,
        'rs_nama' => 'RS Legacy',
    ])->and(array_keys($res->json('data')))->toBe(['opname_id', 'opname_start', 'opname_end', 'rs_id', 'rs_nama']);
});

it('GET /api/rs dan /api/rs/{id} menyertakan rs_ruangan & rs_jenis', function () {
    $list = $this->actingAs($this->admin, 'sanctum')->getJson('/api/rs')->assertOk();

    $row = collect($list->json('data'))->firstWhere('rs_id', $this->rs->rs_id);
    expect($row)->not->toBeNull()
        ->and($row['rs_ruangan'][0])->toBe(['ruangan_id' => $this->ruangan->ruangan_id, 'ruangan_nama' => 'Ruang Legacy'])
        ->and($row['rs_jenis'][0])->toBe(['jenis_id' => $this->jenis->jenis_id, 'jenis_nama' => 'Seprai Legacy']);

    $single = $this->actingAs($this->admin, 'sanctum')->getJson("/api/rs/{$this->rs->rs_id}")
        ->assertOk()->assertJson(['status' => true, 'name' => 'List']);

    expect($single->json('data.rs_ruangan.0.ruangan_nama'))->toBe('Ruang Legacy')
        ->and($single->json('data.rs_jenis.0.jenis_nama'))->toBe('Seprai Legacy');
});

it('GET /api/configuration mentah ala legacy (tanpa envelope)', function () {
    $res = $this->actingAs($this->admin, 'sanctum')->getJson('/api/configuration')->assertOk();

    // Raw: key di root, bukan di `data`; tanpa status/code/name/message.
    expect($res->json())->not->toHaveKey('status')
        ->and(array_keys($res->json()))->toContain('supplier', 'jenis_bahan', 'jenis_linen', 'status_proses', 'status_transaksi', 'status_cuci', 'status_register')
        ->and($res->json('status_proses'))->toBe([
            ['status_id' => 'REGISTER', 'status_name' => 'REGISTER'],
            ['status_id' => 'KOTOR', 'status_name' => 'KOTOR'],
            ['status_id' => 'SCAN', 'status_name' => 'SCAN'],
            ['status_id' => 'QC', 'status_name' => 'QC'],
            ['status_id' => 'PACKING', 'status_name' => 'PACKING'],
            ['status_id' => 'BERSIH', 'status_name' => 'BERSIH'],
        ])
        ->and($res->json('status_transaksi.0'))->toBe(['status_id' => 'KOTOR', 'status_name' => 'KOTOR'])
        ->and($res->json('status_cuci'))->toBe([
            ['status_id' => 'CUCI', 'status_name' => 'CUCI'],
            ['status_id' => 'RENTAL', 'status_name' => 'RENTAL'],
        ])
        ->and($res->json('status_register'))->toBe([
            ['status_id' => 'REGISTER', 'status_name' => 'REGISTER'],
            ['status_id' => 'GANTI_CHIP', 'status_name' => 'GANTI_CHIP'],
        ]);
});

it('GET /api/total/bersih melaporkan 5 key seperti legacy', function () {
    $res = $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/total/bersih/{$this->rs->rs_id}/{$this->ruangan->ruangan_id}/{$this->jenis->jenis_id}/BERSIH")
        ->assertOk();

    expect(array_keys($res->json('data')))->toBe(['view_jenis_id', 'view_ruangan_id', 'view_rs_id', 'view_total', 'view_status'])
        ->and($res->json('data.view_status'))->toBe('BERSIH')
        ->and($res->json('data.view_rs_id'))->toBe($this->rs->rs_id);
});

it('login mengembalikan field yang dibaca LoginDAO desktop', function () {
    $res = $this->postJson('/api/login', ['username' => 'legacy@bka.test', 'password' => 'password'])
        ->assertOk()->assertJson(['status' => true, 'name' => 'Token']);

    expect($res->json('data'))->toHaveKeys([
        'api_token', 'id', 'name', 'username', 'phone', 'email', 'email_verified_at',
        'role', 'level', 'active', 'created_at', 'updated_at', 'vendor', 'rs_id',
    ]);
});
