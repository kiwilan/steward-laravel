<?php

namespace Kiwilan\Steward\Utils;

class NavigationItem
{
    public function __construct(
        public ?string $label = null,
        public ?string $route = null,
        public ?string $active = null,
        public ?string $icon = null,
        public bool $external = false,
    ) {
    }

    public function current(): bool
    {
        return request()->routeIs($this->active);
    }
}
