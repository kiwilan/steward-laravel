<?php

namespace Kiwilan\Steward\Services\Http\Utils;

use GuzzleHttp\Psr7\Response;

/**
 * Manage responses from HttpService with external API.
 */
class HttpMetadata
{
    protected function __construct(
        protected int $statusCode = 404,
        protected ?string $reason = null,
        protected bool $isJson = false,
        protected bool $isXml = false,
        protected ?string $server = null,
        protected ?string $date = null,
        protected ?string $contentType = null,
        protected ?string $origin = null,
    ) {
    }

    /**
     * Create HttpMetadata from HttpResponse.
     *
     * @param  ?\GuzzleHttp\Psr7\Response  $response
     */
    public static function make(?Response $response): self
    {
        $self = new HttpMetadata();

        if (! $response) {
            $self->date = now();

            return $self;
        }

        $contentType = $response->getHeaderLine('Content-Type');

        $self->statusCode = $response->getStatusCode();
        $self->reason = $response->getReasonPhrase();
        $self->isJson = str_contains($contentType, 'json');
        $self->isXml = str_contains($contentType, 'xml');
        $self->server = $response->getHeaderLine('Server');
        $self->date = $response->getHeaderLine('Date');
        $self->contentType = $contentType;
        $self->origin = $self->setOrigin($response);

        return $self;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function reason(): ?string
    {
        return $this->reason;
    }

    public function isJson(): bool
    {
        return $this->isJson;
    }

    public function isXml(): bool
    {
        return $this->isXml;
    }

    public function server(): ?string
    {
        return $this->server;
    }

    public function date(): ?string
    {
        return $this->date;
    }

    public function contentType(): ?string
    {
        return $this->contentType;
    }

    public function origin(): ?string
    {
        return $this->origin;
    }

    /**
     * Get query URL from Response.
     */
    private function setOrigin(?Response $response): ?string
    {
        $origin = $response->getHeader('Origin');

        if (array_key_exists(0, $origin)) {
            return $origin[0];
        }

        return null;
    }
}
