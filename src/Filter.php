<?php

namespace Travelbuild\Filterable;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Filter
{
    /**
     * The query builder instance.
     *
     * @var Builder
     */
    protected $query;

    /**
     * The model instance.
     *
     * @var Model
     */
    protected $model;

    /**
     * Create a new filter instancce.
     *
     * @param  Builder  $query
     * @param  Filterable  $model
     * @return void
     */
    public function __construct(Builder $query, $model)
    {
        $this->query = $query;
        $this->model = $model;
    }

    /**
     * Handle the filter.
     *
     * @param  array  $filters
     * @return void
     */
    public function handle(array $filters): void
    {
        foreach ($this->normalization($filters) as $name => $value) {
            $this->callFilter($name, $value);
        }
    }

    /**
     * Normalize the client filters with usable filters.
     *
     * @param  array  $filters
     * @return array
     */
    public function normalization(array $filters)
    {
        $usableFilters = $this->model->getFilters();

        if (array_key_exists('*', $usableFilters)) {
            return $filters;
        }

        return array_intersect_key($filters, $usableFilters);
    }

    /**
     * Call the filter method, if exist.
     *
     * @param  string  $method
     * @param  mixed  $value
     * @return void
     */
    final public function callFilter(string $method, $value): void
    {
        if (method_exists($this->model, $scope = 'scope'.Str::studly($method))) {
            $value = array_filter(Arr::wrap($value), function ($value) {
                return filled($value);
            });

            $this->model->$scope($this->query, ...$value);
        }
    }
}
