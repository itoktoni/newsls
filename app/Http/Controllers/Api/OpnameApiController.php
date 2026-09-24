<?php

namespace App\Http\Controllers\Api;

use App\Actions\CaptureOpnameAction;
use App\Actions\SyncOpnameAction;
use App\Http\Controllers\Controller;
use App\Models\Opname;
use App\Models\OpnameDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Plugins\Notes;

class OpnameApiController extends Controller
{
    // GET /api/opname — list opname Proses yang sedang berjalan (rentang tanggal)
    public function index(Request $request)
    {
        $today = today()->format('Y-m-d');

        $opnames = Opname::with('hasRs')
            ->where('opname_status', 1)
            ->where('opname_mulai', '<=', $today)
            ->where('opname_selesai', '>=', $today)
            ->orderByDesc('opname_id')
            ->get();

        // Kontrak legacy: {opname_id, opname_start, opname_end, rs_id, rs_nama}
        $data = $opnames->map(fn (Opname $opname) => [
            'opname_id' => (int) $opname->opname_id,
            'opname_start' => $opname->opname_mulai?->format('Y-m-d'),
            'opname_end' => $opname->opname_selesai?->format('Y-m-d'),
            'rs_id' => $opname->hasRs?->rs_id ?? '',
            'rs_nama' => $opname->hasRs?->rs_nama ?? '',
        ]);

        // Key tambahan diambil dari opname aktif PERTAMA: RS-nya, daftar ruangan RS
        // itu, dan RFID yang sudah ketemu di opname tersebut.
        $first = $opnames->first();
        $rsId = $first?->opname_id_rs;

        return Notes::data($data, [
            'rs' => $rsId === null ? [] : [
                'rs_id' => (int) $rsId,
                'rs_nama' => $first->hasRs?->rs_nama ?? '',
            ],
            'ruangan' => $rsId === null ? [] : $this->ruanganList((int) $rsId),
            'opname' => $first === null ? [] : OpnameDetail::where('opname_detail_id_opname', $first->opname_id)
                ->where('opname_detail_ketemu', 1)
                ->pluck('opname_detail_rfid')
                ->all(),
        ]);
    }

    /** Ruangan milik RS (pivot rs_dan_ruangan) — dipakai desktop untuk dropdown lokasi. */
    private function ruanganList(int $rsId): array
    {
        return DB::table('ruangan')
            ->join('rs_dan_ruangan', 'rs_dan_ruangan.ruangan_id', '=', 'ruangan.ruangan_id')
            ->where('rs_dan_ruangan.rs_id', $rsId)
            ->orderBy('ruangan.ruangan_nama')
            ->get(['ruangan.ruangan_id', 'ruangan.ruangan_nama'])
            ->map(fn ($row) => [
                'ruangan_id' => (int) $row->ruangan_id,
                'ruangan_nama' => $row->ruangan_nama,
            ])
            ->all();
    }

    // GET /api/opname/{id}/detail — detail per opname
    public function detail($id)
    {
        $data = OpnameDetail::where('opname_detail_id_opname', $id)->get();

        return Notes::data($data);
    }

    // POST /api/opname/sync — bulk rfid sync (desktop)
    public function sync(Request $request)
    {
        $request->validate([
            'opname_id' => 'required|integer|exists:opname,opname_id',
            'rfid' => 'required|array|min:1',
            'rfid.*' => 'required|string',
            'code' => 'nullable|string',
        ]);
        User::ensureRsAccess((int) Opname::find($request->input('opname_id'))->opname_id_rs);
        $result = SyncOpnameAction::run((int) $request->input('opname_id'), $request->input('rfid'), $request->input('code', 'SYNC-'.date('ymdHis')));

        return Notes::create($result);
    }

    // POST /api/opname/capture/{id}
    public function capture($id)
    {
        $opname = Opname::findOrFail($id);
        User::ensureRsAccess((int) $opname->opname_id_rs);
        $result = CaptureOpnameAction::run($opname);

        return Notes::create($result);
    }
}
