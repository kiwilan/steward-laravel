<?php

namespace Kiwilan\Steward\Services\Datayable;

class DatayableItem
{
    public function __construct(
        public string $name,
        public string $label,
        public string $placeholder = 'username',
        public ?string $value = null,
        public ?string $icon = null,
        public ?string $color = null,
        public ?string $url = null,
        public ?string $full_url = null,
        public ?string $display_url = null,
        public ?string $type = null,
        public bool $with_at = false,
        public bool $is_active = true,
        public bool $is_link = true,
    ) {}
}
