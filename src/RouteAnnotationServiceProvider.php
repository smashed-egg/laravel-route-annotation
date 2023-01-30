<?php

namespace SmashedEgg\RouteAnnotation;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use SmashedEgg\RouteAnnotation\Console\RouteDebugCommand;
use SmashedEgg\RouteAnnotation\Console\RouteMatchCommand;
use SmashedEgg\RouteAnnotation\Loader\AnnotationDirectoryLoader;
use SmashedEgg\RouteAnnotation\Loader\AnnotationClassLoader;

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

        Route::macro('annotation', function($controller) {
            $loader = app()->make(AnnotationClassLoader::class);
            $collection = $loader->load($controller);
            /**
             * @var string $name
             * @var Route $route
             */
            foreach ($collection as $route) {
                Route::getRoutes()->add($route);
            }
        });

        Route::macro('directory', function($directory) {
            $loader = app()->make(AnnotationDirectoryLoader::class);
            $collection = $loader->load($directory);
            /**
             * @var string $name
             * @var Route $route
             */
            foreach ($collection as $route) {
                Route::getRoutes()->add($route);
            }
        });
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
}
