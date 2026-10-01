<?php

use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\LaundryDashboardController;
use App\Http\Controllers\Dashboard\RsDashboardController;
use App\Http\Controllers\DashboardRouterController;
use App\Http\Controllers\OpnameController;
use App\Http\Controllers\WebsiteSettingController;
use App\Models\Notification;
use App\Services\CentrifugoService;
use Buki\AutoRoute\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::middleware('auth')->post('/centrifugo/token', function (Request $request) {
    if (! config('langkahkecil.notification_enable')) {
        return response()->json(['token' => 'disabled']);
    }

    $centrifugo = app(CentrifugoService::class);
    $user = Auth::user();

    if ($request->input('channel')) {
        return response()->json([
            'token' => $centrifugo->generateSubscriptionToken((string) $user->id, $request->input('channel')),
        ]);
    }

    return response()->json([
        'token' => $centrifugo->generateConnectionToken((string) $user->id),
    ]);
});

Route::middleware(['auth', 'verified', 'access'])->group(function () {

    Route::get('dashboard', DashboardRouterController::class)->name('dashboard');
    Route::get('dashboard/admin', AdminDashboardController::class)->name('dashboard.admin');
    Route::get('dashboard/rs', RsDashboardController::class)->name('dashboard.rs');
    Route::get('dashboard/laundry', LaundryDashboardController::class)->name('dashboard.laundry');

    Route::auto('/user', 'UsersController', ['name' => 'user']);
    Route::auto('/mobile-menu', 'MobileMenuController', ['name' => 'mobile-menu']);
    Route::auto('/rs', 'RsController', ['name' => 'rs']);
    Route::auto('/group-rs', 'GroupRsController', ['name' => 'group-rs']);
    Route::auto('/transaksi', 'TransaksiController', ['name' => 'transaksi']);
    Route::auto('/bersih', 'BersihController', ['name' => 'bersih']);
    Route::auto('/warehouse', 'WarehouseController', ['name' => 'warehouse', 'only' => ['getTable']]);

    // Report + proteksi RS per user (pivot rs_dan_user, middleware rs.access).
    Route::middleware(['rs.access'])->group(function () {
        Route::auto('/report-rekap-opname', 'ReportRekapOpnameController', ['name' => 'report-rekap-opname', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-opname-detail', 'ReportOpnameDetailController', ['name' => 'report-opname-detail', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-opname-summary', 'ReportOpnameSummaryController', ['name' => 'report-opname-summary', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-opname-hilang', 'ReportOpnameHilangController', ['name' => 'report-opname-hilang', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-opname-hilang-warehouse', 'ReportOpnameHilangWarehouseController', ['name' => 'report-opname-hilang-warehouse', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-opname-mutasi', 'ReportOpnameMutasiController', ['name' => 'report-opname-mutasi', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-data-linen', 'ReportDataLinenController', ['name' => 'report-data-linen', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-rekap-kotor', 'ReportRekapKotorController', ['name' => 'report-rekap-kotor', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-rekap-bersih', 'ReportRekapBersihController', ['name' => 'report-rekap-bersih', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-rekap-retur', 'ReportRekapReturController', ['name' => 'report-rekap-retur', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-rekap-rewash', 'ReportRekapRewashController', ['name' => 'report-rekap-rewash', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-detail-kotor', 'ReportDetailKotorController', ['name' => 'report-detail-kotor', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-detail-retur', 'ReportDetailReturController', ['name' => 'report-detail-retur', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-detail-rewash', 'ReportDetailRewashController', ['name' => 'report-detail-rewash', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-detail-pengiriman-bersih', 'ReportDetailPengirimanBersihController', ['name' => 'report-detail-pengiriman-bersih', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-detail-pengiriman-retur', 'ReportDetailPengirimanReturController', ['name' => 'report-detail-pengiriman-retur', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-detail-pengiriman-rewash', 'ReportDetailPengirimanRewashController', ['name' => 'report-detail-pengiriman-rewash', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-detail-pengiriman-linen-baru', 'ReportDetailPengirimanLinenBaruController', ['name' => 'report-detail-pengiriman-linen-baru', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-summary-pengiriman-bersih', 'ReportSummaryPengirimanBersihController', ['name' => 'report-summary-pengiriman-bersih', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-summary-pengiriman-retur', 'ReportSummaryPengirimanReturController', ['name' => 'report-summary-pengiriman-retur', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-summary-pengiriman-rewash', 'ReportSummaryPengirimanRewashController', ['name' => 'report-summary-pengiriman-rewash', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-summary-pengiriman-linen-baru', 'ReportSummaryPengirimanLinenBaruController', ['name' => 'report-summary-pengiriman-linen-baru', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-register-linen', 'ReportRegisterLinenController', ['name' => 'report-register-linen', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-hilang-linen', 'ReportHilangLinenController', ['name' => 'report-hilang-linen', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-penggantian-linen', 'ReportPenggantianLinenController', ['name' => 'report-penggantian-linen', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-kotor-vs-bersih', 'ReportKotorVsBersihController', ['name' => 'report-kotor-vs-bersih', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-in-vs-out', 'ReportInVsOutController', ['name' => 'report-in-vs-out', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-invoice', 'ReportInvoiceController', ['name' => 'report-invoice', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-pending-linen', 'ReportPendingLinenController', ['name' => 'report-pending-linen', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-detail-pending-linen', 'ReportDetailPendingLinenController', ['name' => 'report-detail-pending-linen', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-pending-jenis', 'ReportPendingJenisController', ['name' => 'report-pending-jenis', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-pelunasan-pending', 'ReportPelunasanPendingController', ['name' => 'report-pelunasan-pending', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
        Route::auto('/report-summary-pelunasan', 'ReportSummaryPelunasanController', ['name' => 'report-summary-pelunasan', 'only' => ['getTable', 'getPrint', 'getExportExcel']]);
    });
    Route::auto('/ruangan', 'RuanganController', ['name' => 'ruangan']);
    Route::auto('/kategori', 'KategoriController', ['name' => 'kategori']);
    Route::auto('/jenis-bahan', 'JenisBahanController', ['name' => 'jenis-bahan']);
    Route::auto('/supplier', 'SupplierController', ['name' => 'supplier']);
    Route::auto('/jenis-linen', 'JenisLinenController', ['name' => 'jenis-linen']);
    Route::auto('/detail-linen', 'DetailLinenController', ['name' => 'detail-linen']);
    Route::auto('/config-linen', 'ConfigLinenController', ['name' => 'config-linen']);
    Route::auto('/opname', 'OpnameController', ['name' => 'opname']);
    Route::get('/opname/capture/{code}', [OpnameController::class, 'getCapture'])->name('opname.capture');
    Route::get('/opname/sync/{code}', [OpnameController::class, 'getSync'])->name('opname.sync');
    Route::post('/opname/sync/{code}', [OpnameController::class, 'postSync'])->name('opname.sync.post');
    Route::get('/opname/detail/{code}', [OpnameController::class, 'getDetail'])->name('opname.detail');

    Route::get('/native-bridge-test', function () {
        return view('pages.settings.native-bridge-test');
    })->name('native-bridge-test');

    Route::get('/settings/website', [WebsiteSettingController::class, 'index'])->name('settings.website');
    Route::post('/settings/website', [WebsiteSettingController::class, 'save'])->name('settings.website.save');

    Route::prefix('notifications-web')->group(function () {
        Route::get('/', function (Request $request) {
            $notifications = Notification::where('user_id', Auth::id())
                ->orderByDesc('created_at')
                ->limit($request->input('limit', 50))
                ->get();

            $unreadCount = Notification::where('user_id', Auth::id())
                ->where('read', false)
                ->count();

            return response()->json([
                'notifications' => $notifications->map(fn ($n) => [
                    'id' => $n->id,
                    'icon' => $n->icon,
                    'iconColor' => $n->icon_color,
                    'title' => $n->title,
                    'body' => $n->body,
                    'url' => $n->url,
                    'type' => $n->type,
                    'read' => $n->read,
                    'time' => $n->created_at?->diffForHumans() ?? '',
                    'created_at' => $n->created_at->toIso8601String(),
                ]),
                'unread_count' => $unreadCount,
            ]);
        });

        Route::put('/{id}/read', function (int $id) {
            $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
            $notification->update(['read' => true]);

            return response()->json(['message' => 'Marked as read']);
        });

        Route::put('/read-all', function () {
            Notification::where('user_id', Auth::id())
                ->where('read', false)
                ->update(['read' => true]);

            return response()->json(['message' => 'All marked as read']);
        });
    });
});

require __DIR__.'/settings.php';
