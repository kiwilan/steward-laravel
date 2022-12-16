<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;

class LaravelStewardCommand extends CommandSteward
{
    public $signature = 'laravel-steward';

    public $description = 'My command';

    public function handle(): int
    {
        $this->title();

        $this->comment('All done');

        return Command::SUCCESS;
    }
}
