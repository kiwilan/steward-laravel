<?php

namespace Kiwilan\Steward\Commands\Table;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Kiwilan\Steward\Commands\Commandable;

class TableSaveCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:save
                            {table : The table name.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save a table to JSON file.';

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

        $this->info('Saving table...');
        $table = DB::table($table)->get();
        $table = json_encode($table, JSON_PRETTY_PRINT);

        $date = date('Y-m-d_H-i-s');
        $name = "table_{$table}_{$date}";
        $savePath = "{$path}/{$name}.json";

        file_put_contents($savePath, $table);

        $this->info("Table saved to {$savePath}.json.");

        return Command::SUCCESS;
    }
}
