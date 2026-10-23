<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\ConfigEntity;
use App\Dao\Entities\ConfigLinenEntity;
use App\Dao\Entities\RegisterEntity;
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

class ConfigLinen extends Model
{
    use ActiveTrait, ApiTrait, DataTableTrait, FilterQueryString, OptionTrait, PowerJoins, ConfigLinenEntity, Sanitizable, Sortable;

    protected $table = 'config_linen';

    protected $primaryKey = 'detail_rfid';

    protected $fillable = [
        'rs_id',
        'detail_rfid',
    ];

    public $sortable = [

    ];

    protected $casts = [
        'rs_id' => 'integer',
        'detail_rfid' => 'string',
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
        $data = [
            DataBuilder::build($this->field_primary())->name('ID')->width(20)->sort(),
            DataBuilder::build($this->field_name())->name('Nama Rumah Sakit')->show()->sort(),
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
        return $this->hasMany(Detail::class, Detail::field_rs_id(), $this->field_primary());
    }

    public function has_jenis()
    {
        return $this->hasMany(JenisLinen::class, JenisLinen::field_rs_id(), $this->field_primary());
    }
}
