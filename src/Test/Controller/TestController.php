<?php

namespace SmashedEgg\LaravelRouteAnnotation\Test\Controller;

use Illuminate\Routing\Controller;
use SmashedEgg\LaravelRouteAnnotation\Route;

#[Route('/test', name: 'test.')]
class TestController extends Controller
{
    #[Route('/', name: 'home', methods: ['GET', 'POST'])]
    public function home()
    {
        return response()->make('home');
    }

    #[Route('/list', name: 'list', methods: ['GET', 'POST'])]
    public function list()
    {
        return response()->make('list');
    }
}
