<?php

namespace Kiwilan\Steward\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Kiwilan\Steward\Settings\GeneralSettings;
use Kiwilan\Steward\Utils\Picture;
use Spatie\Image\Enums\CropPosition;

class ProcessOpenGraph implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
    ) {}

    /**
     * Execute the job.
     */
    public function handle()
    {
        $settings = app(GeneralSettings::class);
        $this->setOpenGraph($settings->default_image);
    }

    private function setOpenGraph(string $path): ?string
    {
        $path = storage_path("app/public/{$path}");

        if (! $path || ! File::exists($path)) {
            return null;
        }

        Log::info('Generating default');

        $path = public_path('default.jpg');
        Picture::load($path)
            ->crop(1200, 630, CropPosition::Center)
            ->optimize()
            ->save($path);

        return $path;
    }
}
