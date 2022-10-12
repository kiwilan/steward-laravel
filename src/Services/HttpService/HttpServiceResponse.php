<?php

namespace Kiwilan\Steward\Services\HttpService;

use Illuminate\Http\Client\Response;

/**
 * Manage responses from HttpService with external API.
 *
 * @property string|int $id id
 * @property ?Response $response response
 * @property HttpServiceMetadata $metadata response
 * @property bool $success success
 * @property bool $body_exist body_exist
 * @property mixed $body body
 */
class HttpServiceResponse
{
    public function __construct(
        public mixed $id,
        public ?Response $guzzle,
        public HttpServiceMetadata $metadata,
        public bool $success = false,
        public bool $body_exist = false,
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
        $hs_response->body = $body;
        if ($hs_response->body) {
            $hs_response->body_exist = true;
        }

        return $hs_response;
    }

    /**
     * Body as `array`.
     */
    public function body(): array
    {
        return $this->body;
    }

    /**
     * Body as `json`.
     */
    public function json(): string
    {
        return $this->response?->json();
    }

    /**
     * Body as `object`.
     */
    public function object(): object
    {
        return json_decode(json_encode($this->body));
    }

    /**
     * Check if `$key` exist into `body`.
     */
    public function bodyKeyExists(string $key): bool
    {
        return $this->findKey($this->body, $key);
    }

    /**
     * Check if key exists in array.
     */
    private function findKey(array $array, string $keySearch): bool
    {
        foreach ($array as $key => $item) {
            if ($key == $keySearch) {
                return true;
            } elseif (is_array($item) && $this->findKey($item, $keySearch)) {
                return true;
            }
        }

        return false;
    }

    // @phpstan-ignore-next-line
    private function arrayKeyExists(string $needle, array $haystack): bool
    {
        $result = array_key_exists($needle, $haystack);
        if ($result) {
            return $result;
        }

        foreach ($haystack as $v) {
            if (is_array($v)) {
                $result = $this->arrayKeyExists($needle, $v);
            }
            if ($result) {
                return $result;
            }
        }

        return $result;
    }
}
