<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route as FacadesRoute;

class RouteService
{
    /**
     * @param  Route[]  $routes
     * @param  string[]  $skip
     * @param  Collection<string, RouteItem>  $list
     */
    protected function __construct(
        protected array $routes = [],
        protected array $skip = [],
        protected ?Collection $list = null,
    ) {
    }

    public static function make(array $skip = []): self
    {
        $routes = FacadesRoute::getRoutes()->getRoutes();
        ray($routes);

        $self = new self(
            routes: $routes,
            skip: $skip,
        );
        $self->list = $self->setList();
        ray($self->list);

        return $self;
    }

    /**
     * @return Collection<string, RouteItem>
     */
    public function get(): Collection
    {
        return $this->list;
    }

    public function byName(string $name): ?RouteItem
    {
        return $this->list->get($name);
    }

    public function byUri(string $uri): ?RouteItem
    {
        return $this->list->first(fn (RouteItem $item) => $item->uri() === $uri);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $items = [];

        foreach ($this->list as $name => $route) {
            $items[$name] = $route->toArray();
        }

        return $items;
    }

    private function setList(): Collection
    {
        /** @var Collection<string, RouteItem> $list */
        $list = collect([]);

        foreach ($this->routes as $route) {
            $uri = $route->uri();
            $name = $route->getName();

            if (! $name) {
                $name = $uri;
            }

            $list->put($name, $this->route($route, $uri));

            foreach ($this->skip as $value) {
                if (str_contains($name, $value)) {
                    $list->forget($name);
                }
            }
        }

        return $list->sortKeys();
    }

    private function route(Route $route, string $uri): RouteItem
    {
        return new RouteItem(
            name: $route->getName(),
            methods: $route->methods(),
            uri: $route->uri(),
            action: $route->getActionName(),
            middleware: $route->middleware(),
            example: config('app.url')."/{$uri}",
            parameters: $route->parameterNames(),
        );
    }
}

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
    ) {
    }

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
