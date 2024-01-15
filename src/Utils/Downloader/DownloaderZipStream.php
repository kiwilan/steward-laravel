<?php

namespace Kiwilan\Steward\Utils\Downloader;

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
     * Set the value of files.
     *
     * @param  DownloaderZipStreamItem[]  $files
     */
    public function files(array $files): self
    {
        $this->files = $files;

        return $this;
    }

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
            $zip->addFileFromPath(
                fileName: $file->fileName,
                path: $file->path,
            );
        }

        $zip->finish();
    }
}
