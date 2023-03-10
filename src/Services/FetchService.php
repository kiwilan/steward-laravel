<?php

namespace Kiwilan\Steward\Services;

use GuzzleHttp\Client;
use Kiwilan\Steward\Utils\Console;
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

        $console = Console::make();
        $self = new self($url, $client, $method);

        $start_time = microtime(true);
        $domain = parse_url($self->url, PHP_URL_HOST);
        $console->newLine();
        $console->print("  Fetching {$domain}...", 'yellow');
        $self->response = $client->request($method, $url);

        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time);
        $execution_time = number_format((float) $execution_time, 2, '.', '');
        $console->print("  Done in {$execution_time} seconds.", 'green');

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

    public function xml(): array
    {
        return json_decode(json_encode(simplexml_load_string($this->body())), true);
    }
}
