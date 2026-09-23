<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\OpnameEntity;
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
use Wildside\Userstamps\Userstamps;

class Opname extends Model
{
    use ActiveTrait, ApiTrait, DataTableTrait, FilterQueryString, OpnameEntity, OptionTrait, PowerJoins, Sanitizable, Sortable, Userstamps;

    protected $table = 'opname';

    protected $primaryKey = 'opname_id';

    protected $fillable = [
        'opname_id',
        'opname_nama',
        'opname_mulai',
        'opname_selesai',
        'opname_status',
        'opname_id_rs',
        'opname_created_at',
        'opname_created_by',
        'opname_updated_at',
        'opname_updated_by',
        'opname_capture',
    ];

    public $sortable = [
        'opname_status',
        'opname_created_at',
        'opname_mulai',
        'opname_id',
        'opname_selesai',
    ];

    protected $casts = [
        'opname_id_rs' => 'int',
        'opname_id' => 'int',
        'opname_status' => 'int',
    ];

    protected $filteopname = [
        'filter',
    ];

    const CREATED_AT = 'opname_created_at';

    const UPDATED_AT = 'opname_updated_at';

    const CREATED_BY = 'opname_created_by';

    const UPDATED_BY = 'opname_updated_by';

    public $timestamps = true;

    public $incrementing = true;

    public function fieldSearching()
    {
        return $this->field_name();
    }

    public function fieldDatatable(): array
    {
        $data = [
            DataBuilder::build($this->field_primary())->name('ID Opname')->width('100px')->sort(),
            DataBuilder::build(Rs::field_name())->name('Nama RS')->width(20),
            DataBuilder::build($this->field_name())->name('Keterangan Opname')->width('250px')->show(false),
            DataBuilder::build($this->field_created_at())->name('Tgl Buat')->show()->sort(),
            DataBuilder::build($this->field_start())->name('Tgl Mulai')->show()->sort(),
            DataBuilder::build($this->field_end())->name('Tgl Selesai')->show()->sort(),
            DataBuilder::build($this->field_capture())->name('Waktu Capture')->show()->sort(),
            DataBuilder::build($this->field_status())->name('Status')->show()->sort(),
        ];

        return $data;
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }

    public function has_detail()
    {
        return $this->hasMany(OpnameDetail::class, OpnameDetail::field_opname(), $this->field_primary());
    }

    public function has_rs()
    {
        return $this->hasOne(Rs::class, Rs::field_primary(), self::field_rs_id());
    }
}
