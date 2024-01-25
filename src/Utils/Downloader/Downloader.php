<?php

namespace Kiwilan\Steward\Utils\Downloader;

/**
 * Downloader to download a file or a stream of zip files.
 *
 * - Use `Downloader::direct()` to download a file directly.
 * - Use `Downloader::stream()` to download a stream of zip files.
 */
class Downloader
{
    protected function __construct(
        protected ?string $filename = null,
        protected ?int $size = null,
        protected string $mimeType = 'application/octet-stream',
        protected int $maxExecutionTime = 36000,
    ) {
    }

    /**
     * Create a downloader to download a file directly.
     *
     * Name of file can be override with `->name('new_name.pdf')`.
     */
    public static function direct(string $path): DownloaderDirect
    {
        $download = new DownloaderDirect($path);
        $download->filename = basename($path);

        return $download;
    }

    /**
     * Create a downloader to download a stream of zip files.
     *
     * @param  string  $filename The filename of the zip file to be downloaded. The extension will be automatically added.
     */
    public static function stream(string $filename): DownloaderZipStream
    {
        $zip = new DownloaderZipStream();
        $zip->filename = "{$filename}.zip";

        return $zip;
    }

    /**
     * Set the value of maxExecutionTime, in seconds.
     *
     * @default 36000
     */
    public function maxExecutionTime(int $maxExecutionTime): static
    {
        $this->maxExecutionTime = $maxExecutionTime;

        return $this;
    }

    /**
     * Set the value of mimeType. If null, it will be automatically determined from the filename.
     */
    public function mimeType(?string $mimeType = null): static
    {
        if ($mimeType) {
            $this->mimeType = $mimeType;
        } else {
            $extension = pathinfo($this->filename, PATHINFO_EXTENSION);
            $this->mimeType = $this->extensionToMimetype($extension);
        }

        return $this;
    }

    /**
     * Send headers for the download.
     */
    protected function sendHeaders(): void
    {
        if (headers_sent($filename, $linenum)) {
            throw new \Exception("Headers have already been sent. File: {$filename} Line: {$linenum}");
        }
        header("Content-Type: {$this->mimeType}");
        header('Content-Description: file transfer');
        header("Content-Disposition: attachment;filename={$this->filename}");

        if ($this->size) {
            header("Content-Length: {$this->size}");
        }
        header('Accept-Ranges: bytes');
        header('Pragma: public');
        header('Expires: -1');
        header('Cache-Control: no-cache');
        header('Cache-Control: public, must-revalidate, post-check=0, pre-check=0');
    }

    /**
     * Convert extension to mimetype.
     */
    private function extensionToMimetype(string $extension): string
    {
        return match ($extension) {
            'avi' => 'video/x-msvideo',
            'mkv' => 'video/x-matroska',
            'mp4' => 'video/mp4',
            'zip' => 'application/zip',
            'epub' => 'application/epub+zip',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };
    }
}
