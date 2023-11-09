<?php

namespace SmashedEgg\LaravelRouteAnnotation;

use Countable;
use ArrayIterator;
use IteratorAggregate;
use Illuminate\Routing\Route;

/**
 * A RouteCollection represents a set of Route instances.
 *
 * Base on the RouteCollection from Symfony
 *
 * When adding a route at the end of the collection, an existing route
 * with the same name is removed first. So there can only be one route
 * with a given name.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 *
 * @implements IteratorAggregate<string, Route>
 */
class RouteCollection implements IteratorAggregate, Countable
{
    /**
     * @var array<string, Route>
     */
    private array $routes = [];

    /**
     * @var array<string, int>
     */
    private array $priorities = [];

    /**
     * Gets the current RouteCollection as an Iterator that includes all routes.
     *
     * It implements IteratorAggregate.
     *
     * @see all()
     *
     * @return ArrayIterator<string, Route>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->all());
    }

    /**
     * Gets the number of Routes in this collection.
     */
    public function count(): int
    {
        return \count($this->routes);
    }

    /**
     * @return void
     */
    public function add(string $name, Route $route, int $priority = 0)
    {
        unset($this->routes[$name], $this->priorities[$name]);

        $this->routes[$name] = $route;

        if ($priority) {
            $this->priorities[$name] = $priority;
        }
    }

    /**
     * Returns all routes in this collection.
     *
     * @return array<string, Route>
     */
    public function all(): array
    {
        if ($this->priorities) {
            $priorities = $this->priorities;
            $keysOrder = array_flip(array_keys($this->routes));
            uksort($this->routes, static fn ($n1, $n2) => (($priorities[$n2] ?? 0) <=> ($priorities[$n1] ?? 0)) ?: ($keysOrder[$n1] <=> $keysOrder[$n2]));
        }

        return $this->routes;
    }


}