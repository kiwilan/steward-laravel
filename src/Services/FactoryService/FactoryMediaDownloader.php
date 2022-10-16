<?php

namespace Kiwilan\Steward\Services\FactoryService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kiwilan\Steward\Services\FactoryService;
use Symfony\Component\Finder\SplFileInfo;
use UnitEnum;

/**
 * Class FactoryMedia
 *
 * @property FactoryMedia $media
 * @property string[] $media_urls
 */
class FactoryMediaDownloader
{
    public function __construct(
        public FactoryMedia $media,
        public array $media_urls = []
    ) {
    }

    public static function make(FactoryMedia $media)
    {
        $downloader = new FactoryMediaDownloader($media);
        $downloader->media_urls = $downloader->setMediaUrls();

        return ;
    }

    private function setMediaUrls(int $width = 600, int $height = 600)
    {
        $endpoint= "https://picsum.photos/{$width}/{$height}";
        $media_count = $this->media->factory->faker->numberBetween(1, 5);

        $list = [];
        for ($i = 0; $i < $media_count; ++$i) {
            $list[] = $endpoint;
        }

        return $list;
    }
}
