<?php

namespace Kiwilan\Steward\Services\Api\Seeds;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Enums\Api\SeedsApiCategoryEnum;
use Kiwilan\Steward\Enums\Api\SeedsApiSizeEnum;
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
        // protected string $mediaPath,
        // protected SeedsApiCategoryEnum $type,
        // protected ?int $count = null,
    ) {
    }

    public static function make(): self
    {
        $api = config('steward.factory.seeds').'/api';

        return new self($api);
    }

    public function config(...$config): self
    {
        dump($config);

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
        return $this->medias;
    }

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

        $http = HttpService::make([$apiURL])->execute();
        $responses = $http->responses();

        $data = $responses->first()->array();

        // return SeedsPictureResponse::make($data['data']);

        return collect([]);
    }
}
