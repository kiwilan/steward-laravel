<?php

namespace Kiwilan\Steward\Services\FactoryService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kiwilan\Steward\Services\FactoryService;
use Symfony\Component\Finder\SplFileInfo;

class FactoryMedia
{
    public function __construct(
        public FactoryService $factory,
    ) {
    }

    public function setMedia(mixed $model): Model
    {
        $table = Str::replace('_', '-', $model->getTable());
        if (! $model->isFillable('slug') || ! $model->isFillable('picture')) {
            return $model;
        }

        $media_path = database_path("seeders/media/{$table}/{$model->slug}.webp");
        if (File::exists($media_path)) {
            $media = File::get($media_path);

            $directory = public_path("storage/{$table}");
            if (! File::exists($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }

            $filename = uniqid().'_'."{$model->slug}.webp";
            File::put("{$directory}/{$filename}", $media);

            $media = "{$table}/{$filename}";
            $model->picture = $media;

            return $model;
        }

        return $model;
    }

    public function randomMediaPath(string $type, string $category, string $extension = 'jpg'): string
    {
        $types = [
            'man' => 13,
            'woman' => 16,
        ];

        $type = (string) $this->factory->faker->numberBetween(1, $types[$type]);
        $i = str_pad($type, 2, '0', STR_PAD_LEFT);

        return database_path("seeders/media/{$category}/{$type}-{$i}.{$extension}");
    }

    public function medias(string $category, bool $multiple = false): string|array
    {
        $media_path = database_path("seeders/media/{$category}");
        $medias = File::allFiles($media_path);

        if ($multiple) {
            /** @var SplFileInfo[] */
            $medias_gallery = $this->factory->faker->randomElements($medias, $this->factory->faker->numberBetween(0, count($medias) > 5 ? 5 : count($medias)));
            $medias_gallery_entries = [];
            foreach ($medias_gallery as $item) {
                $name = FactoryMedia::createMedia($item, $category);
                $medias_gallery_entries[] = $name;
            }

            return $medias_gallery_entries;
        }

        /** @var SplFileInfo */
        $media = $this->factory->faker->randomElement($medias);

        return FactoryMedia::createMedia($media, $category);
    }

    /**
     * Clear all media collection manage by spatie/laravel-medialibrary.
     */
    public function clearAllMediaCollection(): bool
    {
        $isSuccess = false;

        // try {
        //     $collectors = Collector::all();
        //     $collectors->each(function ($query) {
        //         $query->clearMediaCollection('collectors_avatar');
        //     });
        //     $miniatures = Collector::all();
        //     $miniatures->each(function ($query) {
        //         $query->clearMediaCollection('miniatures_primary');
        //     });
        //     $isSuccess = true;
        // } catch (\Throwable $th) {
        //     // throw $th;
        // }
        Storage::disk('public')->deleteDirectory('picture');

        return $isSuccess;
    }

    protected function createMedia(SplFileInfo $media, string $category): string
    {
        $filename = uniqid().'_'.$media->getFilename();

        $directory = public_path("storage/{$category}");
        $item_path = "{$category}/{$filename}";
        $media_path_dist = public_path("storage/{$item_path}");
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }
        File::put($media_path_dist, $media->getContents());

        return $item_path;
    }
}
