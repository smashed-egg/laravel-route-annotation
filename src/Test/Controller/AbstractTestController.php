<?php

namespace SmashedEgg\LaravelRouteAnnotation\Test\Controller;

use Illuminate\Routing\Controller;
use SmashedEgg\LaravelRouteAnnotation\Route;

#[Route('/test', name: 'test.')]
abstract class AbstractTestController extends Controller
{
    #[Route('/', name: 'home', methods: ['GET', 'POST'])]
    public function home()
    {
        return response()->make('home');
    }
}
