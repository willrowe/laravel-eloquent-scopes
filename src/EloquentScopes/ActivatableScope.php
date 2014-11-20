<?php

namespace Wowe\Eloquent\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ScopeInterface;

class ActivatableScope implements ScopeInterface
{

    /**
     * A map of the where types and how to determine the
     * number of bindings it generated.
     * 
     * @var array
     */
    private $whereTypes = [
        'Basic' => 'value',
        'raw' => ['static', 'getRawBindingCount'],
        'between' => 2,
        'NotIn' => 'values',
        'In' => 'values',
        'Day' => 'value',
        'Month' => 'value',
        'Year' => 'value'
    ];

    /**
     * All of the extensions to be added to the builder.
     *
     * @var array
     */
    protected $extensions = ['WithInactive', 'OnlyInactive'];

    /**
     * Determines how many PDO bindings are in the where SQL.
     * 
     * @param  array   $where
     * @return integer        The number of bindings.
     */
    private static function getRawBindingCount(array $where)
    {
        return substr_count($where['sql'], '?');
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function apply(Builder $builder)
    {
        $model = $builder->getModel();

        $builder->where($model->getQualifiedActiveColumn(), 1);

        $this->extend($builder);
    }

    /**
     * Remove the scope from the given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function remove(Builder $builder)
    {
        $column = $builder->getModel()->getQualifiedActiveColumn();

        $query = $builder->getQuery();
        $bindingIndex = 0;
        foreach ((array) $query->wheres as $key => $where) {
            if ($this->isActiveConstraint($where, $column)) {
                unset($query->wheres[$key]);
                $query->wheres = array_values($query->wheres);
                $bindings = $query->getRawBindings()['where'];
                unset($bindings[$bindingIndex]);
                $query->setBindings($bindings);
            }

            $bindingIndex += $this->getBindingCount($where);
        }
    }

    /**
     * Determine how many bindings the where has.
     * 
     * @param  array $where
     * @return integer The number of bindings
     */
    private function getBindingCount(array $where)
    {
        if (array_key_exists($where['type'], $this->whereTypes)) {
            $bindingLocation = $this->whereTypes[$where['type']];

            if (is_int($bindingLocation)) {
                return $bindingLocation;
            }
            if (is_string($bindingLocation)) {
                return $where[$bindingLocation];
            }
            if (is_callable($bindingLocation)) {
                return call_user_func($bindingLocation, $where);
            }
            return 1;
        }

        return 0;
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * Add the with-inactive extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithInactive(Builder $builder)
    {
        $builder->macro('withInactive', function (Builder $builder) {
            $this->remove($builder);

            return $builder;
        });
    }

    /**
     * Add the only-inactive extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addOnlyInactive(Builder $builder)
    {
        $builder->macro('onlyInactive', function (Builder $builder) {
            $this->remove($builder);

            $builder->getQuery()->where($builder->getModel()->getQualifiedActiveColumn(), 0);

            return $builder;
        });
    }

    /**
     * Determine if the given where clause is an active constraint.
     *
     * @param  array   $where
     * @param  string  $column
     * @return bool
     */
    protected function isActiveConstraint(array $where, $column)
    {
        return $where['type'] == 'Basic' && $where['operator'] == '=' && $where['column'] == $column && $where['value'] == 1;
    }
}
