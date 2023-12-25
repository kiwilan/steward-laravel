<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class JobListCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:list
                            {--c|clear : clear all jobs}
                            {--l|limit= : limit jobs output}
                            {--f|full : display full job informations}
                            {--count : count of jobs}';

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

        $clear = (bool) $this->option('clear') ?: false;
        $limit = $this->option('limit') ?: false;

        if ($limit && ! is_numeric($limit)) {
            // remove first character if it's not numeric
            $limit = substr($limit, 1);
            $limit = intval($limit);
        }
        $full = (bool) $this->option('full') ?: false;
        $count = (bool) $this->option('count') ?: false;

        if ($count) {
            $this->info('Jobs count: '.DB::table('jobs')->count());

            return Command::SUCCESS;
        }

        if ($clear) {
            $this->info('Clearing all jobs...');
            $this->clearAll();
            $this->info('All jobs cleared.');

            return Command::SUCCESS;
        }

        $this->parseJobs($limit, $full);

        return Command::SUCCESS;
    }

    private function parseJobs(int|false $limit, bool $full): void
    {
        $jobs = DB::table('jobs')->get();
        $items = [];

        // payload to `displayName`
        foreach ($jobs as $job) {
            $item = [
                'id' => $job->id,
                'queue' => $job->queue,
                'payload' => json_decode($job->payload)->displayName,
                'reserved_at' => $job->reserved_at,
                'available_at' => date('Y-m-d H:i:s', substr($job->available_at, 0, 10)),
            ];

            if ($full) {
                $item = [
                    'id' => $job->id,
                    'queue' => $job->queue,
                    'payload' => $job->payload,
                    'attempts' => $job->attempts,
                    'reserved_at' => $job->reserved_at,
                    'available_at' => $job->available_at,
                ];
            }

            $items[] = $item;
        }

        if ($limit) {
            $items = array_slice($items, 0, $limit);
        }

        $table = $full ? ['id', 'queue', 'payload', 'attempts', 'reserved_at', 'available_at'] : ['id', 'queue', 'payload', 'reserved_at', 'available_at'];
        $this->table($table, $items);
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
