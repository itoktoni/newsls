<?php

use App\Enums\CuciEnum;
use App\Enums\RsStatusEnum;
use App\Models\ConfigLinen;
use App\Models\DetailLinen;
use App\Models\Rs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::create([
        'name' => 'Admin Lini',
        'email' => 'admin@bka.test',
        'password' => 'password',
        'role' => 'admin',
    ]);
});

function admin(): User
{
    return User::create([
        'name' => 'Admin Lini',
        'email' => 'admin-'.uniqid().'@bka.test',
        'password' => 'password',
        'role' => 'admin',
    ]);
}

it('mencatat aktivitas saat register linen baru', function () {
    $admin = admin();
    $rs = Rs::create([
        'rs_nama' => 'RS Test',
        'rs_status' => 'ACTIVE',
        'rs_code' => 'TEST-RS-0001',
    ]);
    $rfid = 'LOG-TEST-0001';

    $this->actingAs($admin)
        ->from('detail-linen/create')
        ->post('detail-linen/create', [
            'detail_rfid' => $rfid,
            'detail_id_rs' => $rs->rs_id,
            'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'rs_ids' => [$rs->rs_id],
        ]);

    $linen = DetailLinen::where('detail_rfid', $rfid)->firstOrFail();

    $activity = Activity::forSubject($linen)->firstOrFail();

    expect($activity->event)->toBe('created')
        ->and($activity->log_name)->toBe('linen')
        ->and($activity->description)->toBe("Detail linen {$rfid} berhasil diregister.")
        ->and($activity->causer->id)->toBe($admin->id)
        ->and($activity->subject->getKey())->toBe($rfid)
        ->and($activity->subject_type)->toBe(DetailLinen::class)
        ->and($activity->attribute_changes['attributes'])->toMatchArray([
            'detail_rfid' => $rfid,
            'detail_id_rs' => $rs->rs_id,
            'detail_status_linen' => 'REGISTER',
            'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'detail_created_by' => $admin->id,
        ]);
});

it('mencatat aktivitas saat linen diubah (hanya kolom yang berubah yang terekam)', function () {
    [$linen] = makeLinen('ACT-0002', RsStatusEnum::DEDICATED);
    $admin = admin();

    $this->actingAs($admin)
        ->from("detail-linen/update/{$linen->detail_rfid}")
        ->post("detail-linen/update/{$linen->detail_rfid}", [
            'detail_rfid' => $linen->detail_rfid,
            'detail_id_rs' => $linen->detail_id_rs,
            'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'rs_ids' => [$linen->detail_id_rs],
            'detail_status_cuci' => CuciEnum::CUCI,
        ])
        ->assertRedirect();

    $updatedActivity = Activity::forSubject($linen)
        ->where('event', 'updated')
        ->latest('id')
        ->firstOrFail();

    expect($updatedActivity->event)->toBe('updated')
        ->and($updatedActivity->log_name)->toBe('linen')
        ->and($updatedActivity->description)->toBe("Detail linen {$linen->detail_rfid} berhasil diperbarui.")
        ->and($updatedActivity->causer->id)->toBe($admin->id)
        ->and($updatedActivity->attribute_changes['attributes'])->toMatchArray([
            'detail_status_cuci' => CuciEnum::CUCI,
            'detail_updated_by' => $admin->id,
        ]);
});

it('tidak mencatat log jika linen disubmit ulang tanpa perubahan', function () {
    [$linen] = makeLinen('ACT-0003', RsStatusEnum::DEDICATED);
    $admin = admin();

    $before = Activity::forSubject($linen)->where('event', 'updated')->count();

    $this->actingAs($admin)
        ->from("detail-linen/update/{$linen->detail_rfid}")
        ->post("detail-linen/update/{$linen->detail_rfid}", [
            'detail_rfid' => $linen->detail_rfid,
            'detail_id_rs' => $linen->detail_id_rs,
            'detail_status_kepemilikan' => RsStatusEnum::DEDICATED,
            'rs_ids' => [$linen->detail_id_rs],
            'detail_status_cuci' => $linen->detail_status_cuci,
            'detail_deskripsi' => $linen->detail_deskripsi,
        ])
        ->assertRedirect();

    $after = Activity::forSubject($linen)->where('event', 'updated')->count();

    expect($after)->toBe($before);
});

it('mencatat aktivitas saat linen dihapus', function () {
    [$linen, $rs] = makeLinen('ACT-0004', RsStatusEnum::FREE);
    $admin = admin();

    $this->actingAs($admin)
        ->from('detail-linen/table')
        ->get("detail-linen/delete/{$linen->detail_rfid}");

    expect(DetailLinen::where('detail_rfid', $linen->detail_rfid)->exists())->toBeFalse();

    $deletedActivity = Activity::where('event', 'deleted')
        ->where('subject_type', DetailLinen::class)
        ->where('subject_id', $linen->detail_rfid)
        ->firstOrFail();

    $expectedOld = [
        'detail_rfid' => $linen->detail_rfid,
        'detail_id_rs' => $rs->rs_id,
        'detail_status_kepemilikan' => RsStatusEnum::FREE,
        'detail_status_linen' => 'REGISTER',
        'detail_created_by' => $rs->rs_id,
        'detail_updated_by' => null,
    ];

    expect($deletedActivity->log_name)->toBe('linen')
        ->and($deletedActivity->description)->toBe("Detail linen {$linen->detail_rfid} berhasil dihapus.")
        ->and($deletedActivity->causer->id)->toBe($admin->id)
        ->and($deletedActivity->subject_type)->toBe(DetailLinen::class)
        ->and($deletedActivity->subject_id)->toBe($linen->detail_rfid)
        ->and($deletedActivity->attribute_changes['old'])->toMatchArray($expectedOld);
});

function makeLinen($rfid, string $kepemilikan, ?int $rsId = null): array
{
    $rs = Rs::first();
    if ($rs === null) {
        $rs = Rs::create([
            'rs_nama' => 'RS Test',
            'rs_status' => 'ACTIVE',
            'rs_code' => 'TEST-RS',
        ]);
    }

    if ($rsId === null) {
        $rsId = $rs->rs_id;
    }

    $linen = DetailLinen::create([
        'detail_rfid' => $rfid,
        'detail_id_rs' => $rsId,
        'detail_status_kepemilikan' => $kepemilikan,
        'detail_status_linen' => 'REGISTER',
        'detail_created_by' => $rsId,
    ]);

    ConfigLinen::syncRs($rfid, [$rsId]);

    return [$linen, $rs];
}
