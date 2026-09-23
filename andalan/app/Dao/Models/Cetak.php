<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\CetakEntity;
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

class Cetak extends Model
{
    use ActiveTrait, ApiTrait, CetakEntity, DataTableTrait, FilterQueryString, OptionTrait, PowerJoins, Sanitizable, Sortable;

    protected $table = 'cetak';

    protected $primaryKey = 'cetak_id';

    protected $fillable = [
        'cetak_id',
        'cetak_code',
        'cetak_date',
        'cetak_user',
        'cetak_id_ruangan',
        'cetak_id_rs',
        'cetak_type',
        'cetak_barcode',
        'cetak_delivery',
    ];

    public $sortable = [
        'cetak_code',
        'cetak_user',
        'cetak_date',
        'cetak_user',
    ];

    protected $casts = [
        'cetak_date' => 'date:Y-m-d',
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
            DataBuilder::build($this->field_name())->name('Nama Kategori Linen')->show()->sort(),
            DataBuilder::build($this->field_description())->name('Deskripsi')->show()->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }

    public function has_rs()
    {
        return $this->hasOne(Rs::class, Rs::field_primary(), $this->field_rs_id());
    }

    public function has_ruangan()
    {
        return $this->hasOne(Ruangan::class, Ruangan::field_primary(), $this->field_ruangan_id());
    }
}
