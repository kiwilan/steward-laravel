<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use ReflectionClass;

class ScoutFreshCommand extends Command
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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->alert($this->signature);
        $this->warn($this->description);

        $list = config('steward.scoutable');
        $this->info('Models to search engine:'.implode(', ', $list));
        $this->newLine();
        foreach ($list as $model) {
            $this->getScoutName($model);
        }

        try {
            $this->warn('Clean all models in search engine.');
            foreach ($this->models as $key => $value) {
                Artisan::call('scout:flush "'.$key.'"', [], $this->getOutput());
                Artisan::call('scout:delete-index "'.$value.'"', [], $this->getOutput());
            }
            $this->newLine();

            $this->info('Import all models in search engine.');
            foreach ($this->models as $key => $value) {
                Artisan::call('scout:import "'.$key.'"', [], $this->getOutput());
            }
            $this->newLine();
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }

        $this->newLine();
        $this->info('Done.');

        return 0;
    }

    public function getScoutName(string $model)
    {
        $instance = new $model();
        $class = new ReflectionClass($instance);
        $name = str_replace('\\', '\\\\', $class->getName());
        if (method_exists($this, 'searchableAs')) {
            $this->models[$name] = $instance->searchableAs();
        }
    }
}
