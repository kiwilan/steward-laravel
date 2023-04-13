<?php

namespace Kiwilan\Steward\Commands\Submission;

use Illuminate\Console\Command;
use Kiwilan\Steward\Commands\Commandable;
use Kiwilan\Steward\Models\Submission;

class SubmissionGdprCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'submission:gdpr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old submissions that are not accepted the GDPR.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $submissions = Submission::whereYear('created_at', '<', date('Y') - 4)->get();
        $submissions->each(fn (Submission $submission) => $submission->delete());

        $this->info("{$submissions->count()} submissions deleted");

        return Command::SUCCESS;
    }
}
