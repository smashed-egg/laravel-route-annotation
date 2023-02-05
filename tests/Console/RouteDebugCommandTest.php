<?php

namespace SmashedEgg\LaravelRouteAnnotation\Tests\Console;

use Illuminate\Foundation\Application;
use Illuminate\Console\Application as ConsoleApplication;
use Illuminate\Support\Facades\Route;
use SmashedEgg\LaravelRouteAnnotation\Console\RouteMatchCommand;
use SmashedEgg\LaravelRouteAnnotation\RouteAnnotationServiceProvider;
use Symfony\Component\Console\Tester\CommandTester;
use SmashedEgg\LaravelRouteAnnotation\Tests\TestCase;

class RouteDebugCommandTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            RouteAnnotationServiceProvider::class,
        ];
    }

    public function testCommand()
    {
        Route::get('/my-account', ['Controller', '__invoke'])->name('my_account');

        $this
            ->artisan('se:route:debug', [
                'name' => 'my_account',
            ])
            ->assertExitCode(0)
        ;
    }
}
