<?php

namespace Kiwilan\Steward\Utils\Downloader;

class DownloaderZipStreamItem
{
    public function __construct(
        public string $fileName,
        public string $path,
    ) {
    }
}
