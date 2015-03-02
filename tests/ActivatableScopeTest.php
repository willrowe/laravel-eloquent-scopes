<?php

use Illuminate\Database\Eloquent\Model;
use Wowe\Eloquent\Scopes\ActivatableTrait;
use Wowe\Eloquent\Scopes\ActivatableScope;

class ActivatableScopeTest extends \Orchestra\Testbench\TestCase
{
    private $scope;
    private $builder;
    public function setUp()
    {
        parent::setUp();

        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');
        $this->scope = new ActivatableScope();
        $this->model = new TestModel();
    }

    public function testScopeApplied()
    {
        $query = $this->model->newQuery()->getQuery();
        $this->assertEquals(['type' => 'Basic', 'column' => 'test_models.active', 'operator' => '=', 'value' => 1, 'boolean' => 'and'], $query->wheres[0]);
        $this->assertEquals(1, $query->getRawBindings()['where'][0]);
    }

    public function testScopeRemoved()
    {
        $query = $this->model->newQueryWithoutScope($this->scope)->getQuery();
        $this->assertEmpty($query->wheres);
        $this->assertEmpty($query->getRawBindings()['where']);
    }

    public function testModelActivates()
    {
        $this->model->activate();
        $this->assertEquals(1, $this->model->active);
    }

    public function testModelDeactivates()
    {
        $this->model->deactivate();
        $this->assertEquals(0, $this->model->active);
    }
}

class TestModel extends Model
{
    use ActivatableTrait;
}
