<?php

use App\Http\Controllers\Api\ConfigurationController;
use App\Http\Controllers\Api\DetailApiController;
use App\Http\Controllers\Api\DownloadApiController;
use App\Http\Controllers\Api\GroupingController;
use App\Http\Controllers\Api\OpnameApiController;
use App\Http\Controllers\Api\PackingDeliveryController;
use App\Http\Controllers\Api\RsApiController;
use App\Http\Controllers\Api\TransaksiApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterLinenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Plugins\Notes;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// Legacy andalan: klien mendaftarkan langganan push.
// Dibatas throttle + validasi ketat agar tidak jadi tempat spam DB anonim:
// max 4KB, wajib JSON valid dengan key `endpoint` (standar Web Push).
Route::post('push-subscribe', function (Request $request) {
    $raw = $request->getContent();

    if (strlen($raw) === 0 || strlen($raw) > 4096) {
        return Notes::validation('Payload tidak valid.', ['data' => ['Payload melebihi batas 4KB.']]);
    }

    $json = json_decode($raw, true);
    if (! is_array($json) || empty($json['endpoint']) || ! is_string($json['endpoint'])) {
        return Notes::validation('Payload tidak valid.', ['data' => ['Field endpoint wajib diisi.']]);
    }

    DB::table('push_subscriptions')->insert([
        'data' => $raw,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return Notes::data();
})->middleware('throttle:10,1');

Route::middleware(['auth:sanctum', 'rs.access'])->group(function () {

    // =====================================================================
    // 1. AKUN — sesi, profil, dan CRUD user (admin tooling bertoken).
    // =====================================================================
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'updateProfile']);

    // ponytail: CRUD user via API SENGAJA tidak didaftarkan — tidak dipakai
    // desktop/mobile. Mengeksposnya ( Route::auto maupun eksplisit ) berarti
    // token valid bisa list/create/update/delete user. Kelola user hanya via
    // web /user/* (session + verified + access + gate role admin/developer
    // di config/permision.php). Jangan tambahkan route users di sini.

    // =====================================================================
    // 2. MASTER DATA — sekali unduh saat sync mobile/desktop.
    // =====================================================================
    // Master dropdown — logic di Api\ConfigurationController (mentah, tanpa envelope).
    Route::get('configuration', ConfigurationController::class)->name('api.configuration');

    // Sync RFID per RS — logic di Api\DownloadApiController (stream JSON).
    Route::get('download/{rsid}', DownloadApiController::class)
        ->whereNumber('rsid')
        ->name('api.download');

    // Master RS — logic di Api\RsApiController (kontrak RsAllDAO/RsSingleDAO).
    Route::get('rs', [RsApiController::class, 'index'])->name('api.rs');
    Route::get('rs_lite', [RsApiController::class, 'lite'])->name('api.rs_lite');
    Route::get('rs/{rsid}', [RsApiController::class, 'detail'])->name('api.rs_detail');

    Route::post('detail/rfid', [DetailApiController::class, 'rfid'])->name('api.detail.rfid');

    // =====================================================================
    // 3. REGISTER — registrasi linen massal + dropdown + grouping QC.
    // =====================================================================
    Route::post('/register', [RegisterLinenController::class, 'store'])->name('register.store');

    // Opsi dropdown form register. Query opsional ?rs_id=75 untuk menyaring jenis & ruangan.
    Route::get('/register/config', [RegisterLinenController::class, 'config'])->name('register.config');

    // Grouping QC per RFID - logic di Api\GroupingController (kontrak GroupingDAO desktop).
    Route::get('grouping/{rfid}', [GroupingController::class, 'show'])->name('api.grouping');

    // =====================================================================
    // 4. KOTOR — linen kotor/retur/rewash masuk laundry (1 endpoint per tipe).
    // Throttle bulk sebagai pengaman DoS.
    // =====================================================================
    Route::post('kotor', [TransaksiApiController::class, 'kotor'])->name('transaksi.kotor');
    Route::post('retur', [TransaksiApiController::class, 'retur'])->name('transaksi.retur');
    Route::post('rewash', [TransaksiApiController::class, 'rewash'])->name('transaksi.rewash');

    // =====================================================================
    // 5. BERSIH — packing + delivery + reprint + counter (desktop andalan).
    // =====================================================================
    Route::post('packing', [PackingDeliveryController::class, 'packing'])->name('api.packing');
    Route::post('delivery', [PackingDeliveryController::class, 'delivery'])->name('api.delivery');
    Route::get('packing/{code}', [PackingDeliveryController::class, 'printPacking'])->name('api.printPacking');
    Route::get('delivery/{code}', [PackingDeliveryController::class, 'printDelivery'])->name('api.printDelivery');
    Route::get('list/packing/{rsid}', [PackingDeliveryController::class, 'listPacking'])->name('api.listPacking');
    Route::get('list/delivery/{rsid}', [PackingDeliveryController::class, 'listDelivery'])->name('api.listDelivery');
    Route::get('total/delivery/{rsid}/{status}', [PackingDeliveryController::class, 'totalDelivery'])->name('api.totalDelivery');
    Route::get('total/outstanding/{rsid}/{ruangan}/{jenis}/{transaksi}', [PackingDeliveryController::class, 'totalOutstanding'])->name('api.totalOutstanding');
    Route::get('total/bersih/{rsid}/{ruangan}/{jenis}/{transaksi}', [PackingDeliveryController::class, 'totalBersih'])->name('api.totalBersih');

    // =====================================================================
    // 6. OPNAME — sync stock opname desktop (capture sudah di web).
    // =====================================================================
    Route::get('opname', [OpnameApiController::class, 'index'])->name('api.opname.index');
    // Alias legacy andalan: POST /opname (opname_id, code, rfid[]) = POST /opname/sync.
    Route::post('opname', [OpnameApiController::class, 'sync'])->name('api.opname.store');
    Route::get('opname/{id}', [OpnameApiController::class, 'show'])->name('api.opname.show');
    Route::get('opname/{id}/detail', [OpnameApiController::class, 'detail'])->name('api.opname.detail');
    Route::post('opname/sync', [OpnameApiController::class, 'sync'])->name('api.opname.sync');
    Route::post('opname/capture/{id}', [OpnameApiController::class, 'capture'])->name('api.opname.capture');
});
