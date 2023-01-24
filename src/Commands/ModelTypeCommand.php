<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Kiwilan\Steward\Services\ModelTypeService;

class ModelTypeCommand extends CommandSteward
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate TypeScript types based on Eloquent models.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->title();

        $converter = ModelTypeService::make();

        $namespaces = [];
        foreach ($converter->models_namespaces as $name) {
            $namespaces[] = [$name];
        }
        $this->table(['Models'], $namespaces);

        $this->info('Done.');

        return CommandSteward::SUCCESS;
    }
}
