<?php

namespace App\Http\Services;

use App\Dao\Models\User;
use GeoSot\EnvEditor\Facades\EnvEditor as EnvEditor;
use Illuminate\Support\Facades\Auth;
use Plugins\Alert;

class UpdateProfileService
{
    public function save($data)
    {
        $check = false;
        try {

            User::find(Auth::user()->id)->update([
                'name' => $data->name,
                'username' => $data->username,
                'phone' => $data->phone,
                'email' => $data->email,
                'password' => bcrypt(request()->get('password')),
            ]);

            Alert::update();

        } catch (\Throwable $th) {
            Alert::error($th->getMessage());

            return $th->getMessage();
        }

        return $check;
    }
}
