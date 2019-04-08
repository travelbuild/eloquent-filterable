<?php

namespace Travelbuild\Filterable\Test;

use Illuminate\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = $app->get('config');

        $config->set('database.default', 'testbench');

        $config->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function refreshDatabase()
    {
        $this->artisan('migrate', [
            '--path' => __DIR__.'/database/migrations',
            '--realpath' => true,
            '--seed' => true,
        ]);
    }

    /**
     * Get the SQL representation of the query.
     *
     * @param  Builder  $builder
     * @return string
     */
    public function toSql(Builder $builder)
    {
        $addSlashes = str_replace('?', "'?'", $builder->toSql());

        return vsprintf(str_replace('?', '%s', $addSlashes), $builder->getBindings());
    }
}
