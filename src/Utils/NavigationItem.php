<?php

namespace Kiwilan\Steward\Utils;

use Kiwilan\Steward\Enums\UserRoleEnum;

class NavigationItem
{
    public function __construct(
        public ?string $label = null,
        public ?string $route = null,
        public ?string $active = null,
        public bool $activeStrict = false,
        public ?string $icon = null,
        public bool $external = false,
        public ?UserRoleEnum $role = null,
    ) {}

    public function current(): bool
    {
        return request()->routeIs($this->active);
    }
}
