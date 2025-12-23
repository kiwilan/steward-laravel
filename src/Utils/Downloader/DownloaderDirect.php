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
        $mime = mime_content_type($this->path);
        if ($mime !== false) {
            $this->mimeType = $mime;
        }

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
        try {
            $this->size = filesize($this->path);
        } catch (\Throwable $th) {
            throw new \Exception("File not found: {$this->path}");
        }
        ini_set('max_execution_time', $this->maxExecutionTime);

        $this->clearOutputBuffers();
        $this->sendHeaders();
        $file = fopen($this->path, 'rb');
        if ($file === false) {
            throw new \RuntimeException("Unable to open file: {$this->path}");
        }

        while (!feof($file)) {
            $chunk = fread($file, $this->chunkSize);
            if ($chunk === false) {
                break;
            }

            echo $chunk;
            $this->flushOutput();

            if ($this->speed) {
                usleep($this->speed);
            }
        }

        fclose($file);
    }
}
