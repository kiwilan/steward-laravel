<?php

namespace Kiwilan\Steward\Services\FactoryService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Kiwilan\Steward\Services\FactoryService;
use Kiwilan\Steward\Services\HttpService;

/**
 * Class FactoryMedia
 *
 * @property string[] $media_urls
 */
class FactoryMediaDownloader
{
    public function __construct(
        public FactoryService $factory,
        // protected string $temp_media_path,
        /** @var string[] */
        protected array $media_urls = [],
        // protected int $with = 600,
        // protected int $height = 600,
        // public array $media_paths = [],
    ) {
    }

    public static function make()
    {
        //     $downloader = new FactoryMediaDownloader($media, public_path('storage/temp'));
        //     if (! File::exists($downloader->temp_media_path)) {
        //         File::makeDirectory($downloader->temp_media_path, 0755, true, true);
        //     }
        //     File::cleanDirectory($downloader->temp_media_path);

        //     $responses = $downloader->getMedias(25);
        //     $downloader->saveMedias($responses);

        //     return $downloader;
    }

    // /**
    //  * @param  int  $media_count
    //  * @param  int[]  $size [`width`, `height`]
    //  */
    // public function getMedias(?int $media_count = null, array $size = [600, 600])
    // {
    //     if (!$media_count) {
    //         $media_count = $this->media->factory->faker->numberBetween(1, 5);
    //     }
    //     $this->setSize($size);
    //     $this->media_urls = $this->setMediaUrls($media_count, $this->with, $this->height);

    //     return $this->downloadMedias();
    // }

    // /**
    //  * @param  Collection<int,HttpServiceResponse>  $responses
    //  */
    // public function saveMedias($responses)
    // {
    //     $medias = [];
    //     foreach ($responses as $response) {
    //         $medias[] = $response->json();
    //     }

    //     foreach ($medias as $media) {
    //         $name = uniqid();
    //         $path = "{$this->temp_media_path}/{$name}.jpg";
    //         File::put($path, $media);
    //         $this->media_paths[] = $path;
    //     }
    // }

    // /**
    //  * @param  int[]  $size
    //  */
    // private function setSize(array $size): self
    // {
    //     if (empty($size)) {
    //         return $this;
    //     }

    //     $this->with = array_key_exists(0, $size) ? $size[0] : 600;
    //     $this->height = array_key_exists(1, $size) ? $size[1] : 600;

    //     return $this;
    // }

    /**
     * @return Collection<int,string>
     */
    public function multiple(int $count)
    {
        $this->media_urls = $this->setMediaUrls($count, 600, 600);
        $responses = $this->downloadMedias();

        $images = [];
        foreach ($responses as $key => $response) {
            $images[] = $this->saveMediaFromResponse($response);
        }

        return collect($images);
    }

    public function single(): string
    {
        $this->media_urls = $this->setMediaUrls(1, 600, 600);
        $responses = $this->downloadMedias();
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

    private function saveMediaFromResponse(\Illuminate\Http\Client\Response $response): string
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
     * @return Collection<int,\Illuminate\Http\Client\Response>
     */
    private function downloadMedias()
    {
        $responses = Http::pool(function (Pool $pool) {
            foreach ($this->media_urls as $key => $url) {
                $pool->as($key)->get($url);
            }
        });

        return collect($responses);
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
