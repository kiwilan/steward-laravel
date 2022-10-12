<?php

namespace Kiwilan\Steward\Services\HttpService;

use GuzzleHttp\Psr7\Response;

/**
 * Manage responses from HttpService with external API.
 *
 * @property int     $status_code  status_code
 * @property string  $reason       reason
 * @property bool    $is_json      is_json
 * @property bool    $is_xml       is_xml
 * @property ?string $server       server
 * @property ?string $date         date
 * @property ?string $content_type content_type
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

        return $metadata;
    }
}
