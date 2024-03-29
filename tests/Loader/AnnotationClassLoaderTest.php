<?php

namespace SmashedEgg\LaravelRouteAnnotation\Tests\Loader;

use Illuminate\Container\Container;
use Illuminate\Routing\Router;
use SmashedEgg\LaravelRouteAnnotation\Loader\AnnotationClassLoader;
use SmashedEgg\LaravelRouteAnnotation\Test\Controller\AbstractTestController;
use SmashedEgg\LaravelRouteAnnotation\Test\Controller\ComplexController;
use SmashedEgg\LaravelRouteAnnotation\Test\Controller\SimpleController;
use SmashedEgg\LaravelRouteAnnotation\Tests\TestCase;

class AnnotationClassLoaderTest extends TestCase
{
    public function testClassLoaderLoadsRoutesFromSimpleControllerCorrectly()
    {
        $loader = new AnnotationClassLoader(
            router: app(Router::class),
            container: app(),
        );
        $routeCollection = $loader->load(SimpleController::class);
        $routes = $routeCollection->getRoutesByName();

        $this->assertCount(4, $routes);

        $this->assertArrayHasKey('simple.home', $routes);
        $this->assertArrayHasKey('simple.list', $routes);
        $this->assertArrayHasKey('simple.create', $routes);
        $this->assertArrayHasKey('simple.edit', $routes);
    }

    public function testClassLoaderLoadsRoutesFromComplexControllerCorrectly()
    {
        $loader = new AnnotationClassLoader(
            router: app(Router::class),
            container: app()
        );
        $routeCollection = $loader->load(ComplexController::class);
        $routes = $routeCollection->getRoutesByName();

        $this->assertCount(4, $routes);

        $this->assertArrayHasKey('complex.home', $routes);
        $this->assertArrayHasKey('complex.list', $routes);
        $this->assertArrayHasKey('complex.create', $routes);
        $this->assertArrayHasKey('complex.edit', $routes);

        $editRoute = $routes['complex.edit'];

        $this->assertSame(['id' => '[0-9]+'], $editRoute->wheres);
    }

    public function testClassLoaderLoadsRoutesFailsOnAbstractControllerClass()
    {
        $router = $this->createMock(Router::class);

        $router->expects($this->never())
            ->method('getRoutes')
        ;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Annotations from class "%s" cannot be read as it is abstract.', AbstractTestController::class));

        $loader = new AnnotationClassLoader(
            router: $router,
            container: $this->createMock(Container::class),
            routeAnnotationClass: 'local'
        );
        $loader->load(AbstractTestController::class);
    }


}
