<?php

namespace Kiwilan\Steward\Services\FactoryService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Services\FactoryService;
use Kiwilan\Steward\Services\HttpPoolService;
use Kiwilan\Steward\Services\HttpService;

/**
 * Class FactoryMedia
 */
class FactoryMediaDownloader
{
    /** @var string[] */
    protected array $mediaUrls = [];

    public function __construct(
        public FactoryService $factory,
    ) {
    }

    /**
     * @return Collection<int,string>
     */
    public function multiple(int $count)
    {
        $this->mediaUrls = $this->setMediaUrls($count, 600, 600);
        $responses = HttpPoolService::make($this->mediaUrls);

        $images = [];

        foreach ($responses as $key => $response) {
            $images[] = $this->saveMediaFromResponse($response);
        }

        return collect($images);
    }

    public function single(): string
    {
        $this->mediaUrls = $this->setMediaUrls(1, 600, 600);
        $responses = HttpPoolService::make($this->mediaUrls);
        $response = $responses->first();

        return $this->saveMediaFromResponse($response);
    }

    /**
     * @param  Collection<int,Model>  $models
     * @return void
     */
    public function associateMultiple(mixed $models, string $field = 'picture')
    {
        $images = $this->multiple($models->count());

        foreach ($models as $key => $model) {
            $model->{$field} = $images->shift();
            $model->save();
        }
    }

    private function saveMediaFromResponse(Response $response): string
    {
        $base64 = HttpService::responseToImage($response);
        $random_name = uniqid();

        $path = public_path('storage/seeders');

        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }
        $name = "{$random_name}.jpg";
        File::put("{$path}/{$name}", file_get_contents($base64));

        return "seeders/{$name}";
    }

    /**
     * @return string[]
     */
    private function setMediaUrls(int $media_count = 1, int $width = 600, int $height = 600)
    {
        $endpoint = "https://picsum.photos/{$width}/{$height}";

        $list = [];

        for ($i = 0; $i < $media_count; $i++) {
            $list[] = $endpoint;
        }

        return $list;
    }
}
