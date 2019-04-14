# Eloquent Filterable

With this package, you can call local scopes with `filter` scope method.

## Installation

[PHP](https://php.net/) 7.1.3+ or [HHVM](http://hhvm.com/), and [Composer](https://getcomposer.org/) are required.

To get thepackage, simply add the following line to the require block of your `composer.json` file:

```json
"require": {
    "travelbuild/eloquent-filterable": "~1.0"
}
```

You'll then need to run composer install or composer update to download it and have the autoloader updated. Or use to shortcut installed through terminal:

```bash
composer require travelbuild/eloquent-filterable
```

## Usage

Each model must be implement the `Filterable` trait.

```php
<?php

namespace App;

use Travelbuild\Filterable\Filterable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Filterable;
}
```

Then, you can define local scopes in your model. If you need help to local scopes, please follow the [Laravel documentation](https://laravel.com/docs/5.8/eloquent#local-scopes). 

```php
/**
 * Scope a query to only include only active posts.
 *
 * @param  Builder  $query
 * @return Builder
 */
public function scopeActive(Builder $query): Builder
{
    return $query->where('active', true);
}
```

And finally, you must be define usable local scopes to the `filterable` property in your model if you need. Why need it? We don't want some scopes to be used by the client.

```php
/**
 * The usable scope filters.
 *
 * @return array
 */
protected $filterable = [
    'active',
];
```

if you want all scopes to be usable to the client, do not define `filterable` property or define as follows:

> This way is not recommended. Take control, access to all scopes may be risky.

```php
/**
 * The usable scope filters.
 *
 * @return array
 */
protected $filterable = ['*'];
```

That's it. Now, you can call dynamically all callable scopes with `filter` scope.

```php
$users = User::filter(['active' => true])->paginate();
```

## Real World Example

We needed this package to quickly respond HTTP requests. Our client send to us some conditions using by search endpoint. We wanted to handle these filter conditions dynamically for simplicity and reusability.

```php
<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class PostSearchController extends Controller
{
    /**
     * Respond the post search request.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $posts = Post::filter($request->all())->paginate(
            $request->get('limit', 25)
        );

        return response()->json($posts);
    }
}
```

Create a new route for post search in `routes/api.php`:

```php
Route::get('/posts', 'PostSearchController');
```

Define model and filterable local scopes:

```php
<?php

namespace App;

use Travelbuild\Filterable\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        'author',
    ];

    /**
     * Scope a query to only include only active posts.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include author's posts.
     *
     * @param  Builder  $query
     * @param  int  $authorId
     * @return Builder
     */
    public function scopeAuthor(Builder $query, int $authorId): Builder
    {
        return $query->where('author_id', $authorId);
    }
}
```

Now, the client may be send to server some search conditions. Here is some example endpoints:

```
/posts
/posts?active
/posts?author=5
/posts?active&author=5
/posts?active&author=5&limit=10
...
```

Our model will be apply the filter when exists local scope.

# Contributing

Love innovation and simplicity. Please use issues all errors or suggestions reporting. Follow the steps for contributing:

1. Fork the repo.
2. Follow the [Laravel Coding Style](http://laravel.com/docs/master/contributions#coding-style).
3. If necessary, check your changes with unittest.
4. Commit all changes.
5. Push your commit and create a new PR.
6. Wait for merge the PR.

# Unit Test

Please create your tests and check before PR. Use the command:

```bash
$ phpunit
```

# License

Eloquent Filterable is licensed under [The MIT License (MIT)](https://opensource.org/licenses/MIT).
