<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Enums\RsStatusEnum;
use App\Http\Requests\GeneralRequest;
use App\Models\ConfigLinen;
use App\Models\DetailLinen;
use App\Models\Rs;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ConfigLinenController extends Controller
{
    use ControllerTrait {
        postCreate as private traitPostCreate;
        getUpdate as private traitGetUpdate;
        getDelete as private traitGetDelete;
        postDelete as private traitPostDelete;
        getData as private traitGetData;
    }

    public function __construct(ConfigLinen $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        $default = [
            'model' => $this->model,
            'rfid' => DetailLinen::query()
                ->whereNotIn('detail_status_kepemilikan', [RsStatusEnum::FREE])
                ->orderBy('detail_rfid')->limit(500)
                ->pluck('detail_rfid', 'detail_rfid'),
            'rs' => User::rsOptions(),
        ];

        return array_merge($default, $data);
    }

    protected function getData()
    {
        // rs (hasRs)            = pemilik/master baris config_linen.
        // rs_current            = RS yang sekarang memegang RFID (detail_linen.detail_id_rs).
        return User::scopeRs($this->traitGetData(), 'config_linen.rs_id')
            ->leftJoinRelationship('hasRs')
            ->leftJoinRelationship('hasDetail')
            ->leftJoin('rs as rs_current', 'rs_current.rs_id', '=', 'detail_linen.detail_id_rs')
            ->addSelect([
                'config_linen.*',
                'rs.rs_nama as rs_nama',
                'rs_current.rs_nama as current_nama',
                'detail_linen.detail_status_kepemilikan as kepemilikan',
            ]);
    }

    public function getUpdate(GeneralRequest $request, $id)
    {
        [$rfid, $rsId] = $this->parseId($id);

        $row = $this->model->where('detail_rfid', $rfid)->where('rs_id', $rsId)->firstOrFail();

        return $this->views($this->template(), ['model' => $row]);
    }

    public function postCreate(GeneralRequest $request)
    {
        $data = $this->validated($request);

        if ($this->model->where('detail_rfid', $data['detail_rfid'])->where('rs_id', $data['rs_id'])->exists()) {
            throw ValidationException::withMessages(['detail_rfid' => 'Pasangan RFID + RS sudah terdaftar.']);
        }

        $request->merge($data);

        return $this->traitPostCreate($request);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        [$rfid, $rsId] = $this->parseId($id);

        $data = $this->validated($request);

        if (($data['detail_rfid'] !== $rfid || (int) $data['rs_id'] !== $rsId)
            && $this->model->where('detail_rfid', $data['detail_rfid'])->where('rs_id', $data['rs_id'])->exists()) {
            throw ValidationException::withMessages(['detail_rfid' => 'Pasangan RFID + RS sudah terdaftar.']);
        }

        $request->merge($data);

        // ponytail: tidak pakai UpdateAction::run — PK komposit (rs_id, detail_rfid)
        // bikin findOrFail($rfid) ambigu untuk GROUP (2 baris berbagi RFID).
        $row = $this->model->where('detail_rfid', $rfid)->where('rs_id', $rsId)->firstOrFail();
        $row->update($data);

        return $this->response($this->payload(TOAST_SUCCESS, $row->fresh()));
    }

    public function getDelete(GeneralRequest $request, $id)
    {
        [$rfid, $rsId] = $this->parseId($id);

        $this->guardMinRows($rfid);

        $this->model->where('detail_rfid', $rfid)->where('rs_id', $rsId)->delete();

        return $this->response($this->payload(TOAST_SUCCESS, ['id' => $id]));
    }

    public function postDelete(GeneralRequest $request)
    {
        $ids = $request->input('ids', []);

        foreach ((array) $ids as $id) {
            [$rfid] = $this->parseId($id);
            $this->guardMinRows($rfid);
        }

        foreach ((array) $ids as $id) {
            [$rfid, $rsId] = $this->parseId($id);
            $this->model->where('detail_rfid', $rfid)->where('rs_id', $rsId)->delete();
        }

        return $this->response($this->payload(TOAST_SUCCESS, $ids));
    }

    // ponytail: id komposit "RFID:rsId" karena PK tabel (rs_id, detail_rfid);
    // Route::auto hanya memberi satu segmen {id} jadi dikodekan dengan ':'.
    private function parseId(string $id): array
    {
        if (! str_contains($id, ':')) {
            throw ValidationException::withMessages(['id' => 'ID config tidak valid.']);
        }

        [$rfid, $rsId] = explode(':', $id, 2);

        if ($rfid === '' || ! is_numeric($rsId)) {
            throw ValidationException::withMessages(['id' => 'ID config tidak valid.']);
        }

        return [$rfid, (int) $rsId];
    }

    // ponytail: FREE tidak punya baris config; DEDICATED min 1 RS; GROUP min 2 RS.
    // Guard delete agar tidak menyisakan DEDICATED 0 RS / GROUP 1 RS (orphan).
    private function validated(GeneralRequest $request): array
    {
        $data = $request->validate([
            'detail_rfid' => 'required|string|max:255|exists:detail_linen,detail_rfid',
            'rs_id' => 'required|integer|exists:rs,rs_id',
        ]);

        // ponytail: config hanya boleh ke RS dalam hak user.
        User::ensureRsAccess((int) $data['rs_id']);

        $milik = DetailLinen::where('detail_rfid', $data['detail_rfid'])->value('detail_status_kepemilikan');

        if ($milik === RsStatusEnum::FREE || $milik === null) {
            throw ValidationException::withMessages(['detail_rfid' => 'Linen FREE tidak butuh baris config (bebas dipakai RS lain).']);
        }

        return $data;
    }

    private function guardMinRows(string $rfid): void
    {
        $milik = DetailLinen::where('detail_rfid', $rfid)->value('detail_status_kepemilikan');
        $total = $this->model->where('detail_rfid', $rfid)->count();
        $min = $milik === RsStatusEnum::GROUP ? 2 : 1;

        if ($total <= $min) {
            throw ValidationException::withMessages(['id' => "Tidak bisa hapus: {$milik} wajib punya minimal {$min} RS. Ubah kepemilikan ke FREE dulu bila memang bebas."]);
        }
    }
}
