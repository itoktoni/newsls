<?php

use App\Enums\CuciEnum;
use App\Enums\LinenStatusEnum;
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

    $uniq = strtoupper(uniqid());
    $this->rs = Rs::create(['rs_nama' => 'RS Pending', 'rs_code' => 'PND'.$uniq, 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang Pending', 'ruangan_code' => 'PND'.$uniq]);
    $this->jenis = JenisLinen::create(['jenis_nama' => 'Seprai Pending '.$uniq]);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Katun Pending '.$uniq]);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier Pending '.$uniq]);
    DB::table('rs_dan_user')->insert(['user_id' => ($this->admin = User::create([
        'name' => 'Admin Pending', 'email' => 'pending-'.$uniq.'@bka.test',
        'password' => Hash::make('password'), 'role' => 'admin',
        'verified_at' => now(), 'email_verified_at' => now(),
    ]))->id, 'rs_id' => $this->rs->rs_id]);
    DB::table('warehouse')->updateOrInsert(['warehouse_id' => 1], ['warehouse_nama' => 'Gudang Utama']);
    $this->token = $this->admin->createToken('test-token-'.uniqid())->plainTextToken;

    $this->makeLinen = function (string $rfid, string $kepemilikan, string $proses, int $hoursAgo) {
        DetailLinen::create([
            'detail_rfid' => $rfid, 'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
            'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
            'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => $kepemilikan,
            'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::KOTOR,
            'detail_created_by' => $this->admin->id,
        ]);
        DB::table('config_linen')->insert(['detail_rfid' => $rfid, 'rs_id' => $this->rs->rs_id]);
        DB::table('outstanding')->insert([
            'outstanding_rfid' => $rfid, 'outstanding_key' => 'KTR-'.$rfid,
            'outstanding_rs_ori' => $this->rs->rs_id, 'outstanding_rs_scan' => $this->rs->rs_id,
            'outstanding_id_ruangan' => $this->ruangan->ruangan_id,
            'outstanding_status_transaksi' => 'KOTOR', 'outstanding_status_proses' => $proses,
            'outstanding_status_hilang' => 'NORMAL',
            'outstanding_created_at' => now()->subHours($hoursAgo + 1)->format('Y-m-d H:i:s'),
            'outstanding_updated_at' => now()->subHours($hoursAgo)->format('Y-m-d H:i:s'),
            'outstanding_created_by' => $this->admin->id, 'outstanding_updated_by' => $this->admin->id,
        ]);
    };
});

/**
 * Outstanding SCAN >24 jam milik linen DEDICATED → masuk tabel pending.
 */
it('check:pending-dedicated memasukkan SCAN >24 jam milik dedicated ke pending', function () {
    $uniq = strtoupper(uniqid());
    ($this->makeLinen)('PND-OLD-'.$uniq, RsStatusEnum::DEDICATED, 'SCAN', 25);
    ($this->makeLinen)('PND-FRESH-'.$uniq, RsStatusEnum::DEDICATED, 'SCAN', 1);
    ($this->makeLinen)('PND-FREE-'.$uniq, RsStatusEnum::FREE, 'SCAN', 30);
    ($this->makeLinen)('PND-PACK-'.$uniq, RsStatusEnum::DEDICATED, 'PACKING', 30);

    $this->artisan('check:pending-dedicated')->assertSuccessful();

    // Hanya yang dedicated + SCAN + >24 jam yang masuk
    $pending = DB::table('pending')->where('pending_rfid', 'PND-OLD-'.$uniq)->first();
    expect($pending)->not->toBeNull();
    expect($pending->pending_transaksi)->toBe('KOTOR');
    expect($pending->pending_proses)->toBe('SCAN');
    expect($pending->pending_bersih_at)->toBeNull();
    expect($pending->pending_id_rs)->toBe($this->rs->rs_id);

    foreach (['PND-FRESH-'.$uniq, 'PND-FREE-'.$uniq, 'PND-PACK-'.$uniq] as $rfid) {
        expect(DB::table('pending')->where('pending_rfid', $rfid)->count())->toBe(0);
    }

    // Outstanding yang masuk ditandai PENDING
    $out = DB::table('outstanding')->where('outstanding_rfid', 'PND-OLD-'.$uniq)->first();
    expect($out->outstanding_status_hilang)->toBe('PENDING');
    expect($out->outstanding_pending_created_at)->not->toBeNull();

    // Jalan kedua: idempoten, tidak duplikat
    $this->artisan('check:pending-dedicated')->assertSuccessful();
    expect(DB::table('pending')->where('pending_rfid', 'PND-OLD-'.$uniq)->count())->toBe(1);
});

/**
 * Delivery = bayar pending: RFID terkirim yang punya pending terbuka
 * ditutup (bersih_at + kode delivery terisi).
 */
it('delivery menutup pending RFID yang terkirim', function () {
    $uniq = strtoupper(uniqid());
    $rfid = 'PND-PAY-'.$uniq;
    ($this->makeLinen)($rfid, RsStatusEnum::DEDICATED, 'SCAN', 25);

    $this->artisan('check:pending-dedicated')->assertSuccessful();
    expect(DB::table('pending')->where('pending_rfid', $rfid)->whereNull('pending_bersih_at')->count())->toBe(1);

    // Alur normal: grouping → packing → delivery
    $this->withToken($this->token)->getJson("/api/grouping/{$rfid}")->assertOk();
    $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => [$rfid], 'rs_id' => $this->rs->rs_id,
        'ruangan_id' => $this->ruangan->ruangan_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => true]);
    $this->withToken($this->token)->postJson('/api/delivery', [
        'rs_id' => $this->rs->rs_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => true]);

    $pending = DB::table('pending')->where('pending_rfid', $rfid)->first();
    expect($pending->pending_bersih_at)->not->toBeNull();
    expect($pending->pending_delivery)->not->toBeNull();
    expect((int) $pending->pending_bersih_by)->toBe($this->admin->id);
});

/**
 * Mode stagnan: outstanding GUDANG tak bergerak >1 bulan muncul,
 * yang kemarin bergerak tidak ikut.
 */
it('report pending outstanding mode stagnan menampilkan gudang tak bergerak', function () {
    $uniq = strtoupper(uniqid());
    ($this->makeLinen)('STG-OLD-'.$uniq, RsStatusEnum::DEDICATED, 'GUDANG', 24 * 40);
    ($this->makeLinen)('STG-FRESH-'.$uniq, RsStatusEnum::DEDICATED, 'GUDANG', 1);

    // Tanpa filter stagnan: bukan PENDING → tidak muncul
    $this->actingAs($this->admin)->get('/report-detail-pending-linen/print')
        ->assertOk()->assertDontSee('STG-OLD-'.$uniq);

    // Mode stagnan 1 bulan: yang tua muncul, yang segar tidak
    $this->actingAs($this->admin)->get('/report-detail-pending-linen/print?stagnan=1bulan&proses=GUDANG')
        ->assertOk()->assertSee('STG-OLD-'.$uniq)->assertDontSee('STG-FRESH-'.$uniq)
        ->assertSee('Tidak bergerak', false)->assertSee('40 Hari');

    // Tanggal eksplisit juga bisa
    $sejak = now()->subDays(10)->format('Y-m-d');
    $this->actingAs($this->admin)->get("/report-detail-pending-linen/print?stagnan_sejak={$sejak}")
        ->assertOk()->assertSee('STG-OLD-'.$uniq)->assertDontSee('STG-FRESH-'.$uniq);
});
