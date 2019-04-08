<?php

use Illuminate\Database\Seeder;
use Travelbuild\Filterable\Test\Post;

class DatabaseSeeder extends Seeder
{
    protected $mocks = [
        [
            'name' => 'Foo',
            'views_count' => 10,
            'published' => false,
        ], [
            'name' => 'Bar',
            'views_count' => 20,
            'published' => true,
        ], [
            'name' => 'Baz',
            'views_count' => 30,
            'published' => true,
        ],
    ];

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->mocks as $mock) {
            Post::create($mock);
        }
    }
}
