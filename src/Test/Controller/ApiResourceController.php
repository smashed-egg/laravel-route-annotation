<?php

namespace SmashedEgg\LaravelRouteAnnotation\Test\Controller;

use Illuminate\Routing\Controller;
use SmashedEgg\LaravelRouteAnnotation\ApiResourceRoute;

#[ApiResourceRoute(name: 'reports.player')]
class ApiResourceController extends Controller
{
    public function index()
    {
    }

    public function show()
    {
    }

    public function store()
    {
    }

    public function update()
    {
    }

    public function destroy()
    {
    }
}
