<?php

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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Register batch: nomor dari waktu + keterangan (opsional) + API ambil ulang.
beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');
    Artisan::call('migrate:fresh', ['--database' => 'mariadb', '--force' => true]);
    $this->rs = Rs::create(['rs_nama' => 'RS Batch', 'rs_code' => 'BAT', 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang Batch', 'ruangan_code' => 'BT']);
    $this->jenis = JenisLinen::create(['jenis_nama' => 'Seprai Batch']);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Katun']);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier Batch']);
    DB::table('rs_dan_ruangan')->insert(['rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id]);
    DB::table('rs_dan_jenis')->insert(['rs_id' => $this->rs->rs_id, 'jenis_id' => $this->jenis->jenis_id, 'parstock' => 10]);
    $this->admin = User::create(['name' => 'Admin Batch', 'email' => 'batch@bka.test', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    $this->token = $this->admin->createToken('test-token')->plainTextToken;
});

function batchPayload($test, array $rfids, ?string $keterangan = null, ?string $kepemilikan = null, $rsId = null): array
{
    $payload = [
        'rfid' => $rfids,
        'jenis_id' => $test->jenis->jenis_id,
        'supplier_id' => $test->supplier->supplier_id,
        'bahan_id' => $test->bahan->bahan_id,
        'status_cuci' => CuciEnum::RENTAL,
        'rs_id' => $rsId ?? $test->rs->rs_id,
        'ruangan_id' => $test->ruangan->ruangan_id,
        'status_register' => RegisterEnum::REGISTER,
    ];

    // null = tidak dikirim → kepemilikan ditebak dari rs_id + tipe RS.
    if ($kepemilikan !== null) {
        $payload['status_kepemilikan'] = $kepemilikan;
    }

    if ($keterangan !== null) {
        $payload['keterangan'] = $keterangan;
    }

    return $payload;
}

it('generates no_register dari waktu dan menyimpan keterangan', function () {
    $rfids = ['BATCH_1', 'BATCH_2'];
    $res = $this->withToken($this->token)->postJson('/api/register', batchPayload($this, $rfids, 'Batch pagi dari desktop', RsStatusEnum::DEDICATED));

    $res->assertOk()->assertJson(['status' => true]);
    $no = $res->json('no_register');
    expect($no)->toMatch('/^REG-\d{14}-\d{4}$/');
    expect($res->json('keterangan'))->toBe('Batch pagi dari desktop');

    // Batch kedua dapat nomor waktunya sendiri; tiap baris bawa nomornya.
    $no2 = $this->withToken($this->token)
        ->postJson('/api/register', batchPayload($this, ['BATCH_X'], null, RsStatusEnum::DEDICATED))
        ->assertOk()
        ->json('no_register');
    expect($no2)->toMatch('/^REG-\d{14}-\d{4}$/');
    expect(DetailLinen::where('detail_rfid', 'BATCH_X')->value('detail_no_register'))->toBe($no2);

    // Tiap baris detail membawa nomor + keterangan yang sama.
    foreach ($rfids as $rfid) {
        $d = DetailLinen::where('detail_rfid', $rfid)->firstOrFail();
        expect($d->detail_no_register)->toBe($no);
        expect($d->detail_deskripsi)->toBe('Batch pagi dari desktop');
        expect($d->detail_status_cuci)->toBe(CuciEnum::RENTAL);
        expect($d->detail_id_bahan)->toBe($this->bahan->bahan_id);
        expect($d->detail_id_supplier)->toBe($this->supplier->supplier_id);
    }
});

it('tetap generates no_register walau tanpa keterangan', function () {
    $res = $this->withToken($this->token)->postJson('/api/register', batchPayload($this, ['BATCH_3'], null, RsStatusEnum::DEDICATED));

    $res->assertOk()->assertJson(['status' => true]);
    expect($res->json('no_register'))->toMatch('/^REG-\d{14}-\d{4}$/');
    expect($res->json('keterangan'))->toBeNull();
    expect(DetailLinen::where('detail_rfid', 'BATCH_3')->value('detail_no_register'))->toBe($res->json('no_register'));
});

it('register 1 RS FREE tanpa status_kepemilikan jadi FREE', function () {
    $free = Rs::create(['rs_nama' => 'RS Free', 'rs_code' => 'FRE', 'rs_status' => RsStatusEnum::FREE, 'rs_aktif' => 1]);

    $no = $this->withToken($this->token)
        ->postJson('/api/register', batchPayload($this, ['FREE_1'], 'Linen bebas', null, $free->rs_id))
        ->assertOk()
        ->json('no_register');

    $d = DetailLinen::where('detail_rfid', 'FREE_1')->firstOrFail();
    expect($d->detail_status_kepemilikan)->toBe(RsStatusEnum::FREE);
    expect($d->detail_id_rs)->toBe($free->rs_id);
    expect($d->detail_id_ruangan)->toBeNull();
    expect(DB::table('config_linen')->where('detail_rfid', 'FREE_1')->count())->toBe(0);
});

it('register 1 RS GROUP tanpa status_kepemilikan jadi GROUP anggota', function () {
    $groupId = DB::table('group_rs')->insertGetId(['group_rs_nama' => 'Grup Uji', 'group_rs_code' => 'GU']);
    $g1 = Rs::create(['rs_nama' => 'RS G1', 'rs_code' => 'G1', 'rs_status' => RsStatusEnum::GROUP, 'rs_id_group' => $groupId, 'rs_aktif' => 1]);
    $g2 = Rs::create(['rs_nama' => 'RS G2', 'rs_code' => 'G2', 'rs_status' => RsStatusEnum::GROUP, 'rs_id_group' => $groupId, 'rs_aktif' => 1]);

    $no = $this->withToken($this->token)
        ->postJson('/api/register', batchPayload($this, ['GRP_1'], null, null, $g1->rs_id))
        ->assertOk()
        ->json('no_register');

    $d = DetailLinen::where('detail_rfid', 'GRP_1')->firstOrFail();
    expect($d->detail_status_kepemilikan)->toBe(RsStatusEnum::GROUP);
    expect($d->detail_id_rs)->toBe($g1->rs_id);
    expect($d->detail_id_ruangan)->toBeNull();
    expect(DB::table('config_linen')->where('detail_rfid', 'GRP_1')->pluck('rs_id')->sort()->values()->all())
        ->toBe(collect([$g1->rs_id, $g2->rs_id])->sort()->values()->all());
});

it('menampilkan no register dan keterangan di tabel detail linen', function () {
    $no = $this->withToken($this->token)
        ->postJson('/api/register', batchPayload($this, ['TBL_1'], 'Keterangan tabel'))
        ->assertOk()
        ->json('no_register');

    $this->actingAs($this->admin)
        ->get('/detail-linen/table?filters[detail_no_register][$contains]='.urlencode($no))
        ->assertOk()
        ->assertSee($no)
        ->assertSee('Keterangan tabel');

    $this->actingAs($this->admin)
        ->get('/detail-linen/table?filters[detail_deskripsi][$contains]=Keterangan')
        ->assertOk()
        ->assertSee('TBL_1');
});

it('mengambil ulang batch via GET /api/register/{no}', function () {
    $no = $this->withToken($this->token)
        ->postJson('/api/register', batchPayload($this, ['BATCH_4'], 'Cek ulang'))
        ->assertOk()
        ->json('no_register');

    $this->withToken($this->token)->getJson("/api/register/{$no}")
        ->assertOk()
        ->assertJson(['status' => true, 'no_register' => $no, 'keterangan' => 'Cek ulang', 'total' => 1])
        ->assertJsonPath('rfid.0', 'BATCH_4');

    $this->withToken($this->token)->getJson('/api/register/REG-20990101-9999')->assertOk()->assertJson(['status' => false]);
});
