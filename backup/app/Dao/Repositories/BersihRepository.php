<?php

namespace App\Dao\Repositories;

use App\Dao\Interfaces\CrudInterface;
use App\Dao\Models\Bersih;
use App\Dao\Models\JenisLinen;
use App\Dao\Models\ViewBersih;
use Plugins\Notes;

class BersihRepository extends MasterRepository implements CrudInterface
{
    public function __construct()
    {
        $this->model = empty($this->model) ? new Bersih() : $this->model;
    }

    public function dataRepository()
    {
        $query = ViewBersih::query()
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


    public function getReport()
    {
        return ViewBersih::query()
            ->orderBy(JenisLinen::field_name(), 'ASC')
            ->filter();
    }
}
