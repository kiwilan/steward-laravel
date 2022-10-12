<?php

namespace Kiwilan\Steward\Services\HttpService;

use GuzzleHttp\Psr7\Response;

/**
 * Manage responses from HttpService with external API.
 *
 * @property int     $status_code  Request status code
 * @property string  $reason       Request reason phrase
 * @property bool    $is_json      Content-Type is JSON
 * @property bool    $is_xml       Content-Type is XML
 * @property ?string $server       Server header
 * @property ?string $date         Date header
 * @property ?string $content_type Content-Type header
 * @property ?string $origin       Request origin URL
 */
class HttpServiceMetadata
{
    public function __construct(
        public int $status_code = 404,
        public ?string $reason = null,
        public bool $is_json = false,
        public bool $is_xml = false,
        public ?string $server = null,
        public ?string $date = null,
        public ?string $content_type = null,
        public ?string $origin = null,
    ) {
    }

    /**
     * Create HttpServiceMetadata from HttpServiceResponse.
     *
     * @param  ?\GuzzleHttp\Psr7\Response  $response
     */
    public static function make(?Response $response): self
    {
        $metadata = new HttpServiceMetadata();

        if (! $response) {
            $metadata->date = now();

            return $metadata;
        }

        $content_type = $response->getHeaderLine('Content-Type');

        $metadata->status_code = $response->getStatusCode();
        $metadata->reason = $response->getReasonPhrase();
        $metadata->is_json = str_contains($content_type, 'json');
        $metadata->is_xml = str_contains($content_type, 'xml');
        $metadata->server = $response->getHeaderLine('Server');
        $metadata->date = $response->getHeaderLine('Date');
        $metadata->content_type = $content_type;
        $metadata->origin = $metadata->getOrigin($response);

        return $metadata;
    }

    /**
     * Get query URL from Response.
     */
    public function getOrigin(?Response $response): ?string
    {
        $origin = $response->getHeader('Origin');
        if (array_key_exists(0, $origin)) {
            return $origin[0];
        }

        return null;
    }
}
