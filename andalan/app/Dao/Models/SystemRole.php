<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\SystemRoleEntity;
use App\Dao\Traits\ActiveTrait;
use App\Dao\Traits\DataTableTrait;
use App\Dao\Traits\OptionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Kyslik\ColumnSortable\Sortable;
use Mehradsadeghi\FilterQueryString\FilterQueryString as FilterQueryString;
use Touhidurabir\ModelSanitize\Sanitizable as Sanitizable;

class SystemRole extends Model
{
    use ActiveTrait, ActiveTrait, DataTableTrait, FilterQueryString, OptionTrait, Sanitizable, Sortable, SystemRoleEntity;

    protected $table = 'system_role';

    protected $primaryKey = 'system_role_code';

    protected $keyType = 'string';

    protected $fillable = [
        'system_role_code',
        'system_role_name',
        'system_role_level',
        'system_role_description',
    ];

    public $sortable = [
        'system_role_code',
        'system_role_name',
        'system_role_level',
        'system_role_description',
    ];

    protected $casts = [
        'system_role_code' => 'string',
    ];

    protected $filters = [
        'filter',
    ];

    public $timestamps = false;

    public $incrementing = false;

    public function fieldSearching()
    {
        return $this->field_name();
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build($this->field_primary())->name('Code')->sort(),
            DataBuilder::build($this->field_name())->name('Name')->show(true)->sort(),
            DataBuilder::build($this->field_level())->name('Level')->show(true)->sort(),
            DataBuilder::build($this->field_description())->name('Description')->show(true),
        ];
    }

    public function has_group()
    {
        return $this->belongsToMany(SystemGroup::class, 'system_group_connection_role', 'system_role_code', 'system_group_code');
    }

    public static function boot()
    {
        parent::creating(function ($model) {
            if (empty($model->{SystemRole::field_primary()})) {
                $model->{SystemRole::field_primary()} = Str::camel($model->{SystemRole::field_name()});
            }
        });
        parent::boot();
    }
}
