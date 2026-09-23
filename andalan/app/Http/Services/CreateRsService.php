<?php

namespace App\Http\Services;

use App\Dao\Interfaces\CrudInterface;
use Plugins\Alert;

class CreateRsService
{
    public function save(CrudInterface $repository, $data)
    {
        $check = false;
        try {
            $check = $repository->saveRepository($data);
            if (isset($check['status']) && $check['status']) {
                $check['data']->has_ruangan()->sync($data['ruangan']);
                $check['data']->has_jenis()->sync($data['jenis']);

                if (request()->wantsJson()) {
                    return response()->json($check)->getData();
                }
                Alert::create();
            } else {
                $message = env('APP_DEBUG') ? $check['data'] : $check['message'];
                Alert::error($message);
            }
        } catch (\Throwable $th) {
            Alert::error($th->getMessage());

            return $th->getMessage();
        }

        return $check;
    }
}
