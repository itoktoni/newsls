<?php

namespace Ibex\CrudGenerator;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Class ModelGenerator.
 */
class ModelGenerator
{
    private $functions = null;

    private $table;

    private $properties;

    private $modelNamespace;

    /**
     * ModelGenerator constructor.
     */
    public function __construct(string $table, string $properties, string $modelNamespace)
    {
        $this->table = $table;
        $this->properties = $properties;
        $this->modelNamespace = $modelNamespace;
        $this->_init();
    }

    /**
     * Get all the eloquent relations.
     *
     * @return array
     */
    public function getEloquentRelations()
    {
        return [$this->functions, $this->properties];
    }

    private function _init()
    {
        foreach ($this->_getTableRelations() as $relation) {
            $this->functions .= $this->_getFunction($relation);
        }
    }

    private function _getFunction(array $relation)
    {
        switch ($relation['name']) {
            case 'hasOne':
            case 'belongsTo':
                $this->properties .= "\n * @property {$relation['class']} \${$relation['relation_name']}";
                break;
            case 'hasMany':
                $this->properties .= "\n * @property ".$relation['class']."[] \${$relation['relation_name']}";
                break;
        }

        return '
    /**
     * @return \Illuminate\Database\Eloquent\Relations\\'.ucfirst($relation['name']).'
     */
    public function has_'.$relation['relation_name'].'()
    {
        return $this->'.$relation['name'].'(\\App\\Facades\\Model\\'.$relation['class'].'Model::getModel(), \''.$relation['foreign_key'].'\', \''.$relation['owner_key'].'\');
    }
    ';
    }

    /**
     * Get all relations from Table.
     *
     * @return array
     */
    private function _getTableRelations()
    {
        return [
            ...$this->getBelongsTo(),
            ...$this->getOtherRelations(),
        ];
    }

    protected function getBelongsTo()
    {
        $relations = Schema::getForeignKeys($this->table);

        $eloquent = [];

        foreach ($relations as $relation) {
            if (count($relation['foreign_columns']) != 1 || count($relation['columns']) != 1) {
                continue;
            }

            $eloquent[] = [
                'name' => 'belongsTo',
                'relation_name' => Str::replace('.', '', Str::camel($relation['foreign_table'])),
                'class' => Str::replace('.', '', Str::studly($relation['foreign_table'])),
                'foreign_key' => $relation['columns'][0],
                'owner_key' => $relation['foreign_columns'][0],
            ];
        }

        return $eloquent;
    }

    protected function getOtherRelations()
    {
        $tables = Schema::getTableListing();
        $eloquent = [];

        foreach ($tables as $table) {
            $relations = Schema::getForeignKeys($table);
            $indexes = collect(Schema::getIndexes($table));

            foreach ($relations as $relation) {
                if ($relation['foreign_table'] != $this->table) {
                    continue;
                }

                if (count($relation['foreign_columns']) != 1 || count($relation['columns']) != 1) {
                    continue;
                }

                $isUniqueColumn = $this->getUniqueIndex($indexes, $relation['columns'][0]);

                $eloquent[] = [
                    'name' => $isUniqueColumn ? 'hasOne' : 'hasMany',
                    'relation_name' => Str::replace('.', '', Str::camel($table)),
                    'class' => Str::replace('.', '', Str::studly($table)),
                    'foreign_key' => $relation['foreign_columns'][0],
                    'owner_key' => $relation['columns'][0],
                ];
            }
        }

        return $eloquent;
    }

    private function getUniqueIndex($indexes, $column)
    {
        $isUnique = false;

        foreach ($indexes as $index) {
            if (
                (count($index['columns']) == 1)
                && ($index['columns'][0] == $column)
                && $index['unique']
            ) {
                $isUnique = true;
                break;
            }
        }

        return $isUnique;
    }
}
