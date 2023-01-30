<?php

namespace SmashedEgg\RouteAnnotation\Test\Controller;

use Illuminate\Routing\Controller;
use SmashedEgg\RouteAnnotation\Route;

#[Route('/test2', name: 'test2.')]
class Test2Controller extends Controller
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
