<?php

namespace SmashedEgg\LaravelRouteAnnotation;

/**
 * Annotation class for @ResourceRoute().
 *
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS", "METHOD"})
 *
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class ResourceRoute extends Route
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
            resource: true
        );
    }
}
