<?php

namespace App\Http\Controllers\Api;

use App\Enums\CuciEnum;
use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Plugins\Notes;

/**
 * Master RS untuk desktop (RsAllDAO / RsSingleDAO).
 *
 * Adopsi closure legacy di routes/api.php — kontrak response dipertahankan
 * persis (envelope Notes + key rs_ruangan/rs_jenis + status_id/status_nama).
 */
class RsApiController extends Controller
{
    /**
     * GET /api/rs?type=free|dedicated|register — daftar RS + dropdown
     * ruangan/jenis/status, disaring ke RS milik user (pivot rs_dan_user).
     */
    public function index(Request $request)
    {
        $type = $request->query('type');

        // ponytail: desktop hanya boleh lihat RS sesuai pivot rs_dan_user
        // (user A = RS A+B → dropdown desktop cuma RS A+B). Kosong = semua.
        $query = User::scopeRs(Rs::query(), 'rs.rs_id');

        if ($type === 'free') {
            $query->where('rs_status', RsStatusEnum::FREE);
        } elseif ($type === 'dedicated') {
            $query->where('rs_status', RsStatusEnum::DEDICATED);
        } elseif ($type === 'register') {
            // register butuh semua RS (FREE+Dedicated) karena desktop filter di client by rs_status
            // no filter
        }

        // rs_ruangan & rs_jenis per RS — dibaca RsAllDAO desktop (kontrak legacy).
        $rs = $query->with(['hasRuangan', 'hasJenis'])->get();
        $allowed = User::allowedRsIds();

        // bahan & supplier
        $bahan = JenisBahan::select('bahan_id', 'bahan_nama')->get();
        $supplier = Supplier::select('supplier_id', 'supplier_nama')->get();
        $jenis = JenisLinen::select('jenis_id', 'jenis_nama')->get();

        // Kembalikan envelope Notes agar desktop deserialize ke RsAllDAO.Rootobject (status/code/name/message/data + extra)
        return Notes::data($this->rsPayload($rs), [
            'ruangan' => $this->ruanganList($allowed),
            'jenis' => $jenis,
            'jenis_rs' => $this->jenisRsList($allowed),
            // ponytail: ruangan_rs ikut disaring ke RS milik user.
            'ruangan_rs' => $this->ruanganRsList($allowed),
            'jenis_linen' => $jenis,
            'bahan' => $bahan,
            'supplier' => $supplier,
            'status_cuci' => $this->desktopStatuses(CuciEnum::class),
            'status_register' => $this->desktopStatuses(RegisterEnum::class),
            'status_transaksi' => $this->desktopStatuses(TransactionType::class),
            // alias sama, desktop pakai keduanya
            'status_proses' => $this->desktopStatuses(TransactionType::class),
            'status_linen' => $this->desktopStatuses(LinenStatusEnum::class),
        ]);
    }

    /**
     * GET /api/rs_lite — id + nama RS milik user.
     */
    public function lite()
    {
        $query = User::scopeRs(Rs::query(), 'rs.rs_id');

        return Notes::data($query->select('rs_id', 'rs_nama')->get());
    }

    /**
     * GET /api/rs/{rsid} — satu RS (RsSingleDAO desktop: 5 key).
     */
    public function detail($rsid)
    {
        $rs = Rs::with(['hasRuangan', 'hasJenis'])->findOrFail($rsid);

        // Notes::data (bukan single) + item 5 key — sama dengan legacy andalan
        // (RsSingleDAO desktop).
        return Notes::data([
            'rs_id' => (int) $rs->rs_id,
            'rs_nama' => $rs->rs_nama,
            'rs_status' => $rs->rs_status,
            'rs_ruangan' => $rs->rs_ruangan,
            'rs_jenis' => $rs->rs_jenis,
        ]);
    }

    /**
     * Item RS mengikuti andalan: rs_id/rs_nama/rs_status + rs_ruangan/rs_jenis
     * (yang dibaca RsAllDAO desktop) — kolom RS lain tidak dikirim lagi.
     */
    private function rsPayload($rs): array
    {
        return $rs->map(fn ($item) => [
            'rs_id' => (int) $item->rs_id,
            'rs_nama' => $item->rs_nama,
            'rs_status' => $item->rs_status,
            'rs_ruangan' => $item->rs_ruangan,
            'rs_jenis' => $item->rs_jenis,
        ])->values()->all();
    }

    /**
     * Desktop expects status_id/status_nama (bukan map value => label).
     */
    private function desktopStatuses(string $enumClass): array
    {
        $out = [];

        foreach ($enumClass::getOptions() as $val => $label) {
            $out[] = ['status_id' => $val, 'status_nama' => $label];
        }

        return $out;
    }

    /**
     * Ruangan dengan rs_id (join pivot) — desktop filter by rs_id.
     * Fallback jika pivot kosong (RS belum mapping) → semua ruangan tanpa rs_id.
     */
    private function ruanganList(?array $allowed)
    {
        $ruangan = DB::table('ruangan')
            ->join('rs_dan_ruangan', 'ruangan.ruangan_id', '=', 'rs_dan_ruangan.ruangan_id')
            ->select('ruangan.ruangan_id', 'ruangan.ruangan_nama', 'rs_dan_ruangan.rs_id')
            ->when($allowed !== null, fn ($q) => $q->whereIn('rs_dan_ruangan.rs_id', $allowed))
            ->get();

        if ($ruangan->isEmpty()) {
            $ruangan = Ruangan::select('ruangan_id', 'ruangan_nama')->get()->map(function ($r) {
                $r->rs_id = null;

                return $r;
            });
        }

        return $ruangan;
    }

    /**
     * Jenis_rs dengan jenis_nama + rs_id (join) — saring ke RS milik user.
     */
    private function jenisRsList(?array $allowed)
    {
        $jenisRs = DB::table('rs_dan_jenis')
            ->join('jenis_linen', 'jenis_linen.jenis_id', '=', 'rs_dan_jenis.jenis_id')
            ->select('rs_dan_jenis.rs_id', 'rs_dan_jenis.jenis_id', 'jenis_linen.jenis_nama')
            ->when($allowed !== null, fn ($q) => $q->whereIn('rs_dan_jenis.rs_id', $allowed))
            ->get();

        if ($jenisRs->isEmpty()) {
            $jenisRs = DB::table('rs_dan_jenis')->select('rs_id', 'jenis_id')->get()->map(function ($r) {
                $r->jenis_nama = null;

                return $r;
            });
        }

        return $jenisRs;
    }

    private function ruanganRsList(?array $allowed)
    {
        return DB::table('rs_dan_ruangan')->select('rs_id', 'ruangan_id')
            ->when($allowed !== null, fn ($q) => $q->whereIn('rs_id', $allowed))
            ->get();
    }
}
