<?php

namespace SmashedEgg\LaravelRouteAnnotation\Tests;

use SmashedEgg\LaravelRouteAnnotation\Loader\AnnotationDirectoryLoader;

class AnnotationDirectoryLoaderTest extends TestCase
{
    public function testDirectoryLoaderLoadsRoutesFromDirectoryCorrectly()
    {
        $loader = new AnnotationDirectoryLoader();
        $routeCollection = $loader->load(__DIR__ . '/../src/Test/Controller');
        $routes = $routeCollection->getRoutesByName();

        $this->assertCount(10, $routes);

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
    }

}
