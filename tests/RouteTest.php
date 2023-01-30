<?php

namespace Tests;

use SmashedEgg\RouteAnnotation\Route;

class RouteTest extends TestCase
{
    public function test_route_class()
    {
        $route = new Route(
            uri: '/test',
            name: 'test',
        );

        $this->assertEquals('/test', $route->getUri());
        $this->assertEquals('test', $route->getName());
    }


}
