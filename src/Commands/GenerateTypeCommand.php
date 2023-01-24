<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Kiwilan\Steward\Services\ModelTypeService;
use Kiwilan\Steward\Services\ZiggyTypeService;

class GenerateTypeCommand extends CommandSteward
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:type
                            {type=models : Generate `models` or `ziggy` types}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate TypeScript types for Eloquent models or Ziggy.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->title();
        $type = $this->argument('type');

        if ($type === 'models') {
            $this->models();
        }

        if ($type === 'ziggy') {
            $this->ziggy();
        }

        $this->info('Done.');

        return CommandSteward::SUCCESS;
    }

    private function models()
    {
        $converter = ModelTypeService::make();

        $namespaces = [];

        foreach ($converter->models_namespaces as $name) {
            $namespaces[] = [$name];
        }
        $this->table(['Models'], $namespaces);

        $this->info('Generated model types.');
    }

    private function ziggy()
    {
        $converter = ZiggyTypeService::make();

        $this->info('Generated Ziggy types.');
    }
}
