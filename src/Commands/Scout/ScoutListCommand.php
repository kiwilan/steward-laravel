<?php

namespace Kiwilan\Steward\Commands\Scout;

use Illuminate\Console\Command;
use Kiwilan\Steward\Commands\CommandSteward;
use KiwiLan\Steward\Services\ScoutService;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'scout:list')]
class ScoutListCommand extends CommandSteward
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Steward addon, list models all scoutable from Laravel Scout.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $scout = ScoutService::make();

        $list = [];

        foreach ($scout->models() as $model) {
            $list[] = [
                'Model' => $model->name(),
                'Index' => ScoutService::getIndexName($model),
            ];
        }
        $this->table(
            ['Model', 'Index'],
            $list,
        );

        return Command::SUCCESS;
    }
}
