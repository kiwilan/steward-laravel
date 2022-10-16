<?php

namespace Kiwilan\Steward\Services\FactoryService;

use Kiwilan\Steward\Services\HttpService;

/**
 * Class FactoryMedia
 *
 * @property string[] $media_paths
 */
class FactoryMediaDownloader
{
    public function __construct(
        protected FactoryMedia $media,
        protected array $media_urls = [],
        protected int $with = 600,
        protected int $height = 600,
        public array $media_paths = [],
    ) {
    }

    public static function make(FactoryMedia $media)
    {
        $downloader = new FactoryMediaDownloader($media);

        return $downloader;
    }

    /**
     * @param  int  $media_count
     * @param  int[]  $size width and height
     */
    public function getMedias(?int $media_count = null, array $size = [600, 600])
    {
        if (! $media_count) {
            $media_count = $this->media->factory->faker->numberBetween(1, 5);
        }
        $this->setSize($size);
        $this->media_urls = $this->setMediaUrls($media_count, $this->with, $this->height);
        $responses = $this->downloadMedias();

        return $responses;
    }

    /**
     * @param  int[]  $size
     */
    private function setSize(array $size): self
    {
        if (empty($size)) {
            return $this;
        }

        $this->with = array_key_exists(0, $size) ? $size[0] : 600;
        $this->height = array_key_exists(1, $size) ? $size[1] : 600;

        return $this;
    }

    private function downloadMedias()
    {
        $service = HttpService::make($this->media_urls);
        $responses = $service->execute();

        return $responses;
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
