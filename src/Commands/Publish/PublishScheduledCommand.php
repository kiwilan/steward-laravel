<?php

namespace Kiwilan\Steward\Commands\Publish;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Kiwilan\Steward\Commands\Commandable;
use Kiwilan\Steward\Enums\PublishStatusEnum;
use Kiwilan\Steward\Services\ClassParserService;

class PublishScheduledCommand extends Commandable
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

        $items = ClassParserService::toCollection(app_path('Models'));

        foreach ($items as $item) {
            if (! $item->isModel()) {
                continue;
            }

            if (! $item->useTrait('Publishable')) {
                continue;
            }

            $date_column = Schema::hasColumn($item->getModel()->getTable(), 'published_at') ? 'published_at' : 'created_at';

            $models_udpated = $item->getModel()::query()
                ->where('status', '=', PublishStatusEnum::scheduled)
                ->where($date_column, '<', Carbon::now())
                ->get();

            $models_udpated->each(function ($model_updated) {
                $model_updated->update(['status' => PublishStatusEnum::published]);
            });

            $this->info("Publish {$models_udpated->count()} {$item->getName()}.");
        }

        $this->newLine();
        $this->info('Done.');

        return Command::SUCCESS;
    }
}
