<?php

namespace SmashedEgg\LaravelRouteAnnotation\Tests;

use Illuminate\Foundation\Application;
use SmashedEgg\LaravelRouteAnnotation\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use SmashedEgg\LaravelRouteAnnotation\RouteAnnotationServiceProvider;
use SmashedEgg\LaravelRouteAnnotation\Test\Controller\SimpleController;

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

    public function testDirectoryMacroLoadsRoutesCorrectly()
    {
        // Tell Laravel to load controller routes
        RouteFacade::directory(__DIR__ . '/../src/Test/Controller');

        // Get routes loaded into Laravel
        $routes = RouteFacade::getRoutes()->getRoutesByName();

        //$this->assertCount(10, $routes);
        $this->assertCount(22, $routes);

        $this->assertArrayHasKey('simple.home', $routes);
        $this->assertArrayHasKey('simple.list', $routes);
        $this->assertArrayHasKey('simple.create', $routes);
        $this->assertArrayHasKey('simple.edit', $routes);

        $this->assertArrayHasKey('simple2.home', $routes);
        $this->assertArrayHasKey('simple2.list', $routes);

        $this->assertArrayHasKey('complex.home', $routes);
        $this->assertArrayHasKey('complex.list', $routes);
        $this->assertArrayHasKey('complex.create', $routes);
        $this->assertArrayHasKey('complex.edit', $routes);

        $this->assertArrayHasKey('photos.index', $routes);
        $this->assertArrayHasKey('photos.create', $routes);
        $this->assertArrayHasKey('photos.store', $routes);
        $this->assertArrayHasKey('photos.edit', $routes);
        $this->assertArrayHasKey('photos.update', $routes);
        $this->assertArrayHasKey('photos.destroy', $routes);

        $this->assertArrayHasKey('reports.player.index', $routes);
        $this->assertArrayHasKey('reports.player.store', $routes);
        $this->assertArrayHasKey('reports.player.show', $routes);
        $this->assertArrayHasKey('reports.player.update', $routes);
        $this->assertArrayHasKey('reports.player.destroy', $routes);
    }


}
