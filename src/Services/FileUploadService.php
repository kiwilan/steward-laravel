<?php

namespace Kiwilan\Steward\Services;

use Illuminate\Support\Facades\File;

class FileUploadService
{
    protected function __construct(
        public ?string $saveDirectory = null,
        public ?string $baseUrl = null,
    ) {
    }

    public static function make(?string $saveDirectory = null, ?string $baseUrl = null): self
    {
        $self = new self();

        if (! $saveDirectory) {
            $self->saveDirectory = public_path('storage/uploads');
        }

        if (! $baseUrl) {
            $self->baseUrl = config('app.url').'/storage/uploads';
        }

        if (! File::exists($self->saveDirectory)) {
            File::makeDirectory($self->saveDirectory, 0775, true);
        }

        return $self;
    }

    /**
     * Upload a list of files from path.
     *
     * @return FileServiceItem[]|FileServiceItem|null
     */
    public function uploadAll(array|string $paths, ?string $prefix = null): array|FileServiceItem|null
    {
        /** @var FileServiceItem[] $items */
        $items = [];

        foreach ($paths as $p) {
            $items[] = $this->upload($p, $prefix);
        }

        if (is_string($paths)) {
            return $items[0];
        }

        return $items;
    }

    public function upload(string $p, ?string $prefix = null): FileServiceItem
    {
        $self = new FileServiceItem($p);
        $path = str_replace(['(', ')'], '', $p);

        $self->isLink = str_contains($path, 'http');

        if ($self->isLink) {
            return $self;
        }

        $self->fullPath = $path;

        if ($prefix) {
            $self->fullPath = "{$prefix}/{$path}";
        }

        $self->exists = File::exists($self->fullPath);

        if (! $self->exists) {
            return $self;
        }

        $self->name = basename($self->fullPath);

        $self->savePath = "{$this->saveDirectory}/{$self->name}";

        while (File::exists($self->savePath)) {
            $self->name = uniqid().'-'.$self->name;
            $self->savePath = "{$this->saveDirectory}/{$self->name}";
        }

        File::copy($self->fullPath, $self->savePath);

        // replace in content
        $self->localUrl = $this->baseUrl.'/'.$self->name;
        $relative = explode('storage/', $self->savePath);
        $self->relativePath = $relative[1] ?? null;

        return $self;
    }
}

class FileServiceItem
{
    public function __construct(
        public string $path,
        public ?string $fullPath = null,
        public ?string $relativePath = null,
        public bool $exists = false,
        public bool $isLink = false,
        public ?string $name = null,
        public ?string $savePath = null,
        public ?string $localUrl = null,
    ) {
    }
}
