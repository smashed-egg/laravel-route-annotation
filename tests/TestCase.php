<?php

namespace SmashedEgg\LaravelRouteAnnotation\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

use Illuminate\Contracts\Config\Repository;

class TestCase extends BaseTestCase
{
     protected function defineEnvironment($app)
     {
         // Setup default database to use sqlite :memory:
         tap($app['config'], function (Repository $config) {
             $config->set('filesystems.default', 'public');
         });
     }
}
