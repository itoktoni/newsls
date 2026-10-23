<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\BersihEntity;
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

class Bersih extends Model
{
    use ActiveTrait, ApiTrait, DataTableTrait, FilterQueryString, BersihEntity, OptionTrait, PowerJoins, Sanitizable, Sortable;

    protected $table = 'bersih';

    protected $primaryKey = 'bersih_id';

    protected $fillable = [
        'bersih_id',
        'bersih_rfid',
        'bersih_status',
        'bersih_id_rs',
        'bersih_id_ruangan',
        'bersih_barcode',
        'bersih_delivery',
        'bersih_created_at',
        'bersih_updated_at',
        'bersih_created_by',
        'bersih_updated_by',
        'bersih_report',
    ];

    public $sortable = [
        'bersih_nama',
        'bersih_deskripsi',
    ];

    protected $casts = [
        'bersih_id' => 'integer',
        'bersih_rfid' => 'string',
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

    public function has_detail()
    {
        return $this->hasOne(Detail::class, Detail::field_primary(), self::field_rfid());
    }

    public function has_ruangan()
    {
        return $this->hasOne(Ruangan::class, Ruangan::field_primary(), self::field_ruangan_id());
    }

    public function has_rs()
    {
        return $this->hasOne(Rs::class, Rs::field_primary(), self::field_rs_id());
    }

    public function has_user()
    {
        return $this->hasOne(User::class, User::field_primary(), self::field_created_by());
    }
}
