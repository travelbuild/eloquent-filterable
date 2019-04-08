<?php

namespace Travelbuild\Filterable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * The trait of Filterable.
 *
 * @method static Builder filter(array $filters)
 *
 * @mixin Builder
 * @mixin Model
 */
trait Filterable
{
    /**
     * Filter the query by given filters.
     *
     * @param  Builder  $query
     * @param  array  $filters
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $filter = new Filter($query, $this);

        $filter->handle($filters);

        return $query;
    }

    /**
     * Get the allowed filter methods.
     *
     * @return array
     */
    public function getFilters(): array
    {
        if (isset($this->filterable)) {
            return array_flip($this->filterable);
        }

        return ['*' => null];
    }
}
