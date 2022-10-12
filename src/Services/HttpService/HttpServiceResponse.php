<?php

namespace Kiwilan\Steward\Services\HttpService;

use Illuminate\Http\Client\Response;

/**
 * Manage responses from HttpService with external API.
 *
 * @property int|string          $id         id
 * @property ?Response           $response   response
 * @property HttpServiceMetadata $metadata   response
 * @property bool                $success    success
 * @property bool                $body_exist body_exist
 * @property mixed               $body       body
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
     * @param  int|string  $id
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
    public function body(): ?array
    {
        return $this->body;
    }

    /**
     * Body as `json`.
     */
    public function json(): ?string
    {
        return $this->response?->json();
    }

    /**
     * Body as `object`.
     */
    public function object(): ?object
    {
        return json_decode(json_encode($this->body ?? []));
    }

    /**
     * Check if `$key` exist into `body`.
     */
    public function bodyKeyExists(string $key): bool
    {
        try {
            return array_key_exists($key, $this->body);
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Check if `$key` exist into `body`.
     */
    public function bodyRecursiveKeyExists(string $key): bool
    {
        return $this->keyExists($this->body, $key);
    }

    /**
     * Find `$key` into `body`.
     */
    public function bodyRecursiveKeyFind(string $key): ?string
    {
        return $this->keyFind($this->body, $key);
    }

    /**
     * Check if key exists in array.
     */
    private function keyExists(?array $array, string $keySearch): bool
    {
        if (! $array) {
            return false;
        }

        foreach ($array as $key => $item) {
            if ($key == $keySearch) {
                return true;
            }
            if (is_array($item) && $this->keyExists($item, $keySearch)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find key in array.
     *
     * @return null|array|string
     */
    private function keyFind(?array $array, string $keySearch)
    {
        if (! $array) {
            return null;
        }

        foreach ($array as $key => $item) {
            if ($key == $keySearch) {
                return $array[$keySearch];
            }
            if (is_array($item) && $array = $this->keyFind($item, $keySearch)) {
                return $array;
            }
        }

        return null;
    }
}
