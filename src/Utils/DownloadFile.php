<?php

namespace Kiwilan\Steward\Utils;

/**
 * Represent a DownloadFile.
 *
 * @property ?string $name
 * @property ?string $size
 * @property ?string $path
 * @property ?string $url
 * @property ?string $reader
 * @property ?string $format
 * @property ?int $count
 * @property ?bool $isZip
 */
class DownloadFile
{
    public function __construct(
        public ?string $name = null,
        public ?string $size = null,
        public ?string $path = null,
        public ?string $url = null,
        public ?string $reader = null,
        public ?string $format = null,
        public ?int $count = null,
        public ?bool $isZip = null,
    ) {}
}
