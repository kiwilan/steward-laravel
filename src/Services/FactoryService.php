<?php

namespace Kiwilan\Steward\Services;

use Faker\Generator;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Enums\FactoryTextEnum;
use Kiwilan\Steward\Enums\PictureDownloadEnum;
use Kiwilan\Steward\Services\FactoryService\FactoryBuilder;
use Kiwilan\Steward\Services\FactoryService\FactoryDateTime;
use Kiwilan\Steward\Services\FactoryService\FactoryMediaDownloader;
use Kiwilan\Steward\Services\FactoryService\FactoryMediaLocal;
use Kiwilan\Steward\Services\FactoryService\FactoryText;

/**
 * Improve Faker Laravel factory service.
 */
class FactoryService
{
    public function __construct(
        protected Generator $faker,
        protected ?FactoryText $text = null,
        protected ?FactoryDateTime $dateTime = null,
        protected ?FactoryMediaLocal $mediaLocal = null,
        protected ?FactoryMediaDownloader $mediaDownloader = null,
        protected PictureDownloadEnum $type = PictureDownloadEnum::all,
    ) {
    }

    public static function clean(): bool
    {
        $paths = [
            public_path('storage/seeders'),
            public_path('storage/temp'),
            storage_path('app/download'),
        ];

        if (config('app.env') === 'production') {
            $paths[] = storage_path('app/media');
        }

        foreach ($paths as $key => $path) {
            if (File::exists($path)) {
                File::cleanDirectory($path);
            }
        }

        return true;
    }

    public static function make(string|\UnitEnum|null $mediaPath = null): self
    {
        $faker = \Faker\Factory::create();
        $service = new FactoryService($faker);
        $service->text = $service->setFactoryText();
        $service->dateTime = $service->setFactoryDate();
        $service->mediaLocal = $service->setFactoryMediaLocal($mediaPath);
        $service->mediaDownloader = $service->setFactoryMediaDownloader();

        return $service;
    }

    public function useSindarin(): self
    {
        $this->text = $this->setFactoryText(FactoryTextEnum::sindarin);

        return $this;
    }

    public function faker()
    {
        return $this->faker;
    }

    public function text()
    {
        return $this->text;
    }

    public function dateTime()
    {
        return $this->dateTime;
    }

    public function mediaLocal()
    {
        return $this->mediaLocal;
    }

    public function mediaDownloader(PictureDownloadEnum $type = PictureDownloadEnum::all)
    {
        $this->type = $type;

        return $this->mediaDownloader;
    }

    public function mediaDownloaderType(): PictureDownloadEnum
    {
        return $this->type;
    }

    // private function builder(string $builder): array
    // {
    //     return FactoryBuilder::make($this, $builder);
    // }

    private function setFactoryText(FactoryTextEnum $type = FactoryTextEnum::lorem): FactoryText
    {
        return new FactoryText($this, $type);
    }

    private function setFactoryDate(): FactoryDateTime
    {
        return new FactoryDateTime($this);
    }

    private function setFactoryMediaLocal(string|\UnitEnum|null $media_path = null): FactoryMediaLocal
    {
        if ($media_path && $media_path instanceof \UnitEnum) {
            $media_path = $media_path->name;
        }

        return new FactoryMediaLocal($this, $media_path);
    }

    private function setFactoryMediaDownloader(): FactoryMediaDownloader
    {
        return new FactoryMediaDownloader($this);
    }
}
