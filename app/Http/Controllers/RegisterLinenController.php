<?php

namespace App\Http\Controllers;

use App\Actions\RegisterLinenAction;
use App\Enums\CuciEnum;
use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Http\Requests\RegisterLinenRequest;
use App\Models\ConfigLinen;
use App\Models\DetailLinen;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Plugins\Notes;

/**
 * API register linen (scan masuk) — dipakai klien mobile/scanner.
 *
 * POST /api/register          → register massal
 * GET  /api/register/config   → opsi dropdown untuk form register
 *
 * Logika register ada di App\Actions\RegisterLinenAction (sesuai AGENTS.md:
 * business logic ditaruh di Actions, controller hanya jadi lapisan HTTP).
 * Controller ini bertugas memvalidasi request, memanggil Action, lalu
 * membentuk respons JSON.
 *
 * Bentuk respons memakai envelope standar Plugins\Notes:
 *   { status, code, name, message, data }
 */
class RegisterLinenController extends Controller
{
    /**
     * Register satu atau banyak RFID sekaligus.
     */
    public function store(RegisterLinenRequest $request)
    {
        // Register massal bisa ratusan RFID per keranjang; samakan dengan andalan.
        set_time_limit(0);

        // Action mengembalikan payload {code, status, message, data}: data berisi
        // Collection DetailLinen saat sukses, atau pesan error saat gagal.
        // ValidationException dari Action dilempar ke handler global (422).
        $payload = RegisterLinenAction::run($request->validated());

        if (! $payload['status']) {
            return Notes::failed(500, $payload['data']);
        }

        /** @var \Illuminate\Support\Collection<int, DetailLinen> $linen */
        $linen = $payload['data'];

        // Desktop RegisterSingleDAO expects data: Datum[] with linen_id/linen_nama etc (legacy andalan).
        // Web new format expects data: {total, rfid, data:[{rfid,jenis_id,...}]}.
        // Kirim keduanya agar desktop lama tidak "cannot deserialize" dan klien baru tetap dapat detail.
        $desktopData = $this->linenResponsesForDesktop($linen);
        $detailedData = $this->linenResponses($linen);

        // Notes::data (name=List, sama dengan andalan) + key tambahan `detail`/`total`/`rfid`
        // di root. Sebelumnya Notes::create() dipanggil dengan 2 argumen padahal helper itu
        // hanya menerima satu, sehingga detail/total/rfid terbuang diam-diam.
        return Notes::data($desktopData, [
            'detail' => $detailedData,
            'total' => $linen->count(),
            'rfid' => $linen->pluck(DetailLinen::field_primary())->all(),
        ]);
    }

    /**
     * Opsi dropdown untuk form register.
     *
     * Query opsional ?rs_id=1 → jenis & ruangan disaring ke RS tersebut
     * (meniru endpoint `rs?type=register` di andalan yang memakai pivot
     * rs_dan_jenis / rs_dan_ruangan).
     */
    public function config(Request $request)
    {
        $rsId = $request->integer('rs_id') ?: null;
        // ponytail: dropdown register desktop ikut hak akses RS (pivot rs_dan_user).
        $allowed = \App\Models\User::allowedRsIds();
        if ($rsId !== null && $allowed !== null && ! in_array($rsId, $allowed, true)) {
            abort(403, 'Akses rumah sakit ditolak.');
        }


        return Notes::data([
            'rs' => $this->modelOptions(\App\Models\User::scopeRs(Rs::query(), 'rs.rs_id')->orderBy('rs_nama')->get(['rs_id', 'rs_nama']), 'rs_id', 'rs_nama'),
            'jenis' => $this->jenisOptions($rsId),
            'ruangan' => $this->ruanganOptions($rsId),
            'bahan' => $this->modelOptions(JenisBahan::query()->orderBy('bahan_nama')->get(['bahan_id', 'bahan_nama']), 'bahan_id', 'bahan_nama'),
            'supplier' => $this->modelOptions(Supplier::query()->orderBy('supplier_nama')->get(['supplier_id', 'supplier_nama']), 'supplier_id', 'supplier_nama'),
            'status_cuci' => CuciEnum::getApi(),
            'status_register' => RegisterEnum::getApi(),
            'status_kepemilikan' => RsStatusEnum::getApi(),
            'status_linen' => LinenStatusEnum::getApi(),
        ]);
    }

