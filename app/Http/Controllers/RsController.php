<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Enums\RsStatusEnum;
use App\Http\Requests\GeneralRequest;
use App\Models\GroupRs;
use App\Models\JenisLinen;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RsController extends Controller
{
    use ControllerTrait {
        getData as private traitGetData;
    }

    public function __construct(Rs $model)
    {
        $this->model = $model::getModel();
    }

    protected function getData()
    {
        // Batasi ke RS user (pivot rs_dan_user; kosong = semua).
        return User::scopeRs($this->traitGetData(), 'rs.rs_id');
    }

    protected function share($data = [])
    {
        $default = [
            'model' => $this->model,
            'status' => RsStatusEnum::getOptions(),
            'group' => GroupRs::getOptions(),
            'ruangan' => Ruangan::getOptions(),
            'jenis' => JenisLinen::getOptions(),
        ];

        return array_merge($default, $data);
    }

    public function getCreate(GeneralRequest $request)
    {
        return $this->views($this->template(), [
            'selectedRuangan' => [],
            'selectedJenis' => [],
        ]);
    }

    public function getUpdate(GeneralRequest $request, $id)
    {
        $data = $this->model->findOrFail($id);

        return $this->views($this->template(), [
            'model' => $data,
            'selectedRuangan' => $data->hasRuangan()->pluck('ruangan.ruangan_id')->all(),
            'selectedJenis' => $data->hasJenis()->pluck('jenis_linen.jenis_id')->all(),
        ]);
    }

    public function postCreate(GeneralRequest $request)
    {
        [$ruanganIds, $jenisIds] = $this->resolveRelations($request);
        $payload = CreateAction::run($request, $this->model);

        if ($payload['status']) {
            $this->syncRelations($payload['data'], $ruanganIds, $jenisIds);
        }

        return $this->response($payload, null, 'create');
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        [$ruanganIds, $jenisIds] = $this->resolveRelations($request);
        $payload = UpdateAction::run($request, $id, $this->model);

        if ($payload['status']) {
            $this->syncRelations($payload['data'], $ruanganIds, $jenisIds);
        }

        return $this->response($payload, null, 'update');
    }

    // ponytail: halaman parstock per RS — dibuka dari tombol inventory di
    // tiap baris tabel RS (GET /rs/parstock/{id}). Register = MASTER
    // config_linen join detail_linen. Kurang = parstock - register.
    public function getParstock(GeneralRequest $request, $id)
    {
        $rs = $this->model->findOrFail($id);

        return $this->views('pages.rs.parstock', [
            'rsRecord' => $rs,
            'rows' => $this->parstockRows((int) $rs->rs_id, $request->input('jenis')),
        ]);
    }

    // ponytail: satu tombol Simpan — input parstock[{jenis_id}]. String
    // kosong = null (belum di-set). Hanya jenis milik RS ini yang diproses.
    public function postParstock(GeneralRequest $request, $id)
    {
        $rs = $this->model->findOrFail($id);
        $owned = $rs->hasJenis()->pluck('jenis_linen.jenis_id')->all();
        $rows = $request->input('parstock', []);

        if (! is_array($rows)) {
            return redirect()->back()->withErrors(['parstock' => 'Data parstock tidak valid.']);
        }

        $errors = [];
        $validated = [];

        foreach ($rows as $jenisId => $value) {
            if (! is_numeric($jenisId) || ! in_array((int) $jenisId, $owned, true)) {
                continue;
            }

            $value = trim((string) $value);

            if ($value === '') {
                $validated[(int) $jenisId] = null;

                continue;
            }

            if (! ctype_digit($value)) {
                $errors['parstock.'.$jenisId] = 'Parstock harus angka 0 atau lebih.';

                continue;
            }

            $validated[(int) $jenisId] = (int) $value;
        }

        if ($errors !== []) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        foreach ($validated as $jenisId => $parstock) {
            DB::table('rs_dan_jenis')
                ->where('rs_id', $rs->rs_id)
                ->where('jenis_id', $jenisId)
                ->update(['parstock' => $parstock]);
        }

        flash()->success(TOAST_SUCCESS);

        return redirect()->back();
    }

    private function parstockRows(int $rsId, ?string $jenisFilter): array
    {
        $register = DB::table('config_linen')
            ->join('detail_linen', 'detail_linen.detail_rfid', '=', 'config_linen.detail_rfid')
            ->select('config_linen.rs_id', 'detail_linen.detail_id_jenis', DB::raw('COUNT(*) as total'))
            ->where('config_linen.rs_id', $rsId)
            ->groupBy('config_linen.rs_id', 'detail_linen.detail_id_jenis');

        $query = DB::table('rs_dan_jenis')
            ->join('jenis_linen', 'jenis_linen.jenis_id', '=', 'rs_dan_jenis.jenis_id')
            ->leftJoinSub($register, 'reg', function ($join) {
                $join->on('reg.rs_id', '=', 'rs_dan_jenis.rs_id')
                    ->on('reg.detail_id_jenis', '=', 'rs_dan_jenis.jenis_id');
            })
            ->select(
                'rs_dan_jenis.jenis_id',
                'rs_dan_jenis.parstock',
                'jenis_linen.jenis_nama',
                DB::raw('COALESCE(reg.total, 0) as total_register')
            )
            ->where('rs_dan_jenis.rs_id', $rsId);

        if (is_string($jenisFilter) && $jenisFilter !== '') {
            $query->whereRaw('LOWER(jenis_linen.jenis_nama) LIKE ?', ['%'.strtolower($jenisFilter).'%']);
        }

        return $query->orderBy('jenis_linen.jenis_nama')->get()->all();
    }

    // ponytail: pivot rs_dan_ruangan / rs_dan_jenis di-sync dari checkbox
    // ruangan_ids[] / jenis_ids[] di form. Kosong = lepas semua (detach).
    // parstock pivot jenis dibiarkan null di form ini — diisi dari halaman
    // parstock per RS (tombol inventory di tabel RS).
    private function resolveRelations(GeneralRequest $request): array
    {
        $ruanganIds = $this->normalizeIds($request->input('ruangan_ids', []));
        $jenisIds = $this->normalizeIds($request->input('jenis_ids', []));

        $missing = array_diff($ruanganIds, Ruangan::whereIn('ruangan_id', $ruanganIds)->pluck('ruangan_id')->all());

        if ($missing !== []) {
            throw ValidationException::withMessages(['ruangan_ids' => 'Ruangan tidak ditemukan: '.implode(', ', $missing).'.']);
        }

        $missing = array_diff($jenisIds, JenisLinen::whereIn('jenis_id', $jenisIds)->pluck('jenis_id')->all());

        if ($missing !== []) {
            throw ValidationException::withMessages(['jenis_ids' => 'Jenis linen tidak ditemukan: '.implode(', ', $missing).'.']);
        }

        return [$ruanganIds, $jenisIds];
    }

    private function syncRelations(Rs $rs, array $ruanganIds, array $jenisIds): void
    {
        $rs->hasRuangan()->sync($ruanganIds);
        $rs->hasJenis()->sync($jenisIds);
    }

    private function normalizeIds(mixed $raw): array
    {
        $items = is_array($raw) ? $raw : [$raw];

        return array_values(array_unique(array_filter(array_map(
            fn ($v) => is_numeric($v) ? (int) $v : null,
            $items
        ))));
    }
}
