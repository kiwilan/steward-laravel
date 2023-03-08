<?php

namespace Kiwilan\Steward\Services\Factory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Enums\Api\SeedsApiCategoryEnum;
use Kiwilan\Steward\Enums\Api\SeedsApiSizeEnum;
use Kiwilan\Steward\Services\Factory\Media\MediaProvider;
use Kiwilan\Steward\Services\FactoryService;
use Kiwilan\Steward\Services\Http\HttpResponse;

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

    public function seedsConfig(
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
        $this->config['count'] = $count;

        $medias = MediaProvider::make()
            ->seeds(...$this->config)
            ->medias()
        ;

        $images = collect([]);

        foreach ($medias as $media) {
            $images->push($this->mediaFromResponse($media));
        }

        return $images;
    }

    private function mediaFromResponse(?HttpResponse $response): ?string
    {
        if (! $response) {
            return null;
        }

        $type = $response->metadata()->contentType();
        $ext = explode('/', $type)[1];
        $data = $response->body();
        $random_name = uniqid();

        $path = public_path('storage/seeders');

        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }
        $name = "{$random_name}.{$ext}";
        File::put("{$path}/{$name}", $data);

        return "seeders/{$name}";
    }
}
