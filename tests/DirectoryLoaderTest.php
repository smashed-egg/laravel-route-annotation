<?php

namespace Tests;

use SmashedEgg\RouteAnnotation\Loader\AnnotationDirectoryLoader;

class DirectoryLoaderTest extends TestCase
{
    public function test_loader_loads_routes_from_directory_correctly()
    {
        $loader = new AnnotationDirectoryLoader('local');
        $routeCollection = $loader->load(__DIR__ . '/../src/Test/Controller');
        $routes = $routeCollection->getRoutesByName();

        $this->assertCount(4, $routes);

        $this->assertArrayHasKey('test.home', $routes);
        $this->assertArrayHasKey('test.list', $routes);

        $this->assertArrayHasKey('test2.home', $routes);
        $this->assertArrayHasKey('test2.list', $routes);
    }

}
