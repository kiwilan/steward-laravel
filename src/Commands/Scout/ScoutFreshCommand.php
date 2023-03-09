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
    protected $signature = 'scout:fresh
                            {--l|list : List all models to search engine.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Steward addon, flush all indexes and reimport models to search engine with Laravel Scout.';

    /** @var Collection<int,ClassItem> */
    protected ?Collection $models = null;

    /** @var array<string,string> */
    protected array $scout = [];

    protected bool $list = false;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $this->list = $this->option('list') ?? false;

        $this->models = $this->findModels();

        $list = [];

        foreach ($this->models as $model) {
            $list[] = [
                'Model' => $model->name(),
                'Index' => $this->getIndexName($model),
            ];
        }
        $this->table(
            ['Model', 'Index'],
            $list,
        );

        if ($this->list) {
            return Command::SUCCESS;
        }

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
     *
     * @return Collection<int,ClassItem>
     */
    private function findModels(): Collection
    {
        $models = collect([]);

        $files = ClassService::files(app_path('Models'));
        $items = ClassService::make($files);

        foreach ($items as $item) {
            if ($item->useTrait('Laravel\Scout\Searchable')) {
                $models->push($item);
            }
        }

        return $models;
    }

    private function scoutName(ClassItem $model)
    {
        $instance = $model->instance();
        $name = $model->namespace();
        $name = str_replace('\\', '\\\\', $name);

        $this->scout[$name] = $this->getIndexName($model);
    }

    private function getIndexName(ClassItem $model): string
    {
        if ($model->methodExists('searchableAs')) {
            return $model->instance()->searchableAs();
        } else {
            if ($model->isModel()) {
                return $model->model()->getTable();
            }
        }

        throw new \Exception('Model '.$model->name().' not have searchableAs() method.');
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
