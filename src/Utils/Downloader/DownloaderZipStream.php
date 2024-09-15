<?php

namespace Kiwilan\Steward\Utils\Downloader;

/**
 * Downloader to download a stream of zip files.
 */
class DownloaderZipStream extends Downloader
{
    /**
     * @param  DownloaderZipStreamItem[]  $files
     */
    protected function __construct(
        protected array $files = [],
    ) {
        parent::__construct();
    }

    /**
     * Set files to be downloaded, use `DownloaderZipStreamItem` to create a file.
     *
     * @param  DownloaderZipStreamItem[]  $files
     */
    public function files(array $files): self
    {
        $this->files = $files;

        return $this;
    }

    /**
     * Trigger the download.
     */
    public function get(): void
    {
        ini_set('max_execution_time', $this->maxExecutionTime);

        if (! \Composer\InstalledVersions::isInstalled('maennchen/zipstream-php')) {
            throw new \Exception('`maennchen/zipstream-php` is not installed. Install it from GitHub https://github.com/maennchen/ZipStream-PHP');
        }

        $this->sendHeaders();
        $zip = new \ZipStream\ZipStream(
            outputName: str_replace(' ', '.', $this->filename),
            sendHttpHeaders: true,
        );

        foreach ($this->files as $file) {
            if (! file_exists($file->path)) {
                throw new \Exception("File not found: {$file->path}");
            }

            $zip->addFileFromPath(
                fileName: $file->fileName,
                path: $file->path,
            );
        }

        $zip->finish();
    }
}
