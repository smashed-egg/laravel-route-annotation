<?php

namespace SmashedEgg\LaravelRouteAnnotation;

/**
 * Annotation class for @Route().
 *
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS", "METHOD"})
 *
 */
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        private ?string $uri = null,
        private ?string $name = null,
        private ?string $domain = null,
        private array $schemes = [],
        private array $defaults = [],
        private array $methods = [],
        private array $middleware = [],
        private array $wheres = [],
        private array $options = [],
        private bool $resource = false,
        private bool $api = false,
        private bool $singleton = false,
        private int $priority = 0,
        private ?bool $scopeBindings = null
    )
    {
    }

    /**
     * @return string|null
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return array|string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return array|string[]
     */
    public function getSchemes(): array
    {
        return $this->schemes;
    }

    /**
     * @return array|string[]
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * @return array
     */
    public function getWheres(): array
    {
        return $this->wheres;
    }

    /**
     * @return string|null
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function isResource(): bool
    {
        return $this->resource;
    }

    /**
     * @return bool
     */
    public function isApi(): bool
    {
        return $this->api;
    }

    /**
     * @return bool
     */
    public function isSingleton(): bool
    {
        return $this->singleton;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return bool|null
     */
    public function scopeBindings()
    {
        return $this->scopeBindings;
    }

    public function enforcesScopedBindings(): bool
    {
        return true === $this->scopeBindings();
    }

    public function preventsScopedBindings(): bool
    {
        return false === $this->scopeBindings();
    }
}
