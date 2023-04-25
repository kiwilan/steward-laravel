<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Kiwilan\Steward\Services\NotifyService;

class NotifyCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify
                            {message : The message to send.}
                            {--a|app=discord : The app to send the message.}
                            {--sendto : Bypass config and send to any server with this pattern `APP:ID:TOKEN` where APP is `discord`.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications on Discord.';

    public function __construct(
        protected ?string $message = null,
        protected ?string $server = 'default',
        protected ?string $app = 'discord',
        protected ?string $sendto = null,
        protected ?string $inlineServers = null,
        protected ?array $servers = null,
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
        // $this->server = strval($this->option('server')) ?: 'default';
        // $this->app = strval($this->option('app')) ?: 'discord';
        // $this->sendto = strval($this->option('sendto')) ?: null;
        // $this->inlineServers = strval($this->option('inline-servers')) ?: null;

        $notify = NotifyService::make()
            ->message($this->message)
            ->send()
        ;

        $this->newLine();

        $this->info('Done!');

        return $notify->isSuccess();
    }
}
