<?php

namespace Kiwilan\Steward\Commands\Publish;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Kiwilan\Steward\Class\MetaClass;
use Kiwilan\Steward\Commands\CommandSteward;

class PublishCommand extends CommandSteward
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish {class : The class to publish, e.g. App\\Models\\Post};
                            {--p|publish : Publish all}
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
        $publish = $this->option('publish') ?? false;
        $unpublish = $this->option('unpublish') ?? false;

        if (! $publish && ! $unpublish) {
            $publish = true;
        }

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

        if (! method_exists($instance, 'getPublishableEnumPublished') && ! method_exists($instance, 'getPublishableEnumDraft')) {
            $this->error('Class does not have a publishable enum.');

            return Command::FAILURE;
        }

        if ($publish) {
            $this->info("Publishing all models of class {$meta->meta_class_namespaced}");
            $instance::query()->update(['status' => $instance->getPublishableEnumPublished()]);
        } elseif ($unpublish) {
            $this->info("Unpublishing all models of class {$meta->meta_class_namespaced}");
            $instance::query()->update(['status' => $instance->getPublishableEnumDraft()]);
        }

        return Command::SUCCESS;
    }
}
