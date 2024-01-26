<?php

namespace Kiwilan\Steward\Commands;

use App\Utils\Notifier;
use Illuminate\Console\Command;

class NotifierCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifier
                            {message : Message to send.}
                            {--t|type= : `mail`, `slack` or `discord`.}
                            {--w|webhook : Webhook URL for Slack or Discord.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications with mail, Discord or Slack.';

    public function __construct(
        protected ?string $message = null,
        protected ?string $type = 'discord',
        protected ?string $webhook = null,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->title();

        $this->message = (string) $this->argument('message');
        $this->type = (string) $this->option('type');
        $this->webhook = (string) $this->option('webhook');

        $this->info("Sending notification to {$this->type}...");

        if ($this->type === 'mail') {
            Notifier::mail()
                ->auto()
                ->message($this->message)
                ->send();

            return Command::SUCCESS;
        }

        if ($this->type === 'discord') {
            $this->info("Webhook: {$this->webhook}");
            Notifier::discord($this->webhook)
                ->message($this->message)
                ->send();

            return Command::SUCCESS;
        }

        if ($this->type === 'slack') {
            $this->info("Webhook: {$this->webhook}");
            Notifier::slack($this->webhook)
                ->message($this->message)
                ->send();

            return Command::SUCCESS;
        }

        $this->error('Type not found.');

        return Command::FAILURE;
    }
}
