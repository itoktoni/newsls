<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\GantiChipEntity;
use App\Dao\Traits\ApiTrait;
use App\Http\Resources\GeneralResource;
use Illuminate\Database\Eloquent\Model;

class GantiChip extends Model
{
    use ApiTrait, GantiChipEntity;

    protected $table = 'ganti_chip';

    protected $primaryKey = 'ganti_id';

    protected $fillable = [
        'ganti_id',
        'ganti_rfid_lama',
        'ganti_rfid_baru',
        'ganti_tanggal',
        'ganti_by',
        'ganti_keterangan',
    ];

    protected $casts = [
        'ganti_rfid_lama' => 'string',
        'ganti_rfid_baru' => 'string',
        'ganti_tanggal' => 'datetime',
    ];

    public $timestamps = false;

    public $incrementing = true;

    public function fieldSearching()
    {
        return $this->field_baru();
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build($this->field_lama())->name('RFID Lama')->show()->sort(),
            DataBuilder::build($this->field_baru())->name('RFID Baru')->show()->sort(),
            DataBuilder::build($this->field_keterangan())->name('Keterangan')->show()->sort(),
            DataBuilder::build($this->field_tanggal())->name('Tanggal')->show()->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }

    public function has_detail()
    {
        return $this->hasOne(Detail::class, Detail::field_primary(), self::field_baru());
    }

    public function has_user()
    {
        return $this->hasOne(User::class, User::field_primary(), self::field_by());
    }
}
