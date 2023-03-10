<?php

namespace Kiwilan\Steward\Commands\Scout;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Kiwilan\Steward\Commands\CommandSteward;
use KiwiLan\Steward\Services\ScoutService;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'scout:fresh')]
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
    protected $description = 'Steward addon, flush all indexes and reimport models with Laravel Scout.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $scout = ScoutService::make();

        $this->warn('Clean all models in search engine.');
        $this->newLine();

        foreach ($scout->list() as $key => $value) {
            Artisan::call('scout:flush "'.$key.'"', [], $this->getOutput());
            Artisan::call('scout:delete-index "'.$value.'"', [], $this->getOutput());
            $this->newLine();
        }

        $this->warn('Import all models in search engine.');
        $this->newLine();

        foreach ($scout->list() as $key => $value) {
            Artisan::call('scout:import "'.$key.'"', [], $this->getOutput());
            $this->newLine();
        }

        $this->info('Done.');

        return Command::SUCCESS;
    }
}
