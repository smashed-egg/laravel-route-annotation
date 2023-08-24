<?php

namespace SmashedEgg\LaravelRouteAnnotation;

use Illuminate\Routing\ResourceRegistrar;
use Illuminate\Routing\Router;

class RouteRegistrar
{
    public function __construct(
        protected Router $router,
        protected ResourceRegistrar $resourceRegistrar
    ) {
    }


}