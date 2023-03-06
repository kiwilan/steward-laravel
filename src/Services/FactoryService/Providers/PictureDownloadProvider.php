<?php

namespace Kiwilan\Steward\Services\FactoryService\Providers;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Enums\PictureDownloadEnum;
use Kiwilan\Steward\Services\HttpPoolService;

class PictureDownloadProvider
{
    /** @var Collection<string,Response> */
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

        $queryParams = [
            'count' => $this->count,
            'category' => $this->type->value,
            'size' => 'large',
        ];

        $apiURL = "{$apiBaseURL}?".http_build_query($queryParams);

        $http = HttpPoolService::make([$apiURL]);
        $res = $http->responses()
            ->first()
        ;

        $json = $res->json();

        return PictureDownloadItem::make($json['data']);
    }

    /**
     * @return Collection<string,Response>
     */
    private function setFiles(): Collection
    {
        $mediasUrl = [];

        foreach ($this->items as $item) {
            $mediasUrl[$item->pathFilename] = $item->links->render;
        }

        $http = HttpPoolService::make($mediasUrl);
        $res = $http->responses();

        File::deleteDirectory($this->mediaPath);
        File::ensureDirectoryExists($this->mediaPath, 0755, true);

        foreach ($res as $key => $value) {
            $content = $value->body();

            $path = "{$this->mediaPath}/{$key}";
            $path = str_replace('/large', '', $path);
            File::makeDirectory(dirname($path), 0755, true, true);
            File::put($path, $content);

            $this->index[] = $path;
        }

        return $res;
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
