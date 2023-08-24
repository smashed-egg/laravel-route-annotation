<?php

namespace SmashedEgg\LaravelRouteAnnotation\Loader;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;
use InvalidArgumentException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use SmashedEgg\LaravelRouteAnnotation\Route as RouteAnnotation;
use SmashedEgg\LaravelRouteAnnotation\Test\Controller\ResourceController;

/**
 * AnnotationClassLoader loads routing information from annotations set
 * on PHP classes and methods via PHP Attributes
 *
 * @author Tom Ellis <tellishtc@gmail.com>
 *
 * Based on the Route Annotation loading From Symfony
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AnnotationClassLoader
{
    /**
     * @var int
     */
    protected int $defaultRouteIndex = 0;

    public function __construct(
        protected Router $router,
        protected string $routeAnnotationClass = RouteAnnotation::class
    )
    {}

    /**
     * Loads from annotations from a class.
     *
     * @throws InvalidArgumentException When route can't be parsed
     */
    public function load(mixed $class): RouteCollection
    {
        if ( ! class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $class = new ReflectionClass($class);
        if ($class->isAbstract()) {
            throw new InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class->getName()));
        }

        $globals = $this->getGlobals($class);

        $collection = new RouteCollection();

        // If it's a resource controller
        if (isset($globals['resource']) && true === $globals['resource']) {

            $pendingResourceRegistration = $this->router->resource($globals['name'], $globals['controller'], $globals['options']);

            /** @var Route $route */
            foreach ($pendingResourceRegistration->register() as $route) {
                $collection->add($route);
            }

        }

        if (isset($globals['api']) && true === $globals['api']) {

            $pendingResourceRegistration = $this->router->apiResource($globals['name'], $globals['controller'], $globals['options']);

            /** @var Route $route */
            foreach ($pendingResourceRegistration->register() as $route) {
                $collection->add($route);
            }

        }

        foreach ($class->getMethods() as $method) {
            $this->defaultRouteIndex = 0;
            foreach ($this->getAnnotations($method) as $annot) {
                $this->addRoute($collection, $annot, $globals, $class, $method);
            }
        }

        if (0 === $collection->count() && $class->hasMethod('__invoke')) {
            $globals = $this->resetGlobals();
            foreach ($this->getAnnotations($class) as $annot) {
                $this->addRoute($collection, $annot, $globals, $class, $class->getMethod('__invoke'));
            }
        }

        return $collection;
    }

    /**
     * @param RouteCollection $collection
     * @param object $annot or an object that exposes a similar interface
     * @param array $globals
     * @param ReflectionClass $class
     * @param ReflectionMethod $method
     */
    protected function addRoute(
        RouteCollection $collection,
        object $annot,
        array $globals,
        ReflectionClass $class,
        ReflectionMethod $method
    ): void {
        $name = $annot->getName();

        if (null === $name) {
            $name = $this->getDefaultRouteName($class, $method);
        }

        $name = $globals['name'].$name;

        $schemes = array_merge($globals['schemes'], $annot->getSchemes());
        $defaults = array_replace($globals['defaults'], $annot->getDefaults());
        $methods = array_merge($globals['methods'], $annot->getMethods());
        $middleware = array_merge($globals['middleware'], $annot->getMiddleware());
        $wheres = array_merge($globals['wheres'], $annot->getWheres());

        $domain = $annot->getDomain();

        if ( ! $methods) {
            $methods = ['GET'];
        }

        if (null === $domain) {
            $domain = $globals['domain'];
        }

        $uri = $annot->getUri();
        $prefix = $globals['uri'];
        $path = $prefix.$uri;

        $route = $this->createRoute($path, $schemes, $methods, [$class->getName(), $method->getName()], $middleware);
        $route->setDefaults($defaults);
        $route->name($name);
        $route->domain($domain);
        $route->setWheres($wheres);

        $collection->add($route);
    }

    /**
     * Gets the default route name for a class method.
     */
    protected function getDefaultRouteName(ReflectionClass $class, ReflectionMethod $method): string
    {
        $name = str_replace('\\', '_', $class->name).'_'.$method->name;
        $name = function_exists('mb_strtolower') && preg_match('//u', $name) ? mb_strtolower($name, 'UTF-8') : strtolower($name);
        if ($this->defaultRouteIndex > 0) {
            $name .= '_'.$this->defaultRouteIndex;
        }
        ++$this->defaultRouteIndex;

        return $name;
    }

    protected function getGlobals(ReflectionClass $class): array
    {
        $globals = $this->resetGlobals();

        $annot = null;
        if ($attribute = $class->getAttributes($this->routeAnnotationClass, ReflectionAttribute::IS_INSTANCEOF)[0] ?? null) {
            $annot = $attribute->newInstance();
        }

        if ($annot) {
            $globals['controller'] = $class->getName();

            if (null !== $annot->getName()) {
                $globals['name'] = $annot->getName();
            }

            if (null !== $annot->getUri()) {
                $globals['uri'] = $annot->getUri();
            }

            if (null !== $annot->getSchemes()) {
                $globals['schemes'] = $annot->getSchemes();
            }

            if (null !== $annot->getDefaults()) {
                $globals['defaults'] = $annot->getDefaults();
            }

            if (null !== $annot->getMethods()) {
                $globals['methods'] = $annot->getMethods();
            }

            if (null !== $annot->getDomain()) {
                $globals['domain'] = $annot->getDomain();
            }

            if (null !== $annot->getMiddleware()) {
                $globals['middleware'] = $annot->getMiddleware();
            }

            if (null !== $annot->getWheres()) {
                $globals['wheres'] = $annot->getWheres();
            }

            if (null !== $annot->isResource()) {
                $globals['resource'] = $annot->isResource();
            }

            if (null !== $annot->isApi()) {
                $globals['api'] = $annot->isApi();
            }

            if (null !== $annot->getOptions()) {
                $globals['options'] = $annot->getOptions();
            }
        }

        return $globals;
    }

    private function resetGlobals(): array
    {
        return [
            'uri' => null,
            'defaults' => [],
            'wheres' => [],
            'schemes' => [],
            'methods' => [],
            'middleware' => [],
            'domain' => '',
            'name' => '',
            'resource' => false,
            'api' => false,
            'options' => '',
            'controller' => '',
        ];
    }

    protected function createRoute(
        string $uri,
        array $schemes,
        array $methods,
               $action,
        array $middleware
    )
    {
        $route = new Route(
            $methods,
            $uri,
            $action
        );

        $route->middleware($middleware);

        return $route;
    }

    /**
     * @param ReflectionClass|ReflectionMethod $reflection
     *
     * @return iterable<int, RouteAnnotation>
     */
    private function getAnnotations(ReflectionClass|ReflectionMethod $reflection): iterable
    {
        foreach ($reflection->getAttributes($this->routeAnnotationClass, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            yield $attribute->newInstance();
        }
    }
}
