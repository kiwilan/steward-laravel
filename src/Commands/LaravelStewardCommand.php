<?php

namespace Kiwilan\LaravelSteward\Commands;

use Illuminate\Console\Command;

class LaravelStewardCommand extends Command
{
    public $signature = 'laravel-steward';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
