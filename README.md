<p align="center">
  <img src="https://raw.githubusercontent.com/smashed-egg/.github/05d922c99f1a3bddea88339064534566b941eca9/profile/main.jpg" width="300">
</p>

# Laravel Route Annotation
[![Latest Stable Version](https://poser.pugx.org/smashed-egg/laravel-route-annotation/v/stable)](https://github.com/smashed-egg/laravel-route-annotation/releases)
[![Downloads this Month](https://img.shields.io/packagist/dm/smashed-egg/laravel-route-annotation.svg)](https://packagist.org/packages/smashed-egg/laravel-route-annotation)


This package allows you to load routes using PHP Attributes to define routes in your controller classes.

More details to follow.

## Requirements

* PHP 8.0.2+
* Laravel 9.0+

## Installation

To install this package please run:

```
composer require smashed-egg/laravel-route-annotation
```

[Support Me](https://github.com/sponsors/tomgrohl)
--------------------------------------------

Do you like this package? Does it improve you're development. Consider sponsoring to help with future development.

[Buy me a coffee!](https://github.com/sponsors/tomgrohl)

Thank you!

## Usage

## Registering Routes

To register routes in your controller, first you have to import the Route annotation class:

```php
<?php

use SmashedEgg\LaravelRouteAnnotation\Route;

```

The Route annotation class takes the following arguments:

- string|null $uri
- string|null $name
- string|null $domain
- array $schemes
- array $defaults
- array $methods
- array $middleware 
- array $wheres
- int $priority (Set order of priority for routes, defaults to 0)


Here is an example controller using Route annotations:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use SmashedEgg\LaravelRouteAnnotation\Route;

#[Route('/users', name: 'users.')]
class UserController extends Controller
{
    #[Route('/', name: 'home', methods: ['GET', 'POST'])]
    public function home()
    {
        return response()->make('users.home');
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create()
    {
        return response()->make('users.create');
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'], wheres: ['id' => '[0-9]+'])]
    public function edit($id)
    {
        return response()->make('users.edit');
    }
}

```

### Resource Routes

You can configure resource routes by doing the following:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use SmashedEgg\LaravelRouteAnnotation\ResourceRoute;

#[ResourceRoute(name: 'photos')]
class PhotoController extends Controller
{
    public function index()
    {
    }

    public function create()
    {
    }

    public function store()
    {
    }

    public function edit($id)
    {
    }

    public function update()
    {
    }

    public function destroy()
    {
    }
}
```

### Api Resource Routes

You can configure api resource routes by doing the following:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use SmashedEgg\LaravelRouteAnnotation\ApiResourceRoute;

#[ApiResourceRoute(name: 'api.photos')]
class PhotoApiController extends Controller
{
    public function index()
    {
    }

    public function show()
    {
    }

    public function store()
    {
    }

    public function update()
    {
    }

    public function destroy()
    {
    }
}

```

## Loading Routes

### Loading routes from a single controller

In your routes file or service provider you can add the following to load routes for a given controller class.

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


Route::annotation(UserController::class);
```

### Loading routes from a directory

In your routes file or service provider you can add the following to load routes for a given directory.

```php
<?php

use Illuminate\Support\Facades\Route;


Route::directory(__DIR__ . '/Controllers');
```

You can also wrap them in route groups:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')->prefix('/app')->as('app.')->scopeBindings()->group(function() {
    Route::directory(__DIR__ . '/Controllers');
});
```
