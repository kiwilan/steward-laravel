<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kiwilan\Steward\Commands\Commandable;

class LighthouseCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lighthouse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run lighthouse with spatie/lighthouse';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->title();

        if (! class_exists('\Spatie\Lighthouse\Lighthouse')) {
            $this->error('Lighthouse is not installed. Please run `composer require spatie/lighthouse`');

            return;
        }

        $result = \Spatie\Lighthouse\Lighthouse::url('https://recette.koguart.com')->run();

        $scores = $result->scores();
        $this->table(
            ['Scores', 'Value'],
            [
                ['Performance', $scores['performance']],
                ['Accessibility', $scores['accessibility']],
                ['Best Practices', $scores['best-practices']],
                ['SEO', $scores['seo']],
                ['PWA', $scores['pwa']],
            ]
        );

        $this->newLine();

        $fcp = $result->formattedFirstContentfulPaint();
        $lcp = $result->formattedLargestContentfulPaint();
        $si = $result->formattedSpeedIndex();
        $tbt = $result->formattedTotalBlockingTime();
        $tti = $result->formattedTimeToInteractive();
        $cls = $result->formattedCumulativeLayoutShift();
        $tps = $result->totalPageSizeInBytes();
        $kiloBytes = number_format((float) $tps / 1024, 2, '.', '');

        $this->table(
            ['Audit', 'Value'],
            [
                ['First Contentful Paint (FCP)', $fcp],
                ['Largest Contentful Paint (LCP)', $lcp],
                ['Speed Index (SI)', $si],
                ['Total Blocking Time (TBT)', $tbt],
                ['Time To Interactive (TTI)', $tti],
                ['Cumulative Layout Shift (CLS)', $cls],
                ['Total Page Size (TPS)', "{$kiloBytes} KB"],
            ]
        );

        $this->newLine();

        $this->info('Done!');
    }
}
