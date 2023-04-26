<?php

namespace Kiwilan\Steward\Services\Notify;

use Kiwilan\Steward\StewardConfig;

class DiscordNotify extends Notifying
{
    const BASE_URL = 'https://discord.com/api/webhooks/';

    public static function send(array $options, string $message): self
    {
        $self = new self(
            options: $options,
            message: $message,
        );

        if (empty($options)) {
            $options = StewardConfig::notifyDiscord();
            $data = explode(':', $options);

            $self->options = [
                $data[0] ?? null,
                $data[1] ?? null,
            ];
        }

        $id = $self->options[0] ?? null;
        $token = $self->options[1] ?? null;

        if (! $id || ! $token) {
            throw new \Exception("Missing ID or token for server {$id}:{$token}");
        }

        $baseURL = self::BASE_URL;

        $self->url = "{$baseURL}{$id}/{$token}";

        $client = new \GuzzleHttp\Client();
        $self->response = $client->request('POST', $self->url, [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'json' => [
                'content' => $self->message,
            ],
            'http_errors' => false,
        ]);

        $code = $self->response->getStatusCode();
        $body = $self->response->getBody()->getContents();

        if ($code === 204) {
            $self->success = true;
        }

        return $self;
    }
}
