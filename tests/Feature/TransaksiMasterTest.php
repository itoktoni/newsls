<?php

use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Models\DetailLinen;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Supplier;
use App\Models\TransaksiMaster;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $this->rs = Rs::updateOrCreate(['rs_code' => 'TMASTER'], ['rs_nama' => 'RS Master Test', 'rs_status' => RsStatusEnum::DEDICATED]);
    $this->ruangan = Ruangan::firstOrCreate(['ruangan_code' => 'TMT'], ['ruangan_nama' => 'Ruang TMaster']);
    $this->jenis = JenisLinen::updateOrCreate(['jenis_nama' => 'Jenis TMaster'], ['jenis_berat' => 0.5]);
    $this->bahan = JenisBahan::firstOrCreate(['bahan_nama' => 'Bahan TMaster']);
    $this->supplier = Supplier::firstOrCreate(['supplier_nama' => 'Sup TMaster']);
    DB::table('rs_dan_ruangan')->updateOrInsert(['rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id], []);
    $this->admin = User::updateOrCreate(['email' => 'tmaster@bka.test'], ['name' => 'Op Master', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    DB::table('rs_dan_user')->updateOrInsert(['user_id' => $this->admin->id, 'rs_id' => $this->rs->rs_id], []);
    $this->token = $this->admin->createToken('tmaster-'.uniqid())->plainTextToken;

    $this->rfids = ['TMASTER_1', 'TMASTER_2'];
    foreach ($this->rfids as $rfid) {
        DetailLinen::updateOrCreate(['detail_rfid' => $rfid], [
            'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
            'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id,
            'detail_id_supplier' => $this->supplier->supplier_id,
            'detail_status_linen' => LinenStatusEnum::BERSIH,
            'detail_status_register' => RegisterEnum::REGISTER,
            'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
        ]);
    }
    DetailLinen::whereIn('detail_rfid', $this->rfids)->update(['detail_updated_at' => now()->subDays(2)]);

    // Idempoten: singkirkan sisa run sebelumnya untuk RFID + key prefix ini.
    DB::table('outstanding')->whereIn('outstanding_rfid', $this->rfids)->delete();
    DB::table('transaksi')->whereIn('transaksi_rfid', $this->rfids)->whereDate('transaksi_created_at', today())->delete();
    DB::table('transaksi')->where('transaksi_key', 'like', 'TMASTER%')->delete();
    DB::table('transaksi_master')->where('master_kode', 'like', 'TMASTER%')->delete();
});

afterEach(function () {
    // Bersihkan jejak (DB test dipakai bersama antar file, tanpa RefreshDatabase).
    foreach (['TMASTER%', 'TMWEB%', 'TMSEL%'] as $like) {
        DB::table('transaksi')->where('transaksi_key', 'like', $like)->delete();
        DB::table('transaksi_master')->where('master_kode', 'like', $like)->delete();
    }
    DB::table('outstanding')->whereIn('outstanding_rfid', ['TMASTER_1', 'TMASTER_2'])->delete();
    DetailLinen::whereIn('detail_rfid', ['TMASTER_1', 'TMASTER_2'])->delete();
});

it('master detail kotor via api dengan berat kotor opsional', function () {
    $key = 'TMASTER-'.strtoupper(uniqid());

    $res = $this->withToken($this->token)->postJson('/api/kotor', [
        'rfid' => $this->rfids, 'rs_id' => $this->rs->rs_id, 'key' => $key, 'qty' => 1.25,
    ])->assertOk()->assertJson(['status' => true]);

    expect($res->json('data.0.status_sync'))->toBe(1)
        ->and($res->json('data.0.status_transaksi'))->toBe('KOTOR')
        ->and(collect($res->json('data'))->where('status_sync', 1)->count())->toBe(2);
    $master = TransaksiMaster::where('master_kode', $key)->first();
    expect($master)->not->toBeNull()
        ->and((float) $master->master_qty)->toBe(1.25)
        ->and($master->master_status)->toBe('KOTOR')
        ->and($master->master_rs_id)->toBe($this->rs->rs_id)
        ->and($master->master_tanggal->format('Y-m-d'))->toBe(today()->format('Y-m-d'));

    // Retry key sama = update master (idempoten), bukan duplikat.
    // RFID sama di hari sama tidak nambah detail (existing today skip).
    $res3 = $this->withToken($this->token)->postJson('/api/kotor', [
        'rfid' => $this->rfids, 'rs_id' => $this->rs->rs_id, 'key' => $key, 'qty' => 2.5,
    ])->assertOk()->assertJson(['status' => true]);
    expect((float) TransaksiMaster::where('master_kode', $key)->value('master_qty'))->toBe(2.5)
        ->and(collect($res3->json('data'))->where('status_sync', 1)->count())->toBe(0);

    $master = TransaksiMaster::where('master_kode', $key)->first();
    expect($master)->not->toBeNull()
        ->and((float) $master->master_qty)->toBe(2.5)
        ->and($master->hasDetails()->count())->toBe(2);

    // qty bukan angka -> envelope validasi (HTTP tetap 200, code 422)
    $this->withToken($this->token)->postJson('/api/kotor', [
        'rfid' => $this->rfids, 'rs_id' => $this->rs->rs_id, 'key' => $key.'-X', 'qty' => 'berat',
    ])->assertOk()->assertJson(['status' => false, 'code' => 422]);

    // tanpa qty -> null, berat bersih tetap dihitung
    $key2 = 'TMASTER2-'.strtoupper(uniqid());
    DetailLinen::whereIn('detail_rfid', $this->rfids)->update(['detail_updated_at' => now()->subDays(2), 'detail_status_linen' => LinenStatusEnum::BERSIH]);
    DB::table('outstanding')->whereIn('outstanding_rfid', $this->rfids)->delete();
    DB::table('transaksi')->whereIn('transaksi_rfid', $this->rfids)->whereDate('transaksi_created_at', today())->delete();
    $res2 = $this->withToken($this->token)->postJson('/api/retur', [
        'rfid' => $this->rfids, 'rs_id' => $this->rs->rs_id, 'key' => $key2,
    ])->assertOk()->assertJson(['status' => true]);
    expect(TransaksiMaster::where('master_kode', $key2)->value('master_status'))->toBe('REJECT')
        ->and($res2->json('data.0.status_transaksi'))->toBe('REJECT')
        ->and($res2->json('data.0.status_sync'))->toBe(1);
});

it('web transaksi master list dan show', function () {
    $key = 'TMWEB-'.strtoupper(uniqid());
    TransaksiMaster::updateOrCreate(['master_kode' => $key], [
        'master_rs_id' => $this->rs->rs_id, 'master_status' => 'KOTOR',
        'master_tanggal' => today()->format('Y-m-d'), 'master_qty' => 1,
        'master_created_by' => $this->admin->id, 'master_created_at' => now(), 'master_updated_at' => now(),
    ]);

    $this->actingAs($this->admin)->get('/transaksi/table')->assertOk()->assertSee($key);

    $master = TransaksiMaster::where('master_kode', $key)->first();
    $this->actingAs($this->admin)->get("/transaksi/show/{$master->master_id}")->assertOk()->assertSee($key);
});

it('report selisih berat dan rekap kotor memuat ringkasan berat', function () {
    $uniq = strtoupper(uniqid());
    $keys = ["TMSEL-A-{$uniq}", "TMSEL-B-{$uniq}"];

    TransaksiMaster::updateOrCreate(['master_kode' => $keys[0]], [
        'master_rs_id' => $this->rs->rs_id, 'master_status' => 'KOTOR',
        'master_tanggal' => today()->format('Y-m-d'), 'master_qty' => 10.5,
        'master_created_by' => $this->admin->id, 'master_created_at' => now(), 'master_updated_at' => now(),
    ]);
    TransaksiMaster::updateOrCreate(['master_kode' => $keys[1]], [
        'master_rs_id' => $this->rs->rs_id, 'master_status' => 'KOTOR',
        'master_tanggal' => today()->format('Y-m-d'), 'master_qty' => null,
        'master_created_by' => $this->admin->id, 'master_created_at' => now(), 'master_updated_at' => now(),
    ]);

    // Detail untuk keys[0]: 2 RFID x 0,5 kg = bersih 1,0 kg (dihitung live).
    foreach ($this->rfids as $rfid) {
        DB::table('transaksi')->insert([
            'transaksi_key' => $keys[0], 'transaksi_rfid' => $rfid,
            'transaksi_rs_ori' => $this->rs->rs_id, 'transaksi_rs_scan' => $this->rs->rs_id,
            'transaksi_beda_rs' => 'TIDAK', 'transaksi_id_ruangan' => $this->ruangan->ruangan_id,
            'transaksi_status' => 'KOTOR', 'transaksi_created_at' => now(), 'transaksi_updated_at' => now(),
            'transaksi_created_by' => $this->admin->id, 'transaksi_updated_by' => $this->admin->id,
        ]);
    }

    $this->actingAs($this->admin)->get('/report-selisih-berat/table')->assertOk();

    $this->actingAs($this->admin)
        ->get("/report-selisih-berat/print?rs_id={$this->rs->rs_id}")
        ->assertOk()
        ->assertSee('SELISIH BERAT TRANSAKSI')
        ->assertSee($keys[0])
        ->assertSee('10,5')
        ->assertSee('9,5');

    $this->actingAs($this->admin)
        ->get("/report-selisih-berat/print?rs_id={$this->rs->rs_id}&status=KOTOR")
        ->assertOk()
        ->assertSee($keys[1]);

    $this->actingAs($this->admin)
        ->get("/report-rekap-kotor/print?rs_id={$this->rs->rs_id}")
        ->assertOk()
        ->assertSee('RINGKASAN BERAT TIMBANGAN');

    $this->actingAs($this->admin)
        ->get("/report-detail-kotor/print?rs_id={$this->rs->rs_id}")
        ->assertOk()
        ->assertSee('RINGKASAN BERAT TIMBANGAN')
        ->assertSee('BERAT (KG)');
});

it('dashboard admin laundry rs menampilkan bobot kg', function () {
    $rfids = ['TMSEL-D1', 'TMSEL-D2'];
    foreach ($rfids as $rfid) {
        DetailLinen::updateOrCreate(['detail_rfid' => $rfid], [
            'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
            'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id,
            'detail_id_supplier' => $this->supplier->supplier_id,
            'detail_status_linen' => LinenStatusEnum::KOTOR,
            'detail_status_register' => RegisterEnum::REGISTER,
            'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
        ]);
    }

    // 2 RFID kotor x 0,5 kg = 1 kg tampil sebagai badge di dashboard admin.
    $this->actingAs($this->admin)->get('/dashboard/admin')->assertOk()->assertSee('1 kg');

    $laundry = User::updateOrCreate(['email' => 'tmaster-laundry@bka.test'], [
        'name' => 'Laundry', 'password' => Hash::make('password'), 'role' => 'laundry',
        'verified_at' => now(), 'email_verified_at' => now(),
    ]);
    DB::table('rs_dan_user')->updateOrInsert(['user_id' => $laundry->id, 'rs_id' => $this->rs->rs_id], []);
    $this->actingAs($laundry)->get('/dashboard/laundry')->assertOk()->assertSee('1 kg');

    $rsUser = User::updateOrCreate(['email' => 'tmaster-rs@bka.test'], [
        'name' => 'RS User', 'password' => Hash::make('password'), 'role' => 'rs',
        'verified_at' => now(), 'email_verified_at' => now(),
    ]);
    DB::table('rs_dan_user')->updateOrInsert(['user_id' => $rsUser->id, 'rs_id' => $this->rs->rs_id], []);
    $this->actingAs($rsUser)->get('/dashboard/rs')->assertOk()->assertSee('1 kg');

    DB::table('bersih')->whereIn('bersih_rfid', $rfids)->delete();
    DetailLinen::whereIn('detail_rfid', $rfids)->delete();
});

it('report summary pengiriman memuat berat per DO', function () {
    $rfids = ['TMSEL-B1', 'TMSEL-B2'];
    foreach ($rfids as $rfid) {
        DetailLinen::updateOrCreate(['detail_rfid' => $rfid], [
            'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
            'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id,
            'detail_id_supplier' => $this->supplier->supplier_id,
            'detail_status_linen' => LinenStatusEnum::BERSIH,
            'detail_status_register' => RegisterEnum::REGISTER,
            'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
        ]);
        DB::table('bersih')->updateOrInsert(['bersih_rfid' => $rfid], [
            'bersih_status' => 'BERSIH', 'bersih_id_rs' => $this->rs->rs_id,
            'bersih_id_ruangan' => $this->ruangan->ruangan_id,
            'bersih_barcode' => 'BC-'.$rfid, 'bersih_delivery' => 'DO-TMSEL',
            'bersih_report' => today()->format('Y-m-d'),
            'bersih_created_at' => now(), 'bersih_updated_at' => now(),
            'bersih_created_by' => $this->admin->id,
        ]);
    }

    // 2 baris x 0,5 kg = 1 kg per DO.
    $this->actingAs($this->admin)
        ->get("/report-summary-pengiriman-bersih/print?rs_id={$this->rs->rs_id}")
        ->assertOk()
        ->assertSee('BERAT (KG)')
        ->assertSee('DO-TMSEL')
        ->assertSee('<b>1</b>', false);

    DB::table('bersih')->whereIn('bersih_rfid', $rfids)->delete();
    DetailLinen::whereIn('detail_rfid', $rfids)->delete();
});
