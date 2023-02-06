<?php

namespace SmashedEgg\LaravelRouteAnnotation\Tests;

use Illuminate\Foundation\Application;
use SmashedEgg\LaravelRouteAnnotation\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use SmashedEgg\LaravelRouteAnnotation\RouteAnnotationServiceProvider;
use SmashedEgg\LaravelRouteAnnotation\Test\Controller\SimpleController;

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

    public function testAnnotationMacroLoadsRoutesCorrectly()
    {
        // Tell Laravel to load controller routes
        RouteFacade::annotation(SimpleController::class);

        // Get routes loaded into Laravel
        $routes = RouteFacade::getRoutes()->getRoutesByName();

        $this->assertCount(4, $routes);

        $this->assertArrayHasKey('simple.home', $routes);
        $this->assertArrayHasKey('simple.list', $routes);
        $this->assertArrayHasKey('simple.create', $routes);
        $this->assertArrayHasKey('simple.edit', $routes);
    }


}
