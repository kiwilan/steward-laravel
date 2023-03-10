<?php

namespace Kiwilan\Steward\Services;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class FetchService
{
    protected function __construct(
        protected string $url,
        protected Client $client,
        protected string $method = 'GET',
        protected ?ResponseInterface $response = null,
    ) {
    }

    /**
     * Create FetchService instance.
     */
    public static function request(string $url, string $method = 'GET'): FetchService
    {
        $client = new \GuzzleHttp\Client();

        $self = new self($url, $client, $method);
        $self->response = $client->request($method, $url);

        return $self;
    }

    public function response(): ResponseInterface
    {
        return $this->response;
    }

    public function headers(): array
    {
        return $this->response->getHeaders();
    }

    public function status(): int
    {
        return $this->response->getStatusCode();
    }

    public function body(): string
    {
        return $this->response->getBody()->getContents();
    }

    public function json(): array
    {
        return json_decode($this->body(), true);
    }
}
