<?php

namespace App\Http\Controllers;

use App\Actions\CaptureOpnameAction;
use App\Actions\SyncOpnameAction;
use App\Concerns\ControllerTrait;
use App\Enums\OpnameStatusEnum;
use App\Http\Requests\GeneralRequest;
use App\Models\Opname;
use App\Models\OpnameDetail;
use App\Models\User;
use Plugins\Notes;

class OpnameController extends Controller
{
    use ControllerTrait {
        getData as private traitGetData;
    }

    public function __construct(Opname $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        return array_merge([
            'model' => $this->model,
            'rs' => User::rsOptions(),
            'status' => OpnameStatusEnum::getOptions(),
        ], $data);
    }

    protected function getData()
    {
        return User::scopeRs($this->traitGetData(), 'opname.opname_id_rs')
            ->leftJoin('rs', 'rs.rs_id', '=', 'opname.opname_id_rs')
            ->addSelect(['opname.*', 'rs.rs_nama']);
    }

    // GET /opname/capture/{code}
    public function getCapture($code)
    {
        $model = Opname::findOrFail($code);
        if (! empty($model->opname_capture)) {
            return redirect()->back()->with('error', 'Opname sudah di capture !');
        }
        try {
            CaptureOpnameAction::run($model);

            return redirect()->back()->with('success', 'Capture berhasil, '.OpnameDetail::where('opname_detail_id_opname', $model->opname_id)->count().' RFID tercapture');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    // GET /opname/sync/{code} — form sync (web)
    public function getSync($code)
    {
        $model = Opname::with('hasRs')->findOrFail($code);
        $total = OpnameDetail::where('opname_detail_id_opname', $code)->count();
        $ketemu = OpnameDetail::where('opname_detail_id_opname', $code)->where('opname_detail_ketemu', 1)->count();
        $hilang = $total - $ketemu;

        return view('pages.opname.sync', [
            'model' => $model,
            'total' => $total,
            'ketemu' => $ketemu,
            'hilang' => $hilang,
        ]);
    }

    // POST /opname/sync/{code} — RFID[] atau rfid_text (satu per baris)
    public function postSync($code, GeneralRequest $request)
    {
        $request->validate([
            'rfid' => 'nullable|array',
            'rfid.*' => 'nullable|string',
            'rfid_text' => 'nullable|string',
            'code' => 'nullable|string',
        ]);

        $rfids = $request->input('rfid');
        if (! is_array($rfids) || ! array_filter($rfids)) {
            $rfids = preg_split('/\r\n|\r|\n/', (string) $request->input('rfid_text', ''));
        }
        $rfids = array_values(array_unique(array_filter(array_map('trim', (array) $rfids))));
        if (empty($rfids)) {
            return redirect()->back()->with('error', 'RFID kosong');
        }

        try {
            $result = SyncOpnameAction::run((int) $code, $rfids, $request->input('code', 'SYNC-'.date('ymdHis')));

            return redirect()->back()->with('success', 'Sync berhasil: '.json_encode($result));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    // GET /opname/detail/{code} — daftar RFID hasil capture (config → opname_detail) + join detail_linen
    public function getDetail($code)
    {
        $opname = Opname::with('hasRs')->findOrFail($code);

        $data = OpnameDetail::query()
            ->with(['hasView.hasJenis', 'hasView.hasRuangan', 'hasView.hasRs', 'hasView.hasBahan'])
            ->where('opname_detail_id_opname', $opname->opname_id)
            ->orderBy('opname_detail_rfid')
            ->paginate(50)
            ->withQueryString();

        $total = OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->count();
        $ketemu = OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_ketemu', 1)->count();

        // JSON hanya bila klien minta (ajax/API); browser desktop dapat halaman HTML
        if (request()->wantsJson() || request()->is('api/*')) {
            return Notes::data($data);
        }

        return view('pages.opname.detail', [
            'model' => $this->model,
            'opname' => $opname,
            'data' => $data,
            'total' => $total,
            'ketemu' => $ketemu,
            'hilang' => $total - $ketemu,
        ]);
    }
}
