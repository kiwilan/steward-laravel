<?php

namespace Kiwilan\Steward\Services\Social\Modules;

use Kiwilan\Steward\Services\Social\SocialInterface;
use Kiwilan\Steward\Services\Social\SocialModule;

class SocialSpotify extends SocialModule implements SocialInterface
{
    protected function __construct(
        $url,
        protected ?string $type = null,
    ) {
        parent::__construct($url);
    }

    public static function make(string $url): self
    {
        $module = new SocialSpotify($url);
        $module->regex();
        $module->setHtml();

        return $module;
    }

    public function regex()
    {
        $regex = '/^(https:\/\/open.spotify.com\/|user:track:album:artist:playlist:)([a-zA-Z0-9]+)(.*)$/m';

        if (preg_match($regex, $this->url, $matches)) {
            $this->type = $matches[2] ?? 'track';
            $this->media_id = $matches[3]
                ? str_replace('/', '', $matches[3])
                : null;

            $url = "https://open.spotify.com/embed/{$this->type}/{$this->media_id}?";
            $query = http_build_query([
                'utm_source' => 'generator',
                'theme' => '1',
            ]);

            $this->embed_url = "{$url}{$query}";
        }
    }
}
