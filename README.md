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

## Loading Routes

### Loading routes from a single controller

In your routes file or service provider you can add the following to load routes for a given controller class.

```
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


Route::annotation(UserController::class);
```

### Loading routes from a directory

In your routes file or service provider you can add the following to load routes for a given directory.

```
use Illuminate\Support\Facades\Route;


Route::directory(__DIR__ . '/Controllers');
```