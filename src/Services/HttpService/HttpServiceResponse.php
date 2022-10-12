<?php

namespace Kiwilan\Steward\Services\HttpService;

use DateTime;
use Illuminate\Http\Client\Response;

/**
 * Manage responses from HttpService with external API.
 *
 * @property string|int                    $id   id
 * @property ?Response                    $response      response
 */
class HttpServiceResponse
{
    public function __construct(
        public mixed $id,
        public ?Response $response,
        public bool $is_json = false,
        public bool $is_xml = false,
        public bool $success = false,
        public ?string $server = null,
        public ?DateTime $date_time = null,
        public ?string $content_type = null,
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
        $http_service_response = new HttpServiceResponse($id, $response);
        $http_service_response->setMetadata();

        return $http_service_response;
    }

    public function setMetadata(): self
    {
        if (! $this->response) {
            return $this;
        }

        $this->is_json = $this->response->header('Content-Type') === 'application/json';
        $this->is_xml = $this->response->header('Content-Type') === 'application/xml';
        $this->success = $this->response->successful();
        $this->server = $this->response->header('Server');
        $date = $this->response->header('Date');
        $date_time = DateTime::createFromFormat('Y-m-d H:m:s', $date);
        $this->date_time = $date_time ? $date_time : null;
        $this->content_type = $this->response->header('Content-Type');

        return $this;
    }
}
