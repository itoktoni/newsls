<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\LogEntity;
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

class Logs extends Model
{
    use ActiveTrait, ApiTrait, DataTableTrait, FilterQueryString, LogEntity, OptionTrait, PowerJoins, Sanitizable, Sortable;

    protected $table = 'logs';

    protected $primaryKey = 'log_id';

    protected $fillable = [
        'log_id',
        'log_rfid',
        'log_id_rs',
        'log_id_jenis',
        'log_id_ruangan',
        'log_tanggal',
        'log_in',
        'log_out',
        'log_user',
        'log_check',
    ];

    public $sortable = [
        'log_nama',
        'log_deskripsi',
    ];

    protected $casts = [
        'log_id' => 'integer',
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
            DataBuilder::build($this->field_name())->name('Nama log Linen')->show()->sort(),
            DataBuilder::build($this->field_description())->name('Deskripsi')->show()->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }

    public function has_jenis()
    {
        return $this->hasOne(JenisLinen::class, JenisLinen::field_primary(), self::field_jenis());
    }

    public function has_ruangan()
    {
        return $this->hasOne(Ruangan::class, Ruangan::field_primary(), self::field_ruangan());
    }

    public function has_rs()
    {
        return $this->hasOne(Rs::class, Rs::field_primary(), self::field_rs());
    }

    public function has_user()
    {
        return $this->hasOne(User::class, User::field_primary(), self::field_user());
    }

}
