<?php

namespace Wowe\Eloquent\Scopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;

class WhereScope extends ScopeExtension implements ScopeInterface
{
    /**
     * Apply the scopes to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function apply(Builder $builder)
    {
        foreach ($builder->getModel()->getWhereScopes() as $scope) {
            $builder->where($scope['column'], $scope['operator'], $scope['value']);
        }
    }

    /**
     * Remove the scopes from the given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function remove(Builder $builder)
    {
        $this->removeWhere($builder, [$this, 'isConstraint'], $builder->getModel()->getWhereScopes());
    }

    /**
     * Determine if the given where clause is an constraint from this scope.
     *
     * @param  array   $where
     * @param  string  $column
     * @return bool
     */
    protected function isConstraint(array $where, $scopes)
    {
        foreach ($scopes as $scope) {
            if (array_intersect_assoc($scope, $where) == $scope) {
                return true;
            }
        }

        return false;
    }
}
