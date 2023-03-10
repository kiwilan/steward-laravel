<?php

namespace Kiwilan\Steward\Services\Api\Seeds;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Enums\Api\SeedsApiCategoryEnum;
use Kiwilan\Steward\Enums\Api\SeedsApiSizeEnum;
use Kiwilan\Steward\Services\Api\MediaApi;
use Kiwilan\Steward\Services\FetchService;
use Kiwilan\Steward\Services\Http\HttpResponse;
use Kiwilan\Steward\Services\HttpService;

class SeedsApi implements MediaApi
{
    /** @var Collection<string,HttpResponse> */
    protected mixed $medias = null;

    // /** @var SeedsPictureResponse[] */
    // protected array $items = [];

    // /** @var string[] */
    // protected array $index = [];

    // /** @var Collection<string,string> */
    // protected ?Collection $medias = null;

    protected function __construct(
        protected string $api,
        protected SeedsApiCategoryEnum $category = SeedsApiCategoryEnum::all,
        protected SeedsApiSizeEnum $size = SeedsApiSizeEnum::medium,
        protected ?int $count = 1,
    ) {
    }

    public static function make(): self
    {
        $api = config('steward.factory.seeds').'/api';

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
     * @return Collection<string,HttpResponse>
     */
    public function medias(): Collection
    {
        if ($this->medias === null) {
            $this->medias = $this->fetchPictures($this->category, $this->size, $this->count);
        }

        return $this->medias;
    }

    /**
     * @return Collection<string,HttpResponse>
     */
    public function fetchPictures(
        SeedsApiCategoryEnum $category = SeedsApiCategoryEnum::all,
        SeedsApiSizeEnum $size = SeedsApiSizeEnum::medium,
        ?int $count = null
    ): Collection {
        $apiBaseURL = "{$this->api}/pictures";
        $count = $count ?? null;

        $queryParams = [
            'count' => $count,
            'category' => $category->value,
            'size' => $size->value,
        ];

        $apiURL = "{$apiBaseURL}?".http_build_query($queryParams);

        $fetch = FetchService::request($apiURL);
        $data = $fetch->json();

        $mediasURL = [];
        $seeds = SeedsPictureResponse::convertList($data);

        foreach ($seeds as $seed) {
            $mediasURL[] = $seed->links->render;
        }

        $http = HttpService::make($mediasURL)->execute();

        return $http->responses();
    }

    public function fetchPictureRandom(): FetchService
    {
        $apiBaseURL = "{$this->api}/pictures/random";

        $queryParams = [
            'category' => $this->category->value,
            'size' => $this->size->value,
        ];

        $apiURL = "{$apiBaseURL}?".http_build_query($queryParams);

        return FetchService::request($apiURL);
    }

    public function fetchPictureRandomUrl(): string
    {
        $data = SeedsRandomUrls::get();
        $url = $data[array_rand($data)];

        return "{$url}?size=small";
    }
}
