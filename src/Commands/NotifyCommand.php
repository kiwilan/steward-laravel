<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Kiwilan\Steward\StewardConfig;

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
                            {--sendto : Bypass config and send to any server with this pattern APP:ID:TOKEN where APP is `discord`.}
                            {--servers : Bypass config and set servers listing.}';

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
        protected array|string|null $servers = null,
    ) {
        parent::__construct();
    }

    // Execute the console command.
    public function handle()
    {
        $this->title();

        $this->message = $this->argument('message') ?? 'Hello world!';
        $this->server = $this->option('server') ?? 'default';
        $this->app = $this->option('app') ?? 'discord';
        $this->sendto = $this->option('sendto') ?? null;
        $this->servers = $this->option('servers') ?? null;
        $success = false;

        if (! $this->sendto) {
            $this->server = strtolower($this->server);
            $this->servers = $this->parseConfig();

            $success = match ($this->app) {
                'discord' => $this->discord(),
                default => false,
            };
        } else {
            $success = $this->handleSendTo();
        }

        $this->info("Sending message on {$this->app}.");
        $this->line($this->message);

        if ($success) {
            $this->info('Message sent successfully.');
        } else {
            $this->error('Message failed to send.');
        }

        $this->newLine();

        $this->info('Done!');

        return $success;
    }

    /**
     * Parse the config file.
     */
    private function parseConfig(): array
    {
        $servers = [];

        if ($this->servers) {
            $config = explode(',', $this->servers);

            foreach ($config as $key => $value) {
                $value = explode(':', $value);
                $name = $value[0] ?? null;
                $id = $value[1] ?? null;
                $token = $value[2] ?? null;

                $servers[$name] = [
                    'name' => $name,
                    'id' => $id,
                    'token' => $token,
                ];
            }

            return $servers;
        }

        $config = StewardConfig::notifyDiscordServers();

        foreach ($config as $key => $value) {
            $value = explode(':', $value);
            $id = $value[0] ?? null;
            $token = $value[1] ?? null;

            $servers[$key] = [
                'name' => $key,
                'id' => $id,
                'token' => $token,
            ];

            if (! $id || ! $token) {
                $this->error("Missing ID or token for server {$key}.");
                $this->error('Please check your .env file.');
                $this->error("{$key}={$id}:{$token}");

                throw new \Exception('Missing ID or token for server.');
            }
        }

        return $servers;
    }

    private function handleSendTo(): bool
    {
        $data = explode(':', $this->sendto);
        $app = $data[0] ?? null;
        $id = $data[1] ?? null;
        $token = $data[2] ?? null;

        if (! $app || ! $id || ! $token) {
            $this->error('Invalid sendto pattern.');
            $this->error('Please check your .env file.');
            $this->error("{$this->sendto}");

            throw new \Exception('Invalid sendto pattern.');
        }

        return match ($this->app) {
            'discord' => $this->discord($id, $token),
            default => false,
        };
    }

    private function discord(?string $id = null, ?string $token = null): bool
    {
        $success = false;
        $baseURL = 'https://discord.com/api/webhooks/';

        $url = "{$baseURL}{$id}/{$token}";

        if (! $id && ! $token) {
            $current = $this->servers[$this->server] ?? null;

            if (! $current) {
                $this->error("Server {$this->server} not found.");
                $this->error('Please check your .env file.');

                throw new \Exception('Server not found.');
            }

            $url = "{$baseURL}{$current['id']}/{$current['token']}";
        }

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, [
            'json' => [
                'content' => $this->message,
            ],
        ]);

        $code = $response->getStatusCode();

        if ($code === 204) {
            $success = true;
        }

        return $success;
    }
}
