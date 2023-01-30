<?php

namespace SmashedEgg\RouteAnnotation\Loader;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use InvalidArgumentException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use SmashedEgg\RouteAnnotation\Route as RouteAnnotation;

/**
 * AnnotationClassLoader loads routing information from annotations set
 * on PHP classes and methods via PHP Attributes
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
        protected ?string $env = null,
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
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $class = new ReflectionClass($class);
        if ($class->isAbstract()) {
            throw new InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class->getName()));
        }

        $globals = $this->getGlobals($class);

        $collection = new RouteCollection();

        if ($globals['env'] && $this->env !== $globals['env']) {
            return $collection;
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
    protected function addRoute(RouteCollection $collection, object $annot, array $globals, ReflectionClass $class, ReflectionMethod $method)
    {
        if ($annot->getEnv() && $annot->getEnv() !== $this->env) {
            return;
        }

        $name = $annot->getName();
        if (null === $name) {
            $name = $this->getDefaultRouteName($class, $method);
        }
        $name = $globals['name'].$name;

        /*$requirements = $annot->getRequirements();

        foreach ($requirements as $placeholder => $requirement) {
            if (\is_int($placeholder)) {
                throw new \InvalidArgumentException(sprintf('A placeholder name must be a string (%d given). Did you forget to specify the placeholder key for the requirement "%s" of route "%s" in "%s::%s()"?', $placeholder, $requirement, $name, $class->getName(), $method->getName()));
            }
        }*/

        //$defaults = array_replace($globals['defaults'], $annot->getDefaults());
        //$requirements = array_replace($globals['requirements'], $requirements);
        //$options = array_replace($globals['options'], $annot->getOptions());
        $schemes = [];
        //$schemes = array_merge($globals['schemes'], $annot->getSchemes());
        $methods = array_merge($globals['methods'], $annot->getMethods());
        $middleware = array_merge($globals['middleware'], $annot->getMiddleware());
        $wheres = array_merge($globals['wheres'], $annot->getWheres());

        $domain = $annot->getDomain();
        if (null === $domain) {
            $domain = $globals['domain'];
        }

        $uri = $annot->getUri();
        $prefix = $globals['uri'];
        $paths = [];

        $paths[] = $prefix.$uri;

        foreach ($method->getParameters() as $param) {
            if (isset($defaults[$param->name]) || !$param->isDefaultValueAvailable()) {
                continue;
            }
            foreach ($paths as $locale => $path) {
                if (preg_match(sprintf('/\{%s(?:<.*?>)?\}/', preg_quote($param->name)), $path)) {
                    $defaults[$param->name] = $param->getDefaultValue();
                    break;
                }
            }
        }

        foreach ($paths as $locale => $uri) {
            $route = $this->createRoute($uri, $schemes, $methods, [$class->getName(), $method->getName()], $middleware);
            //$route = $this->createRoute($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
            $route->name($name);

            $route->setWheres($wheres);
            $this->configureRoute($route, $class, $method, $annot);

            $collection->add($route);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(mixed $resource, string $type = null): bool
    {
        return is_string($resource) && preg_match('/^(?:\\\\?[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)+$/', $resource) && (!$type || 'annotation' === $type);
    }

    /**
     * Gets the default route name for a class method.
     *
     * @return string
     */
    protected function getDefaultRouteName(ReflectionClass $class, ReflectionMethod $method)
    {
        $name = str_replace('\\', '_', $class->name).'_'.$method->name;
        $name = function_exists('mb_strtolower') && preg_match('//u', $name) ? mb_strtolower($name, 'UTF-8') : strtolower($name);
        if ($this->defaultRouteIndex > 0) {
            $name .= '_'.$this->defaultRouteIndex;
        }
        ++$this->defaultRouteIndex;

        return $name;
    }

    protected function getGlobals(ReflectionClass $class)
    {
        $globals = $this->resetGlobals();

        $annot = null;
        if ($attribute = $class->getAttributes($this->routeAnnotationClass, ReflectionAttribute::IS_INSTANCEOF)[0] ?? null) {
            $annot = $attribute->newInstance();
        }

        if ($annot) {
            if (null !== $annot->getName()) {
                $globals['name'] = $annot->getName();
            }

            if (null !== $annot->getUri()) {
                $globals['uri'] = $annot->getUri();
            }

            //$globals['localized_paths'] = $annot->getLocalizedPaths();

            /*if (null !== $annot->getRequirements()) {
                $globals['requirements'] = $annot->getRequirements();
            }

            if (null !== $annot->getOptions()) {
                $globals['options'] = $annot->getOptions();
            }

            if (null !== $annot->getDefaults()) {
                $globals['defaults'] = $annot->getDefaults();
            }

            if (null !== $annot->getSchemes()) {
                $globals['schemes'] = $annot->getSchemes();
            }*/

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

            /*if (null !== $annot->getCondition()) {
                $globals['condition'] = $annot->getCondition();
            }*/

            //$globals['priority'] = $annot->getPriority() ?? 0;
            //$globals['env'] = $annot->getEnv();

            /*foreach ($globals['requirements'] as $placeholder => $requirement) {
                if (\is_int($placeholder)) {
                    throw new \InvalidArgumentException(sprintf('A placeholder name must be a string (%d given). Did you forget to specify the placeholder key for the requirement "%s" in "%s"?', $placeholder, $requirement, $class->getName()));
                }
            }*/
        }

        return $globals;
    }

    private function resetGlobals(): array
    {
        return [
            'uri' => null,
            'wheres' => [],
            //'options' => [],
            //'defaults' => [],
            'schemes' => [],
            'methods' => [],
            'middleware' => [],
            'domain' => '',
            'name' => '',
            'env' => null,
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
        //return new Route($uri, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
        $route = new Route(
            $methods,
            $uri,
            $action
        );

        $route->middleware($middleware);
        //$route->setContainer(app());

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

    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, object $annot)
    {
        // @TODO - Is this needed
        //$route->setContainer(app());
        //$route->setRouter(\Illuminate\Support\Facades\Route::getFacadeRoot());
        //$route = LaravelRoute::match($route->getMethods(), $route->getPath());

        //$route->set

        //$route->d('_controller', [$class->getName(), $method->getName()]);
        //$route->setOption('_controller', [$class->getName(), $method->getName()]);
    }
}
