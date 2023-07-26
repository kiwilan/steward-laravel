<?php

namespace Kiwilan\Steward\Utils;

class NavigationItem
{
    public function __construct(
        public ?string $title = null,
        public ?string $route = null,
        public ?string $active = null,
        public ?string $icon = null,
        public bool $external = false,
    ) {
    }
}
