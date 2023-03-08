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
    // /** @var string[] */
    // protected array $mediaUrls = [];

    public function __construct(
        public FactoryService $factory,
        public array $config = [],
    ) {
    }

    public function useMediaSeeds(
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
    public function associate(Collection $models, string $field = 'picture')
    {
        $images = $this->fetchMedias($models->count());

        foreach ($models as $key => $model) {
            $model->{$field} = $images->shift();
            $model->save();
        }
    }

    /**
     * @return Collection<int,string>
     */
    private function fetchMedias(int $count = 1)
    {
        // $this->factory->mediaDownloaderType(), $count
        $this->config['count'] = $count;

        $provider = MediaProvider::make()
            ->seeds(...$this->config)
        ;

        // $images = [];

        // foreach ($provider->medias() as $key => $path) {
        //     $images[] = $this->saveMediaFromFile($path);
        // }

        return collect();
    }
}
