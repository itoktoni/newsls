<?php

namespace App\Http\Services;

use App\Dao\Enums\LogType;
use App\Dao\Interfaces\CrudInterface;
use App\Dao\Models\History;
use Illuminate\Database\Eloquent\Model;
use Plugins\Alert;

class DeleteService
{
    public function delete(CrudInterface $repository, $code)
    {
        $rules = ['code' => 'required'];
        request()->validate($rules, ['code.required' => 'Please select any data !']);

        $log = [];
        $date = now();

        foreach($code as $key){
            $log[] = [
                History::field_rs_id() => 0,
                History::field_name() => $key,
                History::field_status() => LogType::DELETE_RFID,
                History::field_created_by() => auth()->user()->name,
                History::field_created_at() => $date,
                History::field_description() => "user mendelete rfid ".$key,
            ];
        }

        Model::insert($log);

        $check = $repository->deleteRepository($code);

        if ($check['status']) {
            Alert::delete();
        } else {
            Alert::error($check['message']);
        }

        if (request()->wantsJson()) {

            return response()->json($check)->getData();
        }

        return $check;
    }
}
