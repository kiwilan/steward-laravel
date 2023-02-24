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
        protected int $attempt = 0,
    ) {
    }

    /**
     * @return Collection<int,string>
     */
    public function multiple(int $count)
    {
        $provider = $this->setMediaUrls($count);
        $http = HttpPoolService::make($provider->urlsList(), $provider->headers());

        if ($http->failedRequests() > 10 && $this->attempt < 3) {
            $this->attempt++;
            $provider = $this->setMediaUrls($count);
            $http = HttpPoolService::make($provider->urlsList(), $provider->headers());
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

    private function setMediaUrls(int $count = 1, int $width = 600, int $height = 600, bool $usePicsum = true): ImageProvider
    {
        $provider = ImageProvider::make($count, $width, $height);

        if (! $usePicsum) {
            $provider = $provider->useApiNinja();
        }

        return $provider->get();
    }
}
