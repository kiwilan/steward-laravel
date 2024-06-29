<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\Services\OpenGraph\OpenGraphItem;
use Kiwilan\Steward\Services\OpenGraph\OpenGraphTwitter;

class OpenGraphService
{
    protected function __construct(
        protected string $url,
        protected ?OpenGraphItem $openGraph = null,
        protected bool $is_twitter = false,
    ) {}

    public static function make(string $url): ?OpenGraphItem
    {
        $service = new OpenGraphService($url);

        if (str_contains($service->url, 'twitter')) {
            $service->is_twitter = true;
            $twitter = OpenGraphTwitter::make($service->url);
            $service->openGraph = $twitter->getOpenGraph();

            return $service->openGraph;
        }

        // TODO twitter webpage into website settings, media lozad
        $service->openGraph = OpenGraphItem::make($service->url);

        return $service->openGraph;
    }
}
