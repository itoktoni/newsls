<?php

namespace App\Http\Services;

use App\Dao\Enums\LogType;
use App\Dao\Interfaces\CrudInterface;
use App\Dao\Models\Bersih;
use App\Dao\Models\ConfigLinen;
use App\Dao\Models\Detail;
use App\Dao\Models\History;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Outstanding;
use App\Dao\Models\Rs;
use App\Dao\Models\Transaksi;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Plugins\Alert;
use Plugins\Notes;

class UpdateDetailService
{
    public function update(CrudInterface $repository, $request, $code)
    {
        DB::beginTransaction();

        try {

            $data = $request->all();
            $check = $repository->updateRepository($data, $code);

            $det = [
                Detail::field_primary() => $data[Detail::field_primary()],
            ];

            if(!empty($data['rfid_lama']))
            {
                $det = array_merge($det, [
                    'detail_lama' => $data['rfid_lama'],
                    'detail_pengantian_user' => auth()->user()->id,
                    'detail_pengantian_waktu' => date('Y-m-d H:i:s'),
                ]);
            }

            Detail::where(Detail::field_primary(), $code)->update($det);

            Outstanding::where(Outstanding::field_primary(), $code)->update([
                Outstanding::field_primary() => $data[Detail::field_primary()],
                Outstanding::field_ruangan_id() => $data[Detail::field_ruangan_id()],
            ]);

            Transaksi::where(Transaksi::field_rfid(), $code)->update([
                Transaksi::field_rfid() => $data[Detail::field_primary()],
                Transaksi::field_ruangan_id() => $data[Detail::field_ruangan_id()],
            ]);

            ConfigLinen::where(Detail::field_primary(), $code)
                ->where(Rs::field_primary(), $data[Detail::field_rs_id()])
                ->update([
                    Detail::field_primary() => $data[Detail::field_primary()],
                ]);

            Bersih::where(Bersih::field_rfid(), $code)->update([
                Bersih::field_rfid() => $data[Detail::field_primary()],
                Bersih::field_ruangan_id() => $data[Detail::field_ruangan_id()],
            ]);

            History::where(History::field_name(), $code)->update([
                History::field_name() => $data[Detail::field_primary()],
            ]);

            $test = History::create([
                History::field_rs_id() => $data[Detail::field_rs_id()],
                History::field_name() => Detail::field_primary(),
                History::field_status() => LogType::GANTI_LINEN,
                History::field_created_by() => auth()->user()->name,
                History::field_created_at() => date('Y-m-d H:i:s'),
                History::field_description() => json_encode(['lama' => $data['rfid_lama'], 'rfid_baru' => Detail::field_primary()]),
            ]);

            OpnameDetail::where(OpnameDetail::field_rfid(), $code)->update([
                OpnameDetail::field_rfid() => $data[Detail::field_primary()],
            ]);

            if (request()->wantsJson()) {
                return response()->json($check)->getData();
            }

            Alert::update();

            DB::commit();

            return $check;

        }

        catch (\Throwable $th) {

            if ($th instanceof QueryException)
            {
                Alert::error("No RFID yang di input (" . request()->get('detail_rfid').") Tidak boleh Duplikat di database !");
            }
            else
            {
                Alert::error($th->getMessage());
            }

            DB::rollBack();

            return Notes::error($th->getMessage());
        }

        return $check;
    }
}
