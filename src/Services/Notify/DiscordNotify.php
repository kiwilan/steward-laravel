<?php

namespace Kiwilan\Steward\Services\Notify;

use Kiwilan\Steward\StewardConfig;

class DiscordNotify extends Notifying
{
    const BASE_URL = 'https://discord.com/api/webhooks/';

    public static function make(array $options, string $message): bool
    {
        if (empty($options)) {
            $options = StewardConfig::notifyDiscordServers();
            $data = explode(':', $options);

            $options = [
                $data[0] ?? null,
                $data[1] ?? null,
            ];
        }

        $id = $options[0] ?? null;
        $token = $options[1] ?? null;

        if (! $id || ! $token) {
            throw new \Exception("Missing ID or token for server {$id}:{$token}");
        }

        $success = false;
        $baseURL = self::BASE_URL;

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
