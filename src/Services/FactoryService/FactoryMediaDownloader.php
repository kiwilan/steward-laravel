<?php

namespace Kiwilan\Steward\Services\FactoryService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Services\FactoryService;
use Kiwilan\Steward\Services\FactoryService\Providers\ImageProvider;
use Kiwilan\Steward\Services\FactoryService\Providers\PictureDownloadProvider;
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
        $provider = PictureDownloadProvider::make($this->factory->mediaDownloaderType(), $count);

        $images = [];

        foreach ($provider->medias() as $key => $path) {
            $images[] = $this->saveMediaFromFile($path);
        }

        return collect($images);
    }

    public function single(): string
    {
        $provider = PictureDownloadProvider::make($this->factory->mediaDownloaderType(), 1);

        return $this->saveMediaFromFile($provider->medias()->first());
    }

    /**
     * @param  Collection<int,Model>  $models
     * @return void
     */
    public function associate(mixed $models, string $field = 'picture')
    {
        $images = $this->multiple($models->count());

        foreach ($models as $key => $model) {
            $model->{$field} = $images->shift();
            $model->save();
        }
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

    // private function pool(int $count): HttpPoolService
    // {
    //     $provider = $this->setMediaUrls($count);

    //     return HttpPoolService::make($provider->urlsList(), $provider->headers());
    // }

    // private function saveMediaFromResponse(?Response $response): string
    // {
    //     if (! $response) {
    //         return '';
    //     }

    //     $base64 = HttpService::responseToImage($response);
    //     $random_name = uniqid();

    //     $path = public_path('storage/seeders');

    //     if (! File::exists($path)) {
    //         File::makeDirectory($path, 0755, true, true);
    //     }
    //     $name = "{$random_name}.jpg";
    //     File::put("{$path}/{$name}", file_get_contents($base64));

    //     return "seeders/{$name}";
    // }

    private function saveMediaFromFile(string $filePath): string
    {
        $random_name = uniqid();
        $path = public_path('storage/seeders');

        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }
        $name = "{$random_name}.jpg";
        File::put("{$path}/{$name}", File::get($filePath));

        return "seeders/{$name}";
    }

    // private function setMediaUrls(int $count = 1, int $width = 600, int $height = 600): ImageProvider
    // {
    //     $provider = ImageProvider::make($count, $width, $height);

    //     return $provider->get();
    // }
}
