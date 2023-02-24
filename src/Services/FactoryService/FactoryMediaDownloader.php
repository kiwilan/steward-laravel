<?php

namespace Kiwilan\Steward\Services\FactoryService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Services\FactoryService;
use Kiwilan\Steward\Services\FactoryService\Providers\ImageProvider;
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
        $this->mediaUrls = $this->setMediaUrls($count);
        $http = HttpPoolService::make($this->mediaUrls);

        if ($http->failedRequests() > 10) {
            $this->mediaUrls = $this->setMediaUrls($count);
            $http = HttpPoolService::make($this->mediaUrls);
        }

        $images = [];

        foreach ($http->responses() as $key => $response) {
            $images[] = $this->saveMediaFromResponse($response);
        }

        return collect($images);
    }

    public function single(): string
    {
        $images = $this->multiple(1);

        return $this->saveMediaFromResponse($images->first());
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

    private function saveMediaFromResponse(?Response $response): string
    {
        if (! $response) {
            return '';
        }

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
    private function setMediaUrls(int $count = 1, int $width = 600, int $height = 600, bool $usePicsum = true)
    {
        $provider = ImageProvider::make($count, $width, $height);

        if (! $usePicsum) {
            $provider = $provider->useApiNinja();
        }

        $provider = $provider->get();

        return $provider->urlsList();
    }
}
