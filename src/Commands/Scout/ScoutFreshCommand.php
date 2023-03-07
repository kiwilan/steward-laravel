<?php

namespace Kiwilan\Steward\Commands\Scout;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Kiwilan\Steward\Commands\CommandSteward;
use Kiwilan\Steward\Services\ClassService;
use ReflectionClass;
use SplFileInfo;

class ScoutFreshCommand extends CommandSteward
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage models to search engine with Laravel Scout.';

    protected $models = [];

    protected $scout = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $configAuto = (bool) config('steward.scoutable.auto');
        $configList = (array) config('steward.scoutable.models');

        if ($configAuto) {
            $this->models = $this->findModels();
        } else {
            $this->models = $configList;
        }

        $this->info('Models to search engine: '.implode(', ', $this->models));
        $this->newLine();

        foreach ($this->models as $model) {
            $this->scoutName($model);
        }

        try {
            $this->freshModels();
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }

        $this->info('Done.');

        return Command::SUCCESS;
    }

    private function findModels(): array
    {
        $path = app_path('Models');

        /** @var Collection<int,SplFileInfo> */
        $files = collect(File::allFiles($path));

        /** @var Collection<int,SplFileInfo> */
        $filesModels = collect();

        $files->map(function ($file) use ($filesModels) {
            if ($file->getExtension() === 'php') {
                $filesModels->push($file);
            }
        });

        $models = [];

        foreach ($filesModels as $file) {
            $service = ClassService::make($file->getPath());
            $namespace = $service->namespace();
            $class = new $namespace();
            $traits = class_uses($class);

            if (in_array('Laravel\Scout\Searchable', $traits)) {
                $models[] = $namespace;
            }
        }

        return $models;
    }

    private function scoutName(string $model)
    {
        $instance = new $model();
        $class = new ReflectionClass($instance);
        $name = $class->getName();
        $name = str_replace('\\', '\\\\', $name);

        if (method_exists($instance, 'searchableAs')) {
            $this->scout[$name] = $instance->searchableAs();
        }
    }

    private function freshModels()
    {
        $this->warn('Clean all models in search engine.');
        $this->newLine();

        foreach ($this->scout as $key => $value) {
            Artisan::call('scout:flush "'.$key.'"', [], $this->getOutput());
            Artisan::call('scout:delete-index "'.$value.'"', [], $this->getOutput());
            $this->newLine();
        }

        $this->warn('Import all models in search engine.');
        $this->newLine();

        foreach ($this->scout as $key => $value) {
            Artisan::call('scout:import "'.$key.'"', [], $this->getOutput());
            $this->newLine();
        }
    }
}
