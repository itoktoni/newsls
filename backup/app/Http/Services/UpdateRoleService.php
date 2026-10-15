<?php

namespace App\Http\Services;

use App\Dao\Interfaces\CrudInterface;
use Illuminate\Support\Facades\Session;
use Plugins\Alert;

class UpdateRoleService
{
    public function update(CrudInterface $repository, $data, $code)
    {
        $check = $repository->updateRepository($data->all(), $code);
        if ($check['status']) {
            $check['data']->has_group()->sync($data->group);
            Session::forget('groups');
            if (request()->wantsJson()) {
                return response()->json($check)->getData();
            }
            Alert::update();
        } else {
            Alert::error($check['message']);
        }

        return $check;
    }
}
