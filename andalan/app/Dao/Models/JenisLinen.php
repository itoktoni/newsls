<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\JenisLinenEntity;
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

class JenisLinen extends Model
{
    use ActiveTrait, ApiTrait, DataTableTrait, FilterQueryString, JenisLinenEntity, OptionTrait, PowerJoins, PowerJoins, Sanitizable, Sortable;

    protected $table = 'jenis_linen';

    protected $primaryKey = 'jenis_id';

    protected $fillable = [
        'jenis_id',
        'jenis_id_kategori',
        'jenis_nama',
        'jenis_deskripsi',
        'jenis_id_rs',
        'jenis_gambar',
        'jenis_berat',
    ];

    public $sortable = [
        'jenis_nama',
        'jenis_berat',
        'jenis_deskripsi',
    ];

    protected $casts = [
        'jenis_id' => 'integer',
        'jenis_id_kategori' => 'integer',
    ];

    protected $filters = [
        'filter',
        'jenis_id_kategori',
        'jenis_id',
        'jenis_nama',
    ];

    // protected $with = ['has_category'];

    public $timestamps = false;

    public $incrementing = true;

    public function fieldSearching()
    {
        return $this->field_name();
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build($this->field_primary())->name('ID')->show(false)->width(20)->sort(),
            DataBuilder::build(Kategori::field_name())->name('Kategori')->width('100px')->show()->sort(),
            DataBuilder::build($this->field_name())->name('Nama Linen')->show()->sort(),
            DataBuilder::build($this->field_weight())->name('Berat')->show()->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }

    public function has_category()
    {
        return $this->hasOne(Kategori::class, Kategori::field_primary(), self::field_category_id());
    }

    public function has_rs()
    {
        return $this->hasOne(Rs::class, Rs::field_primary(), self::field_rs_id());
    }

    public function has_supplier()
    {
        return $this->hasOne(Supplier::class, Supplier::field_primary(), self::field_supplier_id());
    }

    public function has_bahan()
    {
        return $this->hasOne(JenisBahan::class, JenisBahan::field_primary(), self::field_bahan_id());
    }

    public function has_detail()
    {
        return $this->hasMany(Detail::class, Detail::field_jenis_id(), self::field_rs_id());
    }

    public function has_total()
    {
        return $this->hasOne(ViewTotalJenis::class, ViewTotalJenis::field_primary(), self::field_primary());
    }

    public static function boot()
    {
        parent::saving(function ($model) {

            if (request()->has('upload')) {
                $file_upload = request()->file('upload');
                $extension = $file_upload->extension();
                $name = time().'.'.$extension;

                $file_upload->storeAs('/public/jenis/', $name);

                $model->{$model->field_image()} = $name;
            }

        });

        parent::boot();
    }
}
