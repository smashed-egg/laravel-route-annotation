<?php

namespace SmashedEgg\LaravelRouteAnnotation\Test\Controller;

use Illuminate\Routing\Controller;
use SmashedEgg\LaravelRouteAnnotation\ResourceRoute;

#[ResourceRoute(name: 'photos')]
class ResourceController extends Controller
{
    public function index()
    {
        return response()->make('home');
    }

    public function create()
    {
        return response()->make('create');
    }

    public function store()
    {
        return response()->make('create');
    }

    public function edit($id)
    {
        return response()->make('edit');
    }

    public function update()
    {
    }

    public function destroy()
    {
    }
}
