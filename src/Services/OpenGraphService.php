<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\Services\OpenGraphService\OpenGraphItem;
use Kiwilan\Steward\Services\OpenGraphService\OpenGraphTwitter;

class OpenGraphService
{
    protected function __construct(
        protected string $url,
        protected ?string $body = null,
        protected ?OpenGraphItem $openGraph = null,
        protected bool $is_twitter = false,
    ) {
    }

    public static function make(string $url): ?OpenGraphItem
    {
        $service = new OpenGraphService($url);

        if (str_contains($service->url, 'twitter')) {
            $service->openGraph = $service->twitter()
                ->getOpenGraph();
        } else {
            $service->openGraph = OpenGraphItem::make($service->url);
        }

        // TODO twitter webpage into website settings

        return $service->openGraph;
    }

    private function twitter(): OpenGraphTwitter
    {
        $this->is_twitter = true;

        return OpenGraphTwitter::make($this->url);
    }
}
