<?php

namespace Kiwilan\Steward\Commands\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Kiwilan\Steward\Commands\Commandable;

class ModelBackupCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:backup {model : The model to backup, can be `User` or `App\Models\User`}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup model from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->title();

        $modelName = (string) $this->argument('model');

        $model = ModelBackupCommand::getModel($modelName);
        $path = ModelBackupCommand::getPath($model['filename']);

        if (file_exists($path)) {
            $contents = json_decode(file_get_contents($path), true);
            if (count($contents) === 0) {
                $this->info("{$model['name']} file is empty, deleting...");
                unlink($path);
            }
        }

        $items = DB::table($model['instance']->getTable())->get();
        $json = $items->toJson(JSON_PRETTY_PRINT);

        file_put_contents($path, $json);

        $this->info("Saved {$model['name']} to {$path}");
    }

    /**
     * @return array{name: string, filename: string, model: string, instance: \Illuminate\Database\Eloquent\Model}
     */
    public static function getModel(string $modelName): array
    {
        if (str_contains($modelName, '\\')) {
            $model = $modelName;
        } else {
            $model = 'App\\Models\\'.$modelName;
        }

        $instance = new $model;
        $reflection = new \ReflectionClass($instance);
        $className = $reflection->name;
        $className = str_replace('\\', '_', $className);
        $fileName = Str::slug($className).'.json';

        return [
            'name' => $reflection->name,
            'model' => $model,
            'filename' => $fileName,
            'instance' => $instance,
        ];
    }

    public static function getPath(string $filename): string
    {
        $basePath = storage_path('app'.DIRECTORY_SEPARATOR.'model-backup');
        if (! is_dir($basePath)) {
            mkdir($basePath, 0775, true);
        }

        return $basePath.DIRECTORY_SEPARATOR.$filename;
    }
}
