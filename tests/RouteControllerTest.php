<?php

namespace SmashedEgg\LaravelRouteAnnotation\Tests;

use Illuminate\Foundation\Application;
use SmashedEgg\LaravelRouteAnnotation\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use SmashedEgg\LaravelRouteAnnotation\RouteAnnotationServiceProvider;
use SmashedEgg\LaravelRouteAnnotation\Test\Controller\ApiResourceController;
use SmashedEgg\LaravelRouteAnnotation\Test\Controller\ResourceController;
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

    public function testAnnotationMacroLoadsResourceRoutesCorrectly()
    {
        // Tell Laravel to load controller routes
        RouteFacade::annotation(ResourceController::class);

        // Get routes loaded into Laravel
        $routes = RouteFacade::getRoutes()->getRoutesByName();

        $this->assertCount(7, $routes);

        $this->assertArrayHasKey('photos.index', $routes);
        $this->assertArrayHasKey('photos.create', $routes);
        $this->assertArrayHasKey('photos.store', $routes);
        $this->assertArrayHasKey('photos.edit', $routes);
        $this->assertArrayHasKey('photos.update', $routes);
        $this->assertArrayHasKey('photos.destroy', $routes);
    }

    public function testAnnotationMacroLoadsApiResourceRoutesCorrectly()
    {
        // Tell Laravel to load controller routes
        RouteFacade::annotation(ApiResourceController::class);

        // Get routes loaded into Laravel
        $routes = RouteFacade::getRoutes()->getRoutesByName();

        $this->assertCount(5, $routes);

        $this->assertArrayHasKey('reports.player.index', $routes);
        $this->assertArrayHasKey('reports.player.store', $routes);
        $this->assertArrayHasKey('reports.player.show', $routes);
        $this->assertArrayHasKey('reports.player.update', $routes);
        $this->assertArrayHasKey('reports.player.destroy', $routes);
    }

}
