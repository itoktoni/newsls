<?php

use App\Enums\CuciEnum;
use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Enums\TransactionType;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterLinenController;
use App\Http\Controllers\UsersController;
use App\Models\DetailLinen;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'rs.access'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'updateProfile']);

    Route::post('/register', [RegisterLinenController::class, 'store'])->name('register.store');

    // Opsi dropdown form register. Query opsional ?rs_id=75 untuk menyaring jenis & ruangan.
    Route::get('/register/config', [RegisterLinenController::class, 'config'])->name('register.config');

    Route::auto('/users', UsersController::class, ['name' => 'users']);

    Route::post('/transaksi/{type}', [\App\Http\Controllers\Api\TransaksiApiController::class, 'transaction'])
        ->whereIn('type', ['kotor', 'retur', 'rewash', 'KOTOR', 'RETUR', 'REWASH'])
        ->name('transaksi.api');

    // ponytail: alias legacy andalan — Route::post('kotor') etc. dipakai device lama
    Route::post('kotor', [\App\Http\Controllers\Api\TransaksiApiController::class, 'kotor'])->name('transaksi.kotor');
    Route::post('retur', [\App\Http\Controllers\Api\TransaksiApiController::class, 'retur'])->name('transaksi.retur');
    Route::post('rewash', [\App\Http\Controllers\Api\TransaksiApiController::class, 'rewash'])->name('transaksi.rewash');

    // ponytail: andalan download/configuration/rs/rs_lite — convert ke model baru
    Route::get('download/{rsid}', function ($rsid, Request $request) {
        $data = DetailLinen::with(['hasRuangan', 'hasJenis', 'hasRs'])
            ->where('detail_id_rs', $rsid)->get();

        if ($data->isEmpty()) {
            return \Plugins\Notes::failed(404, 'Data Tidak Ditemukan !');
        }

        return \Plugins\Notes::data($data);
    })->name('api.download');

    Route::get('configuration', function () {
        return \Plugins\Notes::data([
            'supplier' => Supplier::select('supplier_id', 'supplier_nama')->get(),
            'jenis_bahan' => JenisBahan::select('bahan_id', 'bahan_nama')->get(),
            'jenis_linen' => JenisLinen::select('jenis_id', 'jenis_nama')->get(),
            'status_cuci' => CuciEnum::getOptions(),
            'status_register' => RegisterEnum::getOptions(),
            'status_transaksi' => TransactionType::getOptions(),
            'status_linen' => LinenStatusEnum::getOptions(),
            'kepemilikan' => RsStatusEnum::getOptions(),
            'allowed_rs_ids' => \App\Models\User::allowedRsIds(),
        ]);
    })->name('api.configuration');

    Route::get('rs', function (Request $request) {
        $type = $request->query('type');

        // ponytail: desktop hanya boleh lihat RS sesuai pivot rs_dan_user
        // (user A = RS A+B → dropdown desktop cuma RS A+B). Kosong = semua.
        $query = \App\Models\User::scopeRs(Rs::query(), 'rs.rs_id');

        if ($type === 'free') {
            $query->where('rs_status', RsStatusEnum::FREE);
        } elseif ($type === 'dedicated') {
            $query->where('rs_status', RsStatusEnum::DEDICATED);
        } elseif ($type === 'register') {
            // register butuh semua RS (FREE+Dedicated) karena desktop filter di client by rs_status
            // no filter
        }

        $rs = $query->with(['hasRuangan', 'hasJenis'])->get();
        $allowed = \App\Models\User::allowedRsIds();

        // Desktop RsAllDAO expects format khusus (status_id/status_nama, ruangan dengan rs_id, jenis dengan rs_id)
        $toDesktopStatus = function ($enumClass) {
            $opts = $enumClass::getOptions(); // [value => label]
            $out = [];
            foreach ($opts as $val => $label) {
                $out[] = ['status_id' => $val, 'status_nama' => $label];
            }
            return $out;
        };

        // bahan & supplier
        $bahan = JenisBahan::select('bahan_id', 'bahan_nama')->get();
        $supplier = Supplier::select('supplier_id', 'supplier_nama')->get();
        $jenis = JenisLinen::select('jenis_id', 'jenis_nama')->get();

        // jenis_rs dengan jenis_nama + rs_id (join) — saring ke RS milik user.
        $jenisRs = \Illuminate\Support\Facades\DB::table('rs_dan_jenis')
            ->join('jenis_linen', 'jenis_linen.jenis_id', '=', 'rs_dan_jenis.jenis_id')
            ->select('rs_dan_jenis.rs_id', 'rs_dan_jenis.jenis_id', 'jenis_linen.jenis_nama')
            ->when($allowed !== null, fn ($q) => $q->whereIn('rs_dan_jenis.rs_id', $allowed))
            ->get();

        // ruangan dengan rs_id (join pivot) — desktop filter by rs_id
        $ruangan = \Illuminate\Support\Facades\DB::table('ruangan')
            ->join('rs_dan_ruangan', 'ruangan.ruangan_id', '=', 'rs_dan_ruangan.ruangan_id')
            ->select('ruangan.ruangan_id', 'ruangan.ruangan_nama', 'rs_dan_ruangan.rs_id')
            ->when($allowed !== null, fn ($q) => $q->whereIn('rs_dan_ruangan.rs_id', $allowed))
            ->get();

        // Fallback jika pivot kosong (RS belum mapping) → tetap kembalikan semua ruangan tanpa rs_id
        if ($ruangan->isEmpty()) {
            $ruangan = Ruangan::select('ruangan_id', 'ruangan_nama')->get()->map(function ($r) {
                $r->rs_id = null; return $r;
            });
        }
        if ($jenisRs->isEmpty()) {
            $jenisRs = \Illuminate\Support\Facades\DB::table('rs_dan_jenis')->select('rs_id', 'jenis_id')->get()->map(function ($r) {
                $r->jenis_nama = null; return $r;
            });
        }

        // Status dropdown — desktop expects status_id/status_nama
        $statusCuci = $toDesktopStatus(CuciEnum::class);
        $statusRegister = $toDesktopStatus(RegisterEnum::class);
        $statusTransaksi = $toDesktopStatus(TransactionType::class);
        $statusProses = $toDesktopStatus(TransactionType::class); // alias sama, desktop pakai keduanya
        $statusLinen = $toDesktopStatus(LinenStatusEnum::class);

        // Kembalikan envelope Notes agar desktop deserialize ke RsAllDAO.Rootobject (status/code/name/message/data + extra)
        // ponytail: ruangan_rs ikut disaring ke RS milik user.
        $ruanganRs = \Illuminate\Support\Facades\DB::table('rs_dan_ruangan')->select('rs_id', 'ruangan_id')
            ->when($allowed !== null, fn ($q) => $q->whereIn('rs_id', $allowed))
            ->get();

        return \Plugins\Notes::data($rs, [
            'ruangan' => $ruangan,
            'jenis' => $jenis,
            'jenis_rs' => $jenisRs,
            'ruangan_rs' => $ruanganRs,
            'jenis_linen' => $jenis,
            'bahan' => $bahan,
            'supplier' => $supplier,
            'status_cuci' => $statusCuci,
            'status_register' => $statusRegister,
            'status_transaksi' => $statusTransaksi,
            'status_proses' => $statusProses,
            'status_linen' => $statusLinen,
        ]);
    })->name('api.rs');

    Route::get('rs_lite', function () {
        $query = \App\Models\User::scopeRs(Rs::query(), 'rs.rs_id');
        return \Plugins\Notes::data($query->select('rs_id', 'rs_nama')->get());
    })->name('api.rs_lite');

    Route::get('rs/{rsid}', function ($rsid) {
        $rs = Rs::with(['hasRuangan', 'hasJenis'])->findOrFail($rsid);
        return \Plugins\Notes::single($rs);
    })->name('api.rs_detail');

    // Packing & Delivery — sesuai desktop andalan (BersihController)
    Route::post('packing', [\App\Http\Controllers\Api\PackingDeliveryController::class, 'packing'])->name('api.packing');
    Route::post('delivery', [\App\Http\Controllers\Api\PackingDeliveryController::class, 'delivery'])->name('api.delivery');
    Route::get('packing/{code}', [\App\Http\Controllers\Api\PackingDeliveryController::class, 'printPacking'])->name('api.printPacking');
    Route::get('delivery/{code}', [\App\Http\Controllers\Api\PackingDeliveryController::class, 'printDelivery'])->name('api.printDelivery');
    Route::get('list/packing/{rsid}', [\App\Http\Controllers\Api\PackingDeliveryController::class, 'listPacking'])->name('api.listPacking');
    Route::get('list/delivery/{rsid}', [\App\Http\Controllers\Api\PackingDeliveryController::class, 'listDelivery'])->name('api.listDelivery');
    Route::get('total/delivery/{rsid}/{status}', [\App\Http\Controllers\Api\PackingDeliveryController::class, 'totalDelivery'])->name('api.totalDelivery');
    Route::get('total/outstanding/{rsid}/{ruangan}/{jenis}/{transaksi}', [\App\Http\Controllers\Api\PackingDeliveryController::class, 'totalOutstanding'])->name('api.totalOutstanding');
    Route::get('total/bersih/{rsid}/{ruangan}/{jenis}/{transaksi}', [\App\Http\Controllers\Api\PackingDeliveryController::class, 'totalBersih'])->name('api.totalBersih');

    // Opname sync — desktop andalan (capture sudah di web, sync via RFID scan)
    Route::get('opname', [\App\Http\Controllers\Api\OpnameApiController::class, 'index'])->name('api.opname.index');
    Route::get('opname/{id}/detail', [\App\Http\Controllers\Api\OpnameApiController::class, 'detail'])->name('api.opname.detail');
    Route::post('opname/sync', [\App\Http\Controllers\Api\OpnameApiController::class, 'sync'])->name('api.opname.sync');
    Route::post('opname/capture/{id}', [\App\Http\Controllers\Api\OpnameApiController::class, 'capture'])->name('api.opname.capture');

    Route::get('grouping/{rfid}', function ($rfid) {
        try {
            $rfid = trim((string) $rfid);
            if ($rfid === '') {
                return \Plugins\Notes::error(null, 'RFID tidak boleh kosong');
            }

            $flag = 'Normal';
            $date = now()->format('Y-m-d H:i:s');
            $userId = auth()->id();
            $userName = auth()->user()->name ?? (string) $userId;

            \Illuminate\Support\Facades\DB::beginTransaction();

            // BKA: DetailLinen primary = detail_rfid, bukan id
            $detail = DetailLinen::with(['hasRs', 'hasRuangan', 'hasJenis', 'hasBahan', 'hasSupplier'])
                ->where('detail_rfid', $rfid)
                ->first();

            if (! $detail) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("RFID $rfid tidak ditemukan");
            }

            // Ambil view-like data dari relasi (BKA tidak punya view_detail_linen, pakai relasi)
            $viewLinenId = $detail->detail_id_jenis;
            $viewLinenNama = $detail->hasJenis?->jenis_nama;
            $viewRsId = $detail->detail_id_rs;
            $viewRsNama = $detail->hasRs?->rs_nama;
            $viewRuanganId = $detail->detail_id_ruangan;
            $viewRuanganNama = $detail->hasRuangan?->ruangan_nama;
            $viewCreatedName = $userName;

            // History log sederhana pakai activity log (jika ada) + fallback
            try {
                activity('grouping')->causedBy(auth()->user())->withProperties(['rfid' => $rfid])->log("Grouping QC RFID $rfid");
            } catch (\Throwable $e) {}

            $codeKotor = env('CODE_KOTOR', 'KTR');
            $codeRegister = env('CODE_REGISTER', 'REG');
            $codeReject = env('CODE_REJECT', 'RJK');
            $codeRewash = env('CODE_REWASH', 'WSH');

            $statusLinen = $detail->detail_status_linen;
            if ($statusLinen === LinenStatusEnum::REGISTER || $statusLinen === 'REGISTER') {
                $code = $codeRegister;
            } elseif ($statusLinen === TransactionType::REJECT || $statusLinen === 'REJECT' || $statusLinen === TransactionType::RETUR) {
                $code = $codeReject;
            } elseif ($statusLinen === TransactionType::REWASH) {
                $code = $codeRewash;
            } else {
                $code = $codeKotor;
            }

            // Auto number sederhana: CODE + ymd + 4 digit random (BKA tidak punya Query::autoNumber legacy)
            $autoNumber = $code . date('ymd') . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            // Pastikan unik (retry jika duplikat)
            $try = 0;
            while (\App\Models\Transaksi::where('transaksi_key', $autoNumber)->exists() && $try < 5) {
                $autoNumber = $code . date('ymd') . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
                $try++;
            }

            // Grouping = QC lolos = otomatis masuk gudang utama (env GUDANG_UTAMA_ID).
            // Kolom BKA: outstanding_status_proses (bukan process), outstanding_status_beda_rs
            $dataOutstanding = [
                'outstanding_rfid' => $rfid,
                'outstanding_status_proses' => 'GUDANG',
                'outstanding_id_warehouse' => (int) env('GUDANG_UTAMA_ID', 1),
                'outstanding_updated_at' => $date,
                'outstanding_updated_by' => $userId,
                'outstanding_rs_ori' => $detail->detail_id_rs,
                'outstanding_rs_scan' => $detail->detail_id_rs,
                'outstanding_id_ruangan' => $detail->detail_id_ruangan,
                'outstanding_status_hilang' => 'NORMAL',
                'outstanding_hilang_created_at' => null,
                'outstanding_pending_created_at' => null,
            ];

            // FREE ownership → rs_ori & ruangan null (sesuai legacy)
            if ($detail->detail_status_kepemilikan === \App\Enums\RsStatusEnum::FREE || $detail->detail_status_kepemilikan === 'FREE') {
                $dataOutstanding['outstanding_rs_ori'] = null;
                $dataOutstanding['outstanding_id_ruangan'] = null;
            }

            $outstanding = \App\Models\Outstanding::where('outstanding_rfid', $rfid)->first();

            if ($outstanding) {
                $outstanding->update($dataOutstanding);
            } else {
                // Cek report date: jika ada report dan beda hari → buat transaksi KOTOR/REGISTER
                $needsTransaksi = false;
                $transaksiStatus = TransactionType::KOTOR;
                $flagForTransaksi = 'KOTOR';

                if (!empty($detail->detail_report)) {
                    $reportDate = \Illuminate\Support\Carbon::parse($detail->detail_report)->format('Y-m-d');
                    if ($reportDate !== date('Y-m-d')) {
                        $needsTransaksi = true;
                        $flag = 'KOTOR';
                        if ($statusLinen === LinenStatusEnum::REGISTER || $statusLinen === 'REGISTER') {
                            $flag = 'REGISTER';
                            $transaksiStatus = 'REGISTER';
                        }
                    }
                } else {
                    // Jika belum pernah report, anggap perlu transaksi jika status REGISTER
                    if ($statusLinen === LinenStatusEnum::REGISTER || $statusLinen === 'REGISTER') {
                        $needsTransaksi = false; // akan handle di bawah sebagai outstanding REGISTER tanpa transaksi
                    }
                }

                if ($needsTransaksi) {
                    $existsToday = \App\Models\Transaksi::where('transaksi_rfid', $rfid)
                        ->whereDate('transaksi_created_at', date('Y-m-d'))
                        ->exists();

                    if (! $existsToday) {
                        try {
                            activity('grouping')->causedBy(auth()->user())->withProperties(['rfid' => $rfid])->log("Grouping QC_TRANSACTION RFID $rfid");
                        } catch (\Throwable $e) {}

                        \App\Models\Transaksi::create([
                            'transaksi_key' => $autoNumber,
                            'transaksi_rfid' => $rfid,
                            'transaksi_rs_ori' => $detail->detail_id_rs,
                            'transaksi_rs_scan' => $detail->detail_id_rs,
                            'transaksi_beda_rs' => 'TIDAK',
                            'transaksi_id_ruangan' => $detail->detail_id_ruangan,
                            'transaksi_status' => $transaksiStatus,
                            'transaksi_created_at' => $date,
                            'transaksi_created_by' => $userId,
                            'transaksi_updated_at' => $date,
                            'transaksi_updated_by' => $userId,
                        ]);

                        $detail->update(['detail_status_linen' => $transaksiStatus]);
                        $flag = $flagForTransaksi;
                    }

                    $outstanding = \App\Models\Outstanding::create(array_merge($dataOutstanding, [
                        'outstanding_key' => $autoNumber,
                        'outstanding_status_transaksi' => $transaksiStatus,
                        'outstanding_created_at' => $date,
                        'outstanding_created_by' => $userId,
                    ]));

                    // Pending update legacy — jika tabel ada
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasTable('pending')) {
                            \Illuminate\Support\Facades\DB::table('pending')
                                ->where('pending_transaksi', '!=', TransactionType::BERSIH)
                                ->where('pending_rfid', $rfid)
                                ->update([
                                    'pending_updated_at' => $date,
                                    'pending_transaksi' => $transaksiStatus,
                                    'pending_proses' => 'QC',
                                ]);
                        }
                    } catch (\Throwable $e) {}
                } else {
                    // Outstanding REGISTER tanpa transaksi (sesuai legacy else branch)
                    if ($statusLinen === LinenStatusEnum::REGISTER || $statusLinen === 'REGISTER') {
                        $createdAt = $detail->detail_created_at ? $detail->detail_created_at->format('Y-m-d H:i:s') : $date;
                        $outstanding = \App\Models\Outstanding::create(array_merge($dataOutstanding, [
                            'outstanding_key' => $autoNumber,
                            'outstanding_status_transaksi' => 'REGISTER',
                            'outstanding_created_at' => $createdAt,
                            'outstanding_created_by' => $userId,
                        ]));
                    } else {
                        // Buat outstanding kosong QC untuk RFID yang baru di-grouping
                        $outstanding = \App\Models\Outstanding::create(array_merge($dataOutstanding, [
                            'outstanding_key' => $autoNumber,
                            'outstanding_status_transaksi' => $statusLinen ?: TransactionType::KOTOR,
                            'outstanding_created_at' => $date,
                            'outstanding_created_by' => $userId,
                        ]));
                    }
                }
            }

            // Posisi linen = gudang utama (QC lolos = masuk gudang).
            $detail->update(['detail_status_linen' => \App\Enums\LinenStatusEnum::GUDANG]);

            // Build collection untuk desktop GroupingDAO — andalan: linen_id = RFID (bukan jenis_id), biar konsisten dengan register
            $outstandingFresh = \App\Models\Outstanding::where('outstanding_rfid', $rfid)->first();
            $collection = [
                'rfid' => $rfid,
                'linen_id' => (string) $rfid,
                'linen_nama' => $viewLinenNama ?? '',
                'rs_id' => (string) ($viewRsId ?? ''),
                'rs_nama' => $viewRsNama ?? '',
                'ruangan_id' => (string) ($viewRuanganId ?? ''),
                'ruangan_nama' => $viewRuanganNama ?? '',
                'status_transaksi' => $outstandingFresh->outstanding_status_transaksi ?? $outstanding->outstanding_status_transaksi ?? '',
                'status_proses' => $outstandingFresh->outstanding_status_proses ?? $outstanding->outstanding_status_proses ?? 'GUDANG',
                'status_kepemilikan' => $detail->detail_status_kepemilikan ?? null,
                'tanggal_create' => !empty($outstandingFresh->outstanding_created_at) ? \Illuminate\Support\Carbon::parse($outstandingFresh->outstanding_created_at)->format('Y-m-d') : null,
                'tanggal_update' => !empty($outstandingFresh->outstanding_updated_at) ? \Illuminate\Support\Carbon::parse($outstandingFresh->outstanding_updated_at)->format('Y-m-d') : null,
                'user_nama' => $viewCreatedName,
                'status_linen' => $flag,
            ];

            // Opname detail update legacy — jika tabel ada
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('opname') && \Illuminate\Support\Facades\Schema::hasTable('opname_detail')) {
                    // kontrak kolom tinyint: opname_status 1=Proses, ketemu/sync/scan_rs 0|1 (bukan YA/TIDAK string)
                    $opname = \Illuminate\Support\Facades\DB::table('opname')->where('opname_status', 1)->first();
                    if ($opname) {
                        \Illuminate\Support\Facades\DB::table('opname_detail')
                            ->where('opname_detail_id_opname', $opname->opname_id)
                            ->where('opname_detail_rfid', $rfid)
                            ->where('opname_detail_ketemu', 0)
                            ->update([
                                'opname_detail_scan_rs' => 1,
                                'opname_detail_ketemu' => 1,
                                'opname_detail_waktu' => $date,
                                'opname_detail_sync' => 1,
                                'opname_detail_reff' => $outstandingFresh->outstanding_key ?? $autoNumber,
                                'opname_detail_scan_by' => 'QC',
                            ]);
                    }
                }
            } catch (\Throwable $e) {}

            \Illuminate\Support\Facades\DB::commit();

            // Desktop GroupingDAO expects raw object (bukan envelope Notes) — kembalikan langsung
            return response()->json($collection);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            \Illuminate\Support\Facades\DB::rollBack();
            return \Plugins\Notes::error($rfid, 'RFID ' . $rfid . ' tidak ditemukan');
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\DB::rollBack();
            if ($th->getCode() == 23000) {
                $message = explode('for key', $th->getMessage());
                $clean = str_replace('SQLSTATE[23000]: Integrity constraint violation: 1062', 'RFID', $message[0] ?? $th->getMessage());
                return \Plugins\Notes::error($clean);
            }
            return \Plugins\Notes::error($rfid, $th->getMessage());
        }
    });
});
