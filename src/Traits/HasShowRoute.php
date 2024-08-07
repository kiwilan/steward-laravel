<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * @property array $meta with [`slug` => show_route_column, `show` => `$show_route`]
 * @property string $show_route
 */
trait HasShowRoute
{
    protected $default_show_route_column = 'slug';

    public function getShowRouteColumn(): string
    {
        return $this->show_route_column ?? $this->default_show_route_column;
    }

    public function getShowRouteAttribute(): ?string
    {
        $instance = new $this;
        $class = new ReflectionClass($instance);
        $static = $class->getName();
        $route_name = Str::kebab($class->getShortName());
        $route_name = str_replace('-', '.', $route_name);
        $param_name = str_replace('.', '_', $route_name);

        return route("api.{$route_name}s.show", [
            "{$param_name}_{$this->getShowRouteColumn()}" => $this->{$this->getShowRouteColumn()},
        ]);
    }

    public function getMetaAttribute(): array
    {
        return [
            $this->getShowRouteColumn() => $this->{$this->getShowRouteColumn()},
            'show' => $this->show_route,
        ];
    }
}
