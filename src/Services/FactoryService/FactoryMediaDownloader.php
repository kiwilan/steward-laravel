<?php

namespace Kiwilan\Steward\Services\FactoryService;

use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Services\FactoryService;

/**
 * Class FactoryMedia
 *
 * @property string[] $media_paths
 */
class FactoryMediaDownloader
{
    public function __construct(
        public FactoryService $factory,
        // protected string $temp_media_path,
        // protected array $media_urls = [],
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
}
