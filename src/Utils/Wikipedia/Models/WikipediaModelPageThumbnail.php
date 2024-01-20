<?php

namespace Kiwilan\Steward\Utils\Wikipedia\Models;

class WikipediaModelPageThumbnail
{
    protected function __construct(
        protected ?string $source = null,
        protected ?string $width = null,
        protected ?string $height = null,
    ) {
    }

    public static function make(array $thumbnail): self
    {
        return new self(
            source: $thumbnail['source'] ?? null,
            width: $thumbnail['width'] ?? null,
            height: $thumbnail['height'] ?? null,
        );
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }
}
