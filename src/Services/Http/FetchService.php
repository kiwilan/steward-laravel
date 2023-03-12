<?php

namespace Kiwilan\Steward\Services\Http;

use GuzzleHttp\Client;
use Kiwilan\Steward\Utils\Console;

class FetchService
{
    protected function __construct(
        protected string $url,
        protected Client $client,
        protected string $method = 'GET',
        protected ?HttpResponse $response = null,
    ) {
    }

    /**
     * Create FetchService instance.
     */
    public static function make(string $url, string $method = 'GET'): HttpResponse
    {
        $client = new \GuzzleHttp\Client();

        $console = Console::make();
        $self = new self($url, $client, $method);

        $start_time = microtime(true);
        $domain = parse_url($self->url, PHP_URL_HOST);
        $console->newLine();
        $console->print("  HttpService fetching {$domain}...", 'bright-blue');
        $self->response = HttpResponse::make('self', $client->request($method, $url));

        if ($self->response->metadata()->statusCode() === 200) {
            $console->print('  Success!', 'green');
        } else {
            $console->print('  Failed!', 'red');
        }

        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time);
        $execution_time = number_format((float) $execution_time, 2, '.', '');
        $console->print("  Done in {$execution_time} seconds.", 'bright-green');

        return $self->response;
    }
}
