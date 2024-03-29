<?php

namespace Kiwilan\Steward\Commands\Scout;

use Illuminate\Console\Command;
use Kiwilan\Steward\Commands\Commandable;
use Kiwilan\Steward\Services\ScoutService;
use Kiwilan\Steward\Services\TerminalService;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'scout:list')]
class ScoutListCommand extends Commandable
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

        // $terminal = TerminalService::make($this->output->isVerbose());
        // $this->output->writeln($terminal->output());

        $list = [];

        foreach ($scout->models() as $model) {
            $list[] = [
                'Model' => $model->getName(),
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
