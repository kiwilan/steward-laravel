<?php

namespace Kiwilan\Steward\Commands;

use Kiwilan\Steward\Services\NotifyService;

class NotifyCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify
                            {message : Message to send.}
                            {--a|application=discord : Application to send the message.}
                            {--options : Options with message.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications on Discord.';

    public function __construct(
        protected ?string $message = null,
        protected ?string $application = 'discord',
        protected array $options = [],
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): bool
    {
        $this->title();

        $this->message = strval($this->argument('message')) ?: 'Hello world!';
        $this->application = strval($this->option('application')) ?: 'discord';
        $options = $this->option('options') ?: null;
        $options = explode(':', $options);
        $this->options = $options;

        $notify = NotifyService::make()
            ->message($this->message)
            ->send()
        ;

        $this->newLine();

        $this->info('Done!');

        return $notify->isSuccess();
    }
}
