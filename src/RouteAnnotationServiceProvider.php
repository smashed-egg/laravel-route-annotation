<?php

namespace SmashedEgg\LaravelRouteAnnotation;

use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use SmashedEgg\LaravelRouteAnnotation\Loader\AnnotationDirectoryLoader;
use SmashedEgg\LaravelRouteAnnotation\Loader\AnnotationClassLoader;

class RouteAnnotationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::macro('annotation', [$this, 'loadRoutesFromController']);
        Route::macro('directory', [$this, 'loadRoutesFromDirectory']);
    }

    public function register()
    {
        $this->app->singleton(AnnotationClassLoader::class, AnnotationClassLoader::class);
        $this->app->singleton(AnnotationDirectoryLoader::class, AnnotationDirectoryLoader::class);
    }

    public function loadRoutesFromController(mixed $controller)
    {
        $this->registerRoutes($this->getAnnotationClassLoader()->load($controller));
    }

    public function loadRoutesFromDirectory(mixed $directory)
    {
        $this->registerRoutes($this->getAnnotationDirectoryLoader()->load($directory));
    }

    public function registerRoutes(RouteCollection $routeCollection)
    {
        /** @var Route $route */
        foreach ($routeCollection as $route) {

            $route
                ->setRouter($this->getRouter())
                ->setContainer($this->app)
            ;

            $this->getRouter()->getRoutes()->add($route);
        }
    }

    protected function getRouter(): Router
    {
        return $this->app->make('router');
    }

    protected function getAnnotationClassLoader(): AnnotationClassLoader
    {
        return $this->app->make(AnnotationClassLoader::class);
    }

    protected function getAnnotationDirectoryLoader(): AnnotationDirectoryLoader
    {
        return $this->app->make(AnnotationDirectoryLoader::class);
    }
}
