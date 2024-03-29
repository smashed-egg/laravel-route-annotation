<?php

namespace SmashedEgg\LaravelRouteAnnotation;

/**
 * Annotation class for @ApiResourceRoute().
 *
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS", "METHOD"})
 *
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class ApiResourceRoute extends Route
{
    public function __construct(
        private ?string $name = null,
        private array $middleware = [],
        private array $options = []
    )
    {
        parent::__construct(
            name: $name,
            middleware: $middleware,
            options: $options,
            api: true
        );
    }
}
