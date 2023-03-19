<?php

namespace Kiwilan\Steward\Services\Http;

use GuzzleHttp\Psr7\Response;
use Kiwilan\Steward\Services\Http\Utils\HttpMetadata;
use SimpleXMLElement;

/**
 * Manage responses from HttpService with external API.
 */
class HttpResponse
{
    protected function __construct(
        protected string|int|null $id,
        protected ?Response $guzzle,
        protected HttpMetadata $metadata,
        protected bool $success = false,
        protected bool $bodyExist = false,
        protected mixed $bodyRaw = null,
        protected ?string $bodyString = null,
        protected ?object $bodyJson = null,
        protected ?SimpleXMLElement $bodyXml = null,
    ) {
    }

    /**
     * Create HttpResponse from Response.
     *
     * @param  int|string  $id
     * @param  ?\GuzzleHttp\Psr7\Response  $guzzle
     */
    public static function make(mixed $id, ?Response $guzzle): self
    {
        $metadata = HttpMetadata::make($guzzle);
        $success = ! $guzzle ? false : 200 === $guzzle->getStatusCode();
        $self = new HttpResponse(
            id: is_numeric($id) ? intval($id) : $id,
            guzzle: $guzzle,
            metadata: $metadata,
            success: $success,
        );

        if (! $guzzle) {
            return $self;
        }

        $self->bodyRaw = $guzzle->getBody()->getContents();
        $contents = $self->bodyRaw;

        if ($self->isValidXml($contents)) {
            $self->bodyXml = simplexml_load_string($contents);
        }

        if ($self->isValidJson($contents)) {
            $contents = json_decode($contents);
            $self->bodyJson = is_object($contents) ? $contents : null;
        }

        if (! $self->bodyXml && ! $self->bodyJson && gettype($contents) === 'string') {
            $self->bodyString = $contents;
        }

        if ($self->bodyRaw) {
            $self->bodyExist = true;
        }

        return $self;
    }

    /**
     * Body as `object`.
     *
     * @return object|SimpleXMLElement|null
     */
    public function body()
    {
        return $this->bodyJson ?? $this->bodyXml ?? $this->bodyString ?? $this->bodyRaw;
    }

    public function id(): mixed
    {
        return $this->id;
    }

    public function guzzle(): ?Response
    {
        return $this->guzzle;
    }

    public function metadata(): HttpMetadata
    {
        return $this->metadata;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isBodyExist(): bool
    {
        return $this->bodyExist;
    }

    public function bodyRaw(): mixed
    {
        return $this->bodyRaw;
    }

    public function bodyString(): ?string
    {
        return $this->bodyString;
    }

    public function bodyJson(): ?object
    {
        return $this->bodyJson;
    }

    public function bodyXml(): ?SimpleXMLElement
    {
        return $this->bodyXml;
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
    public function toArray(): ?array
    {
        return json_decode(json_encode($this->body() ?? []), true);
    }

    /**
     * Check if `$key` exist into `body`.
     */
    public function bodyKeyExists(string $key): bool
    {
        try {
            return array_key_exists($key, $this->toArray());
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Check if `$key` exist into `body`.
     */
    public function bodyRecursiveKeyExists(string $key): bool
    {
        return $this->keyExists($this->toArray(), $key);
    }

    /**
     * Find `$key` into `body`.
     */
    public function bodyRecursiveKeyFind(string $key): ?string
    {
        return $this->keyFind($this->toArray(), $key);
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
