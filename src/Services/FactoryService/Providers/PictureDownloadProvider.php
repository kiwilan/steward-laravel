<?php

namespace Kiwilan\Steward\Services\FactoryService\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Enums\PictureDownloadEnum;
use Kiwilan\Steward\Services\HttpPoolService;
use SplFileInfo;
use ZipArchive;

class PictureDownloadProvider
{
    /** @var SplFileInfo[] */
    protected array $files = [];

    /** @var array<string,array> */
    protected array $mediasByCategories = [];

    /** @var Collection<string,string> */
    protected ?Collection $medias = null;

    /** @var PictureDownloadEnum[] */
    protected array $allowedCategories = [];

    /** @var string[] */
    protected array $archivesPath = [];

    protected function __construct(
        protected string $archivePath,
        protected string $mediaPath,
        protected PictureDownloadEnum $type,
        protected int $countFiles = 0,
        protected int $countMedias = 0,
        protected ?int $count = null,
    ) {
    }

    const seeds = [
        'seeds' => 'https://www.dropbox.com/s/5hwazybflgjm0ki/seeds.zip?dl=1',
    ];

    public static function make(PictureDownloadEnum $type = PictureDownloadEnum::all, ?int $count = null): self
    {
        $self = new self(
            storage_path('app/downloads'),
            storage_path('app/media'),
            $type,
        );

        $self->files = $self->setFiles();

        if (count($self->files) === 0) {
            $self->download();
            $self->unzip();

            File::deleteDirectory($self->archivePath);
            $self->files = $self->setFiles();
        }

        $self->allowedCategories = $self->setAllowedCategories();

        $self->mediasByCategories = $self->setMediasByCategories();
        $self->medias = $self->setMedias();

        $self->countFiles = count($self->files);
        $self->countMedias = count($self->medias);

        if ($count !== null) {
            $self->count = $count;

            if ($count < $self->countMedias) {
                $self->medias = $self->medias->slice(0, $count);
            } else {
                $temp = collect([]);

                for ($i = 0; $i < $count; $i++) {
                    $temp->push($self->medias->get($i % $self->countMedias));
                }

                $self->medias = $temp;
            }
        }

        $self->countMedias = count($self->medias);

        return $self;
    }

    public static function clean()
    {
        File::deleteDirectory(storage_path('app/downloads'));
        File::deleteDirectory(storage_path('app/media'));
    }

    /**
     * @return Collection<string,string>
     */
    public function medias(): Collection
    {
        return $this->medias;
    }

    public function countFiles(): int
    {
        return $this->countFiles;
    }

    public function countMedias(): int
    {
        return $this->countMedias;
    }

    /**
     * @return SplFileInfo[]
     */
    private function setFiles(): array
    {
        $medias = [];

        foreach (File::directories($this->mediaPath) as $directory) {
            $medias = array_merge($medias, File::files($directory));
        }

        return $medias;
    }

    private function setMediasByCategories(): array
    {
        $mediasByCategories = [];

        foreach ($this->files as $file) {
            $category = implode('/', array_slice(explode('/', $file->getPathname()), -2, 1));
            $mediasByCategories[$category][] = $file->getPathname();
        }

        return $mediasByCategories;
    }

    private function setMedias(): Collection
    {
        $medias = [];

        foreach ($this->mediasByCategories as $category => $media) {
            $category = PictureDownloadEnum::tryFrom($category);

            if (! in_array($category, $this->allowedCategories)) {
                continue;
            }

            $medias[] = $media;
        }

        $medias = array_merge(...$medias);
        shuffle($medias);

        return collect($medias);
    }

    private function setAllowedCategories(): array
    {
        $categories = PictureDownloadEnum::cases();

        if ($this->type !== PictureDownloadEnum::all
            && $this->type !== PictureDownloadEnum::humans
            && $this->type !== PictureDownloadEnum::landscape
            && $this->type !== PictureDownloadEnum::mainstream
        ) {
            return [$this->type];
        }

        return match ($this->type) {
            PictureDownloadEnum::all => $categories,
            PictureDownloadEnum::humans => [
                PictureDownloadEnum::people,
                PictureDownloadEnum::love,
                PictureDownloadEnum::cultural,
            ],
            PictureDownloadEnum::landscape => [
                PictureDownloadEnum::nature,
                PictureDownloadEnum::city,
                PictureDownloadEnum::space,
                PictureDownloadEnum::monument,
            ],
            PictureDownloadEnum::mainstream => [
                PictureDownloadEnum::decoration,
                PictureDownloadEnum::food,
                PictureDownloadEnum::technology,
            ],
        };
    }

    private function download()
    {
        File::deleteDirectory($this->archivePath);
        File::deleteDirectory($this->mediaPath);

        File::ensureDirectoryExists($this->archivePath, 0755, true);

        $http = HttpPoolService::make(self::seeds, [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36',
            'Accept' => 'application/zip, application/octet-stream',
            'Content-Type' => 'Content-Disposition: attachment;',
        ]);

        foreach ($http->responses() as $name => $response) {
            $path = "{$this->archivePath}/{$name}.zip";
            File::put($path, $response->body());

            $this->archivesPath[$name] = $path;
        }
    }

    private function unzip()
    {
        foreach ($this->archivesPath as $name => $path) {
            $zip = new ZipArchive();
            $zip->open($path);
            $zip->extractTo($this->mediaPath);
            $zip->close();
        }

        File::deleteDirectory("{$this->mediaPath}/__MACOSX");
    }
}
