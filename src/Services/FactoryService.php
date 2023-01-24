<?php

namespace Kiwilan\Steward\Services;

use Faker\Generator;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Services\FactoryService\FactoryBuilder;
use Kiwilan\Steward\Services\FactoryService\FactoryMediaDownloader;
use Kiwilan\Steward\Services\FactoryService\FactoryMediaLocal;
use Kiwilan\Steward\Services\FactoryService\FactoryText;

/**
 * Improve the default Laravel factory service.
 */
class FactoryService
{
    public function __construct(
        public Generator $faker,
        public ?FactoryText $text = null,
        public ?FactoryMediaLocal $media_local = null,
        public ?FactoryMediaDownloader $media_downloader = null,
        // protected ?string $path = null,
    ) {
    }

    public static function clean(): bool
    {
        $paths = [
            public_path('storage/seeders'),
            public_path('storage/temp'),
            public_path('storage/media'),
        ];

        foreach ($paths as $key => $path) {
            if (File::exists($path)) {
                File::cleanDirectory($path);
            }
        }

        return true;
    }

    public static function make(string|\UnitEnum|null $media_path = null, bool $use_sindarin = false): self
    {
        $faker = \Faker\Factory::create();
        $service = new FactoryService($faker);
        $service->text = $service->setFactoryText($use_sindarin);
        $service->media_local = $service->setFactoryMediaLocal($media_path);
        $service->media_downloader = $service->setFactoryMediaDownloader();

        return $service;
    }

    // private function builder(string $builder): array
    // {
    //     return FactoryBuilder::make($this, $builder);
    // }

    public function setFactoryText(bool $use_sindarin): FactoryText
    {
        return new FactoryText($this, $use_sindarin);
    }

    private function setFactoryMediaLocal(string|\UnitEnum|null $media_path = null): FactoryMediaLocal
    {
        if ($media_path && $media_path instanceof \UnitEnum) {
            $media_path = $media_path->name;
        }

        return new FactoryMediaLocal($this, $media_path);
    }

    public function setFactoryMediaDownloader(): FactoryMediaDownloader
    {
        return new FactoryMediaDownloader($this);
    }
}
