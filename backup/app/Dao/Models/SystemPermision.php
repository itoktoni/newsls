<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\SystemPermisionEntity;
use App\Dao\Traits\ActiveTrait;
use App\Dao\Traits\DataTableTrait;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Mehradsadeghi\FilterQueryString\FilterQueryString as FilterQueryString;
use Plugins\Core;
use Touhidurabir\ModelSanitize\Sanitizable as Sanitizable;

class SystemPermision extends Model
{
    use ActiveTrait, ActiveTrait, DataTableTrait, FilterQueryString, Sanitizable, Sortable, SystemPermisionEntity;

    protected $table = 'system_permision';

    protected $primaryKey = 'system_permision_id';

    protected $fillable = [
        'system_permision_id',
        'system_permision_role',
        'system_permision_code',
        'system_permision_controller',
        'system_permision_module',
        'system_permision_user',
        'system_permision_level',
    ];

    public $sortable = [
        'system_permision_role_code',
        'system_permision_code',
        'system_permision_controller',
        'system_permision_module',
    ];

    protected $filters = [
        'filter',
    ];

    public $timestamps = false;

    public $incrementing = false;

    public function fieldSearching()
    {
        return $this->field_code();
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build($this->field_primary())->name('ID')->show(false),
            DataBuilder::build($this->field_role())->name('Role'),
            DataBuilder::build($this->field_module())->name('Module'),
            DataBuilder::build($this->field_code())->name('Code'),
            DataBuilder::build($this->field_level())->name('Level'),
            DataBuilder::build($this->field_user())->name('User'),
        ];
    }

    public function has_user()
    {
        return $this->hasOne(User::class, User::field_primary(), self::field_user());
    }

    public static function boot()
    {
        parent::saving(function ($model) {
            $model->{self::field_module()} = Core::getControllerName($model->{self::field_controller()});
        });
        parent::boot();
    }
}
