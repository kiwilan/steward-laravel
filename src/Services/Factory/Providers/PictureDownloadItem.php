<?php

namespace Kiwilan\Steward\Services\Factory\Providers;

class PictureDownloadItem
{
    public function __construct(
        public string $filename,
        public string $extension,
        public string $sizeRender,
        public string $pathFilename,
        public string $id,
        public string $category,
        public string $size,
        public string $sizeHuman,
        public string $date,
        public PictureDownloadItemCredits $credits,
        public PictureDownloadItemLinks $links,
    ) {
    }

    /**
     * @return PictureDownloadItem[]
     */
    public static function make(array $data): array
    {
        /** @var PictureDownloadItem[] */
        $dataItems = [];

        foreach ($data as $item) {
            $dataItems[] = new self(
                $item['filename'],
                $item['extension'],
                $item['sizeRender'],
                $item['pathFilename'],
                $item['id'],
                $item['category'],
                $item['size'],
                $item['sizeHuman'],
                $item['date'],
                new PictureDownloadItemCredits(
                    $item['credits']['provider'],
                    $item['credits']['author'],
                    $item['credits']['url'],
                ),
                new PictureDownloadItemLinks(
                    $item['links']['show'],
                    $item['links']['render'],
                ),
            );
        }

        return $dataItems;
    }

    public static function makeFromObject(object $data): self
    {
        return new self(
            $data->filename,
            $data->extension,
            $data->sizeRender,
            $data->pathFilename,
            $data->id,
            $data->category,
            $data->size,
            $data->sizeHuman,
            $data->date,
            new PictureDownloadItemCredits(
                $data->credits->provider,
                $data->credits->author,
                $data->credits->url,
            ),
            new PictureDownloadItemLinks(
                $data->links->show,
                $data->links->render,
            ),
        );
    }
}

class PictureDownloadItemCredits
{
    public function __construct(
        public string $provider,
        public string $author,
        public string $url,
    ) {
    }
}

class PictureDownloadItemLinks
{
    public function __construct(
        public string $show,
        public string $render,
    ) {
    }
}
