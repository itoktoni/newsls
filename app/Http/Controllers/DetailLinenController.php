<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Enums\CuciEnum;
use App\Enums\LinenStatusEnum;
use App\Enums\RegisterEnum;
use App\Enums\RsStatusEnum;
use App\Http\Requests\GeneralRequest;
use App\Models\ConfigLinen;
use App\Models\DetailLinen;
use App\Models\GantiChip;
use App\Models\JenisBahan;
use App\Models\JenisLinen;
use App\Models\Rs;
use App\Models\Ruangan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DetailLinenController extends Controller
{
    use ControllerTrait {
        postCreate as private traitPostCreate;
        postUpdate as private traitPostUpdate;
        getDelete as private traitGetDelete;
        postDelete as private traitPostDelete;
        getData as private traitGetData;
    }

    public function __construct(DetailLinen $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        $default = [
            'model' => $this->model,
            'rs' => User::rsOptions(),
            'ruangan' => Ruangan::getOptions(),
            'jenis' => JenisLinen::getOptions(),
            'bahan' => JenisBahan::getOptions(),
            'supplier' => Supplier::getOptions(),
            'cuci' => CuciEnum::getOptions(),
            'register' => RegisterEnum::getOptions(),
            'linen' => LinenStatusEnum::getOptions(),
            'milik' => RsStatusEnum::getOptions(),
            // ponytail: peta RS → ruangan/jenis untuk dropdown dependen di
            // advance filter (pivot rs_dan_ruangan / rs_dan_jenis).
            'ruanganByRs' => DB::table('rs_dan_ruangan')->select('rs_id', 'ruangan_id')->get()
                ->groupBy('rs_id')->map(fn ($g) => $g->pluck('ruangan_id')->all())->all(),
            'jenisByRs' => DB::table('rs_dan_jenis')->select('rs_id', 'jenis_id')->get()
                ->groupBy('rs_id')->map(fn ($g) => $g->pluck('jenis_id')->all())->all(),
        ];

        return array_merge($default, $data);
    }

    public function getUpdate(GeneralRequest $request, $id)
    {
        $data = $this->model->findOrFail($id);

        return $this->views($this->template(), [
            'model' => $data,
            'selectedRs' => ConfigLinen::rsIdsFor($data->detail_rfid),
            'history' => GantiChip::forRfid($data->detail_rfid),
        ]);
    }

    public function getCreate(GeneralRequest $request)
    {
        return $this->views($this->template(), ['selectedRs' => [], 'history' => []]);
    }

    protected function getData()
    {
        // rs_nama (join hasRs) = CURRENT holder (detail_id_rs).
        // pemilik = MASTER dari config_linen (bisa banyak RS), via subquery GROUP_CONCAT
        // supaya tetap satu baris per RFID tanpa menggandakan hasil.
        $isSqlite = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'sqlite';
        $concat = $isSqlite
            ? "group_concat(rs.rs_nama, ', ')"
            : 'GROUP_CONCAT(rs.rs_nama ORDER BY rs.rs_nama SEPARATOR ", ")';
        $pemilik = ConfigLinen::query()
            ->selectRaw($concat)
            ->join('rs', 'rs.rs_id', '=', 'config_linen.rs_id')
            ->whereColumn('config_linen.detail_rfid', 'detail_linen.detail_rfid');

        return User::scopeRs($this->traitGetData(), 'detail_linen.detail_id_rs')
            ->leftJoinRelationship('hasRs')
            ->leftJoinRelationship('hasRuangan')
            ->leftJoinRelationship('hasJenis')
            ->addSelect([
                'detail_linen.*',
                'rs.rs_nama as rs_nama',
                'ruangan.ruangan_nama as ruangan_nama',
                'jenis_linen.jenis_nama as jenis_nama',
                'pemilik' => $pemilik,
            ]);
    }

    public function postCreate(GeneralRequest $request)
    {
        if ($this->model->where('detail_rfid', $request->input('detail_rfid'))->exists()) {
            throw ValidationException::withMessages(['detail_rfid' => 'RFID sudah terdaftar.']);
        }

        $rsIds = $this->resolveOwnership($request);
        $payload = CreateAction::run($request, $this->model);
        ConfigLinen::syncRs($payload['data']->detail_rfid, $rsIds);

        return $this->response($payload);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $rsIds = $this->resolveOwnership($request, $id);
        $newRfid = trim((string) $request->input('detail_rfid', ''));

        if ($newRfid === '') {
            throw ValidationException::withMessages(['detail_rfid' => 'RFID wajib diisi.']);
        }

        $isRfidChange = $newRfid !== (string) $id;

        if ($isRfidChange && $this->model->where('detail_rfid', $newRfid)->exists()) {
            throw ValidationException::withMessages(['detail_rfid' => 'RFID sudah terdaftar.']);
        }

        $payload = DB::transaction(function () use ($request, $id, $newRfid, $rsIds, $isRfidChange) {
            $payload = UpdateAction::run($request, $id, $this->model);

            if (! $payload['status']) {
                return $payload;
            }

            if ($isRfidChange) {
                // ponytail: RFID adalah PK — UpdateAction sudah memindahkannya.
                // Pindahkan baris master config + catat histori (rantai A→B→C).
                DB::table('config_linen')->where('detail_rfid', $id)->update(['detail_rfid' => $newRfid]);

                GantiChip::create([
                    'ganti_rfid_lama' => (string) $id,
                    'ganti_rfid_baru' => $newRfid,
                    'ganti_tanggal' => now(),
                    'ganti_by' => auth()->id(),
                ]);
            }

            ConfigLinen::syncRs($payload['data']->detail_rfid, $rsIds);

            return $payload;
        });

        if ($payload['status'] && $isRfidChange) {
            flash()->success($payload['message']);

            return redirect()->route('detail-linen.getUpdate', ['id' => $newRfid]);
        }

        return $this->response($payload, null, 'update');
    }

    public function getDelete(GeneralRequest $request, $id)
    {
        ConfigLinen::clearRfid($id);

        return $this->traitGetDelete($request, $id);
    }

    public function postDelete(GeneralRequest $request)
    {
        foreach ((array) $request->input('ids', []) as $rfid) {
            ConfigLinen::clearRfid($rfid);
        }

        return $this->traitPostDelete($request);
    }

    // ponytail: dua input terpisah —
    // - rs_ids[]    => MASTER (config_linen): DAFTAR RS pemilik sah.
    //   DEDICATED min 1, GROUP min 2 (mis. siloam A + siloam B), FREE nol baris.
    // - detail_id_rs => CURRENT (detail_linen): RS yang sedang memegang linen.
    //   DEDICATED/GROUP wajib salah satu anggota pemilik; FREE boleh RS mana pun.
    private function resolveOwnership(GeneralRequest $request, ?string $rfid = null): array
    {
        $existing = $rfid ? $this->model->findOrFail($rfid) : null;

        $milik = $request->input('detail_status_kepemilikan')
            ?? $existing?->detail_status_kepemilikan
            ?? RsStatusEnum::DEDICATED;

        // ponytail: DEDICATED single-select mengirim rs_ids sebagai string tunggal,
        // GROUP checklist mengirim rs_ids[] array — keduanya dinormalisasi.
        $owners = $this->normalizeRs($request->input('rs_ids', []));

        if ($milik === RsStatusEnum::FREE) {
            $owners = [];
        } else {
            $min = $milik === RsStatusEnum::GROUP ? 2 : 1;

            if (count($owners) < $min) {
                $label = $milik === RsStatusEnum::GROUP
                    ? 'GROUP minimal 2 RS pemilik (contoh: siloam A + siloam B).'
                    : 'DEDICATED wajib pilih minimal 1 RS pemilik.';

                throw ValidationException::withMessages(['rs_ids' => $label]);
            }
        }

        $missing = array_diff($owners, Rs::whereIn('rs_id', $owners)->pluck('rs_id')->all());

        if ($missing !== []) {
            throw ValidationException::withMessages(['rs_ids' => 'RS tidak ditemukan: '.implode(', ', $missing).'.']);
        }

        // ponytail: user hanya boleh assign ke RS dalam haknya (pivot rs_dan_user).
        $allowed = User::allowedRsIds();
        if ($allowed !== null && array_diff($owners, $allowed) !== []) {
            throw ValidationException::withMessages(['rs_ids' => 'RS pemilik di luar hak akses Anda.']);
        }

        // CURRENT holder.
        $current = $request->input('detail_id_rs');
        $current = is_numeric($current) ? (int) $current : null;

        if ($milik === RsStatusEnum::FREE) {
            // FREE: current holder bebas (boleh RS mana pun, boleh kosong).
            if ($current !== null && ! Rs::where('rs_id', $current)->exists()) {
                throw ValidationException::withMessages(['detail_id_rs' => 'RS sekarang tidak ditemukan.']);
            }
            if ($current !== null) {
                User::ensureRsAccess($current);
            }
        } else {
            $current ??= $owners[0];

            if (! in_array($current, $owners, true)) {
                $ownersLabel = Rs::whereIn('rs_id', $owners)->pluck('rs_nama')->implode(', ');

                throw ValidationException::withMessages([
                    'detail_id_rs' => "RS sekarang bukan pemilik (master: {$ownersLabel}). Pindahkan pemiliknya di daftar RS Pemilik bila memang linen dipakai di sana.",
                ]);
            }
        }

        $request->merge(['detail_id_rs' => $current]);

        return $owners;
    }

    private function normalizeRs(mixed $raw): array
    {
        $items = is_array($raw) ? $raw : [$raw];

        return array_values(array_unique(array_filter(array_map(
            fn ($v) => is_numeric($v) ? (int) $v : null,
            $items
        ))));
    }
}
