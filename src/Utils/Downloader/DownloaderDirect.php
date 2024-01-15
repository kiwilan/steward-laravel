<?php

namespace Kiwilan\Steward\Utils\Downloader;

class DownloaderDirect extends Downloader
{
    protected function __construct(
        protected string $path,
    ) {
        parent::__construct(basename($path));
    }

    public function autoMimeType(): self
    {
        $this->mimeType = mime_content_type($this->path);

        return $this;
    }

    public function get(): void
    {
        $this->size = filesize($this->path);
        ini_set('max_execution_time', $this->maxExecutionTime);

        $this->sendHeaders();
        $file = fopen($this->path, 'rb');

        while (! feof($file)) {
            echo fread($file, 1024 * 8);
            ob_flush();
            flush();
        }

        fclose($file);
    }
}
