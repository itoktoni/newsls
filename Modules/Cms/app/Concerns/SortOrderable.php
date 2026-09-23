<?php

namespace Modules\Cms\Concerns;

// ponytail: ensures sort_order is never stored as null.
// DB columns are NOT NULL with default 0, but empty form inputs
// become null after nullable validation -> MariaDB throws
// "Column 'sort_order' cannot be null". Coerce null/'' to 0 on save.
trait SortOrderable
{
    protected static function bootSortOrderable(): void
    {
        static::saving(function ($model) {
            if ($model->sort_order === null || $model->sort_order === '') {
                $model->sort_order = 0;
            }
        });
    }
}
