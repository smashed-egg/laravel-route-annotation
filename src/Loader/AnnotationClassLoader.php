<?php

namespace SmashedEgg\LaravelRouteAnnotation\Loader;

use ReflectionClass;
use ReflectionMethod;
use ReflectionAttribute;
use Illuminate\Routing\Route;
use InvalidArgumentException;
use Illuminate\Routing\Router;
use Illuminate\Routing\RouteCollection;
use SmashedEgg\LaravelRouteAnnotation\RouteCollection as PriorityRouteCollection;
use SmashedEgg\LaravelRouteAnnotation\Route as RouteAnnotation;

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

        $collection = new PriorityRouteCollection();

        foreach ($class->getMethods() as $method) {
            $this->defaultRouteIndex = 0;
            foreach ($this->getAnnotations($method) as $annot) {
                $this->addRoute($collection, $annot, $globals, $class, $method);
            }
        }

        // If it's a resource controller
        if (isset($globals['resource']) && true === $globals['resource']) {

            if (true === $globals['singleton']) {
                $pendingResourceRegistration = $this->router->singleton($globals['name'], $globals['controller'], $globals['options']);
            } else {
                $pendingResourceRegistration = $this->router->resource($globals['name'], $globals['controller'], $globals['options']);
            }

            /** @var Route $route */
            foreach ($pendingResourceRegistration->register() as $route) {
                $collection->add($route->getName(), $route);
            }
        }

        if (isset($globals['api']) && true === $globals['api']) {

            $pendingResourceRegistration = $this->router->apiResource($globals['name'], $globals['controller'], $globals['options']);

            /** @var Route $route */
            foreach ($pendingResourceRegistration->register() as $route) {
                $collection->add($route->getName(), $route);
            }
        }

        if (0 === $collection->count() && $class->hasMethod('__invoke')) {
            $globals = $this->resetGlobals();
            foreach ($this->getAnnotations($class) as $annot) {
                $this->addRoute($collection, $annot, $globals, $class, $class->getMethod('__invoke'));
            }
        }

        $sortedCollection = new RouteCollection();

        foreach ($collection->all() as $route) {
            $sortedCollection->add($route);
        }

        return $sortedCollection;
    }

    /**
     * @param PriorityRouteCollection $collection
     * @param object $annot or an object that exposes a similar interface
     * @param array $globals
     * @param ReflectionClass $class
     * @param ReflectionMethod $method
     */
    protected function addRoute(
        PriorityRouteCollection $collection,
        object $annot,
        array $globals,
        ReflectionClass $class,
        ReflectionMethod $method
    ): void {
        $name = $annot->getName();

        if (null === $name) {
            $name = $this->getDefaultRouteName($class, $method);
        }

        if ($globals['name']) {
            $name = rtrim($globals['name'], '.') . '.' .$name;
        }

        $schemes = array_merge($globals['schemes'], $annot->getSchemes());
        $defaults = array_replace($globals['defaults'], $annot->getDefaults());
        $methods = array_merge($globals['methods'], $annot->getMethods());
        $middleware = array_merge($globals['middleware'], $annot->getMiddleware());
        $wheres = array_merge($globals['wheres'], $annot->getWheres());
        $priority = $annot->getPriority();

        $domain = $annot->getDomain();

        if ( ! $methods) {
            $methods = ['GET'];
        }

        if (null === $domain) {
            $domain = $globals['domain'];
        }

        $uri = $annot->getUri();
        $prefix = $globals['uri'];
        $path = $prefix . '/' . ltrim($uri, '/');

        $action = [
            'uses' => [$class->getName(), $method->getName()],
        ];

        if ($schemes && in_array('https', $schemes)) {
            $action[] = 'https';
        }

        if ($schemes && in_array('http', $schemes)) {
            $action[] = 'http';
        }

        $route = new Route(
            $methods,
            $path,
            $action
        );

        $route->middleware($middleware);
        $route->name($name);
        $route->domain($domain);
        $route->setDefaults($defaults);
        $route->setWheres($wheres);

        if (true === $globals['scope_bindings']) {
            $route->scopeBindings();
        }

        if (false === $globals['scope_bindings']) {
            $route->withoutScopedBindings();
        }

        if ($annot->enforcesScopedBindings()) {
            $route->scopeBindings();
        }

        if ($annot->preventsScopedBindings()) {
            $route->withoutScopedBindings();
        }

        $collection->add($route->getName(), $route, $priority);
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

        $groupSettings = [];

        // Are we wrapped in a Route::group
        if ($groupStack = $this->router->getGroupStack()) {
            $groupSettings = end($groupStack);
        }

        // Add group settings to global
        if ($groupSettings) {
            if (isset($groupSettings['middleware'])) {
                $globals['middleware'] = $groupSettings['middleware'];
            }

            if (isset($groupSettings['namespace'])) {
                $globals['namespace'] = $groupSettings['namespace'];
            }

            if (isset($groupSettings['as'])) {
                $globals['name'] = $groupSettings['as'];
            }

            if (isset($groupSettings['prefix'])) {
                $globals['uri'] = $groupSettings['prefix'];
            }

            if (isset($groupSettings['where'])) {
                $globals['wheres'] = $groupSettings['where'];
            }

            if (isset($groupSettings['scope_bindings'])) {
                $globals['scope_bindings'] = $groupSettings['scope_bindings'];
            }
        }

        $annot = null;
        if ($attribute = $class->getAttributes($this->routeAnnotationClass, ReflectionAttribute::IS_INSTANCEOF)[0] ?? null) {
            $annot = $attribute->newInstance();
        }

        if ($annot) {
            $globals['controller'] = $class->getName();

            if (null !== $annot->getName()) {
                $globals['name'] = $globals['name'] . $annot->getName();
            }

            if (null !== $annot->getUri()) {
                $globals['uri'] = $globals['uri'] . '/' . ltrim($annot->getUri(), '/');
            }

            if ($annot->getSchemes()) {
                $globals['schemes'] = $annot->getSchemes();
            }

            if ($annot->getDefaults()) {
                $globals['defaults'] = $annot->getDefaults();
            }

            if ($annot->getMethods()) {
                $globals['methods'] = $annot->getMethods();
            }

            if ($annot->getDomain()) {
                $globals['domain'] = $annot->getDomain();
            }

            if ($annot->getMiddleware()) {
                $globals['middleware'] = array_merge($globals['middleware'], $annot->getMiddleware());
            }

            if ($annot->getWheres()) {
                $globals['wheres'] = array_merge($globals['wheres'], $annot->getWheres());
            }

            if (null !== $annot->isResource()) {
                $globals['resource'] = $annot->isResource();
            }

            if (null !== $annot->isSingleton()) {
                $globals['singleton'] = $annot->isSingleton();
            }

            if (null !== $annot->isApi()) {
                $globals['api'] = $annot->isApi();
            }

            if (null !== $annot->getOptions()) {
                $globals['options'] = $annot->getOptions();
            }

            if (null !== $annot->scopeBindings()) {
                $globals['scope_bindings'] = $annot->scopeBindings();
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
            'singleton' => false,
            'api' => false,
            'options' => '',
            'controller' => '',
            'priority' => 0,
            'scope_bindings' => false,
        ];
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
