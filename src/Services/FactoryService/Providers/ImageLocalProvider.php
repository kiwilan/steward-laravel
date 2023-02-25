<?php

namespace Kiwilan\Steward\Services\FactoryService\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class ImageLocalProvider
{
    protected function __construct(
        protected string $downloadLink,
        protected string $archivePath,
        protected string $mediaPath,
    ) {
    }

    public static function make(): self
    {
        $downloadLink = 'https://drive.google.com/uc?export=download&id=1CN_G3RaKkst4RXdlzFBSNd29IfZuGxhf';

        $self = new self(
            $downloadLink,
            storage_path('app/downloads'),
            storage_path('app/media'),
        );

        $self->download();
        $self->unzip();

        return $self;
    }

    private function download()
    {
        $http = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ]);
        $response = $http->get($this->downloadLink);

        File::ensureDirectoryExists($this->archivePath, 0755, true);
        File::put("{$this->archivePath}/seeds.zip", $response->body());
    }

    private function unzip()
    {
        $zip = new ZipArchive();
        $zip->open("{$this->archivePath}/seeds.zip");
        $zip->extractTo($this->mediaPath);
        $zip->close();
    }
}
