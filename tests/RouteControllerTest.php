<?php

namespace SmashedEgg\LaravelRouteAnnotation\Tests;

use Illuminate\Foundation\Application;
use SmashedEgg\LaravelRouteAnnotation\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use SmashedEgg\LaravelRouteAnnotation\RouteAnnotationServiceProvider;
use SmashedEgg\LaravelRouteAnnotation\Test\Controller\TestController;

class RouteControllerTest extends TestCase
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
        RouteFacade::annotation(TestController::class);

        // Get routes loaded into Laravel
        $routes = RouteFacade::getRoutes()->getRoutesByName();

        $this->assertCount(2, $routes);

        $this->assertArrayHasKey('test.home', $routes);
        $this->assertArrayHasKey('test.list', $routes);
    }


}
