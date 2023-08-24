<?php

namespace SmashedEgg\LaravelRouteAnnotation\Test\Controller;

use Illuminate\Routing\Controller;
use SmashedEgg\LaravelRouteAnnotation\ResourceRoute;
use SmashedEgg\LaravelRouteAnnotation\Route;

#[ResourceRoute(name: 'extra', options: ['only' => ['index']])]
class ResourceAndExtraRouteController extends Controller
{
    public function index()
    {
        return response()->make('home');
    }

    #[Route(uri: 'top', name: 'top')]
    public function top()
    {

    }
}
