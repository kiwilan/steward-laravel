<?php

namespace Kiwilan\Steward\Services\FactoryService\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Enums\PictureDownloadEnum;
use Kiwilan\Steward\Services\Http\HttpResponse;
use Kiwilan\Steward\Services\HttpService;

class PictureDownloadProvider
{
    /** @var Collection<string,HttpResponse> */
    protected mixed $files = null;

    /** @var PictureDownloadItem[] */
    protected array $items = [];

    /** @var string[] */
    protected array $index = [];

    /** @var Collection<string,string> */
    protected ?Collection $medias = null;

    protected function __construct(
        protected string $mediaPath,
        protected PictureDownloadEnum $type,
        protected ?int $count = null,
    ) {
    }

    const API_URL = 'https://seeds.git-projects.xyz/api';

    public static function make(PictureDownloadEnum $type = PictureDownloadEnum::all, ?int $count = null): self
    {
        $self = new self(
            storage_path('app/media'),
            $type,
        );

        $self->count = $count;
        $self->items = $self->setItems();
        $self->files = $self->setFiles();
        $self->medias = $self->setMedias();

        return $self;
    }

    public static function clean()
    {
        File::deleteDirectory(storage_path('app/media'));
    }

    /**
     * @return Collection<string,string>
     */
    public function medias(): Collection
    {
        return $this->medias;
    }

    /**
     * @return PictureDownloadItem[]
     */
    private function setItems()
    {
        $apiBaseURL = self::API_URL.'/pictures';
        $count = $this->count ?? 1;

        $queryParams = [
            'count' => $count,
            'category' => $this->type->value,
            'size' => 'medium',
        ];

        $apiURL = "{$apiBaseURL}?".http_build_query($queryParams);

        $http = HttpService::make([$apiURL])->execute();
        $responses = $http->responses();

        $data = $responses->first()->array();

        return PictureDownloadItem::make($data['data']);
    }

    /**
     * @return Collection<string,HttpResponse>
     */
    private function setFiles(): Collection
    {
        $mediasUrl = [];

        foreach ($this->items as $item) {
            $mediasUrl[$item->pathFilename] = $item->links->render;
        }

        $http = HttpService::make($mediasUrl)->execute();
        $responses = $http->responses();

        File::deleteDirectory($this->mediaPath);
        File::ensureDirectoryExists($this->mediaPath, 0755, true);

        foreach ($responses as $key => $value) {
            $content = $value->getBody();

            $path = "{$this->mediaPath}/{$key}";
            $path = str_replace('/large', '', $path);
            File::makeDirectory(dirname($path), 0755, true, true);
            File::put($path, $content);

            $this->index[] = $path;
        }

        return $responses;
    }

    /**
     * @return Collection<string,string>
     */
    private function setMedias(): Collection
    {
        $medias = [];

        foreach ($this->index as $path) {
            $medias[$path] = $path;
        }

        return collect($medias);
    }
}
