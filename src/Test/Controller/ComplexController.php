<?php

namespace SmashedEgg\LaravelRouteAnnotation\Test\Controller;

use Illuminate\Routing\Controller;
use SmashedEgg\LaravelRouteAnnotation\Route;

#[Route('/complex', name: 'complex.')]
class ComplexController extends Controller
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

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create()
    {
        return response()->make('create');
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'], wheres: ['id' => '[0-9]+'])]
    public function edit($id)
    {
        return response()->make('edit');
    }
}
