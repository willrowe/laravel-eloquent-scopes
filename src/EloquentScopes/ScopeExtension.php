<?php

namespace Wowe\Eloquent\Scopes;

use Illuminate\Database\Eloquent\Builder;

class ScopeExtension
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
     * Determines how many PDO bindings are in the where SQL.
     * 
     * @param  array   $where
     * @return integer The number of bindings.
     */
    private static function getRawBindingCount(array $where)
    {
        return substr_count($where['sql'], '?');
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
                return count((array)$where[$bindingLocation]);
            }
            if (is_callable($bindingLocation)) {
                return call_user_func($bindingLocation, $where);
            }
            return 1;
        }

        return 0;
    }

    protected function removeWhere(Builder $builder, $matchCallback)
    {
        $callbackParameters = (func_num_args() > 2) ? array_slice(func_get_args(), 2) : [];
        $query = $builder->getQuery();
        $bindings = $query->getRawBindings()['where'];
        $bindingIndex = 0;
        foreach ((array) $query->wheres as $key => $where) {
            if (call_user_func_array($matchCallback, array_merge([$where], $callbackParameters))) {
                unset($query->wheres[$key]);
                unset($bindings[$bindingIndex]);
            }
            $bindingIndex += $this->getBindingCount($where);
        }
        $query->wheres = array_values($query->wheres);
        $query->setBindings($bindings);
    }
}
