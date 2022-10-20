<?php

namespace Kiwilan\Steward\Services\OpenGraphService;

class OpenGraphItem
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $url = null,
        public ?string $type = null,
        public ?string $site_name = null,
        public ?string $locale = null,
        public ?string $theme_color = null,
    ) {
    }
}
