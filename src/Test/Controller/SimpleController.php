<?php

namespace SmashedEgg\LaravelRouteAnnotation\Test\Controller;

use Illuminate\Routing\Controller;
use SmashedEgg\LaravelRouteAnnotation\Route;

#[Route('/simple', name: 'simple.')]
class SimpleController extends Controller
{
    #[Route('/', name: 'home')]
    public function home()
    {
        return response()->make('home');
    }

    #[Route('list', name: 'list')]
    public function list()
    {
        return response()->make('list');
    }

    #[Route('create', name: 'create')]
    public function create()
    {
        return response()->make('create');
    }

    #[Route('edit/{id}', name: 'edit')]
    public function edit($id)
    {
        return response()->make('edit');
    }
}
