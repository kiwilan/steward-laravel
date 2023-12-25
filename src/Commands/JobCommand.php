<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class JobCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job
                            {--c|clear : clear all jobs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle jobs.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $clear = $this->option('clear') ?: false;

        $jobs = $this->parseJobs();
        $this->table(['ID', 'Queue', 'Payload', 'Attempts', 'Reserved At', 'Available At', 'Created At'], $jobs);

        if ($clear) {
            $this->info('Clearing all jobs...');
            $this->clearAll();
        }

        return Command::SUCCESS;
    }

    private function parseJobs(): array
    {
        $jobs = DB::table('jobs')->get();
        // payload to `displayName`
        $jobs = $jobs->map(function ($job) {
            $job->payload = json_decode($job->payload)->displayName;

            return $job;
        });

        return $jobs->toArray();
    }

    /**
     * Clear all jobs.
     *
     * @return void
     */
    protected function clearAll()
    {
        DB::table('jobs')->truncate();
    }
}
