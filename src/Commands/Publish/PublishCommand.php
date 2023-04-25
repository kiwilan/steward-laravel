<?php

namespace Kiwilan\Steward\Commands\Publish;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Kiwilan\Steward\Class\MetaClass;
use Kiwilan\Steward\Commands\Commandable;

class PublishCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish {class : The class to publish, e.g. App\\Models\\Post};
                            {--u|unpublish : Unpublish all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish or unpublish all models of a given class, default is `publish`. The class have to use `Publishable` trait to use this command.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $class = $this->argument('class');
        $unpublish = $this->option('unpublish') ?: false;

        $meta = MetaClass::make($class);
        $instance = new $class();

        if (! $meta->useTrait(\Kiwilan\Steward\Traits\Publishable::class)) {
            $this->error('Class does not use the Publishable trait.');

            return Command::FAILURE;
        }

        if (! $instance instanceof Model) {
            $this->error('Class is not an Eloquent model.');

            return Command::FAILURE;
        }

        $models = $instance::all();

        if ($unpublish) {
            $this->info("Unpublishing all models of class {$meta->classNamespaced()}");

            foreach ($models as $current) {
                if (! method_exists($current, 'unpublish')) {
                    $this->error('Class does not have a publishable enum.');

                    return Command::FAILURE;
                }
                $current->unpublish();
            }
        } else {
            $this->info("Publishing all models of class {$meta->classNamespaced()}");

            foreach ($models as $current) {
                if (! method_exists($current, 'publish')) {
                    $this->error('Class does not have a publishable enum.');

                    return Command::FAILURE;
                }
                $current->publish();
            }
        }

        return Command::SUCCESS;
    }
}
