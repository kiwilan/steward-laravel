<?php

namespace Kiwilan\Steward\Commands\Scout;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Kiwilan\Steward\Commands\CommandSteward;
use Kiwilan\Steward\Services\Class\ClassItem;
use Kiwilan\Steward\Services\ClassService;

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

    /** @var Collection<int,ClassItem> */
    protected ?Collection $models = null;

    /** @var array<string,string> */
    protected array $scout = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $this->models = collect([]);
        $this->findModels();

        $list = $this->models->map(fn ($model) => $model->name())
            ->toArray()
        ;
        $this->info('Models to search engine: '.implode(', ', $list));
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

    /**
     * Find all models with trait Searchable.
     */
    private function findModels()
    {
        $files = ClassService::files(app_path('Models'));
        $items = ClassService::make($files);

        foreach ($items as $item) {
            if ($item->useTrait('Laravel\Scout\Searchable')) {
                $this->models->push($item);
            }
        }
    }

    private function scoutName(ClassItem $model)
    {
        $instance = $model->instance();
        $name = $model->namespace();
        $name = str_replace('\\', '\\\\', $name);

        if ($model->methodExists('searchableAs')) {
            $this->scout[$name] = $instance->searchableAs();
        } else {
            if ($model->isModel()) {
                $tableName = $model->model()->getTable();
                $this->scout[$name] = $tableName;
            }
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
