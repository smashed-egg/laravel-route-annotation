<?php

namespace Tests;

use Illuminate\Foundation\Application;
use SmashedEgg\RouteAnnotation\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use SmashedEgg\RouteAnnotation\RouteAnnotationServiceProvider;
use SmashedEgg\RouteAnnotation\Test\Controller\TestController;

class DirectoryMacroTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            RouteAnnotationServiceProvider::class,
        ];
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        // Code before application created.

        parent::setUp();

        // Code after application created.


    }

    public function test_route_class()
    {
        // Tell Laravel to load controller routes
        RouteFacade::directory(__DIR__ . '/../src/Test/Controller');

        // Get routes loaded into Laravel
        $routes = RouteFacade::getRoutes()->getRoutesByName();

        $this->assertCount(4, $routes);

        $this->assertArrayHasKey('test.home', $routes);
        $this->assertArrayHasKey('test.list', $routes);

        $this->assertArrayHasKey('test2.home', $routes);
        $this->assertArrayHasKey('test2.list', $routes);
    }


}
