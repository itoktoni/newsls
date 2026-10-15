<?php

namespace App\Dao\Repositories;

use App\Dao\Interfaces\CrudInterface;
use App\Dao\Models\ConfigLinen;
use App\Dao\Models\Detail;
use App\Dao\Models\Rs;
use App\Dao\Models\ViewDetailLinen;
use Illuminate\Support\Facades\DB;
use Plugins\Notes;

class ConfigRepository extends MasterRepository implements CrudInterface
{
    public function __construct()
    {
        $this->model = empty($this->model) ? new ConfigLinen() : $this->model;
    }

    public function dataRepository()
    {
        $query = $this->model
            ->select([
                'view_detail_linen.*',
                'rs.rs_nama',
                DB::raw('config_linen.detail_rfid as view_rfid'),
            ])
            ->leftJoin(Rs::getTableName(), 'config_linen.rs_id', '=', 'rs.rs_id')
            // ->leftJoin(Detail::getTableName(), function($sql){
            //     $sql->on('config_linen.detail_rfid', '=', 'detail_linen.detail_rfid');
            //     $sql->on('config_linen.rs_id', '=', 'detail_linen.detail_id_rs');
            // })
            ->leftJoin('view_detail_linen', 'view_linen_rfid', '=', 'config_linen.detail_rfid')
            ->sortable()->filter();

        if (request()->hasHeader('authorization')) {
            if ($paging = request()->get('paginate')) {
                return $query->paginate($paging);
            }

            if (method_exists($this->model, 'getApiCollection')) {
                return $this->model->getApiCollection($query->get());
            }

            return Notes::data($query->get());
        }

        return $query;
    }
}
