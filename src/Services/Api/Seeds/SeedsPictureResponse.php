<?php

namespace Kiwilan\Steward\Services\Api\Seeds;

class SeedsPictureResponse
{
    public function __construct(
        public ?string $filename = null,
        public ?string $extension = null,
        public ?string $sizeRender = null,
        public ?string $pathFilename = null,
        public ?string $id = null,
        public ?string $category = null,
        public ?string $size = null,
        public ?string $sizeHuman = null,
        public ?string $date = null,
        public ?SeedsPictureResponseCredits $credits = null,
        public ?SeedsPictureResponseLinks $links = null,
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
            $item['credits']['provider'] ?? null,
            $item['credits']['author'] ?? null,
            $item['credits']['url'] ?? null,
        );
        $links = new SeedsPictureResponseLinks(
            $item['links']['show'] ?? null,
            $item['links']['render'] ?? null,
        );

        return new self(
            $item['filename'] ?? null,
            $item['extension'] ?? null,
            $item['sizeRender'] ?? null,
            $item['pathFilename'] ?? null,
            $item['id'] ?? null,
            $item['category'] ?? null,
            $item['size'] ?? null,
            $item['sizeHuman'] ?? null,
            $item['date'] ?? null,
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
