<?php

namespace Kiwilan\Steward\Services\Api\Seeds;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kiwilan\HttpPool\HttpPool;
use Kiwilan\HttpPool\Response\HttpPoolResponse;
use Kiwilan\Steward\Enums\Api\SeedsApiCategoryEnum;
use Kiwilan\Steward\Enums\Api\SeedsApiSizeEnum;
use Kiwilan\Steward\Services\Api\MediaApi;
use Kiwilan\Steward\StewardConfig;

class SeedsApi implements MediaApi
{
    /** @var Collection<string,HttpPoolResponse> */
    protected ?Collection $medias = null;

    protected function __construct(
        protected string $api,
        protected SeedsApiCategoryEnum $category = SeedsApiCategoryEnum::all,
        protected SeedsApiSizeEnum $size = SeedsApiSizeEnum::medium,
        protected ?int $count = 1,
    ) {
    }

    public static function make(): self
    {
        $api = \Kiwilan\Steward\StewardConfig::factoryMediaDownloaderSeedsApi().'/api';

        return new self($api);
    }

    public function config(...$config): self
    {
        $this->category = $config[0] ?? $this->category;
        $this->size = $config[1] ?? $this->size;
        $this->count = $config[2] ?? $this->count;

        return $this;
    }

    public static function clean()
    {
        File::deleteDirectory(storage_path('app/media'));
    }

    /**
     * @return Collection<string,HttpPoolResponse>
     */
    public function medias(): Collection
    {
        if ($this->medias === null) {
            $this->medias = $this->fetchPictures($this->category, $this->size, $this->count);
        }

        return $this->medias;
    }

    /**
     * @return Collection<string,HttpPoolResponse>
     */
    public function fetchPictures(
        SeedsApiCategoryEnum $category = SeedsApiCategoryEnum::all,
        SeedsApiSizeEnum $size = SeedsApiSizeEnum::medium,
        ?int $count = null
    ): Collection {
        $count = $count ?? null;

        $fetch = HttpPool::make([
            self::buildURL("{$this->api}/pictures", query: [
                'count' => $count,
                'category' => $category->value,
                'size' => $size->value,
            ]),
        ])->execute();
        $data = $fetch->getResponses()->first()->getBody()->toArray();

        $mediasURL = [];
        $seeds = SeedsPictureResponse::convertList($data);

        foreach ($seeds as $seed) {
            $mediasURL[] = $seed->links->render;
        }

        $http = HttpPool::make($mediasURL)->execute();

        return $http->getResponses();
    }

    public function fetchPictureRandom(): HttpPoolResponse
    {
        return HttpPool::make([
            self::buildURL("{$this->api}/pictures/random", query: [
                'category' => $this->category->value,
                'size' => $this->size->value,
            ]),
        ])->execute()->getResponses()->first();
    }

    public function fetchPictureRandomUrl(string $size = 'medium'): string
    {
        $data = SeedsRandomUrls::get();
        $baseURL = StewardConfig::factoryMediaDownloaderSeedsApi();
        $url = $data[array_rand($data)];

        return "{$baseURL}{$url}?size={$size}";
    }

    /**
     * Build an URL
     *
     * @param  string[]  $params
     * @param  string[]  $query
     */
    public static function buildURL(string $url, array $params = [], array $query = []): string
    {
        if (! empty($params)) {
            $paramsStr = implode('/', $params);

            $url .= "/{$paramsStr}";
        }

        if (! empty($query)) {
            $queryStr = http_build_query($query);

            $url .= "?{$queryStr}";
        }

        $url = str_replace(' ', '%20', $url);

        return str_replace('//', '/', $url);
    }
}
