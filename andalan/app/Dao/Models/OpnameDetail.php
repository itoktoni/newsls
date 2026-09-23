<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\OpnameDetailEntity;
use App\Dao\Traits\ActiveTrait;
use App\Dao\Traits\ApiTrait;
use App\Dao\Traits\DataTableTrait;
use App\Dao\Traits\OptionTrait;
use App\Http\Resources\GeneralResource;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use Mehradsadeghi\FilterQueryString\FilterQueryString as FilterQueryString;
use Touhidurabir\ModelSanitize\Sanitizable as Sanitizable;
use Wildside\Userstamps\Userstamps;

class OpnameDetail extends Model
{
    use ActiveTrait, ApiTrait, DataTableTrait, FilterQueryString, OpnameDetailEntity, OptionTrait, PowerJoins, Sanitizable, Userstamps;

    protected $table = 'opname_detail';

    protected $primaryKey = 'opname_detail_id';

    const CREATED_AT = 'opname_detail_created_at';

    const UPDATED_AT = 'opname_detail_updated_at';

    const CREATED_BY = 'opname_detail_created_by';

    const UPDATED_BY = 'opname_detail_updated_by';

    protected $fillable = [
        'opname_detail_id',
        'opname_detail_id_opname',
        'opname_detail_code',
        'opname_detail_rfid',
        'opname_detail_waktu',
        'opname_detail_status',
        'opname_detail_transaksi',
        'opname_detail_hilang',
        'opname_detail_proses',
        'opname_detail_ketemu',
        'opname_detail_register',
        'opname_detail_created_at',
        'opname_detail_updated_at',
        'opname_detail_created_by',
        'opname_detail_updated_by',
        'opname_detail_pending_at',
        'opname_detail_hilang_at',
        'opname_detail_scan_rs',
        'opname_detail_reff',
        'opname_detail_scan_by'
    ];

    protected $casts = [
        'opname_detail_rfid' => 'string',
        'opname_detail_status' => 'int',
        'opname_detail_transaksi' => 'string',
        'opname_detail_proses' => 'string',
        'opname_detail_hilang' => 'string',
        'opname_detail_ketemu' => 'int',
        'opname_detail_blm_register' => 'int',
        'opname_detail_created_at' => 'date',
        'opname_detail_updated_at' => 'date',
        'opname_detail_hilang_at' => 'date',
        'opname_detail_pending_at' => 'date',
    ];

    protected $filters = [
        'filter',
    ];

    public $timestamps = true;

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

    public function has_view()
    {
        return $this->hasOne(ViewDetailLinen::class, ViewDetailLinen::field_primary(), self::field_rfid());
    }

    public function has_master()
    {
        return $this->hasOne(Opname::class, Opname::field_primary(), self::field_opname());
    }

    public function has_user()
    {
        return $this->hasOne(User::class, User::field_primary(), self::field_updated_by());
    }
}
