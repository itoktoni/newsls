<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\SystemGroupEntity;
use App\Dao\Traits\ActiveTrait;
use App\Dao\Traits\DataTableTrait;
use App\Dao\Traits\OptionTrait;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Mehradsadeghi\FilterQueryString\FilterQueryString as FilterQueryString;
use Touhidurabir\ModelSanitize\Sanitizable as Sanitizable;

class SystemGroup extends Model
{
    use ActiveTrait, ActiveTrait, DataTableTrait, FilterQueryString, OptionTrait, Sanitizable, Sortable, SystemGroupEntity;

    protected $table = 'system_group';

    protected $primaryKey = 'system_group_code';

    protected $keyType = 'string';

    protected $fillable = [
        'system_group_code',
        'system_group_name',
        'system_group_sort',
        'system_group_enable',
        'system_group_url',
        'system_group_icon',
        'system_group_description',
    ];

    public $sortable = [
        'system_group_code',
        'system_group_name',
        'system_group_sort',
    ];

    protected $casts = [
        'system_group_sort' => 'integer',
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
            DataBuilder::build($this->field_primary())->name('Code'),
            DataBuilder::build($this->field_name())->name('Name')->sort(),
            DataBuilder::build($this->field_icon())->name('Icon')->sort(),
            DataBuilder::build($this->field_url())->name('Url')->sort(false),
            DataBuilder::build($this->field_sort())->name('Sort')->sort()->class('column-active'),
            DataBuilder::build($this->field_enable())->name('Enable')->show(false),
        ];
    }

    public function has_menu()
    {
        return $this->belongsToMany(SystemMenu::class, 'system_group_connection_menu', 'system_group_code', 'system_menu_code');
    }

    public function has_role()
    {
        return $this->belongsToMany(SystemRole::class, 'system_group_connection_role', 'system_group_code', 'system_role_code');
    }
}
