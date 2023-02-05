<?php

namespace SmashedEgg\LaravelRouteAnnotation\Tests;

use SmashedEgg\LaravelRouteAnnotation\Test\Controller\AbstractTestController;
use SmashedEgg\LaravelRouteAnnotation\Test\Controller\TestController;
use SmashedEgg\LaravelRouteAnnotation\Loader\AnnotationClassLoader;

class ControllerLoaderTest extends TestCase
{
    public function test_route_loader_loads_routes_from_controller_correctly()
    {
        $loader = new AnnotationClassLoader('local');
        $routeCollection = $loader->load(TestController::class);
        $routes = $routeCollection->getRoutesByName();

        $this->assertCount(2, $routes);

        $this->assertArrayHasKey('test.home', $routes);
        $this->assertArrayHasKey('test.list', $routes);
    }

    public function test_route_loader_fails_on_abstract_controller_class()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Annotations from class "%s" cannot be read as it is abstract.', AbstractTestController::class));

        $loader = new AnnotationClassLoader('local');
        $loader->load(AbstractTestController::class);
    }


}
