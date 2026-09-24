<?php

use App\Models\DetailLinen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    expect(DB::connection()->getDatabaseName())->toBe('test_bka');

    $this->admin = User::create([
        'name' => 'Admin Aktifitas',
        'email' => 'admin-activity@bka.test',
        'password' => Hash::make('password'),
        'role' => 'admin',
        'verified_at' => now(),
        'email_verified_at' => now(),
    ]);

    $this->operator = User::create([
        'name' => 'Budi Operator',
        'email' => 'budi-activity@bka.test',
        'password' => Hash::make('password'),
        'role' => 'user',
        'verified_at' => now(),
        'email_verified_at' => now(),
    ]);

    // Satu log dikerjakan Budi (causer_id terisi), satu log tanpa causer
    // (actor sistem) — dipakai untuk memastikan join users tidak membuang baris.
    DB::table('activity_log')->insert([
        [
            'log_name' => 'REGISTER',
            'description' => 'Detail linen LOG-RFID-1 berhasil diregister.',
            'event' => 'created',
            'subject_type' => DetailLinen::class,
            'subject_id' => 'LOG-RFID-1',
            'causer_type' => User::class,
            'causer_id' => $this->operator->id,
            'properties' => json_encode(['rfid' => 'LOG-RFID-1']),
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'log_name' => 'KOTOR',
            'description' => 'Detail linen LOG-RFID-2 berhasil diperbarui.',
            'event' => 'updated',
            'subject_type' => DetailLinen::class,
            'subject_id' => 'LOG-RFID-2',
            'causer_type' => null,
            'causer_id' => null,
            'properties' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
});

it('menampilkan kolom: nama, ID (RFID), model, user, tanggal', function () {
    $html = $this->actingAs($this->admin)->get('/activity-log/table')->assertOk()->getContent();

    foreach (['log_name', 'subject_id', 'subject_model_name', 'causer_name', 'created_at'] as $column) {
        expect($html)->toContain("doSort('{$column}')");
    }

    // description & causer_id bukan kolom tabel (description masih ada
    // sebagai input filter, jadi dicek lewat doSort). Event tidak lagi jadi
    // kolom desktop — hanya filter + badge di kartu mobile.
    foreach (['description', 'causer_id', 'event'] as $column) {
        expect($html)->not->toContain("doSort('{$column}')");
    }

    expect($html)->toContain('ID (RFID)')->toContain('Tanggal');
});

it('menampilkan nama user hasil join users, bukan causer id', function () {
    $html = $this->actingAs($this->admin)->get('/activity-log/table')->assertOk()->getContent();

    expect($html)->toContain('Budi Operator')
        // Model = basename subject_type, FQCN lengkapnya di tooltip
        ->and($html)->toContain('>DetailLinen<')
        ->and($html)->toContain('title="'.DetailLinen::class.'"')
        // baris tanpa causer tetap tampil (leftJoin, bukan inner join)
        ->and($html)->toContain('LOG-RFID-2');
});

it('memfilter berdasarkan nama user lewat join users', function () {
    $html = $this->actingAs($this->admin)
        ->get('/activity-log/table?filters[user_name][$contains]=budi')
        ->assertOk()
        ->getContent();

    expect($html)->toContain('LOG-RFID-1')
        ->and($html)->not->toContain('LOG-RFID-2');
});

it('memfilter RFID dan model (subject_type)', function () {
    $rfid = $this->actingAs($this->admin)
        ->get('/activity-log/table?filters[subject_id][$contains]=rfid-2')
        ->assertOk()
        ->getContent();

    expect($rfid)->toContain('LOG-RFID-2')->and($rfid)->not->toContain('LOG-RFID-1');

    $model = $this->actingAs($this->admin)
        ->get('/activity-log/table?filters[subject_type][$eq]='.urlencode(DetailLinen::class))
        ->assertOk()
        ->getContent();

    expect($model)->toContain('LOG-RFID-1')->and($model)->toContain('LOG-RFID-2');
});

it('memfilter tanggal dari input date (Y-m-d) dengan whereDate', function () {
    $html = $this->actingAs($this->admin)
        ->get('/activity-log/table?filters[created_at][$eq]='.now()->format('Y-m-d'))
        ->assertOk()
        ->getContent();

    expect($html)->toContain('LOG-RFID-1')->and($html)->toContain('LOG-RFID-2');
});

it('aman disortir dan dipaginasi walau ada join users', function () {
    foreach (['log_name', 'event', 'subject_id', 'subject_model_name', 'causer_name', 'created_at'] as $column) {
        $this->actingAs($this->admin)
            ->get("/activity-log/table?sort[0]={$column}:desc&per_page=1")
            ->assertOk();
    }
});

it('mengirim causer_name di response JSON tabel', function () {
    $json = $this->actingAs($this->admin)
        ->getJson('/activity-log/table?per_page=10')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->json();

    // views() membungkus paginator di key `data` + `fields`, lalu Notes::data
    // membungkusnya sekali lagi → barisnya ada di data.data.data.
    $rows = collect($json['data']['data']['data'])->keyBy('subject_id');

    expect($rows)->toHaveCount(2)
        ->and($rows['LOG-RFID-1']['causer_name'])->toBe('Budi Operator')
        ->and($rows['LOG-RFID-2']['causer_name'])->toBeNull();
});
