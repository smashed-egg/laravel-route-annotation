<?php

namespace SmashedEgg\LaravelRouteAnnotation\Tests;

use Illuminate\Support\Facades\Route as RouteFacade;
use Orchestra\Testbench\TestCase as BaseTestCase;

use Illuminate\Contracts\Config\Repository;

class TestCase extends BaseTestCase
{
    protected function countExistingRoutes(): int
    {
        return RouteFacade::getRoutes()->count();
    }
}
