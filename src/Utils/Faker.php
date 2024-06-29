<?php

namespace Kiwilan\Steward\Utils;

use Closure;
use Faker\Generator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Kiwilan\HttpPool\Response\HttpPoolResponse;
use Kiwilan\Steward\Commands\MediaCleanCommand;
use Kiwilan\Steward\Commands\Scout\ScoutFreshCommand;
use Kiwilan\Steward\Enums\FakerTextEnum;
use Kiwilan\Steward\Services\ClassParser\ClassParserItem;
use Kiwilan\Steward\Services\DirectoryService;
use Kiwilan\Steward\Utils\Faker\FakerDateTime;
use Kiwilan\Steward\Utils\Faker\FakerJson;
use Kiwilan\Steward\Utils\Faker\FakerMediaDownloader;
use Kiwilan\Steward\Utils\Faker\FakerMediaLocal;
use Kiwilan\Steward\Utils\Faker\FakerRichText;
use Kiwilan\Steward\Utils\Faker\FakerText;

/**
 * Improve Faker Laravel factory service.
 */
class Faker
{
    public function __construct(
        protected Generator $generator,
        protected ?FakerText $text = null,
        protected ?FakerRichText $richText = null,
        protected ?FakerDateTime $dateTime = null,
        protected ?FakerMediaLocal $mediaLocal = null,
        protected ?FakerMediaDownloader $mediaDownloader = null,
        protected ?FakerJson $json = null,
    ) {}

    public static function beforeSeed(): bool
    {
        $paths = [
            storage_path('app/public'),
            storage_path('app/download'),
        ];

        if (config('app.env') === 'production') {
            $paths[] = storage_path('app/media');
        }

        DirectoryService::make()->clear($paths);

        return true;
    }

    public static function afterSeed(): void
    {
        Artisan::call(ScoutFreshCommand::class);
        Artisan::call(MediaCleanCommand::class, ['--force' => true]);
    }

    public static function make(string|\UnitEnum|null $mediaPath = null): self
    {
        $generator = \Faker\Factory::create();
        $service = new Faker($generator);
        $service->text = $service->setFakerText();
        $service->richText = $service->setFakerRichText();
        $service->dateTime = $service->setFakerDate();
        $service->mediaLocal = $service->setFakerMediaLocal($mediaPath);
        $service->mediaDownloader = $service->setFakerMediaDownloader();
        $service->json = $service->setFakerJson();

        return $service;
    }

    public static function noSearch(string $model, Closure $closure): mixed
    {
        $item = ClassParserItem::make($model);

        if (! $item->isModel() && ! $item->useTrait('Laravel\Scout\Searchable')) {
            throw new \Exception("{$model} must be an instance of Illuminate\Database\Eloquent\Model and use Laravel\Scout\Searchable trait");
        }

        return $model::withoutSyncingToSearch(fn () => $closure());
    }

    public function useText(?FakerTextEnum $type = null): self
    {
        $this->text = $this->setFakerText($type);
        $this->richText = $this->setFakerRichText($type);

        return $this;
    }

    public function generator(): Generator
    {
        return $this->generator;
    }

    public function text(): FakerText
    {
        return $this->text;
    }

    public function richText(): FakerRichText
    {
        return $this->richText;
    }

    public function dateTime(): FakerDateTime
    {
        return $this->dateTime;
    }

    /**
     * @param  string|null  $basePath  If null, use `database_path('seeders/media')`
     */
    public function mediaLocal(string $path, ?string $basePath = null): FakerMediaLocal
    {
        if (! $basePath) {
            $this->mediaLocal->basePath = database_path('seeders/media');
        }

        $this->mediaLocal->path = $path;

        return $this->mediaLocal;
    }

    public function mediaDownloader(): FakerMediaDownloader
    {
        return $this->mediaDownloader;
    }

    public function json(): FakerJson
    {
        return $this->json;
    }

    // private function builder(string $builder): array
    // {
    //     return FakerBuilder::make($this, $builder);
    // }

    private function setFakerText(?FakerTextEnum $type = null): FakerText
    {
        $type = $type ?? \Kiwilan\Steward\StewardConfig::factoryText();

        return new FakerText($this, $type);
    }

    private function setFakerRichText(?FakerTextEnum $type = null): FakerRichText
    {
        $type = $type ?? \Kiwilan\Steward\StewardConfig::factoryText();
        $text = $this->setFakerText($type);

        return new FakerRichText($this, $type, $text);
    }

    private function setFakerDate(): FakerDateTime
    {
        return new FakerDateTime($this);
    }

    private function setFakerMediaLocal(string|\UnitEnum|null $media_path = null): FakerMediaLocal
    {
        if ($media_path && $media_path instanceof \UnitEnum) {
            $media_path = $media_path->name;
        }

        return new FakerMediaLocal($this, $media_path);
    }

    private function setFakerMediaDownloader(): FakerMediaDownloader
    {
        return new FakerMediaDownloader($this);
    }

    private function setFakerJson(): FakerJson
    {
        return new FakerJson($this);
    }

    public static function mediaFromResponse(?HttpPoolResponse $response, ?string $basePath = null): ?string
    {
        if (! $response || ! $response->isSuccess()) {
            return null;
        }

        $type = $response->getMetadata()->getContentType();
        $ext = explode('/', $type)[1] ?? 'jpg';
        $data = $response->getBody()->getContents();

        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        return Faker::saveFile($data, $ext, $basePath);
    }

    public static function mediaFromFile(string $path, ?string $basePath = null): ?string
    {
        $data = File::get($path);
        $ext = pathinfo($path)['extension'];

        return Faker::saveFile($data, $ext, $basePath);
    }

    private static function saveFile(string $data, string $ext = 'jpg', ?string $basePath = null): string
    {
        $random = uniqid();

        $subDirectory = $basePath ? $basePath : 'seeders';
        $basePath = storage_path("app/public/{$subDirectory}");

        if (! File::exists($basePath)) {
            File::makeDirectory($basePath, 0755, true, true);
        }
        $name = "{$random}.{$ext}";
        File::put("{$basePath}/{$name}", $data);

        return "{$subDirectory}/{$name}";
    }
}
