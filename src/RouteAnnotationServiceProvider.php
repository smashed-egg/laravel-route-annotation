<?php

namespace SmashedEgg\LaravelRouteAnnotation;

use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use SmashedEgg\LaravelRouteAnnotation\Console\RouteDebugCommand;
use SmashedEgg\LaravelRouteAnnotation\Console\RouteMatchCommand;
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
        $this->commands([
            RouteMatchCommand::class,
            RouteDebugCommand::class,
        ]);

        Route::macro('annotation', [$this, 'loadRoutesFromController']);

        Route::macro('directory', [$this, 'loadRoutesFromDirectory']);
    }

    public function register()
    {
        $this->app->singleton(AnnotationClassLoader::class, function() {
            return new AnnotationClassLoader(app()->environment());
        });

        $this->app->singleton(AnnotationDirectoryLoader::class, function() {
            return new AnnotationDirectoryLoader(app()->environment());
        });

        $this->app->singleton(RouteMatchCommand::class, function() {
            return new RouteMatchCommand(app()->make('router'));
        });

        $this->app->singleton(RouteDebugCommand::class, function() {
            return new RouteDebugCommand(app()->make('router'));
        });
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
            Route::getRoutes()->add($route);
        }
    }

    protected function getAnnotationClassLoader(): AnnotationClassLoader
    {
        return app()->make(AnnotationClassLoader::class);
    }

    protected function getAnnotationDirectoryLoader(): AnnotationDirectoryLoader
    {
        return app()->make(AnnotationDirectoryLoader::class);
    }
}
