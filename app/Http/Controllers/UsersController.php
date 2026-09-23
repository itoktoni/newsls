<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Enums\RoleEnum;
use App\Http\Requests\GeneralRequest;
use App\Models\Rs;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    // ponytail: alias harus `private` + original tetap dipakai sebagai wrapper,
    // supaya upload diproses SEBELUM action validasi+simpan (lihat postCreate/postUpdate).
    use ControllerTrait {
        postCreate as private traitPostCreate;
        postUpdate as private traitPostUpdate;
    }

    protected function share($data = [])
    {
        $default = [
            'model' => $this->model,
            'role' => RoleEnum::getOptions(),
            // ponytail: daftar SEMUA RS untuk checkbox hak akses (jangan discope).
            'allRs' => Rs::orderBy('rs_nama')->pluck('rs_nama', 'rs_id')->all(),
        ];

        return array_merge($default, $data);
    }

    public function getCreate(GeneralRequest $request)
    {
        return $this->views($this->template(), ['selectedRsIds' => []]);
    }

    public function getUpdate(GeneralRequest $request, $id)
    {
        $data = $this->model->findOrFail($id);

        return $this->views($this->template(), [
            'model' => $data,
            'selectedRsIds' => User::rsIdsFor((int) $id),
        ]);
    }

    public function __construct(User $model)
    {
        $this->model = $model::getModel();
    }

    // ponytail: UsersController::boot() sudah dihapus. Itu bukan lifecycle hook Laravel —
    // parent::saving() tidak ada di Controller (fatal kalau sampai dipanggil) dan karena
    // method-nya public, Route::auto() ikut membuat route sampah `user/boot` + `api/users/boot`.
    // Hashing password sudah ditangani cast 'password' => 'hashed' di App\Models\User.

    // ---- avatar helpers (same pattern as JenisLinenController) ----

    private function handleAvatar(GeneralRequest $request, ?string $existing): ?string
    {
        if ($request->hasFile('avatar')) {
            try {
                $path = uploadFile($request->file('avatar'), 'users', ['max_size' => 2048]);
                $this->deleteUserFile($existing);

                return $path;
            } catch (\InvalidArgumentException $e) {
                throw ValidationException::withMessages(['avatar' => $e->getMessage()]);
            }
        }

        if ($request->boolean('remove_avatar')) {
            $this->deleteUserFile($existing);

            return null;
        }

        return $existing;
    }

    private function deleteUserFile(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        $file = storage_path('app/public/'.$path);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public function postCreate(GeneralRequest $request)
    {
        $avatar = $this->handleAvatar($request, null);
        if ($avatar !== null) {
            $request->merge(['avatar' => $avatar]);
        }

        $payload = CreateAction::run($request, $this->model);
        if (($payload['status'] ?? false) && isset($payload['data']->id)) {
            $this->syncRsGuarded((int) $payload['data']->id, (array) $request->input('rs_ids', []));
        }

        return $this->response($payload, null, 'create');
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $existing = $this->model->findOrFail($id)->avatar ?? null;

        $avatar = $this->handleAvatar($request, $existing);
        if ($avatar !== $existing) {
            $request->merge(['avatar' => $avatar]);
        }

        $payload = UpdateAction::run($request, $id, $this->model);
        if ($payload['status'] ?? false) {
            $this->syncRsGuarded((int) $id, (array) $request->input('rs_ids', []));
        }

        return $this->response($payload, null, 'update');
    }

    /**
     * ponytail: mapping RS user hanya boleh diubah admin/developer, dan
     * non-admin tidak boleh memberi RS di luar miliknya sendiri.
     */
    private function syncRsGuarded(int $userId, array $rsIds): void
    {
        $me = auth()->user();
        if (! $me || ! in_array($me->role ?? null, ['admin', 'developer'], true)) {
            return;
        }

        User::syncRs($userId, $rsIds);
    }
}
