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
    $this->rs = Rs::create(['rs_nama' => 'RS Stagnan', 'rs_code' => 'STG'.$uniq, 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruangan = Ruangan::create(['ruangan_nama' => 'Ruang Stagnan', 'ruangan_code' => 'STG'.$uniq]);
    $this->jenis = JenisLinen::create(['jenis_nama' => 'Duk Stagnan '.$uniq]);
    $this->bahan = JenisBahan::create(['bahan_nama' => 'Katun Stagnan '.$uniq]);
    $this->supplier = Supplier::create(['supplier_nama' => 'Supplier Stagnan '.$uniq]);
    $this->admin = User::create(['name' => 'Admin Stagnan', 'email' => 'stagnan-'.$uniq.'@bka.test',
        'password' => Hash::make('password'), 'role' => 'admin',
        'verified_at' => now(), 'email_verified_at' => now()]);

    $this->makeBersih = function (string $rfid, int $daysAgo) {
        DetailLinen::create([
            'detail_rfid' => $rfid, 'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $this->ruangan->ruangan_id,
            'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
            'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::BERSIH,
            'detail_created_by' => $this->admin->id,
        ]);
        // Kolom updated tidak fillable → set lewat query builder
        DetailLinen::where('detail_rfid', $rfid)->update([
            'detail_updated_at' => now()->subDays($daysAgo)->format('Y-m-d H:i:s'),
        ]);
    };
});

/**
 * Linen BERSIH di RS yang tak bergerak >1 bulan muncul; yang segar tidak.
 */
it('report stagnan di RS menampilkan linen bersih tak bergerak', function () {
    $uniq = strtoupper(uniqid());
    ($this->makeBersih)('STGRS-OLD-'.$uniq, 45);
    ($this->makeBersih)('STGRS-FRESH-'.$uniq, 5);

    // Default = 1 bulan
    $this->actingAs($this->admin)->get('/report-hilang-linen/print?rs_id='.$this->rs->rs_id)
        ->assertOk()->assertSee('STAGNAN DI RS')
        ->assertSee('STGRS-OLD-'.$uniq)->assertDontSee('STGRS-FRESH-'.$uniq)
        ->assertSee('45 Hari');

    // Preset 3 bulan: yang 45 hari tidak ikut
    $this->actingAs($this->admin)->get('/report-hilang-linen/print?rs_id='.$this->rs->rs_id.'&stagnan=3bulan')
        ->assertOk()->assertDontSee('STGRS-OLD-'.$uniq);
});
