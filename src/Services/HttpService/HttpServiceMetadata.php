<?php

namespace Kiwilan\Steward\Services\HttpService;

use DateTime;
use Illuminate\Http\Client\Response;

/**
 * Manage responses from HttpService with external API.
 *
 * @property bool $is_json is_json
 * @property bool $is_xml is_xml
 * @property ?string $server server
 * @property DateTime $date_time date_time
 * @property ?string $content_type content_type
 */
class HttpServiceMetadata
{
    public function __construct(
        public bool $is_json = false,
        public bool $is_xml = false,
        public ?string $server = null,
        public ?DateTime $date_time = null,
        public ?string $content_type = null,
    ) {
    }

    /**
     * Create HttpServiceMetadata from HttpServiceResponse.
     *
     * @param  ?Response  $response
     */
    public static function make(?Response $response): self
    {
        $metadata = new HttpServiceMetadata();

        if (! $response) {
            $metadata->date_time = now();

            return $metadata;
        }

        $metadata->is_json = $response->header('Content-Type') === 'application/json';
        $metadata->is_xml = $response->header('Content-Type') === 'application/xml';
        $metadata->server = $response->header('Server');
        $date = $response->header('Date');
        $date_time = DateTime::createFromFormat('Y-m-d H:m:s', $date);
        $metadata->date_time = $date_time ? $date_time : null;
        $metadata->content_type = $response->header('Content-Type');

        return $metadata;
    }
}
