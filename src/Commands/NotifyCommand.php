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
                            {--s|server=default : The channel to send the message.}
                            {--a|app=discord : The app to send the message.}
                            {--sendto : Bypass config and send to any server with this pattern `APP:ID:TOKEN` where APP is `discord`.}
                            {--inline-servers : Bypass config and set servers listing with `NAME:ID:TOKEN,NAME:ID:TOKEN`.}';

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

        $this->message = strval($this->argument('message')) ?? 'Hello world!';
        $this->server = strval($this->option('server')) ?? 'default';
        $this->app = strval($this->option('app')) ?? 'discord';
        $this->sendto = strval($this->option('sendto')) ?? null;
        $this->inlineServers = strval($this->option('inline-servers')) ?? null;
        $success = false;

        $notify = NotifyService::make(
            message: $this->message,
            server: $this->server,
            app: $this->app,
            sendto: $this->sendto,
            inlineConfig: $this->inlineServers,
        );
        $success = $notify->send();

        $this->newLine();

        $this->info('Done!');

        return $success;
    }
}
