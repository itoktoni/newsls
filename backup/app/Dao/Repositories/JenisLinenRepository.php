<?php

namespace App\Dao\Repositories;

use App\Dao\Interfaces\CrudInterface;
use App\Dao\Models\Detail;
use App\Dao\Models\JenisLinen;
use App\Dao\Models\ViewTotalJenis;
use Illuminate\Support\Facades\DB;
use Plugins\Notes;

class JenisLinenRepository extends MasterRepository implements CrudInterface
{
    public function __construct()
    {
        $this->model = empty($this->model) ? new JenisLinen() : $this->model;
    }

    public function dataRepository()
    {
        $query = $this->model
            ->select($this->model->getSelectedField())
            ->addSelect(ViewTotalJenis::field_total())
            ->leftJoinRelationship('has_category')
            ->leftJoinRelationship('has_rs')
            ->leftJoinRelationship('has_total')
            ->sortable()->filter()
            ->orderBy('rs_nama', 'ASC')
            ->orderBy('jenis_nama', 'ASC');

        if (request()->hasHeader('authorization')) {
            if ($paging = request()->get('paginate')) {
                return $query->paginate($paging);
            }

            if (method_exists($this->model, 'getApiCollection')) {
                return $this->model->getApiCollection($query->get());
            }

            return Notes::data($query->get());
        }

        $query = env('PAGINATION_SIMPLE') ? $query->simplePaginate(env('PAGINATION_NUMBER')) : $query->paginate(env('PAGINATION_NUMBER'));

        return $query;
    }

    public function getParstok()
    {
        $query = Detail::query()
            ->addSelect(['*', DB::raw('count(config_linen.detail_rfid) as qty')])
            ->join('config_linen', 'detail_linen.detail_rfid', '=', 'config_linen.detail_rfid')
            ->join('rs', 'rs.rs_id', '=', 'config_linen.rs_id')
            ->leftJoinRelationship('has_jenis')
            ->groupBy(Detail::field_jenis_id())
            ->filter();

        return $query;
    }
}
