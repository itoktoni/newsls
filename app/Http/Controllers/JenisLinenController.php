<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\JenisLinen;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class JenisLinenController extends Controller
{
    use ControllerTrait {
        postCreate as private traitPostCreate;
        postUpdate as private traitPostUpdate;
    }

    public function __construct(JenisLinen $model)
    {
        $this->model = $model::getModel();
    }

    protected function share($data = [])
    {
        $default = [
            'model' => $this->model,
            'kategori' => Kategori::getOptions(),
            // ponytail: dropdown RS form ikut hak akses user (pivot rs_dan_user).
            'rs' => User::rsOptions(),
        ];

        return array_merge($default, $data);
    }

    public function postCreate(GeneralRequest $request)
    {
        $gambar = $this->handleGambar($request, null);
        if ($gambar !== null) {
            $request->merge(['jenis_gambar' => $gambar]);
        }

        return $this->traitPostCreate($request);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $existing = $this->model->findOrFail($id)->jenis_gambar ?? null;

        $gambar = $this->handleGambar($request, $existing);
        if ($gambar !== $existing) {
            $request->merge(['jenis_gambar' => $gambar]);
        }

        return $this->traitPostUpdate($request, $id);
    }

    private function handleGambar(GeneralRequest $request, ?string $existing): ?string
    {
        if ($request->hasFile('jenis_gambar')) {
            try {
                $path = uploadFile($request->file('jenis_gambar'), 'jenis', ['max_size' => 2048]);
                $this->deleteJenisFile($existing);

                return $path;
            } catch (\InvalidArgumentException $e) {
                throw ValidationException::withMessages(['jenis_gambar' => $e->getMessage()]);
            }
        }

        if ($request->boolean('remove_jenis_gambar')) {
            $this->deleteJenisFile($existing);

            return null;
        }

        return $existing;
    }

    private function deleteJenisFile(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        $file = storage_path('app/public/'.$path);
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
