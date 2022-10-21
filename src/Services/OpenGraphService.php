<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\Services\OpenGraphService\OpenGraphItem;
use Kiwilan\Steward\Services\OpenGraphService\OpenGraphTwitter;

class OpenGraphService
{
    protected function __construct(
        protected string $url,
        protected ?OpenGraphItem $openGraph = null,
        protected bool $is_twitter = false,
    ) {
    }

    public static function make(string $url): ?OpenGraphItem
    {
        $service = new OpenGraphService($url);

        if (str_contains($service->url, 'twitter')) {
            $service->is_twitter = true;
            $twitter = OpenGraphTwitter::make($service->url);
            $service->openGraph = $twitter->getOpenGraph();

            return $service->openGraph;
        }

        // TODO twitter webpage into website settings
        $service->openGraph = OpenGraphItem::make($service->url);

        return $service->openGraph;
    }
}
