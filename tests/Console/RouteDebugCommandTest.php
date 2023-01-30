<?php

namespace Tests\Console;

use Illuminate\Foundation\Application;
use Illuminate\Console\Application as ConsoleApplication;
use Illuminate\Support\Facades\Route;
use SmashedEgg\RouteAnnotation\Console\RouteMatchCommand;
use SmashedEgg\RouteAnnotation\RouteAnnotationServiceProvider;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\TestCase;

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
