<?php

namespace Kiwilan\Steward\Services\Social;

use GuzzleHttp\Client;

class SocialServiceTwitter
{
    protected function __construct(
        protected string $url,
        protected ?string $media_id = null,
        protected ?string $embed_url = null,
        protected ?array $response = [],
        protected bool $is_valid = false,
    ) {
    }

    public static function make(string $url): self
    {
        $social = new SocialServiceTwitter($url);
        $social->fetchOembedApi();

        return $social;
    }

    public function getMediaId(): ?string
    {
        return $this->media_id;
    }

    public function getEmbedUrl(): ?string
    {
        return $this->embed_url;
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    public function getIsValid(): bool
    {
        return $this->is_valid;
    }

    public function getHtml(): ?string
    {
        return $this->response['html'] ?? null;
    }

    public function getIframeSrc(): ?string
    {
        $html = $this->getHtml();
        $encoded = rawurlencode($html);

        return "data:text/html;charset=utf-8,{$encoded}";
    }

    private function fetchOembedApi(): self
    {
        $regex = '/\\/(\\d+)\\/?$/is';

        if (preg_match($regex, $this->url, $matches)) {
            $this->media_id = $matches[1]
                ? $matches[1]
                : ($matches[0] ?? null);
            $this->is_valid = true;
        }

        $client = new Client(['http_errors' => false]);

        $url = "https://publish.twitter.com/oembed?url={$this->url}";
        $query = http_build_query([
            'align' => 'center',
            'conversation' => 'none',
            'hide_media' => 'true',
            'lang' => 'fr',
            'theme' => 'dark',
            'omit_script' => true,
        ]);
        $this->embed_url = "{$url}?{$query}";
        $response = $client->get($this->embed_url);

        $body = $response->getBody()->getContents();
        $this->response = json_decode($body, true);

        return $this;
    }
}
