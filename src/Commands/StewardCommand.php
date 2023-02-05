<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;

class StewardCommand extends Command
{
    public $signature = 'steward';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
