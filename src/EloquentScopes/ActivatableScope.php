<?php

namespace Wowe\Eloquent\Scopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;

class ActivatableScope extends ScopeExtension implements ScopeInterface
{
    /**
     * All of the extensions to be added to the builder.
     *
     * @var array
     */
    protected $extensions = ['WithInactive', 'OnlyInactive'];

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
        $this->removeWhere($builder, [$this, 'isActivatableConstraint'], $builder->getModel()->getQualifiedActiveColumn());
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
    protected function isActivatableConstraint(array $where, $column)
    {
        return $where['type'] == 'Basic' && $where['operator'] == '=' && $where['column'] == $column && $where['value'] == 1;
    }
}
