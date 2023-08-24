<?php

namespace SmashedEgg\LaravelRouteAnnotation\Test\Controller;

use Illuminate\Routing\Controller;
use SmashedEgg\LaravelRouteAnnotation\SingletonResourceRoute;

#[SingletonResourceRoute(name: 'profile')]
class SingletonResourceController extends Controller
{
    public function show()
    {
    }

    public function edit()
    {
    }

    public function update()
    {
    }
}
