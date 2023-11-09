<?php

namespace SmashedEgg\LaravelRouteAnnotation\Tests;

use SmashedEgg\LaravelRouteAnnotation\Route;

class RouteTest extends TestCase
{
    public function testRouteClass()
    {
        $route = new Route(
            uri: '/filter/{type}',
            name: 'filter',
            domain: 'mydomain.com',
            schemes: ['https'],
            defaults: ['type' => 'all'],
            methods: ['GET', 'POST'],
            middleware: ['some_middleware'],
            wheres: ['id' => '[.*]+'],
            priority: 10,
        );

        $this->assertEquals('/filter/{type}', $route->getUri());
        $this->assertEquals('filter', $route->getName());
        $this->assertEquals('mydomain.com', $route->getDomain());
        $this->assertSame(['https'], $route->getSchemes());
        $this->assertSame(['type' => 'all'], $route->getDefaults());
        $this->assertSame(['GET', 'POST'], $route->getMethods());
        $this->assertSame(['some_middleware'], $route->getMiddleware());
        $this->assertSame(['id' => '[.*]+'], $route->getWheres());
        $this->assertEquals(10, $route->getPriority());
    }


}
