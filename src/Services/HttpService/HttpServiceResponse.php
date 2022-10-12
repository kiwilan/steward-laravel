<?php

namespace Kiwilan\Steward\Services\HttpService;

use Illuminate\Http\Client\Response;

/**
 * Manage responses from HttpService with external API.
 *
 * @property string|int                    $id   id
 * @property ?Response                    $response      response
 * @property HttpServiceMetadata                    $metadata      response
 * @property bool $success      success
 * @property mixed $body      body
 */
class HttpServiceResponse
{
    public function __construct(
        public mixed $id,
        public ?Response $guzzle,
        public HttpServiceMetadata $metadata,
        public bool $success = false,
        public mixed $body = null,
    ) {
    }

    /**
     * Create HttpServiceResponse from Response.
     *
     * @param  string|int  $id
     * @param  ?Response  $response
     */
    public static function make(mixed $id, ?Response $response): self
    {
        $metadata = HttpServiceMetadata::make($response);
        $success = ! $response ? false : $response->successful();
        $hs_response = new HttpServiceResponse(
            id: $id,
            guzzle: $response,
            metadata: $metadata,
            success: $success,
        );

        if (! $response) {
            return $hs_response;
        }

        $body = $response->json();
        $hs_response->body = json_decode(json_encode($body));

        return $hs_response;
    }
}
