<?php

namespace Kiwilan\Steward\Services;

use Kiwilan\Steward\StewardConfig;

class NotifyService
{
    protected function __construct(
        protected ?string $message = null,
        protected ?string $server = 'default',
        protected ?string $app = 'discord',
        protected ?string $sendto = null,
        protected ?string $inlineConfig = null,
        protected ?array $servers = null,
        //
        protected ?string $id = null,
        protected ?string $token = null,
        protected bool $success = false,
    ) {
    }

    public static function make(
        string $message,
        string $server = 'default',
        string $app = 'discord',
        ?string $sendto = null,
        ?string $inlineConfig = null
    ) {
        $server = strtolower($server);
        $self = new self(
            message: $message,
            server: $server,
            app: $app,
            inlineConfig: $inlineConfig,
            sendto: $sendto,
        );

        if ($self->sendto) {
            $self->handleSendTo();
        } else {
            $self->servers = $self->parseInlineConfig($inlineConfig);

            if (empty($self->servers)) {
                $self->servers = $self->parseLocalConfig();
            }

            if (! $self->id && ! $self->token) {
                $current = $self->servers[$self->server] ?? null;

                if (! $current) {
                    throw new \Exception("Server {$self->server} not found.");
                }

                $self->id = $current['id'];
                $self->token = $current['token'];
            }
        }

        return $self;
    }

    public function send(): bool
    {
        $this->success = match ($this->app) {
            'discord' => $this->discord($this->id, $this->token, $this->message),
            default => false,
        };

        return $this->success;
    }

    /**
     * Parse the config file.
     */
    private function parseInlineConfig(?string $inlineConfig = null): array
    {
        if (! $inlineConfig) {
            return [];
        }

        $servers = [];
        $config = explode(',', $inlineConfig);

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

    private function parseLocalConfig(): array
    {
        $servers = [];
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
                throw new \Exception("Missing ID or token for server {$key}={$id}:{$token}");
            }
        }

        return $servers;
    }

    private function handleSendTo(): void
    {
        $data = explode(':', $this->sendto);
        $app = $data[0] ?? null;
        $id = $data[1] ?? null;
        $token = $data[2] ?? null;

        if (! $app || ! $id || ! $token) {
            throw new \Exception('Invalid sendto pattern.');
        }

        $this->app = $app;
        $this->id = $id;
        $this->token = $token;
    }

    private function discord(string $id, string $token, string $message): bool
    {
        $success = false;
        $baseURL = 'https://discord.com/api/webhooks/';

        $url = "{$baseURL}{$id}/{$token}";

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, [
            'json' => [
                'content' => $message,
            ],
        ]);

        $code = $response->getStatusCode();

        if ($code === 204) {
            $success = true;
        }

        return $success;
    }
}