    private function linenResponsesForDesktop($linen): array
    {
        // Mapping legacy desktop RegisterSingleDAO.Datum: linen_id,linen_nama,rs_id,rs_nama,ruangan_id,ruangan_nama,pemakaian,user_nama
        // plus extra field rfid agar desktop baru bisa pakai, dan field baru untuk kompatibilitas.
        return $linen->map(function (DetailLinen $item) {
            $userName = null;
            try {
                $user = \App\Models\User::find($item->detail_created_by);
                $userName = $user?->name;
            } catch (\Throwable $e) {}
            return [
                // legacy desktop fields — andalan: linen_id = RFID (bukan jenis_id), linen_nama = jenis_nama
                'linen_id' => (string) $item->detail_rfid,
                'linen_nama' => $item->hasJenis?->jenis_nama ?? $item->detail_rfid,
                'rs_id' => (string) ($item->detail_id_rs ?? ''),
                'rs_nama' => $item->hasRs?->rs_nama ?? '',
                'ruangan_id' => (string) ($item->detail_id_ruangan ?? ''),
                'ruangan_nama' => $item->hasRuangan?->ruangan_nama ?? '',
                'pemakaian' => 0,
                'user_nama' => $userName ?? (string) $item->detail_created_by,
                // extra untuk klien baru (tidak diabaikan desktop, tapi berguna)
                'rfid' => $item->detail_rfid,
                'jenis_id' => $item->detail_id_jenis,
                'jenis_nama' => $item->hasJenis?->jenis_nama,
                'bahan_id' => $item->detail_id_bahan,
                'supplier_id' => $item->detail_id_supplier,
                'status_cuci' => $item->detail_status_cuci,
                'status_register' => $item->detail_status_register,
            ];
        })->all();
    }

    /**
     * Bentuk baris detail_linen jadi array yang enak dikonsumsi klien.
     *
     * @param  \Illuminate\Support\Collection<int, DetailLinen>  $linen
     */
    private function linenResponses($linen): array
    {
        // Satu query untuk semua label pemilik, bukan satu query per RFID.
        // sqlite tidak support ORDER BY di dalam GROUP_CONCAT, fallback ke group_concat(x, ", ")
        $isSqlite = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'sqlite';
        $concat = $isSqlite
            ? "group_concat(rs.rs_nama, ', ')"
            : 'GROUP_CONCAT(rs.rs_nama ORDER BY rs.rs_nama SEPARATOR ", ")';
        $pemilik = ConfigLinen::query()
            ->selectRaw("config_linen.detail_rfid, {$concat} as label")
            ->join('rs', 'rs.rs_id', '=', 'config_linen.rs_id')
            ->whereIn('config_linen.detail_rfid', $linen->pluck(DetailLinen::field_primary())->all())
            ->groupBy('config_linen.detail_rfid')
            ->pluck('label', 'config_linen.detail_rfid');

        return $linen->map(fn (DetailLinen $item) => [
            'rfid' => $item->detail_rfid,
            'jenis_id' => $item->detail_id_jenis,
            'jenis_nama' => $item->hasJenis?->jenis_nama,
            'bahan_id' => $item->detail_id_bahan,
            'bahan_nama' => $item->hasBahan?->bahan_nama,
            'supplier_id' => $item->detail_id_supplier,
            'supplier_nama' => $item->hasSupplier?->supplier_nama,
            'rs_id' => $item->detail_id_rs,
            'rs_nama' => $item->hasRs?->rs_nama,
            'ruangan_id' => $item->detail_id_ruangan,
            'ruangan_nama' => $item->hasRuangan?->ruangan_nama,
            'pemilik' => $pemilik[$item->detail_rfid] ?? null,
            'status_cuci' => $item->detail_status_cuci,
            'status_cuci_nama' => $item->detail_status_cuci
                ? CuciEnum::getDescription($item->detail_status_cuci)
                : null,
            'status_register' => $item->detail_status_register,
            'status_kepemilikan' => $item->detail_status_kepemilikan,
            'status_linen' => $item->detail_status_linen,
            'status_linen_nama' => $item->detail_status_linen
                ? LinenStatusEnum::getDescription($item->detail_status_linen)
                : null,
            'tanggal_cek' => $item->detail_tgl_cek?->format('Y-m-d'),
            'created_at' => $item->detail_created_at?->toIso8601String(),
            'created_by' => $item->detail_created_by,
        ])->all();
    }

    private function jenisOptions(?int $rsId): array
    {
        $query = JenisLinen::query()->orderBy('jenis_nama');

        if ($rsId !== null) {
            $query->join('rs_dan_jenis', 'rs_dan_jenis.jenis_id', '=', 'jenis_linen.jenis_id')
                ->where('rs_dan_jenis.rs_id', $rsId)
                ->distinct();
        }

        return $this->modelOptions($query->get(['jenis_linen.jenis_id', 'jenis_linen.jenis_nama']), 'jenis_id', 'jenis_nama');
    }

    private function ruanganOptions(?int $rsId): array
    {
        $query = Ruangan::query()->orderBy('ruangan_nama');

        if ($rsId !== null) {
            $query->join('rs_dan_ruangan', 'rs_dan_ruangan.ruangan_id', '=', 'ruangan.ruangan_id')
                ->where('rs_dan_ruangan.rs_id', $rsId)
                ->distinct();
        }

        return $this->modelOptions($query->get(['ruangan.ruangan_id', 'ruangan.ruangan_nama']), 'ruangan_id', 'ruangan_nama');
    }

    private function modelOptions($rows, string $idKey, string $nameKey): array
    {
        return $rows->map(fn ($row) => [
            'id' => $row->{$idKey},
            'name' => $row->{$nameKey},
        ])->all();
    }
}
