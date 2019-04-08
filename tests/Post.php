<?php

namespace Travelbuild\Filterable\Test;

use Travelbuild\Filterable\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Post class.
 *
 * @method Builder active()
 * @method Builder draft()
 * @method Builder popular()
 * @method Builder recent(string $begin, string $end = null)
 *
 * @mixin Builder
 */
class Post extends Model
{
    use Filterable;

    /**
     * The usable scope filters.
     *
     * @return array
     */
    protected $filterable = [
        'active',
        'popular',
        'recent',
    ];

    /**
     * Scope a query to only include active posts.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('published', 1);
    }

    /**
     * Scope a query to order include draft posts.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('published', 0);
    }

    /**
     * Scope a query to order by view count.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->latest('views_count');
    }

    /**
     * Scope a query to only include recent posts.
     *
     * @param  Builder  $query
     * @param  string  $begin
     * @param  string  $end
     * @return Builder
     */
    public function scopeRecent(Builder $query, string $begin, string $end = null): Builder
    {
        if (is_null($end)) {
            return $query->where('created_at', '>', $begin);
        }

        return $query->whereBetween('created_at', [$begin, $end]);
    }
}
