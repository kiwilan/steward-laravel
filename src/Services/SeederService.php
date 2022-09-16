<?php

namespace Kiwilan\Steward\Services;

use App\Models\Collector;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class SeederService
{
    /**
     * Générer un body riche.
     */
    public static function generateRichBody(Generator $faker): string
    {
        $html = '';

        $dir = 'public/uploads';

        if (! File::exists($dir)) {
            File::makeDirectory($dir);
        }

        /*
         * Generate 1 title + block
         */
        for ($i = 0; $i < $faker->numberBetween(1, 1); $i++) {
            $title = Str::title($faker->words($faker->numberBetween(5, 10), true));
            $html .= "<h2>{$title}</h2>";

            /*
             * Generate 1 subtitle + block
             */
            for ($j = 0; $j < $faker->numberBetween(1, 2); $j++) {
                $title = Str::title($faker->words($faker->numberBetween(5, 10), true));
                $html .= "<h3>{$title}</h3>";

                /*
                 *  Generate many paragraphs
                 */
                for ($k = 0; $k < $faker->numberBetween(2, 5); $k++) {
                    $paragraph = $faker->paragraph(5);
                    $html .= "<p>{$paragraph}</p>";

                    /*
                     * Add image randomly
                     */
                    // if ($faker->boolean(25)) {
                    //     $source = self::randomMediaPath($faker, 'business');
                    //     $image = basename($source);
                    //     $target = "$dir/$image";

                    //     if (! File::exists($target)) {
                    //         File::copy($source, $target);
                    //     }

                    //     $html .= "<p><img src=\"/uploads/{$image}\" alt=\"\"></p>";
                    // }
                }
            }
        }

        return $html;
    }

    public static function jsonToModel(string $model): bool
    {
        /** @var Model */
        $instance = new $model();
        $name = $instance->getTable();
        $name = Str::replace('_', '-', $name);

        $is_searchable = false;
        if (method_exists($instance, 'search')) {
            $is_searchable = true;
        }

        if ($is_searchable) {
            return $model::withoutSyncingToSearch(function () use ($model) {
                return SeederService::parseJson($model);
            });
        }

        return SeederService::parseJson($model);
    }

    public static function parseJson(string $model): bool
    {
        /** @var Model */
        $instance = new $model();
        $name = $instance->getTable();
        $name = Str::replace('_', '-', $name);

        $path = database_path("seeders/data/{$name}.json");
        if (! File::exists($path)) {
            echo 'No JSON detected';

            return false;
        }
        $json = json_decode(File::get($path));

        foreach ($json as $entity) {
            $data = (array) $entity;
            $data_entity = $data;
            unset($data_entity['foreign']);

            if (class_exists($model)) {
                /** @var Model */
                $created_model = $model::create($data_entity);
                if ($created_model->isFillable('picture')) {
                    $created_model = SeederService::setMedia($created_model);
                }

                if (array_key_exists('foreign', $data)) {
                    $foreign = (array) $data['foreign'];
                    foreach ($foreign as $relation => $value) {
                        $foreign_model = "\\App\\Models\\{$value->model}";
                        $foreign_key = $value->data;

                        $foreign_entity = $foreign_model::whereSlug($foreign_key)->first();
                        $created_model->{$relation}()->associate($foreign_entity);
                    }
                }

                $created_model->save();
            }
        }

        return true;
    }

    public static function setMedia(mixed $model): Model
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

    public static function randomMediaPath(string $type, string $category, string $extension = 'jpg'): string
    {
        $faker = Factory::create();

        $types = [
            'man' => 13,
            'woman' => 16,
        ];

        $type = (string) $faker->numberBetween(1, $types[$type]);
        $i = str_pad($type, 2, '0', STR_PAD_LEFT);

        return database_path("seeders/media/{$category}/{$type}-{$i}.{$extension}");
    }

    public static function medias(string $category, bool $multiple = false): string|array
    {
        $faker = Factory::create();

        $media_path = database_path("seeders/media/{$category}");
        $medias = File::allFiles($media_path);

        if ($multiple) {
            /** @var SplFileInfo[] */
            $medias_gallery = $faker->randomElements($medias, $faker->numberBetween(0, count($medias) > 5 ? 5 : count($medias)));
            $medias_gallery_entries = [];
            foreach ($medias_gallery as $item) {
                $name = SeederService::createMedia($item, $category);
                $medias_gallery_entries[] = $name;
            }

            return $medias_gallery_entries;
        }

        /** @var SplFileInfo */
        $media = $faker->randomElement($medias);

        return SeederService::createMedia($media, $category);
    }

    public static function timestamps(): array
    {
        $faker = Factory::create();

        $created_at = Carbon::createFromTimeString(
            $faker->dateTimeBetween('-20 years')->format('Y-m-d H:i:s')
        )->format('Y-m-d H:i:s');
        $updated_at = Carbon::createFromTimeString(
            $faker->dateTimeBetween($created_at)->format('Y-m-d H:i:s')
        )->format('Y-m-d H:i:s');

        return [
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ];
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

    protected static function createMedia(SplFileInfo $media, string $category): string
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
