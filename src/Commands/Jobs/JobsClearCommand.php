<?php

namespace Kiwilan\Steward\Commands\Jobs;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Kiwilan\Steward\Commands\Commandable;

class JobsClearCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear jobs.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $this->info('Clearing all jobs...');
        DB::table('jobs')->truncate();
        $this->info('All jobs cleared.');

        return Command::SUCCESS;
    }
}
