<?php

namespace App\Filters;

use Abbasudo\Purity\Filters\Filter;
use Closure;
use Illuminate\Support\Facades\DB;

class ContainsFilter extends Filter
{
    protected static string $operator = '$contains';

    public function apply(): Closure
    {
        return function ($query) {
            $connection = DB::connection()->getDriverName();

            foreach ($this->values as $value) {
                switch ($connection) {
                    case 'sqlite':
                    case 'mariadb':
                    case 'mysql':
                        $query->whereRaw("LOWER(`{$this->column}`) LIKE ?", ['%'.strtolower($value).'%']);
                        break;
                    case 'pgsql':
                        $query->where($this->column, 'ILIKE', "%{$value}%");
                        break;
                    case 'sqlsrv':
                        $query->whereRaw("`{$this->column}` COLLATE Latin1_General_CI_AS LIKE ?", ["%{$value}%"]);
                        break;
                    default:
                        throw new \RuntimeException("Unsupported database driver: {$connection}");
                }
            }
        };
    }
}
