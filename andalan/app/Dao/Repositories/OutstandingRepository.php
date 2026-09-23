<?php

namespace App\Dao\Repositories;

use App\Dao\Interfaces\CrudInterface;
use App\Dao\Models\Outstanding;
use App\Dao\Models\ViewOutstanding;
use Illuminate\Support\Facades\DB;
use Plugins\Notes;

class OutstandingRepository extends MasterRepository implements CrudInterface
{
    public function __construct()
    {
        $this->model = empty($this->model) ? new Outstanding() : $this->model;
    }

    public function dataRepository()
    {
        $query = ViewOutstanding::query()
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

        $query = $query->paginate(env('PAGINATION_NUMBER'));

        return $query;
    }

    public function getPrint()
    {
        $query = ViewOutstanding::query()
            ->joinRelationship('has_outstanding')
            ->joinRelationship('has_detail')
            ->addSelect([
                'view_outstanding.*',
                'outstanding_pending_created_at',
                'outstanding_hilang_created_at',
                'view_tanggal_create',
                'view_transaksi_bersih_total',
                'view_status_proses',
            ])
            ->sortable()->filter();

        return $query;
    }
}
