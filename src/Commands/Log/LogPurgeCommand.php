<?php

namespace Kiwilan\Steward\Commands\Log;

use Illuminate\Console\Command;
use Kiwilan\Steward\Commands\Commandable;
use Kiwilan\Steward\Jobs\LogPurgeJob;

class LogPurgeCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:purge
                            {--s|size=20 : The maximum size of log files in MB.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge too heavy logs.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $size = $this->optionInt('size', 20);
        LogPurgeJob::dispatch($size);

        return Command::SUCCESS;
    }
}
