<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\JenisBahanEntity;
use App\Dao\Traits\ActiveTrait;
use App\Dao\Traits\ApiTrait;
use App\Dao\Traits\DataTableTrait;
use App\Dao\Traits\OptionTrait;
use App\Http\Resources\GeneralResource;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use Kyslik\ColumnSortable\Sortable;
use Mehradsadeghi\FilterQueryString\FilterQueryString as FilterQueryString;
use Touhidurabir\ModelSanitize\Sanitizable as Sanitizable;

class JenisBahan extends Model
{
    use ActiveTrait, ApiTrait, DataTableTrait, FilterQueryString, JenisBahanEntity, OptionTrait, PowerJoins, Sanitizable, Sortable;

    protected $table = 'jenis_bahan';

    protected $primaryKey = 'bahan_id';

    protected $fillable = [
        'bahan_id',
        'bahan_nama',
        'bahan_deskripsi',
    ];

    public $sortable = [
        'bahan_nama',
        'bahan_deskripsi',
    ];

    protected $casts = [
        'bahan_id' => 'integer',
    ];

    protected $filters = [
        'filter',
    ];

    public $timestamps = false;

    public $incrementing = true;

    public function fieldSearching()
    {
        return $this->field_name();
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build($this->field_primary())->name('ID')->width(20)->sort(),
            DataBuilder::build($this->field_name())->name('Nama Bahan')->show()->sort(),
            DataBuilder::build($this->field_description())->name('Deskripsi')->show()->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }
}
