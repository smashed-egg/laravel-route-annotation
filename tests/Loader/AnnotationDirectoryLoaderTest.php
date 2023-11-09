<?php

namespace SmashedEgg\LaravelRouteAnnotation\Tests\Loader;

use Illuminate\Routing\Router;
use SmashedEgg\LaravelRouteAnnotation\Loader\AnnotationDirectoryLoader;
use SmashedEgg\LaravelRouteAnnotation\Tests\TestCase;

class AnnotationDirectoryLoaderTest extends TestCase
{
    public function testDirectoryLoaderLoadsRoutesFromDirectoryCorrectly()
    {
        $loader = new AnnotationDirectoryLoader(
            router: app(Router::class)
        );
        $routeCollection = $loader->load(__DIR__ . '/../../src/Test/Controller');
        $routes = $routeCollection->getRoutesByName();

        $this->assertCount(28, $routes);

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

        $this->assertArrayHasKey('single', $routes);

        $this->assertArrayHasKey('profile.show', $routes);
        $this->assertArrayHasKey('profile.edit', $routes);
        $this->assertArrayHasKey('profile.update', $routes);

        $this->assertArrayHasKey('extra.index', $routes);
        $this->assertArrayHasKey('extra.top', $routes);
    }

}
