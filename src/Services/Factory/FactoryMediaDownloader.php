<?php

namespace Kiwilan\Steward\Services\Factory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kiwilan\Steward\Enums\Api\SeedsApiCategoryEnum;
use Kiwilan\Steward\Enums\Api\SeedsApiSizeEnum;
use Kiwilan\Steward\Services\Factory\Media\MediaProvider;
use Kiwilan\Steward\Services\FactoryService;

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

    public function config(
        SeedsApiCategoryEnum $category = SeedsApiCategoryEnum::all,
        SeedsApiSizeEnum $size = SeedsApiSizeEnum::medium,
    ): self {
        $this->config = [
            'category' => $category,
            'size' => $size,
        ];

        return $this;
    }

    /**
     * @param  Collection<int,Model>  $models
     * @return void
     */
    public function associate(Collection $models, string $field = 'picture', bool $multiple = false)
    {
        $model = $models->first();
        $images = $this->fetchMedias($models->count(), $model->getTable());

        foreach ($models as $key => $model) {
            $media = null;

            if ($multiple) {
                $media = $this->factory->faker()->randomElements($images, $this->factory->faker()->numberBetween(1, 3));
            } else {
                $media = $images->shift();
            }
            $model->{$field} = $media;
            $model->save();
        }
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
