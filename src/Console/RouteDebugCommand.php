<?php

namespace SmashedEgg\RouteAnnotation\Console;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Symfony\Component\Routing\Matcher\TraceableUrlMatcher;
use Symfony\Component\Routing\RequestContext;

class RouteDebugCommand extends Command
{
    protected $signature = 'se:route:debug
        {name : A route name}
        {--show-controllers : Show controllers}
        {--format=txt : Format}
        {--raw= : To output raw route(s)}
    ';

    public function __construct(protected Router $router)
    {
        parent::__construct();
    }

    public function handle()
    {
        $routes = $this->router->getRoutes();
        $routes->refreshNameLookups();
        $route = $routes->getByName($this->argument('name'));
        dd($route ? $route->getName() : null);

        //$routes = $this->router->getRoutes()->getByName($this->argument('name'));
        //dd($this->router->getRoutes()->to);
        //$routes = $this->router->getRoutes()->toSymfonyRouteCollection();
        //$route = $routes->get($this->argument('name'));
        //dd($route);
        //var_dump($routes->get($this->argument('name')));

        return 0;
    }
}
