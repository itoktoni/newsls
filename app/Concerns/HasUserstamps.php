<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

trait HasUserstamps
{
    public static function bootHasUserstamps()
    {
        static::creating(function ($model) {
            if (! $model->isUserstamping()) {
                return;
            }

            $column = $model->getCreatedByColumn();
            if ($model->isFillable($column) && ! $model->$column) {
                $model->$column = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (! $model->isUserstamping()) {
                return;
            }

            $column = $model->getUpdatedByColumn();
            if ($model->isFillable($column)) {
                $model->$column = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (! $model->isUserstamping()) {
                return;
            }

            $column = $model->getDeletedByColumn();
            if ($model->isFillable($column)) {
                $model->$column = Auth::id();

                if (in_array(SoftDeletes::class, class_uses_recursive(get_class($model)))) {
                    $model->save();
                }
            }
        });
    }

    protected $userstamping = true;

    public function isUserstamping(): bool
    {
        return $this->userstamping;
    }

    public function stopUserstamping(): void
    {
        $this->userstamping = false;
    }

    public function startUserstamping(): void
    {
        $this->userstamping = true;
    }

    public function getCreatedByColumn(): string
    {
        if (defined('static::CREATED_BY')) {
            return static::CREATED_BY;
        }

        $prefix = $this->getTablePrefix();

        return $prefix ? "{$prefix}_created_by" : 'created_by';
    }

    public function getUpdatedByColumn(): string
    {
        if (defined('static::UPDATED_BY')) {
            return static::UPDATED_BY;
        }

        $prefix = $this->getTablePrefix();

        return $prefix ? "{$prefix}_updated_by" : 'updated_by';
    }

    public function getDeletedByColumn(): string
    {
        if (defined('static::DELETED_BY')) {
            return static::DELETED_BY;
        }

        $prefix = $this->getTablePrefix();

        return $prefix ? "{$prefix}_deleted_by" : 'deleted_by';
    }

    protected function getTablePrefix(): string
    {
        $table = $this->getTable();

        if (str_ends_with($table, 's')) {
            return substr($table, 0, -1);
        }

        return $table;
    }

    public function creator()
    {
        return $this->belongsTo(config('auth.providers.users.model'), $this->getCreatedByColumn());
    }

    public function editor()
    {
        return $this->belongsTo(config('auth.providers.users.model'), $this->getUpdatedByColumn());
    }

    public function destroyer()
    {
        return $this->belongsTo(config('auth.providers.users.model'), $this->getDeletedByColumn());
    }
}
