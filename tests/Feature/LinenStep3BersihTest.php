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

    // Lanjut dari step2 jika ada GUDANG — jangan hapus
    $existingGudang = DB::table('outstanding')->where('outstanding_status_proses', 'GUDANG')->pluck('outstanding_rfid')->all();
    if (! empty($existingGudang)) {
        $firstOut = DB::table('outstanding')->whereIn('outstanding_rfid', $existingGudang)->first();
        $this->rs = Rs::find($firstOut->outstanding_rs_scan);
        $this->ruanganA = Ruangan::find($firstOut->outstanding_id_ruangan) ?? Ruangan::where('ruangan_code', 'DHL')->first() ?? Ruangan::create(['ruangan_nama' => 'Ruang Dahlia', 'ruangan_code' => 'DHL']);
        $this->ruanganB = Ruangan::where('ruangan_code', 'MWR')->first() ?? Ruangan::create(['ruangan_nama' => 'Ruang Mawar', 'ruangan_code' => 'MWR']);
        $this->jenis = JenisLinen::first() ?? JenisLinen::create(['jenis_nama' => 'Seprai Single']);
        $this->bahan = JenisBahan::first() ?? JenisBahan::create(['bahan_nama' => 'Katun']);
        $this->supplier = Supplier::first() ?? Supplier::create(['supplier_nama' => 'Supplier Test']);
        $this->admin = User::where('email', 'step1@bka.test')->first() ?? User::where('email', 'step2@bka.test')->first() ?? User::create(['name' => 'Admin Step3', 'email' => 'step3@bka.test', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
        DB::table('rs_dan_user')->updateOrInsert(['user_id' => $this->admin->id, 'rs_id' => $this->rs->rs_id], []);
        $this->token = $this->admin->createToken('test-token-'.uniqid())->plainTextToken;
        // bagi GUDANG jadi 2 ruangan untuk test packing per ruangan — ambil 2+1
        $all = array_values($existingGudang);
        $this->rfidsA = array_slice($all, 0, 2);
        $this->rfidsB = array_slice($all, 2, 1);
        if (empty($this->rfidsB) && count($all) >= 1) {
            $this->rfidsB = [$all[0]];
            $this->rfidsA = [$all[0]];
        }
        $this->allRfids = array_merge($this->rfidsA, $this->rfidsB);
        // update ruanganB untuk rfidsB biar beda ruangan
        if (! empty($this->rfidsB)) {
            DB::table('outstanding')->whereIn('outstanding_rfid', $this->rfidsB)->update(['outstanding_id_ruangan' => $this->ruanganB->ruangan_id]);
            DetailLinen::whereIn('detail_rfid', $this->rfidsB)->update(['detail_id_ruangan' => $this->ruanganB->ruangan_id]);
        }

        return;
    }

    // Fallback standalone — idempoten (boleh run berulang tanpa GUDANG sisa)
    $this->rs = Rs::updateOrCreate(['rs_code' => 'STP3'], ['rs_nama' => 'RS Step3', 'rs_status' => RsStatusEnum::DEDICATED, 'rs_aktif' => 1]);
    $this->ruanganA = Ruangan::firstOrCreate(['ruangan_code' => 'DHL'], ['ruangan_nama' => 'Ruang Dahlia']);
    $this->ruanganB = Ruangan::firstOrCreate(['ruangan_code' => 'MWR'], ['ruangan_nama' => 'Ruang Mawar']);
    $this->jenis = JenisLinen::firstOrCreate(['jenis_nama' => 'Seprai Single']);
    $this->bahan = JenisBahan::firstOrCreate(['bahan_nama' => 'Katun']);
    $this->supplier = Supplier::firstOrCreate(['supplier_nama' => 'Supplier Test']);
    DB::table('rs_dan_ruangan')->updateOrInsert(['rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruanganA->ruangan_id], []);
    DB::table('rs_dan_ruangan')->updateOrInsert(['rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruanganB->ruangan_id], []);
    DB::table('rs_dan_jenis')->updateOrInsert(['rs_id' => $this->rs->rs_id, 'jenis_id' => $this->jenis->jenis_id], ['parstock' => 10]);
    DB::table('warehouse')->updateOrInsert(['warehouse_id' => 1], ['warehouse_nama' => 'Gudang Utama']);
    $this->admin = User::firstOrCreate(['email' => 'step3@bka.test'], ['name' => 'Admin Step3', 'password' => Hash::make('password'), 'role' => 'admin', 'verified_at' => now(), 'email_verified_at' => now()]);
    DB::table('rs_dan_user')->updateOrInsert(['user_id' => $this->admin->id, 'rs_id' => $this->rs->rs_id], []);
    $this->token = $this->admin->createToken('test-token-'.uniqid())->plainTextToken;
    $this->rfidsA = ['STEP3_A1', 'STEP3_A2'];
    $this->rfidsB = ['STEP3_B1'];
    $this->allRfids = array_merge($this->rfidsA, $this->rfidsB);
    $now = now();
    foreach (['A' => $this->rfidsA, 'B' => $this->rfidsB] as $ruang => $rfids) {
        $ruangan = $ruang === 'A' ? $this->ruanganA : $this->ruanganB;
        foreach ($rfids as $rfid) {
            DetailLinen::updateOrCreate(['detail_rfid' => $rfid], [
                'detail_id_rs' => $this->rs->rs_id, 'detail_id_ruangan' => $ruangan->ruangan_id,
                'detail_id_jenis' => $this->jenis->jenis_id, 'detail_id_bahan' => $this->bahan->bahan_id, 'detail_id_supplier' => $this->supplier->supplier_id,
                'detail_status_cuci' => CuciEnum::CUCI, 'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
                'detail_status_register' => RegisterEnum::REGISTER, 'detail_status_linen' => LinenStatusEnum::GUDANG,
                'detail_tgl_cek' => now()->format('Y-m-d'), 'detail_updated_at' => $now, 'detail_updated_by' => $this->admin->id,
            ]);
            DB::table('config_linen')->updateOrInsert(['detail_rfid' => $rfid, 'rs_id' => $this->rs->rs_id], []);
            DB::table('outstanding')->updateOrInsert(['outstanding_rfid' => $rfid], [
                'outstanding_key' => 'REG-STEP3', 'outstanding_rs_ori' => $this->rs->rs_id, 'outstanding_rs_scan' => $this->rs->rs_id,
                'outstanding_id_ruangan' => $ruangan->ruangan_id, 'outstanding_status_transaksi' => 'KOTOR', 'outstanding_status_proses' => 'GUDANG',
                'outstanding_status_hilang' => 'NORMAL', 'outstanding_created_at' => $now, 'outstanding_updated_at' => $now,
                'outstanding_created_by' => $this->admin->id, 'outstanding_updated_by' => $this->admin->id, 'outstanding_id_warehouse' => 1,
            ]);
            // transaksi history — only insert if not exists today
            if (! DB::table('transaksi')->where('transaksi_rfid', $rfid)->whereDate('transaksi_created_at', today())->exists()) {
                DB::table('transaksi')->insert([
                    'transaksi_key' => 'KTR-STEP3', 'transaksi_rfid' => $rfid, 'transaksi_rs_ori' => $this->rs->rs_id, 'transaksi_rs_scan' => $this->rs->rs_id,
                    'transaksi_beda_rs' => 'TIDAK', 'transaksi_id_ruangan' => $ruangan->ruangan_id, 'transaksi_status' => 'KOTOR',
                    'transaksi_created_at' => $now, 'transaksi_updated_at' => $now, 'transaksi_created_by' => $this->admin->id, 'transaksi_updated_by' => $this->admin->id,
                ]);
            }
        }
    }
});

/**
 * STEP 3 — BERSIH (packing per ruangan, pengiriman bersih, cek report bersih vs kotor & pengiriman)
 */
it('step3 packing per ruangan, pengiriman bersih, cek report bersih vs kotor dan pengiriman', function () {
    $initialCetak1 = DB::table('cetak')->where('cetak_type', 1)->count();
    $initialCetak2 = DB::table('cetak')->where('cetak_type', 2)->count();
    // 1) Packing per ruangan — ruangan A dulu (2 RFID), lalu ruangan B (1 RFID)
    // Sesuai PackingDeliveryController: packing butuh status_transaksi + ruangan_id
    $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => $this->rfidsA, 'rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruanganA->ruangan_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => true]);
    expect(DB::table('outstanding')->whereIn('outstanding_rfid', $this->rfidsA)->where('outstanding_status_proses', 'PACKING')->count())->toBe(2);
    expect(DB::table('outstanding')->whereIn('outstanding_rfid', $this->rfidsB)->where('outstanding_status_proses', 'GUDANG')->count())->toBe(1);
    expect(DB::table('cetak')->where('cetak_type', 1)->count())->toBe($initialCetak1 + 1);
    expect(DB::table('cetak')->where('cetak_type', 1)->value('cetak_code'))->toBe(strtoupper(DB::table('cetak')->where('cetak_type', 1)->value('cetak_code')));

    $this->withToken($this->token)->postJson('/api/packing', [
        'rfid' => $this->rfidsB, 'rs_id' => $this->rs->rs_id, 'ruangan_id' => $this->ruanganB->ruangan_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk();
    expect(DB::table('outstanding')->where('outstanding_status_proses', 'PACKING')->count())->toBe(3);
    expect(DB::table('cetak')->where('cetak_type', 1)->count())->toBe($initialCetak1 + 2);

    // Reprint packing per code harus balikin RFID sesuai ruangan — ambil 2 cetak terbaru (milik run ini)
    $codes = DB::table('cetak')->where('cetak_type', 1)->orderByDesc('cetak_id')->limit(2)->pluck('cetak_code')->values()->all();
    $this->withToken($this->token)->getJson("/api/packing/{$codes[1]}")->assertOk()->assertJsonCount(count($this->rfidsA), 'data');
    $this->withToken($this->token)->getJson("/api/packing/{$codes[0]}")->assertOk()->assertJsonCount(count($this->rfidsB), 'data');

    // 2) Pengiriman bersih — POST /api/delivery kirim semua PACKING jadi BERSIH (hapus outstanding)
    $totalBersihBefore = [];
    $bersihRowsBefore = DB::table('bersih')->whereIn('bersih_rfid', $this->allRfids)->count();
    foreach ($this->allRfids as $rfid) {
        $totalBersihBefore[$rfid] = (int) (DetailLinen::where('detail_rfid', $rfid)->value('detail_total_bersih') ?? 0);
    }
    $this->withToken($this->token)->postJson('/api/delivery', [
        'rs_id' => $this->rs->rs_id, 'status_transaksi' => 'KOTOR',
    ])->assertOk()->assertJson(['status' => true]);

    expect(DB::table('outstanding')->whereIn('outstanding_rfid', $this->allRfids)->count())->toBe(0);
    foreach ($this->allRfids as $rfid) {
        expect(DetailLinen::where('detail_rfid', $rfid)->value('detail_status_linen'))->toBe(LinenStatusEnum::BERSIH);
        expect((int) DetailLinen::where('detail_rfid', $rfid)->value('detail_total_bersih'))->toBe($totalBersihBefore[$rfid] + 1);
    }
    expect(DB::table('cetak')->where('cetak_type', 2)->count())->toBe($initialCetak2 + 1);
    // riwayat bersih = history append — naik +N per delivery run
    expect(DB::table('bersih')->whereIn('bersih_rfid', $this->allRfids)->count())->toBe($bersihRowsBefore + count($this->allRfids));
    expect(json_decode(DB::table('cetak')->where('cetak_type', 2)->orderByDesc('cetak_id')->value('cetak_rfids'), true))->toHaveCount(count($this->allRfids));
    // barcode capital semua
    expect(DB::table('cetak')->where('cetak_type', 2)->orderByDesc('cetak_id')->value('cetak_code'))->toBe(strtoupper(DB::table('cetak')->where('cetak_type', 2)->orderByDesc('cetak_id')->value('cetak_code')));

    // 3) Cek report bersih vs kotor & pengiriman
    // DB-level counts — scoped ke rs & rfids test biar aman run berurutan
    $bersihCount = DetailLinen::whereIn('detail_rfid', $this->allRfids)->where('detail_status_linen', LinenStatusEnum::BERSIH)->count();
    $kotorCount = DetailLinen::whereIn('detail_rfid', $this->allRfids)->where('detail_status_linen', LinenStatusEnum::KOTOR)->count();
    $transaksiKotor = DB::table('transaksi')->whereIn('transaksi_rfid', $this->allRfids)->where('transaksi_status', 'KOTOR')->count();
    expect($bersihCount)->toBe(count($this->allRfids));
    expect($kotorCount)->toBe(0);
    expect($transaksiKotor)->toBe(count($this->allRfids));

    // Web report endpoints (harus 200, data terisi)
    $this->actingAs($this->admin)->get('/report-kotor-vs-bersih/table?rs_id='.$this->rs->rs_id)->assertOk();
    $this->actingAs($this->admin)->get('/report-rekap-bersih/table?rs_id='.$this->rs->rs_id)->assertOk();
    $this->actingAs($this->admin)->get('/report-rekap-kotor/table?rs_id='.$this->rs->rs_id)->assertOk();
    $this->actingAs($this->admin)->get('/report-detail-pengiriman-bersih/table?rs_id='.$this->rs->rs_id)->assertOk();
    $this->actingAs($this->admin)->get('/report-summary-pengiriman-bersih/table?rs_id='.$this->rs->rs_id)->assertOk();
    $this->actingAs($this->admin)->get('/report-detail-kotor/table?rs_id='.$this->rs->rs_id)->assertOk();
    $this->actingAs($this->admin)->get('/bersih/table?tab=riwayat')->assertOk();

    // Totals via API (PackingDeliveryController totals)
    $this->withToken($this->token)->getJson("/api/total/delivery/{$this->rs->rs_id}/KOTOR")->assertOk()->assertJsonPath('data.total', 0); // sudah delivery, tidak ada PACKING sisa
    $this->withToken($this->token)->getJson("/api/total/bersih/{$this->rs->rs_id}/{$this->ruanganA->ruangan_id}/{$this->jenis->jenis_id}/KOTOR")->assertOk()->assertJsonPath('data.view_total', count($this->rfidsA));
    $this->withToken($this->token)->getJson("/api/total/bersih/{$this->rs->rs_id}/{$this->ruanganB->ruangan_id}/{$this->jenis->jenis_id}/KOTOR")->assertOk()->assertJsonPath('data.view_total', count($this->rfidsB));

    // 4) List & reprint delivery
    $list = DB::table('cetak')->where('cetak_type', 2)->orderByDesc('cetak_id')->value('cetak_code');
    $this->withToken($this->token)->getJson("/api/list/delivery/{$this->rs->rs_id}")->assertOk()->assertJsonPath('data.0.cetak_code', $list);
    $this->withToken($this->token)->getJson("/api/delivery/{$list}")->assertOk()->assertJsonCount(count($this->allRfids), 'data');
});
