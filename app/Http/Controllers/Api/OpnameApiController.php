<?php

namespace App\Http\Controllers\Api;

use App\Actions\SyncOpnameAction;
use App\Http\Controllers\Controller;
use App\Models\Opname;
use App\Models\OpnameDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Plugins\Notes;

class OpnameApiController extends Controller
{
    // GET /api/opname — list opname Proses
    public function index(Request $request)
    {
        $data = Opname::where('opname_status', 1)->with('hasRs')->get();
        return Notes::data($data);
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
        \App\Models\User::ensureRsAccess((int) Opname::find($request->input('opname_id'))->opname_id_rs);
        $result = SyncOpnameAction::run((int) $request->input('opname_id'), $request->input('rfid'), $request->input('code', 'SYNC-'.date('ymdHis')));
        return Notes::create($result);
    }

    // POST /api/opname/capture/{id}
    public function capture($id)
    {
        $opname = Opname::findOrFail($id);
        \App\Models\User::ensureRsAccess((int) $opname->opname_id_rs);
        $result = \App\Actions\CaptureOpnameAction::run($opname);
        return Notes::create($result);
    }
}
