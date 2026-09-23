<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\OutstandingEntity;
use App\Dao\Enums\UserLevel;
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

class Outstanding extends Model
{
    use ActiveTrait, ApiTrait, DataTableTrait, FilterQueryString, OptionTrait, OutstandingEntity, PowerJoins, Sanitizable, Sortable;

    protected $table = 'outstanding';

    protected $primaryKey = 'outstanding_rfid';

    protected $fillable = [
        'outstanding_rfid',
        'outstanding_key',
        'outstanding_rs_ori',
        'outstanding_rs_scan',
        'outstanding_id_ruangan',
        'outstanding_id_jenis',
        'outstanding_status_transaksi',
        'outstanding_status_proses',
        'outstanding_status_hilang',
        'outstanding_created_at',
        'outstanding_updated_at',
        'outstanding_pending_created_at',
        'outstanding_pending_updated_at',
        'outstanding_hilang_created_at',
        'outstanding_hilang_updated_at',
        'outstanding_created_by',
        'outstanding_updated_by',
    ];

    public $sortable = [
        'outstanding_rfid',
        'outstanding_key',
    ];

    protected $casts = [
        'outstanding_created_at' => 'datetime',
        'outstanding_rs_ori' => 'integer',
        'outstanding_rs_scan' => 'integer',
    ];

    protected $filters = [
        'filter',
        'view_rs_id',
    ];

    public function view_rs_id($query, $value)
    {
        return $query->where('view_rs_ori_id', $value);
    }

    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    public function fieldSearching()
    {
        return $this->field_name();
    }

    public function fieldDatatable(): array
    {
        $data = [
            DataBuilder::build($this->field_primary())->name('ID')->width(20)->sort(),
        ];

        return $data;
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }

    public function has_ruangan()
    {
        return $this->belongsToMany(Ruangan::class, 'rs_dan_ruangan', Outstanding::field_primary(), Ruangan::field_primary());
    }

    public function has_rfid()
    {
        return $this->hasMany(Detail::class, Detail::field_primary(), $this->field_primary());
    }

    public function has_jenis()
    {
        return $this->hasMany(JenisLinen::class, JenisLinen::field_primary(), $this->field_jenis_id());
    }

    public function has_view()
    {
        return $this->hasOne(ViewOutstanding::class, ViewOutstanding::field_primary(), $this->field_primary());
    }

    public function has_pending()
    {
        return $this->hasOne(Pending::class, Pending::field_name(), $this->field_primary());
    }
}
