<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\KategoriEntity;
use App\Dao\Entities\PendingEntity;
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

class Pending extends Model
{
    use ActiveTrait, ApiTrait, DataTableTrait, FilterQueryString, PendingEntity, OptionTrait, PowerJoins, Sanitizable, Sortable;

    protected $table = 'pending';

    protected $primaryKey = 'pending_id';

    protected $fillable = [
        'pending_rfid',
        'pending_key',
        'pending_id_rs',
        'pending_id_ruangan',
        'pending_id_jenis',
        'pending_created_at',
        'pending_updated_at',
        'pending_kotor_at',
        'pending_bersih_at',
        'pending_created_by',
        'pending_updated_by',
        'pending_kotor_by',
        'pending_bersih_by',
        'pending_delivery',
        'pending_transaksi',
        'pending_proses',
    ];

    public $sortable = [
        'pending_nama',
        'pending_deskripsi',
    ];

    protected $casts = [
        'pending_id' => 'integer',
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
}
