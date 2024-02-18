<?php

namespace Kiwilan\Steward\Utils\Faker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Kiwilan\HttpPool\Utils\PrintConsole;
use Kiwilan\Steward\StewardConfig;
use Kiwilan\Steward\Utils\Faker;
use Kiwilan\Steward\Utils\Process;

class FakerMediaLocal
{
    public function __construct(
        public Faker $faker,
        public ?string $basePath = null,
        public ?string $path = null,
    ) {
    }

    /**
     * @param  Collection<int, Model>  $models
     */
    public function associate(Collection $models, string $field = 'picture', bool $multiple = false): void
    {
        $verbose = StewardConfig::factoryVerbose();
        Process::executionTime(function () use ($models, $field, $multiple, $verbose) {
            $console = PrintConsole::make();

            $chunkMax = StewardConfig::factoryMaxHandle();

            if ($verbose) {
                $console->print("  FakerMediaDownloader handle {$chunkMax} items maximum.", 'bright-blue');
            }

            if ($models->count() > $chunkMax) {
                $models->chunk($chunkMax)->each(function (Collection $chunk) use ($field, $multiple) {
                    $this->associateByChunk($chunk, $field, $multiple);
                });
            } else {
                $this->associateByChunk($models, $field, $multiple);
            }
        }, $verbose);
    }

    private function associateByChunk(Collection $models, string $field, bool $multiple): void
    {
        /** @var Model */
        $instance = $models->first();
        $images = $this->fetchMedias($instance->getTable());

        $table = $instance->getTable();
        $id = $instance->getKeyName();
        DB::beginTransaction();

        foreach ($models as $key => $model) {
            $media = null;
            $images = $images->shuffle();

            if ($multiple) {
                $media = $this->faker->generator()->randomElements($images, $this->faker->generator()->numberBetween(1, 5));
            } else {
                $media = $images->first();
            }

            DB::table($table)
                ->where($id, '=', $model->{$id})
                ->update([
                    $field => $media,
                ]);
        }
        DB::commit();
    }

    /**
     * @return Collection<int,string>
     */
    private function fetchMedias(?string $basePath = null): Collection
    {
        $path = "{$this->basePath}/{$this->path}";

        if (! File::exists($path)) {
            throw new \Exception("Media path not found: {$path}");
        }
        $files = File::allFiles($path);

        $images = collect([]);

        foreach ($files as $file) {
            $images->push(
                Faker::mediaFromFile(
                    $file->getRealPath(),
                    $basePath
                )
            );
        }

        return $images;
    }
}
