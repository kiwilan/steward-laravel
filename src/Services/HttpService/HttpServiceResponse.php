<?php

namespace Kiwilan\Steward\Services\HttpService;

use GuzzleHttp\Psr7\Response;
use SimpleXMLElement;

/**
 * Manage responses from HttpService with external API.
 *
 * @property int|string                 $id         id
 * @property ?\GuzzleHttp\Psr7\Response $response   response
 * @property HttpServiceMetadata        $metadata   response
 * @property bool                       $success    success
 * @property bool                       $body_exist body_exist
 * @property ?object                    $body_json  body_json
 * @property ?SimpleXMLElement          $body_xml   body_xml
 */
class HttpServiceResponse
{
    public function __construct(
        public mixed $id,
        public ?Response $guzzle,
        public HttpServiceMetadata $metadata,
        public bool $success = false,
        public bool $body_exist = false,
        protected ?object $body_json = null,
        protected ?SimpleXMLElement $body_xml = null,
    ) {
    }

    /**
     * Create HttpServiceResponse from Response.
     *
     * @param  int|string  $id
     * @param  ?\GuzzleHttp\Psr7\Response  $response
     */
    public static function make(mixed $id, ?Response $response): self
    {
        $metadata = HttpServiceMetadata::make($response);
        $success = ! $response ? false : 200 === $response->getStatusCode();
        $hs_response = new HttpServiceResponse(
            id: $id,
            guzzle: $response,
            metadata: $metadata,
            success: $success,
        );

        if (! $response) {
            return $hs_response;
        }

        $body = $response->getBody()->getContents();
        $contents = (string) $body;

        if ($hs_response->isValidXml($contents)) {
            $hs_response->body_xml = simplexml_load_string($contents);
        } elseif ($hs_response->isValidJson($contents)) {
            $contents = json_decode($contents);
            $hs_response->body_json = is_object($contents) ? $contents : null;
        }

        if ($hs_response->body_xml || $hs_response->body_json) {
            $hs_response->body_exist = true;
        }

        return $hs_response;
    }

    /**
     * Body as `object`.
     *
     * @return object|SimpleXMLElement|null
     */
    public function body()
    {
        return $this->body_json ?? $this->body_xml;
    }

    /**
     * Body as `json`.
     */
    public function json(): ?string
    {
        return json_encode($this->body());
    }

    /**
     * Body as `array`.
     */
    public function array(): ?array
    {
        return json_decode(json_encode($this->body() ?? []), true);
    }

    /**
     * Check if `$key` exist into `body`.
     */
    public function bodyKeyExists(string $key): bool
    {
        try {
            return array_key_exists($key, $this->array());
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Check if `$key` exist into `body`.
     */
    public function bodyRecursiveKeyExists(string $key): bool
    {
        return $this->keyExists($this->array(), $key);
    }

    /**
     * Find `$key` into `body`.
     */
    public function bodyRecursiveKeyFind(string $key): ?string
    {
        return $this->keyFind($this->array(), $key);
    }

    /**
     * Get query URL from Response.
     */
    public function getQuery(): ?string
    {
        $origin = $this->guzzle->getHeader('Origin');
        if (array_key_exists(0, $origin)) {
            return $origin[0];
        }

        return null;
    }

    private function isValidXml(string $contents): bool
    {
        $content = trim($contents);
        if (empty($content)) {
            return false;
        }

        if (false !== stripos($content, '<!DOCTYPE html>')) {
            return false;
        }

        libxml_use_internal_errors(true);
        simplexml_load_string($content);
        $errors = libxml_get_errors();
        libxml_clear_errors();

        return empty($errors);
    }

    private function isValidJson($string): bool
    {
        json_decode($string);

        return JSON_ERROR_NONE === json_last_error();
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
