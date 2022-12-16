<?php

namespace Kiwilan\Steward\Commands\Publish;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Kiwilan\Steward\Commands\CommandSteward;
use Kiwilan\Steward\Enums\PublishStatusEnum;

class PublishScheduledCommand extends CommandSteward
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish:scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish models that are scheduled to be published. The class have to use `Publishable` trait to use this command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $models = config('steward.publishable.models');

        foreach ($models as $model) {
            $instance = new $model();
            $date_column = Schema::hasColumn($instance->getTable(), 'published_at') ? 'published_at' : 'created_at';

            $models_udpated = $model::query()
                ->where('status', '=', PublishStatusEnum::scheduled)
                ->where($date_column, '<', Carbon::now())
                ->get()
            ;

            $models_udpated->each(function ($model_updated) {
                $model_updated->update(['status' => PublishStatusEnum::published]);
            });

            $this->info("Publish {$models_udpated->count()} {$model}");
        }

        return Command::SUCCESS;
    }
}
