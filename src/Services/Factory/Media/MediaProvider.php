<?php

namespace Kiwilan\Steward\Services\Factory\Media;

use Kiwilan\Steward\Enums\Api\MediaApiEnum;
use Kiwilan\Steward\Enums\Api\SeedsApiCategoryEnum;
use Kiwilan\Steward\Enums\Api\SeedsApiSizeEnum;
use Kiwilan\Steward\Services\Api\MediaApi;
use Kiwilan\Steward\Services\Api\Seeds\SeedsApi;

class MediaProvider
{
    protected function __construct(
        protected MediaApiEnum $type = MediaApiEnum::seeds,
        protected ?MediaApi $api = null,
    ) {
    }

    public static function make(): self
    {
        return new self();
    }

    public function seeds(
        SeedsApiCategoryEnum $category = SeedsApiCategoryEnum::all,
        SeedsApiSizeEnum $size = SeedsApiSizeEnum::medium,
        int $count = 1,
    ): MediaApi {
        $this->type = MediaApiEnum::seeds;
        $this->api = $this->setApi();
        $this->api->config($category, $size, $count);

        return $this->api;
    }

    private function setApi(): MediaApi
    {
        return match ($this->type) {
            // MediaApiEnum::seeds => SeedsApi::make(),
            default => SeedsApi::make(),
        };
    }
}
