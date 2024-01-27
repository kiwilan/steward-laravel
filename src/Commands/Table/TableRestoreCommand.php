<?php

namespace Kiwilan\Steward\Commands\Table;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Kiwilan\Steward\Commands\Commandable;

class TableRestoreCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:restore {table}
                            {--j|json= : The name of the JSON file.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore a table from JSON file, default is more recent.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $path = storage_path('app/tables');
        if (! is_dir($path)) {
            mkdir($path, 0775, true);
        }

        $table = $this->argument('table');

        $this->info('Restoring table...');
        $file = "{$path}/{$table}.json";
        if (! file_exists($file)) {
            $this->error("Table {$table} not found.");

            return Command::FAILURE;
        }

        $files = glob("{$path}/table_{$table}*.json");
        $files = array_combine($files, array_map('filemtime', $files));

        $json = $this->option('json');

        if ($json) {
            $file = "{$path}/{$json}.json";
            if (! file_exists($file)) {
                $this->error("File {$json} not found.");

                return Command::FAILURE;
            }
        } else {
            $file = array_search(max($files), $files);
        }

        $table = file_get_contents($file);
        $table = json_decode($table, true);

        DB::table($table)->truncate();

        foreach ($table as $row) {
            DB::table($table)->insert($row);
        }

        $this->info("Table restored from {$file}.");

        return Command::SUCCESS;
    }
}
