<?php

namespace Kiwilan\Console\Commands;

use Illuminate\Console\Command;
use Kiwilan\Steward\Models\Submission;

class SubmissionSendCommand extends Command
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
        $this->alert($this->signature);
        $this->warn($this->description);
        $this->newLine();

        $submission = Submission::factory(1)->make(parent: new Submission());
        /** @var Submission */
        $submission = $submission->first();

        if (property_exists($submission, 'name')) {
            $submission->name = 'Test Submission';
        }
        if (property_exists($submission, 'created_at')) {
            $submission->created_at = now();
        }
        $submission->save();

        $this->info('Notification sent.');

        return 0;
    }
}
