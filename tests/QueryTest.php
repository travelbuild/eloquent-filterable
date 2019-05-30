<?php

namespace Travelbuild\Filterable\Test;

class QueryTest extends TestCase
{
    /**
     * Test the query without filter conditions.
     *
     * @return void
     */
    public function testWithoutFilter()
    {
        $query = Post::filter([]);

        $expected = <<<'SQL'
SELECT * FROM "posts"
SQL;

        $this->assertEqualsIgnoringCase($expected, $this->toSql($query));
    }

    /**
     * Test the query with single filter.
     *
     * @return void
     */
    public function testWithSingleFilter()
    {
        $query = Post::filter(['active' => null]);

        $expected = <<<'SQL'
SELECT * FROM "posts" WHERE "published" = '1'
SQL;

        $this->assertEqualsIgnoringCase($expected, $this->toSql($query));
    }

    /**
     * Test the query with one parameter, this scope accept two parameter.
     *
     * @return void
     */
    public function testWithOneParameter()
    {
        $query = Post::filter(['recent' => '2019-04-08']);

        $expected = <<<'SQL'
SELECT * FROM "posts" WHERE "created_at" > '2019-04-08'
SQL;

        $this->assertEqualsIgnoringCase($expected, $this->toSql($query));
    }

    /**
     * Test the query with multiple parameters.
     *
     * @return void
     */
    public function testWithMultipleParameters()
    {
        $query = Post::filter([
            'recent' => [
                '2019-04-01',
                '2019-04-08',
            ],
        ]);

        $expected = <<<'SQL'
SELECT * FROM "posts" WHERE "created_at" BETWEEN '2019-04-01' AND '2019-04-08'
SQL;

        $this->assertEqualsIgnoringCase($expected, $this->toSql($query));
    }

    /**
     * Test the query with combined filters.
     *
     * @return void
     */
    public function testCombinedFilters()
    {
        $query = Post::filter([
            'active' => null,
            'recent' => '2019-04-08',
            'popular' => null,
        ]);

        $expected = <<<'SQL'
SELECT * FROM "posts" WHERE "published" = '1' AND "created_at" > '2019-04-08' ORDER BY "views_count" DESC
SQL;

        $this->assertEqualsIgnoringCase($expected, $this->toSql($query));
    }

    /**
     * Test the query with builder's additional methods.
     *
     * @return void
     */
    public function testWithBuilderMethods()
    {
        $query = Post::filter(['active' => null])->take(10);

        $expected = <<<'SQL'
SELECT * FROM "posts" WHERE "published" = '1' LIMIT 10
SQL;

        $this->assertEqualsIgnoringCase($expected, $this->toSql($query));
    }

    /**
     * Test the query with not allowed filter.
     *
     * @return void
     */
    public function testNotAllowedFilter()
    {
        $query = Post::filter([
            'draft' => null,
        ]);

        $expected = <<<'SQL'
SELECT * FROM "posts"
SQL;

        $this->assertEqualsIgnoringCase($expected, $this->toSql($query));
    }

    /**
     * Test the query with unknown filter.
     *
     * @return void
     */
    public function testUnknownFilter()
    {
        $query = Post::filter([
            'active' => null,
            'unknown' => null,
        ]);

        $expected = <<<'SQL'
SELECT * FROM "posts" WHERE "published" = '1'
SQL;

        $this->assertEqualsIgnoringCase($expected, $this->toSql($query));
    }

    /**
     * Test the query with views_count filter.
     *
     * @return void
     */
    public function testViewsCountFilter()
    {
        $query = Post::filter([
            'views_count' => [
                'from' => 20,
                'to' => 45,
            ],
        ]);

        $expected = <<<'SQL'
SELECT * FROM "posts" WHERE "views_count" BETWEEN '20' AND '45'
SQL;

        $this->assertEqualsIgnoringCase($expected, $this->toSql($query));
    }

    /**
     * Test the query with views_count filter's result count.
     *
     * @return void
     */
    public function testViewsCountFilterResultCount()
    {
        $resultCount = Post::filter([
            'views_count' => [
                'from' => 20,
                'to' => 45,
            ],
        ])->count();

        $expected = 2;

        $this->assertEqualsIgnoringCase($expected, $resultCount);
    }
}
