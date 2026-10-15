<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\UserEntity;
use App\Dao\Traits\ActiveTrait;
use App\Dao\Traits\DataTableTrait;
use App\Dao\Traits\OptionTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kirschbaum\PowerJoins\PowerJoins;
use Kyslik\ColumnSortable\Sortable;
use Laravel\Sanctum\HasApiTokens;
use Mehradsadeghi\FilterQueryString\FilterQueryString as FilterQueryString;
use Touhidurabir\ModelSanitize\Sanitizable as Sanitizable;

class User extends Authenticatable
{
    use ActiveTrait, DataTableTrait, FilterQueryString, HasApiTokens, Notifiable, OptionTrait, PowerJoins, Sanitizable, Sortable, UserEntity;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'password',
        'username',
        'role',
        'level',
        'vendor',
        'active',
    ];

    public $sortable = [
        'name',
        'email',
        'roles.role_name',
    ];

    protected $filters = [
        'filter',
        'name',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
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
            DataBuilder::build($this->field_primary())->name('ID')->show(false),
            DataBuilder::build($this->field_name())->name('Name')->sort(),
            DataBuilder::build($this->field_username())->name('Username')->sort(),
            DataBuilder::build(SystemRole::field_name())->name('Role'),
            DataBuilder::build($this->field_email())->show(false)->name('Email'),
            DataBuilder::build($this->field_phone())->name('Phone'),
            DataBuilder::build($this->field_active())->name('Active')->show(false),
        ];
    }

    public function has_role()
    {
        return $this->hasOne(SystemRole::class, SystemRole::field_primary(), self::field_role());
    }

    public function roleNameSortable($query, $direction)
    {
        $query = $this->queryFilter($query);
        $query = $query->orderBy(SystemRole::field_name(), $direction);

        return $query;
    }

    public static function boot()
    {
        parent::saving(function ($model) {
            // $model->{User::field_role()} = $model->has_role->field_type ?? 0;
        });
        parent::boot();
    }
}
