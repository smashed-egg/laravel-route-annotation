<?php

namespace SmashedEgg\LaravelRouteAnnotation\Test\Controller;

use Illuminate\Routing\Controller;
use SmashedEgg\LaravelRouteAnnotation\Route;

#[Route(uri: 'single', name: 'single')]
class SingleActionController extends Controller
{
    public function __invoke()
    {
        return response()->make('home');
    }
}
