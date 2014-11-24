<?php

namespace Wowe\Eloquent\Scopes;

trait WhereTrait
{

    /**
     * Boot the where trait for a model.
     *
     * @return void
     */
    public static function bootWhereTrait()
    {
        static::addGlobalScope(new WhereScope);
    }

    /**
     * Get the where scopes
     *
     * @return string
     */
    public function getWhereScopes()
    {
        $scopes = array_merge(property_exists($this, 'whereScopes') ? $this->whereScopes : [], method_exists($this, 'whereScopes') ? $this->whereScopes() : []);
        return array_map(function ($column, $scope) {
            $scope =  (array) $scope;
            return [
                'type' => 'Basic',
                'operator' => count($scope) == 2 ? $scope[0] : '=',
                'column' => $this->getTable() . '.' . $column,
                'value' => count($scope) == 2 ? $scope[1] : $scope[0]
            ];
        }, array_keys($scopes), $scopes);
    }
}
