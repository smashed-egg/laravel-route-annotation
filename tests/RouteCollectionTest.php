<?php

namespace SmashedEgg\LaravelRouteAnnotation\Tests;

use Illuminate\Routing\Route;
use SmashedEgg\LaravelRouteAnnotation\RouteCollection;

class RouteCollectionTest extends TestCase
{
    public function testRouteCollectionReturnsRoutesInCorrectOrderWhenPriorityIsPassed()
    {
        $route1 = new Route('GET', '/{something}', 'SomeController@something');
        $route1->name('something');

        $route2 = new Route('GET', '/list', 'SomeController@list');
        $route2->name('list');

        $routeCollection = new RouteCollection();
        $routeCollection->add($route1->getName(), $route1, 1);
        $routeCollection->add($route2->getName(), $route2, 2);

        $routes = $routeCollection->all();

        $this->assertCount(2, $routes);

        $this->assertSame([0 => 'list', 1 => 'something'], array_keys($routes));
    }


    public function testRouteCollectionReturnsRoutesInCorrectOrderWhenPriorityIsNotPassed()
    {
        $route1 = new Route('GET', '/{something}', 'SomeController@action');
        $route1->name('something');

        $route2 = new Route('GET', '/list', 'SomeController@action');
        $route2->name('list');

        $routeCollection = new RouteCollection();
        $routeCollection->add($route1->getName(), $route1);
        $routeCollection->add($route2->getName(), $route2);

        $routes = $routeCollection->all();

        $this->assertCount(2, $routes);

        $this->assertSame([0 => 'something', 1 => 'list'], array_keys($routes));
    }


}
