<?php

namespace Kiwilan\Steward\Services\Route;

class RouteItem
{
    /**
     * @param  string[]  $methods
     * @param  string[]  $middleware
     * @param  string[]  $parameters
     */
    public function __construct(
        protected ?string $name = null,
        protected ?array $methods = null,
        protected ?string $uri = null,
        protected ?string $action = null,
        protected ?array $middleware = null,
        protected ?string $example = null,
        protected ?array $parameters = null,
    ) {}

    public function name(): ?string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function methods(): ?array
    {
        return $this->methods;
    }

    public function uri(): ?string
    {
        return $this->uri;
    }

    public function action(): ?string
    {
        return $this->action;
    }

    /**
     * @return string[]
     */
    public function middleware(): ?array
    {
        return $this->middleware;
    }

    public function example(): ?string
    {
        return $this->example;
    }

    /**
     * @return string[]
     */
    public function parameters(): ?array
    {
        return $this->parameters;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'methods' => $this->methods,
            'uri' => $this->uri,
            'action' => $this->action,
            'middleware' => $this->middleware,
            'example' => $this->example,
            'parameters' => $this->parameters,
        ];
    }
}
