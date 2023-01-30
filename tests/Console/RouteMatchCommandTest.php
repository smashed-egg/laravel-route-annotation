<?php

namespace Tests\Console;

use Illuminate\Foundation\Application;
use Illuminate\Console\Application as ConsoleApplication;
use Illuminate\Support\Facades\Route;
use SmashedEgg\RouteAnnotation\Console\RouteMatchCommand;
use SmashedEgg\RouteAnnotation\RouteAnnotationServiceProvider;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\TestCase;

class RouteMatchCommandTest extends TestCase
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
        Route::get('/account/user', ['Controller', '__invoke'])->name('a_route');

        $this
            ->artisan('se:route:match', [
                'path_info' => '/account/user',
                '--method' => 'GET',
            ])
            ->expectsOutputToContain('Route "a_route" matches')
            ->assertExitCode(0)
        ;
    }

    public function testCommand2()
    {
        Route::get('/account/user', ['Controller', '__invoke'])->name('a_route');
        Route::get('/account/thing', ['Controller', '__invoke'])->name('a_route2');

        $this
            ->artisan('se:route:match', [
                'path_info' => '/account',
                '--method' => 'GET',
            ])
            ->expectsOutputToContain('None of the routes match the path "/account"')
            ->assertExitCode(1)
        ;
    }

    public function testCommand3()
    {
        Route::get('/account/user', ['Controller', '__invoke'])->name('a_route');
        Route::get('/account/thing', ['Controller', '__invoke'])->name('a_route2');

        $this
            ->artisan('se:route:match', [
                'path_info' => '/account',
                '--method' => 'GET',
                '--verbose' => true,
            ])
            ->expectsOutputToContain('Route "a_route" does not match: Path "/account/user" does not match')
            ->expectsOutputToContain('Route "a_route2" does not match: Path "/account/thing" does not match')
            ->expectsOutputToContain('None of the routes match the path "/account"')
            ->assertExitCode(1)
        ;
    }

    private function createCommandTester(): CommandTester
    {
        $application = new Application();
        //$application->
        //$application->add(new RouterMatchCommand($this->getRouter()));
        //$application->add(new RouterDebugCommand($this->getRouter()));

        return new CommandTester(new RouteMatchCommand($this->getRouter()));
    }

    protected function getRouter()
    {
        return $this->app->make('router');
    }
}
