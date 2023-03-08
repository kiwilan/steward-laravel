<?php

namespace Kiwilan\Steward\Services\Api\Seeds;

class SeedsPictureResponse
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
        public SeedsPictureResponseCredits $credits,
        public SeedsPictureResponseLinks $links,
    ) {
    }

    /**
     * @return SeedsPictureResponse[]
     */
    public static function convertList(array $data)
    {
        $data = $data['data'];

        /** @var SeedsPictureResponse[] */
        $items = [];

        foreach ($data as $item) {
            $items[] = self::convertItem($item);
        }

        return $items;
    }

    public static function convertItem(array $item): self
    {
        if (array_key_exists('data', $item)) {
            $item = $item['data'];
        }

        $credits = new SeedsPictureResponseCredits(
            $item['credits']['provider'],
            $item['credits']['author'],
            $item['credits']['url'],
        );
        $links = new SeedsPictureResponseLinks(
            $item['links']['show'],
            $item['links']['render'],
        );

        return new self(
            $item['filename'],
            $item['extension'],
            $item['sizeRender'],
            $item['pathFilename'],
            $item['id'],
            $item['category'],
            $item['size'],
            $item['sizeHuman'],
            $item['date'],
            $credits,
            $links,
        );
    }
}

class SeedsPictureResponseCredits
{
    public function __construct(
        public string $provider,
        public string $author,
        public string $url,
    ) {
    }
}

class SeedsPictureResponseLinks
{
    public function __construct(
        public string $show,
        public string $render,
    ) {
    }
}
