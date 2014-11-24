<?php

use Illuminate\Database\Eloquent\Model;
use Wowe\Eloquent\Scopes\WhereTrait;
use Wowe\Eloquent\Scopes\WhereScope;

class WhereScopeTest extends \Orchestra\Testbench\TestCase
{
    private $scope;
    private $builder;
    public function setUp()
    {
        parent::setUp();
        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite.database', __DIR__.'/../../../../app/database/production.sqlite');
        $this->scope = new WhereScope();
        $this->model = new TestWhereModel();
    }

    public function testScopesApplied()
    {
        $query = $this->model->newQuery()->getQuery();
        $this->assertEquals(['type' => 'Basic', 'column' => 'test_where_models.foo', 'operator' => '=', 'value' => 'bar', 'boolean' => 'and'], $query->wheres[0]);
        $this->assertEquals('bar', $query->getRawBindings()['where'][0]);
        $this->assertEquals(['type' => 'Basic', 'column' => 'test_where_models.baz', 'operator' => '>=', 'value' => 2, 'boolean' => 'and'], $query->wheres[1]);
        $this->assertEquals(2, $query->getRawBindings()['where'][1]);
    }

    public function testScopesRemoved()
    {
        $query = $this->model->newQueryWithoutScopes()->getQuery();
        $this->assertEmpty($query->wheres);
        $this->assertEmpty($query->getRawBindings()['where']);
    }
}

class TestWhereModel extends Model
{
    use WhereTrait;

    protected $whereScopes = ['foo' => 'bar'];

    protected function whereScopes()
    {
        return [
            'baz' => ['>=', 2]
        ];
    }
}
