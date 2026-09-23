<?php

namespace App\Http\Services;

use App\Dao\Interfaces\CrudInterface;
use Illuminate\Support\Facades\DB;
use Plugins\Alert;

class UpdateJenisService
{
    public function update(CrudInterface $repository, $data, $code)
    {
        $check = $repository->updateRepository($data, $code);
        if ($check['status']) {

            foreach($data['jenis'] as $jenis){
                $update = DB::table('rs_dan_jenis')->where('rs_id', $jenis['rs_id'])
                    ->where('jenis_id', $jenis['jenis_id']);

                if($update->count() > 0){
                    $update->update(['parstock' => $jenis['parstock']]);
                } else{
                    DB::table('rs_dan_jenis')->insert($jenis);
                }
            }

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
