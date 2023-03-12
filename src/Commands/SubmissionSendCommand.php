<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Kiwilan\Steward\Models\Submission;

class SubmissionSendCommand extends CommandSteward
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'submission:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a submission to test notifications.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();
        $model = \Kiwilan\Steward\StewardConfig::submissionModel();

        /** @var Submission $model */
        $submission = $model::factory(1)->make(parent: new $model());
        $submission = $submission->first();

        if (property_exists($submission, 'name')) {
            $submission->name = 'Test Submission';
        }

        if (property_exists($submission, 'created_at')) {
            $submission->created_at = now();
        }
        $submission->save();

        $this->info('Notification sent.');

        return Command::SUCCESS;
    }
}
