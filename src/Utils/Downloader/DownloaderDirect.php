<?php

namespace Kiwilan\Steward\Utils\Downloader;

/**
 * Downloader to download a file.
 */
class DownloaderDirect extends Downloader
{
    protected function __construct(
        protected string $path,
    ) {
        parent::__construct(basename($path));
    }

    /**
     * Set the value of mimeType. If null, it will be automatically determined from the filename.
     */
    public function autoMimeType(): self
    {
        $this->mimeType = mime_content_type($this->path);

        return $this;
    }

    /**
     * Trigger the download.
     */
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
