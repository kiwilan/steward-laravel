<?php

namespace Kiwilan\Steward\Jobs;

use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessPublish implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $label,
        public string $model,
        public bool $unpublish = false,
        public array $recipients = [],
    ) {}

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info('-----');
        Log::info('ProcessPublish: start');

        $action = $this->unpublish ? 'unpublish' : 'publish';
        Log::debug("ProcessPublish: {$action}");
        Artisan::call('publish', [
            'class' => $this->model,
            '--unpublish' => $this->unpublish,
        ]);
        $name = $this->label;

        Log::info('ProcessPublish: success');

        Notification::make()
            ->title($this->unpublish ? 'Unpublish is finished' : 'Publish is finished')
            ->icon($this->unpublish ? 'heroicon-o-archive' : 'heroicon-o-paper-airplane')
            ->iconColor('success')
            ->body($this->unpublish ? "All {$name} have been unpublished." : "All {$name} have been published.")
            ->sendToDatabase($this->recipients);
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception)
    {
        Log::info('ProcessPublish: failed');

        Notification::make()
            ->title($this->unpublish ? 'Publish failed' : 'Unpublish failed')
            ->icon($this->unpublish ? 'heroicon-o-archive' : 'heroicon-o-paper-airplane')
            ->iconColor('danger')
            ->body($exception->getMessage())
            ->sendToDatabase($this->recipients);
    }
}
