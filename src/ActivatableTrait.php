<?php

namespace Wowe\Eloquent\Scopes;

trait ActivatableTrait
{

    /**
     * Boot the activatable trait for a model.
     *
     * @return void
     */
    public static function bootActivatableTrait()
    {
        static::addGlobalScope(new ActivatableScope);
    }

    /**
     * Sets the model as inactive.
     * 
     * @return void
     */
    public function deactivate()
    {
        $this->{$this->getActiveColumn()} = 0;
    }

    /**
     * Sets the model as active.
     * 
     * @return void
     */
    public function activate()
    {
        $this->{$this->getActiveColumn()} = 1;
    }

    /**
     * Get a new query builder that includes inactive.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function withInactive()
    {
        return (new static)->newQueryWithoutScope(new ActivatableScope);
    }

    /**
     * Get a new query builder that only includes inactive.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function onlyInactive()
    {
        $instance = new static;

        $column = $instance->getQualifiedActiveColumn();

        return $instance->newQueryWithoutScope(new ActivatableScope)->where($column, 0);
    }

    /**
     * Get the name of the "active" column.
     *
     * @return string
     */
    public function getActiveColumn()
    {
        return defined('static::ACTIVE') ? static::ACTIVE : 'active';
    }

    /**
     * Get the fully qualified "active" column.
     *
     * @return string
     */
    public function getQualifiedActiveColumn()
    {
        return $this->getTable() . '.' . $this->getActiveColumn();
    }
}
