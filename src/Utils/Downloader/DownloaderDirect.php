<?php

namespace Kiwilan\Steward\Utils\Downloader;

/**
 * Downloader to download a file.
 */
class DownloaderDirect extends Downloader
{
    protected function __construct(
        protected string $path,
        protected ?int $speed = null,
    ) {
        parent::__construct(basename($path));
    }

    /**
     * Replace filename with a new one., you have to specify the extension.
     *
     * Default is the basename of the path.
     */
    public function name(string $name): static
    {
        $this->filename = $name;

        return $this;
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
     * Change the speed of the download, lower is faster.
     */
    public function speed(int $speed): self
    {
        $this->speed = $speed;

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
            if ($this->speed) {
                usleep($this->speed);
            }
        }

        fclose($file);
    }
}
