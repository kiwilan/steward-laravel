<?php

namespace Kiwilan\Steward\Services\Factory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Kiwilan\HttpPool\Utils\PrintConsole;
use Kiwilan\Steward\Enums\Api\SeedsApiCategoryEnum;
use Kiwilan\Steward\Enums\Api\SeedsApiSizeEnum;
use Kiwilan\Steward\Services\Factory\Media\MediaProvider;
use Kiwilan\Steward\Services\FactoryService;
use Kiwilan\Steward\StewardConfig;

/**
 * Class FactoryMedia
 */
class FactoryMediaDownloader
{
    public function __construct(
        public FactoryService $factory,
        public array $config = [],
    ) {
    }

    /**
     * @param  SeedsApiCategoryEnum|null  $category default is `all`
     * @param  SeedsApiSizeEnum|null  $size default is `medium`
     */
    public function config(
        ?SeedsApiCategoryEnum $category = null,
        ?SeedsApiSizeEnum $size = null,
    ): self {
        $this->config = [
            'category' => $category ?? StewardConfig::factoryMediaDownloaderDefaultCategory(),
            'size' => $size ?? StewardConfig::factoryMediaDownloaderDefaultSize(),
        ];

        return $this;
    }

    /**
     * @param  Collection<int,Model>|string  $models Collection of models or class name of model.
     */
    public function associate(Collection|string $models, string $field = 'picture', bool $multiple = false): void
    {
        if (is_string($models)) {
            /** @var Collection<int, Model> */
            $models = $models::all();
        }

        $console = PrintConsole::make();

        $chunkMax = StewardConfig::factoryMaxHandle();

        if (StewardConfig::factoryVerbose()) {
            $console->print("  FactoryMediaDownloader handle {$chunkMax} items maximum.", 'bright-blue');
        }

        if ($models->count() > $chunkMax) {
            $models->chunk($chunkMax)->each(function (Collection $chunk) use ($field, $multiple) {
                $this->associateByChunk($chunk, $field, $multiple);
            });
        } else {
            $this->associateByChunk($models, $field, $multiple);
        }
    }

    private function associateByChunk(Collection $models, string $field, bool $multiple): void
    {
        /** @var Model */
        $instance = $models->first();
        $images = $this->fetchMedias($models->count(), $instance->getTable());

        $table = $instance->getTable();
        $id = $instance->getKeyName();
        DB::beginTransaction();

        foreach ($models as $key => $model) {
            $media = null;

            if ($multiple) {
                $media = $this->factory->faker()->randomElements($images, $this->factory->faker()->numberBetween(1, 3));
            } else {
                $media = $images->shift();
            }

            DB::table($table)
                ->where($id, '=', $model->{$id})
                ->update([
                    $field => $media,
                ])
            ;
        }
        DB::commit();
    }

    /**
     * @return Collection<int,string>
     */
    private function fetchMedias(int $count = 1, ?string $basePath = null)
    {
        $this->config['count'] = $count;

        $medias = MediaProvider::make()
            ->seeds(...$this->config)
            ->medias()
        ;

        $images = collect([]);

        foreach ($medias as $media) {
            $images->push(
                FactoryService::mediaFromResponse($media, $basePath)
            );
        }

        return $images;
    }
}
