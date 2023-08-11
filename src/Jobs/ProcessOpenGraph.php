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
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

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
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $settings = app(GeneralSettings::class);
        $this->setOpenGraph($settings->default_image);
    }

    private function setOpenGraph(string $path)
    {
        $path = storage_path("app/public/{$path}");

        if (! $path || ! File::exists($path)) {
            return;
        }

        Log::info('Generating default');

        $image = Image::load($path);
        $image->manipulate(function (Manipulations $manipulations) {
            return $manipulations
                ->crop(Manipulations::CROP_CENTER, 1200, 630)
                ->optimize()
            ;
        })->save(storage_path('app/public/default.jpg'));
    }
}
