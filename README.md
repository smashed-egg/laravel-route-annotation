<p align="center">
  <img src="https://raw.githubusercontent.com/smashed-egg/.github/05d922c99f1a3bddea88339064534566b941eca9/profile/main.jpg" width="300">
</p>

# Laravel Route Annotation

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


Here is an example controller using Route annotations:

```php
<?php

namespace SmashedEgg\LaravelRouteAnnotation\Test\Controller;

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