<?php

namespace Kiwilan\Steward\Services;

use Closure;
use Faker\Generator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Commands\MediaCleanCommand;
use Kiwilan\Steward\Commands\Scout\ScoutFreshCommand;
use Kiwilan\Steward\Enums\FactoryTextEnum;
use Kiwilan\Steward\Services\Class\ClassItem;
use Kiwilan\Steward\Services\Factory\FactoryBuilder;
use Kiwilan\Steward\Services\Factory\FactoryData;
use Kiwilan\Steward\Services\Factory\FactoryDateTime;
use Kiwilan\Steward\Services\Factory\FactoryMediaDownloader;
use Kiwilan\Steward\Services\Factory\FactoryMediaLocal;
use Kiwilan\Steward\Services\Factory\FactoryRichText;
use Kiwilan\Steward\Services\Factory\FactoryText;
use Kiwilan\Steward\Services\Http\HttpResponse;

/**
 * Improve Faker Laravel factory service.
 */
class FactoryService
{
    public function __construct(
        protected Generator $faker,
        protected ?FactoryText $text = null,
        protected ?FactoryRichText $richText = null,
        protected ?FactoryDateTime $dateTime = null,
        protected ?FactoryMediaLocal $mediaLocal = null,
        protected ?FactoryMediaDownloader $mediaDownloader = null,
        protected ?FactoryData $data = null,
    ) {
    }

    public static function beforeSeed(): bool
    {
        $paths = [
            public_path('storage'),
            storage_path('app/download'),
        ];

        if (config('app.env') === 'production') {
            $paths[] = storage_path('app/media');
        }

        DirectoryClearService::make($paths);

        return true;
    }

    public static function afterSeed(): void
    {
        Artisan::call(ScoutFreshCommand::class);
        Artisan::call(MediaCleanCommand::class, ['--force' => true]);
    }

    public static function make(string|\UnitEnum|null $mediaPath = null): self
    {
        $faker = \Faker\Factory::create();
        $service = new FactoryService($faker);
        $service->text = $service->setFactoryText();
        $service->richText = $service->setFactoryRichText();
        $service->dateTime = $service->setFactoryDate();
        $service->mediaLocal = $service->setFactoryMediaLocal($mediaPath);
        $service->mediaDownloader = $service->setFactoryMediaDownloader();
        $service->data = $service->setFactoryData();

        return $service;
    }

    public static function noSearch(string $model, Closure $closure): mixed
    {
        $item = ClassItem::make($model);

        if (! $item->isModel() && ! $item->useTrait('Laravel\Scout\Searchable')) {
            throw new \Exception("{$model} must be an instance of Illuminate\Database\Eloquent\Model and use Laravel\Scout\Searchable trait");
        }

        return $model::withoutSyncingToSearch(fn () => $closure());
    }

    public function useText(?FactoryTextEnum $type = null): self
    {
        $this->text = $this->setFactoryText($type);
        $this->richText = $this->setFactoryRichText($type);

        return $this;
    }

    public function faker(): Generator
    {
        return $this->faker;
    }

    public function text(): FactoryText
    {
        return $this->text;
    }

    public function richText(): FactoryRichText
    {
        return $this->richText;
    }

    public function dateTime(): FactoryDateTime
    {
        return $this->dateTime;
    }

    /**
     * @param  string|null  $basePath If null, use `database_path('seeders/media')`
     */
    public function mediaLocal(string $path, ?string $basePath = null): FactoryMediaLocal
    {
        if (! $basePath) {
            $this->mediaLocal->basePath = database_path('seeders/media');
        }

        $this->mediaLocal->path = $path;

        return $this->mediaLocal;
    }

    public function mediaDownloader(): FactoryMediaDownloader
    {
        return $this->mediaDownloader;
    }

    public function data(): FactoryData
    {
        return $this->data;
    }

    // private function builder(string $builder): array
    // {
    //     return FactoryBuilder::make($this, $builder);
    // }

    private function setFactoryText(?FactoryTextEnum $type = null): FactoryText
    {
        $type = $type ?? \Kiwilan\Steward\StewardConfig::factoryText();

        return new FactoryText($this, $type);
    }

    private function setFactoryRichText(?FactoryTextEnum $type = null): FactoryRichText
    {
        $type = $type ?? \Kiwilan\Steward\StewardConfig::factoryText();
        $text = $this->setFactoryText($type);

        return new FactoryRichText($this, $type, $text);
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

    private function setFactoryData(): FactoryData
    {
        return new FactoryData($this);
    }

    public static function mediaFromResponse(?HttpResponse $response, ?string $basePath = null): ?string
    {
        if (! $response) {
            return null;
        }

        $type = $response->metadata()->contentType();
        $ext = explode('/', $type)[1];
        $data = $response->body();

        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        return FactoryService::saveFile($data, $ext, $basePath);
    }

    public static function mediaFromFile(string $path, ?string $basePath = null): ?string
    {
        $data = File::get($path);
        $ext = pathinfo($path)['extension'];

        return FactoryService::saveFile($data, $ext, $basePath);
    }

    private static function saveFile(string $data, string $ext = 'jpg', ?string $basePath = null): string
    {
        $random = uniqid();

        $subDirectory = $basePath ? $basePath : 'seeders';
        $basePath = public_path("storage/{$subDirectory}");

        if (! File::exists($basePath)) {
            File::makeDirectory($basePath, 0755, true, true);
        }
        $name = "{$random}.{$ext}";
        File::put("{$basePath}/{$name}", $data);

        return "{$subDirectory}/{$name}";
    }
}
