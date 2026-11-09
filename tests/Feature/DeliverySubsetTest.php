<?php

// Semantik DO (delivery): per RS + per STATUS yang dipilih — bulk
// menghapus semua PACKING RS+status itu; subset rfid[] hanya menghapus
// yang dikirim; satu saja RFID tidak valid (bukan PACKING RS+status itu)
// → seluruh request ditolak 422 dan TIDAK ADA yang terhapus.

use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Models\DetailLinen;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Outstanding;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $uniq = strtoupper(uniqid());
    $this->rs = Rs::create(['rs_nama' => 'RS DOSub '.$uniq, 'rs_code' => 'DOS'.$uniq, 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang DOSub '.$uniq, 'ruangan_code' => 'DOS'.$uniq]);
    $this->jenis = JenisLinen::create(['jenis_nama' => 'Jenis DOSub '.$uniq, 'jenis_berat' => 0.5]);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Bahan DOSub '.$uniq]);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier DOSub '.$uniq]);
    DB::table('rs_dan_ruangan')->insert(['rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id]);
    DB::table('warehouse')->updateOrInsert(['warehouse_id' => 1], ['warehouse_nama' => 'Gudang Utama']);
    $this->admin = User::create(['name' => 'Admin DOSub', 'email' => 'dosub-'.$uniq.'@bka.test', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    $this->token = $this->admin->createToken('test-token')->plainTextToken;

    $this->rfids = ['DOSUB-1-'.$uniq, 'DOSUB-2-'.$uniq, 'DOSUB-3-'.$uniq];
    foreach ($this->rfids as $rfid) {
        DetailLinen::create([
            'detail_rfid' => $rfid, 'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
            'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
            'detail_status_cuci' => 'CUCI', 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::BERSIH,
            'detail_report' => now()->subDays(2)->format('Y-m-d'),
            'detail_total_bersih' => 1, 'detail_created_by' => $this->admin->id,
        ]);
        DB::table('config_linen')->insert(['detail_rfid' => $rfid, 'rs_id' => $this->rs->rs_id]);
    }
    DetailLinen::whereIn('detail_rfid', $this->rfids)->update(['detail_updated_at' => now()->subDays(2)]);

    // kotor → grouping → packing (KOTOR): 3 outstanding PACKING.
    $this->withToken($this->token)->postJson('/api/kotor', [
        'rfid' => $this->rfids, 'rs_id' => $this->rs->rs_id, 'key' => 'DOS-KTR-'.$uniq,
    ])->assertOk()->assertJson(['status' => true]);
    foreach ($this->rfids as $rfid) {
        $this->withToken($this->token)->getJson("/api/grouping/{$rfid}")->assertOk();
    }
    $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => $this->rfids, 'rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => true]);

    expect(Outstanding::whereIn('outstanding_rfid', $this->rfids)->where('outstanding_status_proses', 'PACKING')->count())->toBe(3);
});

it('DO subset hanya menghapus RFID yang dikirim, sisanya tetap ada', function () {
    $do = [$this->rfids[0], $this->rfids[1]];

    $this->withToken($this->token)->postJson('/api/delivery', [
        'rfid' => $do, 'rs_id' => $this->rs->rs_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => true]);

    // 2 terhapus, 1 tersisa (by design: subset).
    expect(Outstanding::where('outstanding_rfid', $this->rfids[0])->exists())->toBeFalse();
    expect(Outstanding::where('outstanding_rfid', $this->rfids[1])->exists())->toBeFalse();
    expect(Outstanding::where('outstanding_rfid', $this->rfids[2])->exists())->toBeTrue()
        ->and(Outstanding::where('outstanding_rfid', $this->rfids[2])->value('outstanding_status_proses'))->toBe('PACKING');
});

it('DO dengan satu RFID basi ditolak total, tidak ada yang terhapus', function () {
    // DO subset valid dulu untuk 1 RFID (jadi basi), lalu coba DO campur.
    $this->withToken($this->token)->postJson('/api/delivery', [
        'rfid' => [$this->rfids[0]], 'rs_id' => $this->rs->rs_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => true]);

    // RFID[0] sudah ter-delivery (basi) + RFID[1] masih PACKING valid.
    $res = $this->withToken($this->token)->postJson('/api/delivery', [
        'rfid' => [$this->rfids[0], $this->rfids[1]], 'rs_id' => $this->rs->rs_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => false]);

    // All-or-nothing: RFID[1] yang valid pun TIDAK ikut terhapus.
    expect(Outstanding::where('outstanding_rfid', $this->rfids[1])->exists())->toBeTrue();
    expect(Outstanding::where('outstanding_rfid', $this->rfids[2])->exists())->toBeTrue();
});

it('DO hanya menghapus packing dengan status yang dipilih (REGISTER tetap ada)', function () {
    $uniq = strtoupper(uniqid());
    // Linen fresh-register (tanpa kotor): grouping → outstanding REGISTER.
    $regRfid = 'DOSUB-REG-'.$uniq;
    DetailLinen::create([
        'detail_rfid' => $regRfid, 'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
        'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
        'detail_status_cuci' => 'CUCI', 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
        'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::REGISTER,
        'detail_created_by' => $this->admin->id,
    ]);
    DB::table('config_linen')->insert(['detail_rfid' => $regRfid, 'rs_id' => $this->rs->rs_id]);

    $this->withToken($this->token)->getJson("/api/grouping/{$regRfid}")->assertOk();
    expect(Outstanding::where('outstanding_rfid', $regRfid)->value('outstanding_status_transaksi'))->toBe('REGISTER');

    // Packing REGISTER terpisah (packing menuntut status seragam per call).
    $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => [$regRfid], 'rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruangan->ruangan_id, 'status_transaksi' => 'REGISTER',
    ])->assertOk()->assertJson(['status' => true]);

    expect(Outstanding::where('outstanding_rs_scan', $this->rs->rs_id)->where('outstanding_status_proses', 'PACKING')->count())->toBe(4);

    // Satu DO (status KOTOR) hanya menghapus 3 KOTOR; REGISTER tetap ada
    // sampai DO dengan status REGISTER.
    $this->withToken($this->token)->postJson('/api/delivery', [
        'rs_id' => $this->rs->rs_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => true]);

    expect(Outstanding::whereIn('outstanding_rfid', $this->rfids)->count())->toBe(0);
    expect(Outstanding::where('outstanding_rfid', $regRfid)->exists())->toBeTrue();

    // DO susulan dengan status REGISTER menghapus sisanya.
    $this->withToken($this->token)->postJson('/api/delivery', [
        'rs_id' => $this->rs->rs_id, 'status_transaksi' => 'REGISTER',
    ])->assertOk()->assertJson(['status' => true]);

    expect(Outstanding::where('outstanding_rs_scan', $this->rs->rs_id)->count())->toBe(0);

    // Counter pemakaian tetap per status asal: REGISTER → total_bersih.
    expect((int) DetailLinen::where('detail_rfid', $regRfid)->value('detail_total_bersih'))->toBe(1);
    expect(DetailLinen::where('detail_rfid', $regRfid)->value('detail_status_linen'))->toBe(LinenStatusEnum::BERSIH);
});
