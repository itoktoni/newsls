<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\SupplierEntity;
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

class Supplier extends Model
{
    use ActiveTrait, ApiTrait, DataTableTrait, FilterQueryString, OptionTrait, PowerJoins, Sanitizable, Sortable, SupplierEntity;

    protected $table = 'supplier';

    protected $primaryKey = 'supplier_id';

    protected $fillable = [
        'supplier_id',
        'supplier_nama',
        'supplier_alamat',
        'supplier_phone',
        'supplier_kontak',
        'supplier_email',
    ];

    public $sortable = [
        'supplier_nama',
        'supplier_alamat',
    ];

    protected $casts = [
        'supplier_id' => 'integer',
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
            DataBuilder::build($this->field_name())->name('Nama Supplier')->show()->sort(),
            DataBuilder::build($this->field_contact())->name('Kontak')->show()->sort(),
            DataBuilder::build($this->field_email())->name('Email')->show()->sort(),
            DataBuilder::build($this->field_phone())->name('Telepon')->show()->sort(),
            DataBuilder::build($this->field_alamat())->name('Alamat')->show()->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }
}
