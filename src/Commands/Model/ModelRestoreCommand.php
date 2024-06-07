<?php

namespace Kiwilan\Steward\Commands\Model;

use Illuminate\Console\Command;
use Kiwilan\Steward\Commands\Commandable;

class ModelRestoreCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:restore {model : The model to restore, can be `User` or `App\Models\User`}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore model to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->title();

        $modelName = (string) $this->argument('model');

        $model = ModelBackupCommand::getModel($modelName);
        $path = ModelBackupCommand::getPath($model['filename']);

        if (! file_exists($path)) {
            $this->error("{$model['name']} file does not exist");

            return;
        }

        $json = file_get_contents($path);
        $items = json_decode($json, true);

        $model['instance']::query()->truncate();
        $model['instance']::query()->insert($items);

        $this->info("Restored {$model['name']} from {$path}");
    }
}
