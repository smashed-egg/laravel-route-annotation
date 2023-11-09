<?php

namespace SmashedEgg\LaravelRouteAnnotation\Test\Priority;

use Illuminate\Routing\Controller;
use SmashedEgg\LaravelRouteAnnotation\Route;

#[Route('/simple', name: 'simple.')]
class SimpleController extends Controller
{
    #[Route('/{something}', name: 'something')]
    public function home()
    {
        return response()->make('something');
    }

    #[Route('/list', name: 'list', priority: 1)]
    public function list()
    {
        return response()->make('list');
    }
}
