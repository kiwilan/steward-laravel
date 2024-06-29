<?php

namespace Kiwilan\Steward\Utils\Wikipedia;

class WikipediaClient
{
    protected function __construct(
        protected string $url,
        protected ?array $body = null,
        protected bool $isFailed = false,
    ) {}

    public static function make(string $url, bool $json = true, bool $skipErrors = true): self
    {
        $self = new self($url);

        $client = new \GuzzleHttp\Client();
        $options = [
            'http_errors' => $skipErrors,
        ];

        if ($json) {
            $options['headers'] = [
                'Accept' => 'application/json',
            ];
        }

        $response = $client->request('GET', $self->url, $options);

        $self->isFailed = $response->getStatusCode() !== 200;

        if ($self->isFailed) {
            return $self;
        }

        $body = $response->getBody()->getContents();
        $self->body = (array) json_decode($body, true);

        return $self;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getBody(): ?array
    {
        return $this->body;
    }

    public function isFailed(): bool
    {
        return $this->isFailed;
    }
}
