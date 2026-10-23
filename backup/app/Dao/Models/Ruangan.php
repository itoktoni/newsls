<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\RuanganEntity;
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

class Ruangan extends Model
{
    use ActiveTrait, ApiTrait, DataTableTrait, FilterQueryString, OptionTrait, PowerJoins, RuanganEntity, Sanitizable, Sortable;

    protected $table = 'ruangan';

    protected $primaryKey = 'ruangan_id';

    protected $fillable = [
        'ruangan_id',
        'ruangan_nama',
        'ruangan_deskripsi',
        'ruangan_code',
    ];

    public $sortable = [
        'ruangan_nama',
        'ruangan_deskripsi',
    ];

    protected $casts = [
        'ruangan_id' => 'integer',
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
            DataBuilder::build($this->field_name())->name('Nama Ruangan')->show()->sort(),
            DataBuilder::build($this->field_description())->name('Deskripsi')->show()->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }

    public function has_instansi()
    {
        return $this->belongsToMany(Instansi::class, 'instansi_Ruangan', 'ruangan_id', 'instansi_id');
    }
}
